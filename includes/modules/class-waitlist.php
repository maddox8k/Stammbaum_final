<?php
/**
 * Waitlist Module
 * Handles all waitlist/application related functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Waitlist {
    
    private static $instance = null;
    private $table_name;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'breeding_applications';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // AJAX hooks
        add_action('wp_ajax_stammbaum_submit_application', array($this, 'ajax_submit_application'));
        add_action('wp_ajax_nopriv_stammbaum_submit_application', array($this, 'ajax_submit_application'));
        add_action('wp_ajax_stammbaum_update_application_status', array($this, 'ajax_update_status'));
        add_action('wp_ajax_stammbaum_delete_application', array($this, 'ajax_delete_application'));
        add_action('wp_ajax_stammbaum_get_applications', array($this, 'ajax_get_applications'));
        add_action('wp_ajax_stammbaum_save_application_notes', array($this, 'ajax_save_notes'));
        add_action('wp_ajax_stammbaum_export_applications', array($this, 'ajax_export_applications'));
        
        // Shortcodes
        add_shortcode('breeding_waitlist', array($this, 'shortcode_waitlist_form'));
    }
    
    /**
     * Submit application
     */
    public function submit_application($data) {
        global $wpdb;
        
        $application_data = array(
            'litter_id' => intval($data['litter_id']),
            'applicant_name' => sanitize_text_field($data['applicant_name']),
            'applicant_email' => sanitize_email($data['applicant_email']),
            'applicant_phone' => sanitize_text_field($data['applicant_phone']),
            'form_data' => !empty($data['form_data']) ? wp_json_encode($data['form_data']) : null,
            'status' => 'pending',
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        );
        
        $result = $wpdb->insert($this->table_name, $application_data);
        
        if ($result) {
            $application_id = $wpdb->insert_id;
            
            // Send notification email
            $this->send_notification_email($application_id);
            
            return $application_id;
        }
        
        return false;
    }
    
    /**
     * Get application by ID
     */
    public function get_application($application_id) {
        global $wpdb;
        
        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT a.*, l.litter_name, l.mother_name, l.father_name, l.expected_date
             FROM {$this->table_name} a
             LEFT JOIN {$wpdb->prefix}breeding_litters l ON a.litter_id = l.id
             WHERE a.id = %d",
            $application_id
        ), ARRAY_A);
        
        if ($application && !empty($application['form_data'])) {
            $application['form_data'] = json_decode($application['form_data'], true);
        }
        
        return $application;
    }
    
    /**
     * Get all applications
     */
    public function get_applications($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'litter_id' => 0,
            'status' => '',
            'search' => '',
            'orderby' => 'submitted_at',
            'order' => 'DESC',
            'limit' => 100,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $params = array();
        
        if (!empty($args['litter_id'])) {
            $where[] = 'a.litter_id = %d';
            $params[] = intval($args['litter_id']);
        }
        
        if (!empty($args['status'])) {
            $where[] = 'a.status = %s';
            $params[] = $args['status'];
        }
        
        if (!empty($args['search'])) {
            $where[] = '(a.applicant_name LIKE %s OR a.applicant_email LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT a.*, l.litter_name, l.mother_name, l.father_name, l.expected_date
                  FROM {$this->table_name} a
                  LEFT JOIN {$wpdb->prefix}breeding_litters l ON a.litter_id = l.id
                  WHERE {$where_clause}
                  ORDER BY a.{$args['orderby']} {$args['order']}
                  LIMIT %d OFFSET %d";
        
        $params[] = $args['limit'];
        $params[] = $args['offset'];
        
        if (!empty($params)) {
            $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        } else {
            $results = $wpdb->get_results($query, ARRAY_A);
        }
        
        // Decode form_data for each application
        foreach ($results as &$app) {
            if (!empty($app['form_data'])) {
                $app['form_data'] = json_decode($app['form_data'], true);
            }
        }
        
        return $results;
    }
    
    /**
     * Update application status
     */
    public function update_status($application_id, $status, $notes = '') {
        global $wpdb;
        
        $data = array(
            'status' => sanitize_text_field($status)
        );
        
        if ($status === 'confirmed' || $status === 'approved') {
            $data['confirmed_at'] = current_time('mysql');
        }
        
        if (!empty($notes)) {
            $data['notes'] = sanitize_textarea_field($notes);
        }
        
        $result = $wpdb->update(
            $this->table_name,
            $data,
            array('id' => $application_id)
        );
        
        if ($result !== false) {
            // Send status update email
            $this->send_status_update_email($application_id, $status);
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete application
     */
    public function delete_application($application_id) {
        global $wpdb;
        
        $result = $wpdb->delete($this->table_name, array('id' => $application_id));
        
        return $result !== false;
    }
    
    /**
     * Save application notes
     */
    public function save_notes($application_id, $notes) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->table_name,
            array('notes' => sanitize_textarea_field($notes)),
            array('id' => $application_id)
        );
        
        return $result !== false;
    }
    
    /**
     * Send notification email
     */
    private function send_notification_email($application_id) {
        $settings = Stammbaum_Core::get_settings();
        
        if (!$settings['email_notifications']) {
            return;
        }
        
        $application = $this->get_application($application_id);
        
        if (!$application) {
            return;
        }
        
        $to = get_option('admin_email');
        $subject = sprintf(__('Neue Wartelisten-Anmeldung: %s', 'stammbaum-manager'), $application['applicant_name']);
        
        $message = sprintf(
            __('Eine neue Wartelisten-Anmeldung wurde eingereicht:<br><br><strong>Name:</strong> %s<br><strong>E-Mail:</strong> %s<br><strong>Telefon:</strong> %s<br><strong>Wurf:</strong> %s<br><br>Bitte überprüfen Sie die Anmeldung im Admin-Bereich.', 'stammbaum-manager'),
            $application['applicant_name'],
            $application['applicant_email'],
            $application['applicant_phone'],
            $application['mother_name'] . ' & ' . $application['father_name']
        );
        
        Stammbaum_Core::send_email($to, $subject, $message);
    }
    
    /**
     * Send status update email
     */
    private function send_status_update_email($application_id, $status) {
        $settings = Stammbaum_Core::get_settings();
        
        if (!$settings['email_notifications']) {
            return;
        }
        
        $application = $this->get_application($application_id);
        
        if (!$application) {
            return;
        }
        
        $status_labels = array(
            'confirmed' => __('bestätigt', 'stammbaum-manager'),
            'approved' => __('genehmigt', 'stammbaum-manager'),
            'rejected' => __('abgelehnt', 'stammbaum-manager')
        );
        
        $status_label = isset($status_labels[$status]) ? $status_labels[$status] : $status;
        
        $to = $application['applicant_email'];
        $subject = sprintf(__('Status Ihrer Wartelisten-Anmeldung: %s', 'stammbaum-manager'), $status_label);
        
        $message = sprintf(
            __('Hallo %s,<br><br>der Status Ihrer Wartelisten-Anmeldung für den Wurf "%s" wurde aktualisiert.<br><br><strong>Neuer Status:</strong> %s<br><br>Bei Fragen können Sie uns gerne kontaktieren.<br><br>Mit freundlichen Grüßen', 'stammbaum-manager'),
            $application['applicant_name'],
            $application['mother_name'] . ' & ' . $application['father_name'],
            $status_label
        );
        
        Stammbaum_Core::send_email($to, $subject, $message);
    }
    
    /**
     * Export applications to CSV
     */
    public function export_to_csv($litter_id = 0) {
        $applications = $this->get_applications(array(
            'litter_id' => $litter_id,
            'limit' => 1000
        ));
        
        if (empty($applications)) {
            return false;
        }
        
        $filename = 'wartelisten-export-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, array(
            'ID',
            'Name',
            'E-Mail',
            'Telefon',
            'Wurf',
            'Status',
            'Eingereicht am',
            'Bestätigt am'
        ));
        
        // Data rows
        foreach ($applications as $app) {
            fputcsv($output, array(
                $app['id'],
                $app['applicant_name'],
                $app['applicant_email'],
                $app['applicant_phone'],
                $app['mother_name'] . ' & ' . $app['father_name'],
                $app['status'],
                $app['submitted_at'],
                $app['confirmed_at']
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * AJAX: Submit application
     */
    public function ajax_submit_application() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $data = Stammbaum_Core::sanitize_array($_POST['application_data']);
        
        $application_id = $this->submit_application($data);
        
        if ($application_id) {
            wp_send_json_success(array(
                'message' => __('Ihre Anmeldung wurde erfolgreich eingereicht', 'stammbaum-manager'),
                'application_id' => $application_id
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Fehler beim Einreichen der Anmeldung', 'stammbaum-manager')
            ));
        }
    }
    
    /**
     * AJAX: Update status
     */
    public function ajax_update_status() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $application_id = intval($_POST['application_id']);
        $status = sanitize_text_field($_POST['status']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = $this->update_status($application_id, $status, $notes);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Status aktualisiert', 'stammbaum-manager')));
        } else {
            wp_send_json_error(array('message' => __('Fehler beim Aktualisieren', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Delete application
     */
    public function ajax_delete_application() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $application_id = intval($_POST['application_id']);
        $result = $this->delete_application($application_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Anmeldung gelöscht', 'stammbaum-manager')));
        } else {
            wp_send_json_error(array('message' => __('Fehler beim Löschen', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Get applications
     */
    public function ajax_get_applications() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $litter_id = intval($_POST['litter_id']);
        $status = sanitize_text_field($_POST['status']);
        
        $applications = $this->get_applications(array(
            'litter_id' => $litter_id,
            'status' => $status
        ));
        
        wp_send_json_success($applications);
    }
    
    /**
     * AJAX: Save notes
     */
    public function ajax_save_notes() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $application_id = intval($_POST['application_id']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $result = $this->save_notes($application_id, $notes);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Notizen gespeichert', 'stammbaum-manager')));
        } else {
            wp_send_json_error(array('message' => __('Fehler beim Speichern', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Export applications
     */
    public function ajax_export_applications() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $litter_id = intval($_POST['litter_id']);
        $this->export_to_csv($litter_id);
    }
    
    /**
     * Shortcode: Waitlist form
     */
    public function shortcode_waitlist_form($atts) {
        $atts = shortcode_atts(array(
            'litter_id' => 0
        ), $atts);
        
        $litter_id = intval($atts['litter_id']);
        
        if (!$litter_id) {
            return '<p>' . __('Keine Wurf-ID angegeben', 'stammbaum-manager') . '</p>';
        }
        
        // Get litter data
        $litters_module = Stammbaum_Litters::get_instance();
        $litter = $litters_module->get_litter($litter_id);
        
        if (!$litter) {
            return '<p>' . __('Wurf nicht gefunden', 'stammbaum-manager') . '</p>';
        }
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/waitlist-form.php';
        return ob_get_clean();
    }
}

