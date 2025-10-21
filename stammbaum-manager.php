<?php
/**
 * Plugin Name: Stammbaum Manager - Complete Edition
 * Plugin URI: https://example.com/stammbaum-manager
 * Description: Komplettes Zucht-Management-System mit Stammbaum, Würfen, Welpen und Wartelisten-Verwaltung
 * Version: 3.0.0
 * Author: Stammbaum Manager Team
 * Author URI: https://example.com
 * Text Domain: stammbaum-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('STAMMBAUM_MANAGER_VERSION', '3.0.0');
define('STAMMBAUM_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STAMMBAUM_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('STAMMBAUM_MANAGER_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class StammbaumManagerComplete {
    
    private static $instance = null;
    
    /**
     * Singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array('StammbaumManagerComplete', 'uninstall'));
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/class-database.php';
        require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/class-core.php';
        
        // Module classes
        require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/modules/class-animals.php';
        require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/modules/class-litters.php';
        require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/modules/class-puppies.php';
        require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/modules/class-waitlist.php';
        
        // Admin classes
        if (is_admin()) {
            require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
            require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-animals.php';
            require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-litters.php';
            require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-puppies.php';
            require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-dashboard.php';
            require_once STAMMBAUM_MANAGER_PLUGIN_DIR . 'includes/class-demo-data.php';
        }
    }
    
    /**
     * Define WordPress hooks
     */
    private function define_hooks() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize modules
        Stammbaum_Animals::get_instance();
        Stammbaum_Litters::get_instance();
        Stammbaum_Puppies::get_instance();
        Stammbaum_Waitlist::get_instance();
        
        // Initialize admin if in admin area
        if (is_admin()) {
            Stammbaum_Admin_Menu::get_instance();
            Stammbaum_Admin_Animals::get_instance();
            Stammbaum_Admin_Litters::get_instance();
            Stammbaum_Admin_Puppies::get_instance();
            Stammbaum_Admin_Dashboard::get_instance();
        }
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'stammbaum-manager',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        // CSS
        wp_enqueue_style(
            'stammbaum-manager-frontend',
            STAMMBAUM_MANAGER_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            STAMMBAUM_MANAGER_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'stammbaum-manager-frontend',
            STAMMBAUM_MANAGER_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            STAMMBAUM_MANAGER_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('stammbaum-manager-frontend', 'stammbaumManager', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('stammbaum_manager_nonce'),
            'site_url' => get_site_url(),
            'strings' => array(
                'loading' => __('Wird geladen...', 'stammbaum-manager'),
                'error' => __('Ein Fehler ist aufgetreten', 'stammbaum-manager'),
                'success' => __('Erfolgreich', 'stammbaum-manager')
            )
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'stammbaum-manager') === false) {
            return;
        }
        
        // Media uploader
        wp_enqueue_media();
        
        // jQuery UI
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('jquery-ui-datepicker');
        
        // CSS
        wp_enqueue_style(
            'stammbaum-manager-admin',
            STAMMBAUM_MANAGER_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            STAMMBAUM_MANAGER_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'stammbaum-manager-admin',
            STAMMBAUM_MANAGER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'),
            STAMMBAUM_MANAGER_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('stammbaum-manager-admin', 'stammbaumManagerAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('stammbaum_manager_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Wirklich löschen?', 'stammbaum-manager'),
                'confirm_delete_animal' => __('Tier wirklich löschen? Dies kann nicht rückgängig gemacht werden!', 'stammbaum-manager'),
                'confirm_delete_litter' => __('Wurf wirklich löschen? Alle Anmeldungen gehen verloren!', 'stammbaum-manager'),
                'confirm_delete_puppy' => __('Welpen wirklich löschen?', 'stammbaum-manager'),
                'loading' => __('Wird geladen...', 'stammbaum-manager'),
                'error' => __('Ein Fehler ist aufgetreten', 'stammbaum-manager'),
                'success' => __('Erfolgreich gespeichert', 'stammbaum-manager'),
                'select_image' => __('Bild auswählen', 'stammbaum-manager'),
                'use_image' => __('Bild verwenden', 'stammbaum-manager')
            )
        ));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        Stammbaum_Database::create_tables();
        
        // Setup capabilities
        $this->setup_capabilities();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create upload directory
        $upload_dir = wp_upload_dir();
        $stammbaum_dir = $upload_dir['basedir'] . '/stammbaum-manager';
        if (!file_exists($stammbaum_dir)) {
            wp_mkdir_p($stammbaum_dir);
        }
        
        // Set activation flag
        update_option('stammbaum_manager_activated', true);
        update_option('stammbaum_manager_version', STAMMBAUM_MANAGER_VERSION);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
        delete_option('stammbaum_manager_activated');
    }
    
    /**
     * Plugin uninstall
     */
    public static function uninstall() {
        // Only remove data if user wants to
        if (get_option('stammbaum_manager_remove_data_on_uninstall', false)) {
            Stammbaum_Database::drop_tables();
            
            // Remove options
            delete_option('stammbaum_manager_version');
            delete_option('stammbaum_manager_activated');
            delete_option('stammbaum_manager_settings');
            
            // Remove capabilities
            $roles = array('administrator', 'editor');
            foreach ($roles as $role_name) {
                $role = get_role($role_name);
                if ($role) {
                    $role->remove_cap('manage_stammbaum');
                    $role->remove_cap('manage_breeding');
                    $role->remove_cap('manage_puppies');
                }
            }
        }
    }
    
    /**
     * Setup user capabilities
     */
    private function setup_capabilities() {
        $roles = array('administrator', 'editor');
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('manage_stammbaum');
                $role->add_cap('manage_breeding');
                $role->add_cap('manage_puppies');
            }
        }
    }
}

/**
 * Initialize the plugin
 */
function stammbaum_manager_init() {
    return StammbaumManagerComplete::get_instance();
}

// Start the plugin
stammbaum_manager_init();

