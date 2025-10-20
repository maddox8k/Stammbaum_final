<?php
/**
 * Litters Module
 * Handles all litter/breeding related functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Litters {
    
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
        $this->table_name = $wpdb->prefix . 'breeding_litters';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // AJAX hooks
        add_action('wp_ajax_stammbaum_save_litter', array($this, 'ajax_save_litter'));
        add_action('wp_ajax_stammbaum_get_litter', array($this, 'ajax_get_litter'));
        add_action('wp_ajax_stammbaum_delete_litter', array($this, 'ajax_delete_litter'));
        add_action('wp_ajax_stammbaum_get_litters', array($this, 'ajax_get_litters'));
        
        // Public AJAX
        add_action('wp_ajax_nopriv_stammbaum_get_litter', array($this, 'ajax_get_litter'));
        add_action('wp_ajax_nopriv_stammbaum_get_litters', array($this, 'ajax_get_litters'));
        
        // Shortcodes
        add_shortcode('breeding_litters', array($this, 'shortcode_litters'));
    }
    
    /**
     * Save litter
     */
    public function save_litter($data) {
        global $wpdb;
        
        $litter_data = array(
            'litter_name' => sanitize_text_field($data['litter_name']),
            'mother_id' => !empty($data['mother_id']) ? intval($data['mother_id']) : null,
            'father_id' => !empty($data['father_id']) ? intval($data['father_id']) : null,
            'breeder_name' => sanitize_text_field($data['breeder_name']),
            'breeder_email' => sanitize_email($data['breeder_email']),
            'breeder_phone' => sanitize_text_field($data['breeder_phone']),
            'mother_name' => sanitize_text_field($data['mother_name']),
            'mother_image' => sanitize_text_field($data['mother_image']),
            'mother_details' => sanitize_textarea_field($data['mother_details']),
            'father_name' => sanitize_text_field($data['father_name']),
            'father_image' => sanitize_text_field($data['father_image']),
            'father_details' => sanitize_textarea_field($data['father_details']),
            'genetics' => sanitize_textarea_field($data['genetics']),
            'colors' => sanitize_textarea_field($data['colors']),
            'health_tests' => sanitize_textarea_field($data['health_tests']),
            'expected_date' => !empty($data['expected_date']) ? sanitize_text_field($data['expected_date']) : null,
            'actual_date' => !empty($data['actual_date']) ? sanitize_text_field($data['actual_date']) : null,
            'expected_puppies' => !empty($data['expected_puppies']) ? intval($data['expected_puppies']) : 0,
            'form_fields' => !empty($data['form_fields']) ? wp_json_encode($data['form_fields']) : null,
            'status' => sanitize_text_field($data['status']),
            'max_applications' => !empty($data['max_applications']) ? intval($data['max_applications']) : 0,
            'notes' => sanitize_textarea_field($data['notes'])
        );
        
        if (isset($data['id']) && !empty($data['id'])) {
            // Update existing litter
            $litter_id = intval($data['id']);
            $result = $wpdb->update($this->table_name, $litter_data, array('id' => $litter_id));
            
            if ($result !== false) {
                return $litter_id;
            }
        } else {
            // Insert new litter
            $result = $wpdb->insert($this->table_name, $litter_data);
            
            if ($result) {
                return $wpdb->insert_id;
            }
        }
        
        return false;
    }
    
    /**
     * Get litter by ID
     */
    public function get_litter($litter_id) {
        global $wpdb;
        
        $litter = $wpdb->get_row($wpdb->prepare(
            "SELECT l.*, 
                    m.name as mother_animal_name, m.profile_image as mother_animal_image,
                    f.name as father_animal_name, f.profile_image as father_animal_image
             FROM {$this->table_name} l
             LEFT JOIN {$wpdb->prefix}stammbaum_animals m ON l.mother_id = m.id
             LEFT JOIN {$wpdb->prefix}stammbaum_animals f ON l.father_id = f.id
             WHERE l.id = %d",
            $litter_id
        ), ARRAY_A);
        
        if ($litter) {
            // Get application count
            $litter['application_count'] = $this->get_application_count($litter_id);
            $litter['pending_count'] = $this->get_application_count($litter_id, 'pending');
            
            // Decode form fields
            if (!empty($litter['form_fields'])) {
                $litter['form_fields'] = json_decode($litter['form_fields'], true);
            }
        }
        
        return $litter;
    }
    
    /**
     * Get all litters
     */
    public function get_litters($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'search' => '',
            'orderby' => 'expected_date',
            'order' => 'DESC',
            'limit' => 100,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $params = array();
        
        if (!empty($args['status'])) {
            $where[] = 'l.status = %s';
            $params[] = $args['status'];
        }
        
        if (!empty($args['search'])) {
            $where[] = '(l.litter_name LIKE %s OR l.mother_name LIKE %s OR l.father_name LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT l.*, 
                         m.name as mother_animal_name, 
                         f.name as father_animal_name,
                         (SELECT COUNT(*) FROM {$wpdb->prefix}breeding_applications WHERE litter_id = l.id) as application_count,
                         (SELECT COUNT(*) FROM {$wpdb->prefix}breeding_applications WHERE litter_id = l.id AND status = 'pending') as pending_count
                  FROM {$this->table_name} l
                  LEFT JOIN {$wpdb->prefix}stammbaum_animals m ON l.mother_id = m.id
                  LEFT JOIN {$wpdb->prefix}stammbaum_animals f ON l.father_id = f.id
                  WHERE {$where_clause} 
                  ORDER BY l.{$args['orderby']} {$args['order']} 
                  LIMIT %d OFFSET %d";
        
        $params[] = $args['limit'];
        $params[] = $args['offset'];
        
        if (!empty($params)) {
            return $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        } else {
            return $wpdb->get_results($query, ARRAY_A);
        }
    }
    
    /**
     * Delete litter
     */
    public function delete_litter($litter_id) {
        global $wpdb;
        
        // Applications will be deleted automatically due to foreign key
        $result = $wpdb->delete($this->table_name, array('id' => $litter_id));
        
        return $result !== false;
    }
    
    /**
     * Get application count for litter
     */
    private function get_application_count($litter_id, $status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'breeding_applications';
        
        if ($status) {
            return $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE litter_id = %d AND status = %s",
                $litter_id, $status
            ));
        } else {
            return $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE litter_id = %d",
                $litter_id
            ));
        }
    }
    
    /**
     * AJAX: Save litter
     */
    public function ajax_save_litter() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $data = Stammbaum_Core::sanitize_array($_POST['litter_data']);
        
        $litter_id = $this->save_litter($data);
        
        if ($litter_id) {
            wp_send_json_success(array(
                'message' => __('Wurf erfolgreich gespeichert', 'stammbaum-manager'),
                'litter_id' => $litter_id
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Fehler beim Speichern', 'stammbaum-manager')
            ));
        }
    }
    
    /**
     * AJAX: Get litter
     */
    public function ajax_get_litter() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $litter_id = intval($_POST['litter_id']);
        $litter = $this->get_litter($litter_id);
        
        if ($litter) {
            wp_send_json_success($litter);
        } else {
            wp_send_json_error(array('message' => __('Wurf nicht gefunden', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Delete litter
     */
    public function ajax_delete_litter() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_breeding');
        
        $litter_id = intval($_POST['litter_id']);
        $result = $this->delete_litter($litter_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Wurf erfolgreich gelöscht', 'stammbaum-manager')));
        } else {
            wp_send_json_error(array('message' => __('Fehler beim Löschen', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Get litters
     */
    public function ajax_get_litters() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $status = sanitize_text_field($_POST['status']);
        $search = sanitize_text_field($_POST['search']);
        
        $litters = $this->get_litters(array(
            'status' => $status,
            'search' => $search
        ));
        
        wp_send_json_success($litters);
    }
    
    /**
     * Shortcode: Litters list
     */
    public function shortcode_litters($atts) {
        $atts = shortcode_atts(array(
            'status' => 'active',
            'limit' => 10
        ), $atts);
        
        $litters = $this->get_litters(array(
            'status' => $atts['status'],
            'limit' => intval($atts['limit'])
        ));
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/litters.php';
        return ob_get_clean();
    }
}

