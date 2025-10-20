<?php
if (!defined('ABSPATH')) exit;
class Stammbaum_Admin_Puppies {
    private static $instance = null;
    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }
    private function __construct() {}
}
