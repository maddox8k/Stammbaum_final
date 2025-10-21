<?php
/**
 * Demo Data Generator
 * Creates sample data for testing and demonstration
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Demo_Data {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_init', array($this, 'check_demo_data_request'));
    }
    
    /**
     * Check if demo data should be generated
     */
    public function check_demo_data_request() {
        if (isset($_GET['stammbaum_generate_demo']) && current_user_can('manage_options')) {
            check_admin_referer('generate_demo_data');
            $this->generate_demo_data();
            wp_redirect(admin_url('admin.php?page=stammbaum-manager&demo_generated=1'));
            exit;
        }
        
        if (isset($_GET['stammbaum_delete_demo']) && current_user_can('manage_options')) {
            check_admin_referer('delete_demo_data');
            $this->delete_demo_data();
            wp_redirect(admin_url('admin.php?page=stammbaum-manager&demo_deleted=1'));
            exit;
        }
    }
    
    /**
     * Generate demo data
     */
    public function generate_demo_data() {
        global $wpdb;
        
        // Create animals
        $animals_module = Stammbaum_Animals::get_instance();
        
        // Grandparents
        $maternal_grandmother = $animals_module->save_animal(array(
            'name' => 'Luna vom Sternenwald',
            'gender' => 'female',
            'birth_date' => '2018-03-15',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz-Braun',
            'registration_number' => 'VDH-DSH-12345',
            'description' => 'Wunderschöne Hündin mit exzellentem Charakter'
        ));
        
        $maternal_grandfather = $animals_module->save_animal(array(
            'name' => 'Max vom Bergland',
            'gender' => 'male',
            'birth_date' => '2017-08-20',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz-Rot',
            'registration_number' => 'VDH-DSH-12346',
            'description' => 'Kräftiger Rüde mit HD-A'
        ));
        
        $paternal_grandmother = $animals_module->save_animal(array(
            'name' => 'Bella vom Sonnenfeld',
            'gender' => 'female',
            'birth_date' => '2018-05-10',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz-Braun',
            'registration_number' => 'VDH-DSH-12347',
            'description' => 'Elegante Hündin mit Top-Gesundheit'
        ));
        
        $paternal_grandfather = $animals_module->save_animal(array(
            'name' => 'Rocky vom Eichenwald',
            'gender' => 'male',
            'birth_date' => '2017-11-05',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz',
            'registration_number' => 'VDH-DSH-12348',
            'description' => 'Imposanter Rüde mit ausgezeichnetem Wesen'
        ));
        
        // Parents
        $mother = $animals_module->save_animal(array(
            'name' => 'Emma vom Rosenpark',
            'gender' => 'female',
            'birth_date' => '2020-04-12',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz-Braun',
            'registration_number' => 'VDH-DSH-56789',
            'mother_id' => $maternal_grandmother,
            'father_id' => $maternal_grandfather,
            'is_breeding_animal' => true,
            'description' => 'Unsere Zuchthündin mit hervorragendem Charakter und besten Gesundheitswerten. HD-A, ED-frei.'
        ));
        
        $father = $animals_module->save_animal(array(
            'name' => 'Bruno vom Adlerhorst',
            'gender' => 'male',
            'birth_date' => '2019-09-08',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz-Rot',
            'registration_number' => 'VDH-DSH-56790',
            'mother_id' => $paternal_grandmother,
            'father_id' => $paternal_grandfather,
            'is_breeding_animal' => true,
            'description' => 'Unser Deckrüde mit ausgezeichnetem Exterieur und Wesen. Mehrfacher Ausstellungssieger.'
        ));
        
        // Additional breeding animals
        $female2 = $animals_module->save_animal(array(
            'name' => 'Mia vom Blütental',
            'gender' => 'female',
            'birth_date' => '2021-02-20',
            'breed' => 'Deutscher Schäferhund',
            'color' => 'Schwarz-Braun',
            'registration_number' => 'VDH-DSH-56791',
            'is_breeding_animal' => true,
            'description' => 'Junge vielversprechende Hündin'
        ));
        
        // Create litters
        $litters_module = Stammbaum_Litters::get_instance();
        
        $litter1 = $litters_module->save_litter(array(
            'mother_id' => $mother,
            'father_id' => $father,
            'expected_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'planned',
            'expected_puppies' => 6,
            'genetics' => 'at/at, ky/ky',
            'colors' => 'Schwarz-Braun, Schwarz-Rot',
            'notes' => 'Vielversprechende Verpaarung mit ausgezeichneten Gesundheitswerten'
        ));
        
        $litter2 = $litters_module->save_litter(array(
            'mother_id' => $mother,
            'father_id' => $father,
            'expected_date' => date('Y-m-d', strtotime('-60 days')),
            'actual_date' => date('Y-m-d', strtotime('-58 days')),
            'status' => 'born',
            'expected_puppies' => 7,
            'genetics' => 'at/at, ky/ky',
            'colors' => 'Schwarz-Braun',
            'notes' => 'Gesunder Wurf mit 7 Welpen'
        ));
        
        // Create puppies (Custom Post Type)
        $puppy_names = array(
            array('name' => 'Apollo', 'gender' => 'male', 'color' => 'Schwarz-Braun'),
            array('name' => 'Athena', 'gender' => 'female', 'color' => 'Schwarz-Braun'),
            array('name' => 'Zeus', 'gender' => 'male', 'color' => 'Schwarz-Rot'),
            array('name' => 'Hera', 'gender' => 'female', 'color' => 'Schwarz-Braun'),
            array('name' => 'Ares', 'gender' => 'male', 'color' => 'Schwarz-Braun'),
        );
        
        foreach ($puppy_names as $index => $puppy_data) {
            $puppy_id = wp_insert_post(array(
                'post_title' => $puppy_data['name'],
                'post_type' => 'welpe',
                'post_status' => 'publish',
                'post_content' => 'Wunderschöner Welpe aus gesunder Zucht mit besten Voraussetzungen.'
            ));
            
            if ($puppy_id) {
                update_post_meta($puppy_id, '_welpe_geschlecht', $puppy_data['gender']);
                update_post_meta($puppy_id, '_welpe_farbe', $puppy_data['color']);
                update_post_meta($puppy_id, '_welpe_geburtsdatum', date('Y-m-d', strtotime('-58 days')));
                update_post_meta($puppy_id, '_welpe_status', $index < 2 ? 'reserviert' : 'verfugbar');
                update_post_meta($puppy_id, '_welpe_preis', '1500');
                update_post_meta($puppy_id, '_welpe_mutter_id', $mother);
                update_post_meta($puppy_id, '_welpe_vater_id', $father);
                update_post_meta($puppy_id, '_welpe_wurf_id', $litter2);
            }
        }
        
        // Create waitlist applications
        $waitlist_module = Stammbaum_Waitlist::get_instance();
        
        $applications = array(
            array(
                'name' => 'Familie Müller',
                'email' => 'mueller@example.com',
                'phone' => '+49 123 456789',
                'status' => 'pending'
            ),
            array(
                'name' => 'Familie Schmidt',
                'email' => 'schmidt@example.com',
                'phone' => '+49 987 654321',
                'status' => 'confirmed'
            ),
            array(
                'name' => 'Familie Weber',
                'email' => 'weber@example.com',
                'phone' => '+49 555 123456',
                'status' => 'pending'
            ),
        );
        
        foreach ($applications as $app_data) {
            $waitlist_module->save_application(array(
                'litter_id' => $litter1,
                'applicant_name' => $app_data['name'],
                'applicant_email' => $app_data['email'],
                'applicant_phone' => $app_data['phone'],
                'message' => 'Wir interessieren uns sehr für einen Welpen aus diesem Wurf.',
                'status' => $app_data['status'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Demo Data Generator'
            ));
        }
        
        return true;
    }
    
    /**
     * Delete demo data
     */
    public function delete_demo_data() {
        global $wpdb;
        
        // Delete animals with "vom" in name (typical German breeding names)
        $animals_table = $wpdb->prefix . 'stammbaum_animals';
        $wpdb->query("DELETE FROM {$animals_table} WHERE name LIKE '%vom%'");
        
        // Delete all litters
        $litters_table = $wpdb->prefix . 'breeding_litters';
        $wpdb->query("TRUNCATE TABLE {$litters_table}");
        
        // Delete all waitlist applications
        $applications_table = $wpdb->prefix . 'breeding_applications';
        $wpdb->query("TRUNCATE TABLE {$applications_table}");
        
        // Delete all puppies (Custom Post Type)
        $puppies = get_posts(array(
            'post_type' => 'welpe',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));
        
        foreach ($puppies as $puppy) {
            wp_delete_post($puppy->ID, true);
        }
        
        return true;
    }
    
    /**
     * Render demo data button in dashboard
     */
    public static function render_demo_button() {
        global $wpdb;
        
        $animals_table = $wpdb->prefix . 'stammbaum_animals';
        $has_data = $wpdb->get_var("SELECT COUNT(*) FROM {$animals_table}") > 0;
        
        $generate_url = wp_nonce_url(
            admin_url('admin.php?stammbaum_generate_demo=1'),
            'generate_demo_data'
        );
        
        $delete_url = wp_nonce_url(
            admin_url('admin.php?stammbaum_delete_demo=1'),
            'delete_demo_data'
        );
        
        ?>
        <div class="stammbaum-card" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(99, 102, 241, 0.1)); border: 2px solid rgba(99, 102, 241, 0.3);">
            <h2 style="margin-top: 0; color: #1e293b;">🎭 <?php _e('Demo-Daten', 'stammbaum-manager'); ?></h2>
            <p style="color: #64748b; margin-bottom: 20px;">
                <?php _e('Generieren Sie Beispiel-Daten zum Testen des Plugins. Enthält Tiere, Würfe, Welpen und Wartelisten-Anmeldungen.', 'stammbaum-manager'); ?>
            </p>
            
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo esc_url($generate_url); ?>" class="button button-primary button-large" onclick="return confirm('<?php _e('Demo-Daten wirklich generieren? Vorhandene Daten bleiben erhalten.', 'stammbaum-manager'); ?>');">
                    ✨ <?php _e('Demo-Daten generieren', 'stammbaum-manager'); ?>
                </a>
                
                <?php if ($has_data): ?>
                    <a href="<?php echo esc_url($delete_url); ?>" class="button button-large" style="background: white; color: #ef4444; border: 2px solid #ef4444;" onclick="return confirm('<?php _e('Alle Demo-Daten wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!', 'stammbaum-manager'); ?>');">
                        🗑️ <?php _e('Demo-Daten löschen', 'stammbaum-manager'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (isset($_GET['demo_generated'])): ?>
                <div style="margin-top: 15px; padding: 12px; background: #d1fae5; border-left: 4px solid #10b981; border-radius: 8px;">
                    <strong style="color: #065f46;">✅ <?php _e('Demo-Daten erfolgreich generiert!', 'stammbaum-manager'); ?></strong>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['demo_deleted'])): ?>
                <div style="margin-top: 15px; padding: 12px; background: #fee2e2; border-left: 4px solid #ef4444; border-radius: 8px;">
                    <strong style="color: #991b1b;">✅ <?php _e('Demo-Daten erfolgreich gelöscht!', 'stammbaum-manager'); ?></strong>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 20px; padding: 15px; background: rgba(255, 255, 255, 0.5); border-radius: 12px;">
                <h4 style="margin: 0 0 10px 0; color: #475569;">📦 <?php _e('Enthaltene Demo-Daten:', 'stammbaum-manager'); ?></h4>
                <ul style="margin: 0; color: #64748b; line-height: 1.8;">
                    <li>🐕 <strong>8 Tiere</strong> (Großeltern, Eltern, Zuchttiere)</li>
                    <li>🐾 <strong>2 Würfe</strong> (1 geplant, 1 geboren)</li>
                    <li>👶 <strong>5 Welpen</strong> (verfügbar & reserviert)</li>
                    <li>📋 <strong>3 Wartelisten-Anmeldungen</strong></li>
                </ul>
            </div>
        </div>
        <?php
    }
}

// Initialize
Stammbaum_Demo_Data::get_instance();
