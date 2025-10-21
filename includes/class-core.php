<?php
/**
 * Core functionality class
 * Provides common functions used across the plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Core {
    
    /**
     * Verify nonce for AJAX requests
     */
    public static function verify_ajax_nonce($nonce_name = 'stammbaum_manager_nonce') {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $nonce_name)) {
            wp_send_json_error(array('message' => __('Sicherheitsüberprüfung fehlgeschlagen', 'stammbaum-manager')));
            wp_die();
        }
    }
    
    /**
     * Check user capability
     */
    public static function check_capability($capability = 'manage_options') {
        // Map custom capabilities to manage_options
        if (in_array($capability, array('manage_stammbaum', 'manage_breeding', 'manage_puppies'))) {
            $capability = 'manage_options';
        }
        
        if (!current_user_can($capability)) {
            wp_send_json_error(array('message' => __('Keine Berechtigung', 'stammbaum-manager')));
            wp_die();
        }
    }
    
    /**
     * Sanitize array recursively
     */
    public static function sanitize_array($array) {
        $sanitized = array();
        
        foreach ($array as $key => $value) {
            $key = sanitize_key($key);
            
            if (is_array($value)) {
                $sanitized[$key] = self::sanitize_array($value);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Format date for display
     */
    public static function format_date($date, $format = null) {
        if (empty($date)) {
            return '';
        }
        
        if ($format === null) {
            $format = get_option('date_format');
        }
        
        return date_i18n($format, strtotime($date));
    }
    
    /**
     * Calculate age from birth date
     */
    public static function calculate_age($birth_date) {
        if (empty($birth_date)) {
            return '';
        }
        
        $birth = new DateTime($birth_date);
        $now = new DateTime();
        $age = $birth->diff($now);
        
        if ($age->y > 0) {
            return sprintf(
                _n('%d Jahr', '%d Jahre', $age->y, 'stammbaum-manager'),
                $age->y
            ) . ', ' . sprintf(
                _n('%d Monat', '%d Monate', $age->m, 'stammbaum-manager'),
                $age->m
            );
        } elseif ($age->m > 0) {
            return sprintf(
                _n('%d Monat', '%d Monate', $age->m, 'stammbaum-manager'),
                $age->m
            ) . ', ' . sprintf(
                _n('%d Tag', '%d Tage', $age->d, 'stammbaum-manager'),
                $age->d
            );
        } else {
            return sprintf(
                _n('%d Tag', '%d Tage', $age->d, 'stammbaum-manager'),
                $age->d
            );
        }
    }
    
    /**
     * Get gender label
     */
    public static function get_gender_label($gender) {
        $labels = array(
            'male' => __('Rüde', 'stammbaum-manager'),
            'female' => __('Hündin', 'stammbaum-manager'),
            'ruede' => __('Rüde', 'stammbaum-manager'),
            'huendin' => __('Hündin', 'stammbaum-manager')
        );
        
        return isset($labels[$gender]) ? $labels[$gender] : $gender;
    }
    
    /**
     * Get status label
     */
    public static function get_status_label($status, $context = 'general') {
        if ($context === 'litter') {
            $labels = array(
                'planned' => __('Geplant', 'stammbaum-manager'),
                'active' => __('Aktiv', 'stammbaum-manager'),
                'born' => __('Geboren', 'stammbaum-manager'),
                'closed' => __('Abgeschlossen', 'stammbaum-manager')
            );
        } elseif ($context === 'puppy') {
            $labels = array(
                'verfugbar' => __('Verfügbar', 'stammbaum-manager'),
                'reserviert' => __('Reserviert', 'stammbaum-manager'),
                'verkauft' => __('Verkauft', 'stammbaum-manager'),
                'available' => __('Verfügbar', 'stammbaum-manager'),
                'reserved' => __('Reserviert', 'stammbaum-manager'),
                'sold' => __('Verkauft', 'stammbaum-manager')
            );
        } elseif ($context === 'application') {
            $labels = array(
                'pending' => __('Ausstehend', 'stammbaum-manager'),
                'approved' => __('Genehmigt', 'stammbaum-manager'),
                'rejected' => __('Abgelehnt', 'stammbaum-manager'),
                'confirmed' => __('Bestätigt', 'stammbaum-manager')
            );
        } else {
            $labels = array(
                'active' => __('Aktiv', 'stammbaum-manager'),
                'inactive' => __('Inaktiv', 'stammbaum-manager')
            );
        }
        
        return isset($labels[$status]) ? $labels[$status] : $status;
    }
    
    /**
     * Upload image
     */
    public static function upload_image($file) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $upload_overrides = array('test_form' => false);
        $uploaded_file = wp_handle_upload($file, $upload_overrides);
        
        if (isset($uploaded_file['error'])) {
            return new WP_Error('upload_error', $uploaded_file['error']);
        }
        
        return $uploaded_file['url'];
    }
    
    /**
     * Send email notification
     */
    public static function send_email($to, $subject, $message, $headers = array()) {
        $default_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        $headers = array_merge($default_headers, $headers);
        
        return wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Get plugin settings
     */
    public static function get_settings() {
        $defaults = array(
            'email_notifications' => true,
            'max_applications' => 10,
            'require_approval' => true,
            'enable_whatsapp' => false,
            'whatsapp_number' => '',
            'enable_social_sharing' => true,
            'enable_favorites' => true,
            'show_anfrage_button' => true,
            'currency' => 'EUR',
            'currency_symbol' => '€'
        );
        
        $settings = get_option('stammbaum_manager_settings', array());
        
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Update plugin settings
     */
    public static function update_settings($settings) {
        $current = self::get_settings();
        $updated = array_merge($current, $settings);
        
        return update_option('stammbaum_manager_settings', $updated);
    }
    
    /**
     * Log activity
     */
    public static function log($message, $type = 'info') {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            error_log('[Stammbaum Manager] [' . strtoupper($type) . '] ' . $message);
        }
    }
}

