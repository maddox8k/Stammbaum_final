<?php
/**
 * Animals Module
 * Handles all animal/pedigree related functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Animals {
    
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
        $this->table_name = $wpdb->prefix . 'stammbaum_animals';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // AJAX hooks
        add_action('wp_ajax_stammbaum_save_animal', array($this, 'ajax_save_animal'));
        add_action('wp_ajax_stammbaum_get_animal', array($this, 'ajax_get_animal'));
        add_action('wp_ajax_stammbaum_delete_animal', array($this, 'ajax_delete_animal'));
        add_action('wp_ajax_stammbaum_search_animals', array($this, 'ajax_search_animals'));
        add_action('wp_ajax_stammbaum_get_pedigree', array($this, 'ajax_get_pedigree'));
        
        // Public AJAX hooks
        add_action('wp_ajax_nopriv_stammbaum_get_animal', array($this, 'ajax_get_animal'));
        add_action('wp_ajax_nopriv_stammbaum_get_pedigree', array($this, 'ajax_get_pedigree'));
        
        // Shortcodes
        add_shortcode('stammbaum', array($this, 'shortcode_pedigree'));
        add_shortcode('stammbaum_profil', array($this, 'shortcode_profile'));
        add_shortcode('stammbaum_galerie', array($this, 'shortcode_gallery'));
    }
    
    /**
     * Save animal
     */
    public function save_animal($data) {
        global $wpdb;
        
        $animal_data = array(
            'name' => sanitize_text_field($data['name']),
            'gender' => sanitize_text_field($data['gender']),
            'animal_type' => !empty($data['animal_type']) ? sanitize_text_field($data['animal_type']) : 'breeding',
            'birth_date' => !empty($data['birth_date']) ? sanitize_text_field($data['birth_date']) : null,
            'breed' => !empty($data['breed']) ? sanitize_text_field($data['breed']) : '',
            'color' => !empty($data['color']) ? sanitize_text_field($data['color']) : '',
            'genetics' => !empty($data['genetics']) ? sanitize_text_field($data['genetics']) : '',
            'registration_number' => !empty($data['registration_number']) ? sanitize_text_field($data['registration_number']) : '',
            'mother_id' => !empty($data['mother_id']) ? intval($data['mother_id']) : null,
            'father_id' => !empty($data['father_id']) ? intval($data['father_id']) : null,
            'maternal_grandmother_id' => !empty($data['maternal_grandmother_id']) ? intval($data['maternal_grandmother_id']) : null,
            'maternal_grandfather_id' => !empty($data['maternal_grandfather_id']) ? intval($data['maternal_grandfather_id']) : null,
            'paternal_grandmother_id' => !empty($data['paternal_grandmother_id']) ? intval($data['paternal_grandmother_id']) : null,
            'paternal_grandfather_id' => !empty($data['paternal_grandfather_id']) ? intval($data['paternal_grandfather_id']) : null,
            'profile_image' => !empty($data['profile_image']) ? sanitize_text_field($data['profile_image']) : '',
            'is_breeding_animal' => isset($data['is_breeding_animal']) ? (bool)$data['is_breeding_animal'] : false,
            'is_external' => isset($data['is_external']) ? (bool)$data['is_external'] : false,
            'external_info' => !empty($data['external_info']) ? sanitize_textarea_field($data['external_info']) : '',
            'description' => !empty($data['description']) ? sanitize_textarea_field($data['description']) : ''
        );
        
        if (isset($data['id']) && !empty($data['id'])) {
            // Update existing animal
            $animal_id = intval($data['id']);
            $result = $wpdb->update($this->table_name, $animal_data, array('id' => $animal_id));
            
            if ($result !== false) {
                return $animal_id;
            }
        } else {
            // Insert new animal
            $result = $wpdb->insert($this->table_name, $animal_data);
            
            if ($result) {
                return $wpdb->insert_id;
            }
        }
        
        return false;
    }
    
    /**
     * Get animal by ID
     */
    public function get_animal($animal_id) {
        global $wpdb;
        
        $animal = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $animal_id
        ), ARRAY_A);
        
        if ($animal) {
            // Get additional data
            $animal['genetics_tests'] = $this->get_genetics($animal_id);
            $animal['achievements'] = $this->get_achievements($animal_id);
            $animal['offspring_gallery'] = $this->get_offspring_gallery($animal_id);
            $animal['additional_info'] = $this->get_additional_info($animal_id);
        }
        
        return $animal;
    }
    
    /**
     * Get all animals (alias for get_animals)
     */
    public function get_all_animals($args = array()) {
        return $this->get_animals($args);
    }
    
    /**
     * Get all animals
     */
    public function get_animals($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'gender' => '',
            'is_breeding_animal' => null,
            'search' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => 100,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $params = array();
        
        if (!empty($args['gender'])) {
            $where[] = 'gender = %s';
            $params[] = $args['gender'];
        }
        
        if ($args['is_breeding_animal'] !== null) {
            $where[] = 'is_breeding_animal = %d';
            $params[] = (int)$args['is_breeding_animal'];
        }
        
        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR registration_number LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = "SELECT * FROM {$this->table_name} WHERE {$where_clause} 
                  ORDER BY {$args['orderby']} {$args['order']} 
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
     * Delete animal
     */
    public function delete_animal($animal_id) {
        global $wpdb;
        
        // Check if animal is used as parent
        $used_as_parent = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} 
             WHERE mother_id = %d OR father_id = %d 
             OR maternal_grandmother_id = %d OR maternal_grandfather_id = %d
             OR paternal_grandmother_id = %d OR paternal_grandfather_id = %d",
            $animal_id, $animal_id, $animal_id, $animal_id, $animal_id, $animal_id
        ));
        
        if ($used_as_parent > 0) {
            return new WP_Error('in_use', __('Tier kann nicht gelöscht werden, da es in Stammbäumen verwendet wird.', 'stammbaum-manager'));
        }
        
        // Delete related data (will be handled by foreign keys)
        $result = $wpdb->delete($this->table_name, array('id' => $animal_id));
        
        return $result !== false;
    }
    
    /**
     * Get complete pedigree data
     */
    public function get_pedigree($animal_id, $generations = 3) {
        $animal = $this->get_animal($animal_id);
        
        if (!$animal) {
            return null;
        }
        
        $pedigree = array(
            'animal' => $animal,
            'parents' => array(),
            'grandparents' => array(),
            'great_grandparents' => array()
        );
        
        // Get parents
        if ($animal['mother_id']) {
            $pedigree['parents']['mother'] = $this->get_animal($animal['mother_id']);
        }
        if ($animal['father_id']) {
            $pedigree['parents']['father'] = $this->get_animal($animal['father_id']);
        }
        
        // Get grandparents
        if ($animal['maternal_grandmother_id']) {
            $pedigree['grandparents']['maternal_grandmother'] = $this->get_animal($animal['maternal_grandmother_id']);
        }
        if ($animal['maternal_grandfather_id']) {
            $pedigree['grandparents']['maternal_grandfather'] = $this->get_animal($animal['maternal_grandfather_id']);
        }
        if ($animal['paternal_grandmother_id']) {
            $pedigree['grandparents']['paternal_grandmother'] = $this->get_animal($animal['paternal_grandmother_id']);
        }
        if ($animal['paternal_grandfather_id']) {
            $pedigree['grandparents']['paternal_grandfather'] = $this->get_animal($animal['paternal_grandfather_id']);
        }
        
        return $pedigree;
    }
    
    /**
     * Get genetics tests
     */
    private function get_genetics($animal_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'stammbaum_genetics';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE animal_id = %d ORDER BY test_date DESC",
            $animal_id
        ), ARRAY_A);
    }
    
    /**
     * Get achievements
     */
    private function get_achievements($animal_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'stammbaum_achievements';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE animal_id = %d ORDER BY date DESC",
            $animal_id
        ), ARRAY_A);
    }
    
    /**
     * Get offspring gallery
     */
    private function get_offspring_gallery($animal_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'stammbaum_offspring_gallery';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE parent_id = %d ORDER BY display_order ASC",
            $animal_id
        ), ARRAY_A);
    }
    
    /**
     * Get additional info
     */
    private function get_additional_info($animal_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'stammbaum_additional_info';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT info_key, info_value FROM {$table} WHERE animal_id = %d",
            $animal_id
        ), ARRAY_A);
        
        $info = array();
        foreach ($results as $row) {
            $info[$row['info_key']] = $row['info_value'];
        }
        
        return $info;
    }
    
    /**
     * AJAX: Save animal
     */
    public function ajax_save_animal() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_stammbaum');
        
        $data = Stammbaum_Core::sanitize_array($_POST['animal_data']);
        
        $animal_id = $this->save_animal($data);
        
        if ($animal_id) {
            wp_send_json_success(array(
                'message' => __('Tier erfolgreich gespeichert', 'stammbaum-manager'),
                'animal_id' => $animal_id
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Fehler beim Speichern', 'stammbaum-manager')
            ));
        }
    }
    
    /**
     * AJAX: Get animal
     */
    public function ajax_get_animal() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $animal_id = intval($_POST['animal_id']);
        $animal = $this->get_animal($animal_id);
        
        if ($animal) {
            wp_send_json_success($animal);
        } else {
            wp_send_json_error(array('message' => __('Tier nicht gefunden', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Delete animal
     */
    public function ajax_delete_animal() {
        Stammbaum_Core::verify_ajax_nonce('stammbaum_manager_admin_nonce');
        Stammbaum_Core::check_capability('manage_stammbaum');
        
        $animal_id = intval($_POST['animal_id']);
        $result = $this->delete_animal($animal_id);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } elseif ($result) {
            wp_send_json_success(array('message' => __('Tier erfolgreich gelöscht', 'stammbaum-manager')));
        } else {
            wp_send_json_error(array('message' => __('Fehler beim Löschen', 'stammbaum-manager')));
        }
    }
    
    /**
     * AJAX: Search animals
     */
    public function ajax_search_animals() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $search = sanitize_text_field($_POST['search']);
        $gender = sanitize_text_field($_POST['gender']);
        
        $animals = $this->get_animals(array(
            'search' => $search,
            'gender' => $gender,
            'limit' => 50
        ));
        
        wp_send_json_success($animals);
    }
    
    /**
     * AJAX: Get pedigree
     */
    public function ajax_get_pedigree() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $animal_id = intval($_POST['animal_id']);
        $pedigree = $this->get_pedigree($animal_id);
        
        if ($pedigree) {
            wp_send_json_success($pedigree);
        } else {
            wp_send_json_error(array('message' => __('Stammbaum nicht gefunden', 'stammbaum-manager')));
        }
    }
    
    /**
     * Shortcode: Pedigree display
     */
    public function shortcode_pedigree($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'generations' => 3
        ), $atts);
        
        $animal_id = intval($atts['id']);
        
        if (!$animal_id) {
            return '<p>' . __('Keine Tier-ID angegeben', 'stammbaum-manager') . '</p>';
        }
        
        $pedigree = $this->get_pedigree($animal_id, intval($atts['generations']));
        
        if (!$pedigree) {
            return '<p>' . __('Stammbaum nicht gefunden', 'stammbaum-manager') . '</p>';
        }
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/pedigree.php';
        return ob_get_clean();
    }
    
    /**
     * Shortcode: Animal profile
     */
    public function shortcode_profile($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);
        
        $animal_id = intval($atts['id']);
        
        if (!$animal_id) {
            return '<p>' . __('Keine Tier-ID angegeben', 'stammbaum-manager') . '</p>';
        }
        
        $animal = $this->get_animal($animal_id);
        
        if (!$animal) {
            return '<p>' . __('Tier nicht gefunden', 'stammbaum-manager') . '</p>';
        }
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/profile.php';
        return ob_get_clean();
    }
    
    /**
     * Shortcode: Gallery
     */
    public function shortcode_gallery($atts) {
        $atts = shortcode_atts(array(
            'type' => 'all',
            'limit' => 12
        ), $atts);
        
        $animals = $this->get_animals(array(
            'is_breeding_animal' => $atts['type'] === 'breeding' ? true : null,
            'limit' => intval($atts['limit'])
        ));
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/gallery.php';
        return ob_get_clean();
    }
}

