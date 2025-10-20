<?php
/**
 * Admin Menu Class
 */

if (!defined('ABSPATH')) exit;

class Stammbaum_Admin_Menu {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
    }
    
    public function add_menu_pages() {
        add_menu_page(
            __('Stammbaum Manager', 'stammbaum-manager'),
            __('Stammbaum Manager', 'stammbaum-manager'),
            'manage_stammbaum',
            'stammbaum-manager',
            array($this, 'dashboard_page'),
            'dashicons-networking',
            30
        );
        
        add_submenu_page('stammbaum-manager', __('Dashboard', 'stammbaum-manager'), __('Dashboard', 'stammbaum-manager'), 'manage_stammbaum', 'stammbaum-manager', array($this, 'dashboard_page'));
        add_submenu_page('stammbaum-manager', __('Tiere', 'stammbaum-manager'), __('Tiere', 'stammbaum-manager'), 'manage_stammbaum', 'stammbaum-animals', array($this, 'animals_page'));
        add_submenu_page('stammbaum-manager', __('Würfe', 'stammbaum-manager'), __('Würfe', 'stammbaum-manager'), 'manage_breeding', 'stammbaum-litters', array($this, 'litters_page'));
        add_submenu_page('stammbaum-manager', __('Wartelisten', 'stammbaum-manager'), __('Wartelisten', 'stammbaum-manager'), 'manage_breeding', 'stammbaum-waitlist', array($this, 'waitlist_page'));
        add_submenu_page('stammbaum-manager', __('Einstellungen', 'stammbaum-manager'), __('Einstellungen', 'stammbaum-manager'), 'manage_options', 'stammbaum-settings', array($this, 'settings_page'));
    }
    
    public function dashboard_page() {
        $template_file = STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/admin/dashboard.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap"><h1>Stammbaum Manager Dashboard</h1><p>Template nicht gefunden: ' . esc_html($template_file) . '</p></div>';
        }
    }
    
    public function animals_page() {
        $template_file = STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/admin/animals-list.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap"><h1>Tiere</h1><p>Template nicht gefunden</p></div>';
        }
    }
    
    public function litters_page() {
        $template_file = STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/admin/litters-list.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap"><h1>Würfe</h1><p>Template wird noch erstellt...</p></div>';
        }
    }
    
    public function waitlist_page() {
        $template_file = STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/admin/waitlist.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap"><h1>Wartelisten</h1><p>Template wird noch erstellt...</p></div>';
        }
    }
    
    public function settings_page() {
        $template_file = STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/admin/settings.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap"><h1>Einstellungen</h1><p>Template wird noch erstellt...</p></div>';
        }
    }
}

