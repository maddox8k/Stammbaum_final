<?php
/**
 * Database Management Class
 * Handles all database operations for the Stammbaum Manager plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Database {
    
    /**
     * Create all plugin tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // 1. Animals Table (from Stammbaum Manager)
        $table_animals = $wpdb->prefix . 'stammbaum_animals';
        $sql_animals = "CREATE TABLE $table_animals (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            gender varchar(10) NOT NULL,
            birth_date date DEFAULT NULL,
            breed varchar(255) DEFAULT NULL,
            color varchar(255) DEFAULT NULL,
            genetics varchar(255) DEFAULT NULL,
            registration_number varchar(255) DEFAULT NULL,
            mother_id mediumint(9) DEFAULT NULL,
            father_id mediumint(9) DEFAULT NULL,
            maternal_grandmother_id mediumint(9) DEFAULT NULL,
            maternal_grandfather_id mediumint(9) DEFAULT NULL,
            paternal_grandmother_id mediumint(9) DEFAULT NULL,
            paternal_grandfather_id mediumint(9) DEFAULT NULL,
            profile_image varchar(500) DEFAULT NULL,
            is_breeding_animal boolean DEFAULT FALSE,
            is_external boolean DEFAULT FALSE,
            external_info text DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY mother_id (mother_id),
            KEY father_id (father_id),
            KEY maternal_grandmother_id (maternal_grandmother_id),
            KEY maternal_grandfather_id (maternal_grandfather_id),
            KEY paternal_grandmother_id (paternal_grandmother_id),
            KEY paternal_grandfather_id (paternal_grandfather_id),
            KEY is_breeding_animal (is_breeding_animal)
        ) $charset_collate;";
        
        // 2. Genetics Table
        $table_genetics = $wpdb->prefix . 'stammbaum_genetics';
        $sql_genetics = "CREATE TABLE $table_genetics (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            animal_id mediumint(9) NOT NULL,
            test_type varchar(255) NOT NULL,
            test_result varchar(255) NOT NULL,
            test_date date DEFAULT NULL,
            certificate_number varchar(255) DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY animal_id (animal_id),
            FOREIGN KEY (animal_id) REFERENCES $table_animals(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // 3. Achievements Table
        $table_achievements = $wpdb->prefix . 'stammbaum_achievements';
        $sql_achievements = "CREATE TABLE $table_achievements (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            animal_id mediumint(9) NOT NULL,
            achievement_type varchar(255) NOT NULL,
            title varchar(255) NOT NULL,
            date date DEFAULT NULL,
            location varchar(255) DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY animal_id (animal_id),
            FOREIGN KEY (animal_id) REFERENCES $table_animals(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // 4. Offspring Gallery Table
        $table_offspring = $wpdb->prefix . 'stammbaum_offspring_gallery';
        $sql_offspring = "CREATE TABLE $table_offspring (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            parent_id mediumint(9) NOT NULL,
            image_url varchar(500) NOT NULL,
            caption varchar(255) DEFAULT NULL,
            display_order int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY parent_id (parent_id),
            FOREIGN KEY (parent_id) REFERENCES $table_animals(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // 5. Additional Info Table
        $table_additional_info = $wpdb->prefix . 'stammbaum_additional_info';
        $sql_additional_info = "CREATE TABLE $table_additional_info (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            animal_id mediumint(9) NOT NULL,
            info_key varchar(255) NOT NULL,
            info_value text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY animal_id (animal_id),
            FOREIGN KEY (animal_id) REFERENCES $table_animals(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // 6. Litters Table (from Breeding Waitlist Manager)
        $table_litters = $wpdb->prefix . 'breeding_litters';
        $sql_litters = "CREATE TABLE $table_litters (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            litter_name varchar(255) DEFAULT NULL,
            mother_id mediumint(9) DEFAULT NULL,
            father_id mediumint(9) DEFAULT NULL,
            breeder_name varchar(255) NOT NULL,
            breeder_email varchar(255) DEFAULT NULL,
            breeder_phone varchar(50) DEFAULT NULL,
            mother_name varchar(255) NOT NULL,
            mother_image varchar(500) DEFAULT NULL,
            mother_details text DEFAULT NULL,
            father_name varchar(255) NOT NULL,
            father_image varchar(500) DEFAULT NULL,
            father_details text DEFAULT NULL,
            genetics text DEFAULT NULL,
            colors text DEFAULT NULL,
            health_tests text DEFAULT NULL,
            expected_date date DEFAULT NULL,
            actual_date date DEFAULT NULL,
            expected_puppies int DEFAULT 0,
            form_fields longtext DEFAULT NULL,
            status varchar(50) DEFAULT 'planned',
            max_applications int DEFAULT 0,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY mother_id (mother_id),
            KEY father_id (father_id),
            KEY status (status),
            KEY expected_date (expected_date),
            FOREIGN KEY (mother_id) REFERENCES $table_animals(id) ON DELETE SET NULL,
            FOREIGN KEY (father_id) REFERENCES $table_animals(id) ON DELETE SET NULL
        ) $charset_collate;";
        
        // 7. Applications/Waitlist Table
        $table_applications = $wpdb->prefix . 'breeding_applications';
        $sql_applications = "CREATE TABLE $table_applications (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            litter_id mediumint(9) NOT NULL,
            applicant_name varchar(255) NOT NULL,
            applicant_email varchar(255) NOT NULL,
            applicant_phone varchar(50) DEFAULT NULL,
            form_data longtext DEFAULT NULL,
            status varchar(50) DEFAULT 'pending',
            priority int DEFAULT 0,
            notes text DEFAULT NULL,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
            confirmed_at datetime DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            PRIMARY KEY (id),
            KEY litter_id (litter_id),
            KEY status (status),
            KEY applicant_email (applicant_email),
            KEY submitted_at (submitted_at),
            FOREIGN KEY (litter_id) REFERENCES $table_litters(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // Execute table creation
        dbDelta($sql_animals);
        dbDelta($sql_genetics);
        dbDelta($sql_achievements);
        dbDelta($sql_offspring);
        dbDelta($sql_additional_info);
        dbDelta($sql_litters);
        dbDelta($sql_applications);
        
        // Update version
        update_option('stammbaum_manager_db_version', STAMMBAUM_MANAGER_VERSION);
    }
    
    /**
     * Drop all plugin tables
     */
    public static function drop_tables() {
        global $wpdb;
        
        // Drop tables in reverse order due to foreign keys
        $tables = array(
            $wpdb->prefix . 'breeding_applications',
            $wpdb->prefix . 'breeding_litters',
            $wpdb->prefix . 'stammbaum_additional_info',
            $wpdb->prefix . 'stammbaum_offspring_gallery',
            $wpdb->prefix . 'stammbaum_achievements',
            $wpdb->prefix . 'stammbaum_genetics',
            $wpdb->prefix . 'stammbaum_animals'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
    
    /**
     * Get table names
     */
    public static function get_table_names() {
        global $wpdb;
        
        return array(
            'animals' => $wpdb->prefix . 'stammbaum_animals',
            'genetics' => $wpdb->prefix . 'stammbaum_genetics',
            'achievements' => $wpdb->prefix . 'stammbaum_achievements',
            'offspring' => $wpdb->prefix . 'stammbaum_offspring_gallery',
            'additional_info' => $wpdb->prefix . 'stammbaum_additional_info',
            'litters' => $wpdb->prefix . 'breeding_litters',
            'applications' => $wpdb->prefix . 'breeding_applications'
        );
    }
}

