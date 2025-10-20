<?php
/**
 * Puppies Module
 * Handles all puppy related functionality using Custom Post Type
 */

if (!defined('ABSPATH')) {
    exit;
}

class Stammbaum_Puppies {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Register custom post type
        add_action('init', array($this, 'register_post_type'));
        
        // Meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_welpe', array($this, 'save_meta'), 10, 2);
        
        // Frontend display
        add_filter('the_content', array($this, 'single_content'));
        add_action('wp_head', array($this, 'single_head'));
        
        // AJAX hooks
        add_action('wp_ajax_stammbaum_get_puppy_details', array($this, 'ajax_get_puppy_details'));
        add_action('wp_ajax_nopriv_stammbaum_get_puppy_details', array($this, 'ajax_get_puppy_details'));
        
        // Shortcodes
        add_shortcode('welpen_liste', array($this, 'shortcode_puppies_list'));
        add_shortcode('whatsapp_button', array($this, 'shortcode_whatsapp_button'));
    }
    
    /**
     * Register custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name' => __('Welpen', 'stammbaum-manager'),
            'singular_name' => __('Welpe', 'stammbaum-manager'),
            'menu_name' => __('Welpen', 'stammbaum-manager'),
            'add_new' => __('Neuen Welpen hinzufügen', 'stammbaum-manager'),
            'add_new_item' => __('Neuen Welpen hinzufügen', 'stammbaum-manager'),
            'edit_item' => __('Welpen bearbeiten', 'stammbaum-manager'),
            'new_item' => __('Neuer Welpe', 'stammbaum-manager'),
            'view_item' => __('Welpen anzeigen', 'stammbaum-manager'),
            'search_items' => __('Welpen suchen', 'stammbaum-manager'),
            'not_found' => __('Keine Welpen gefunden', 'stammbaum-manager'),
            'not_found_in_trash' => __('Keine Welpen im Papierkorb gefunden', 'stammbaum-manager')
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => 'stammbaum-manager',
            'query_var' => true,
            'rewrite' => array('slug' => 'welpe', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest' => true
        );
        
        register_post_type('welpe', $args);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'puppy_details',
            __('Welpen Details', 'stammbaum-manager'),
            array($this, 'render_details_meta_box'),
            'welpe',
            'normal',
            'high'
        );
        
        add_meta_box(
            'puppy_parents',
            __('Elterntiere', 'stammbaum-manager'),
            array($this, 'render_parents_meta_box'),
            'welpe',
            'normal',
            'high'
        );
        
        add_meta_box(
            'puppy_gallery',
            __('Galerie', 'stammbaum-manager'),
            array($this, 'render_gallery_meta_box'),
            'welpe',
            'normal',
            'default'
        );
    }
    
    /**
     * Render details meta box
     */
    public function render_details_meta_box($post) {
        wp_nonce_field('puppy_meta_box', 'puppy_meta_box_nonce');
        
        $birth_date = get_post_meta($post->ID, '_welpe_geburtsdatum', true);
        $gender = get_post_meta($post->ID, '_welpe_geschlecht', true);
        $color = get_post_meta($post->ID, '_welpe_farbe', true);
        $genetics = get_post_meta($post->ID, '_welpe_genetik', true);
        $weight = get_post_meta($post->ID, '_welpe_gewicht', true);
        $price = get_post_meta($post->ID, '_welpe_preis', true);
        $status = get_post_meta($post->ID, '_welpe_status', true) ?: 'verfugbar';
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="welpe_geburtsdatum"><?php _e('Geburtsdatum:', 'stammbaum-manager'); ?></label></th>
                <td><input type="date" id="welpe_geburtsdatum" name="welpe_geburtsdatum" value="<?php echo esc_attr($birth_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="welpe_geschlecht"><?php _e('Geschlecht:', 'stammbaum-manager'); ?></label></th>
                <td>
                    <select id="welpe_geschlecht" name="welpe_geschlecht">
                        <option value="männlich" <?php selected($gender, 'männlich'); ?>><?php _e('Männlich', 'stammbaum-manager'); ?></option>
                        <option value="weiblich" <?php selected($gender, 'weiblich'); ?>><?php _e('Weiblich', 'stammbaum-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="welpe_farbe"><?php _e('Farbe:', 'stammbaum-manager'); ?></label></th>
                <td><input type="text" id="welpe_farbe" name="welpe_farbe" value="<?php echo esc_attr($color); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="welpe_gewicht"><?php _e('Gewicht (g):', 'stammbaum-manager'); ?></label></th>
                <td><input type="number" id="welpe_gewicht" name="welpe_gewicht" value="<?php echo esc_attr($weight); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="welpe_preis"><?php _e('Preis (€):', 'stammbaum-manager'); ?></label></th>
                <td><input type="number" id="welpe_preis" name="welpe_preis" value="<?php echo esc_attr($price); ?>" class="regular-text" step="0.01" /></td>
            </tr>
            <tr>
                <th><label for="welpe_status"><?php _e('Status:', 'stammbaum-manager'); ?></label></th>
                <td>
                    <select id="welpe_status" name="welpe_status">
                        <option value="verfugbar" <?php selected($status, 'verfugbar'); ?>><?php _e('Verfügbar', 'stammbaum-manager'); ?></option>
                        <option value="reserviert" <?php selected($status, 'reserviert'); ?>><?php _e('Reserviert', 'stammbaum-manager'); ?></option>
                        <option value="verkauft" <?php selected($status, 'verkauft'); ?>><?php _e('Verkauft', 'stammbaum-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="welpe_genetik"><?php _e('Genetik/Gesundheit:', 'stammbaum-manager'); ?></label></th>
                <td><textarea id="welpe_genetik" name="welpe_genetik" rows="4" class="large-text"><?php echo esc_textarea($genetics); ?></textarea></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render parents meta box
     */
    public function render_parents_meta_box($post) {
        $mother_id = get_post_meta($post->ID, '_welpe_mutter_id', true);
        $father_id = get_post_meta($post->ID, '_welpe_vater_id', true);
        $litter_id = get_post_meta($post->ID, '_welpe_litter_id', true);
        
        // Get available animals
        global $wpdb;
        $animals_table = $wpdb->prefix . 'stammbaum_animals';
        $litters_table = $wpdb->prefix . 'breeding_litters';
        
        $mothers = $wpdb->get_results("SELECT id, name FROM {$animals_table} WHERE gender IN ('female', 'huendin') ORDER BY name");
        $fathers = $wpdb->get_results("SELECT id, name FROM {$animals_table} WHERE gender IN ('male', 'ruede') ORDER BY name");
        $litters = $wpdb->get_results("SELECT id, litter_name, mother_name, father_name FROM {$litters_table} ORDER BY expected_date DESC");
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="welpe_litter_id"><?php _e('Wurf:', 'stammbaum-manager'); ?></label></th>
                <td>
                    <select id="welpe_litter_id" name="welpe_litter_id" class="regular-text">
                        <option value=""><?php _e('Keinem Wurf zugeordnet', 'stammbaum-manager'); ?></option>
                        <?php foreach ($litters as $litter): ?>
                            <option value="<?php echo $litter->id; ?>" <?php selected($litter_id, $litter->id); ?>>
                                <?php echo esc_html($litter->litter_name ? $litter->litter_name : ($litter->mother_name . ' & ' . $litter->father_name)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="welpe_mutter_id"><?php _e('Mutter:', 'stammbaum-manager'); ?></label></th>
                <td>
                    <select id="welpe_mutter_id" name="welpe_mutter_id" class="regular-text">
                        <option value=""><?php _e('Keine Auswahl', 'stammbaum-manager'); ?></option>
                        <?php foreach ($mothers as $mother): ?>
                            <option value="<?php echo $mother->id; ?>" <?php selected($mother_id, $mother->id); ?>>
                                <?php echo esc_html($mother->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="welpe_vater_id"><?php _e('Vater:', 'stammbaum-manager'); ?></label></th>
                <td>
                    <select id="welpe_vater_id" name="welpe_vater_id" class="regular-text">
                        <option value=""><?php _e('Keine Auswahl', 'stammbaum-manager'); ?></option>
                        <?php foreach ($fathers as $father): ?>
                            <option value="<?php echo $father->id; ?>" <?php selected($father_id, $father->id); ?>>
                                <?php echo esc_html($father->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render gallery meta box
     */
    public function render_gallery_meta_box($post) {
        $gallery_ids = get_post_meta($post->ID, '_welpe_gallery', true);
        
        ?>
        <div id="welpen-gallery-container">
            <input type="hidden" id="welpe_gallery" name="welpe_gallery" value="<?php echo esc_attr($gallery_ids); ?>" />
            <button type="button" id="upload-gallery-button" class="button"><?php _e('Bilder zur Galerie hinzufügen', 'stammbaum-manager'); ?></button>
            <div id="gallery-preview">
                <?php
                if ($gallery_ids) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        if ($id) {
                            $image = wp_get_attachment_image($id, 'thumbnail');
                            echo '<div class="gallery-item" data-id="' . $id . '">' . $image . '<button type="button" class="remove-image">×</button></div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save meta data
     */
    public function save_meta($post_id, $post) {
        if (!isset($_POST['puppy_meta_box_nonce']) || !wp_verify_nonce($_POST['puppy_meta_box_nonce'], 'puppy_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = array(
            'welpe_geburtsdatum' => '_welpe_geburtsdatum',
            'welpe_geschlecht' => '_welpe_geschlecht',
            'welpe_farbe' => '_welpe_farbe',
            'welpe_gewicht' => '_welpe_gewicht',
            'welpe_preis' => '_welpe_preis',
            'welpe_status' => '_welpe_status',
            'welpe_genetik' => '_welpe_genetik',
            'welpe_mutter_id' => '_welpe_mutter_id',
            'welpe_vater_id' => '_welpe_vater_id',
            'welpe_litter_id' => '_welpe_litter_id',
            'welpe_gallery' => '_welpe_gallery'
        );
        
        foreach ($fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    /**
     * Get puppy data
     */
    public function get_puppy_data($post_id) {
        $birth_date = get_post_meta($post_id, '_welpe_geburtsdatum', true);
        
        $age = '';
        if ($birth_date) {
            $age = Stammbaum_Core::calculate_age($birth_date);
        }
        
        return array(
            'id' => $post_id,
            'title' => get_the_title($post_id),
            'birth_date' => $birth_date,
            'age' => $age,
            'gender' => get_post_meta($post_id, '_welpe_geschlecht', true),
            'color' => get_post_meta($post_id, '_welpe_farbe', true),
            'genetics' => get_post_meta($post_id, '_welpe_genetik', true),
            'weight' => get_post_meta($post_id, '_welpe_gewicht', true),
            'price' => get_post_meta($post_id, '_welpe_preis', true),
            'status' => get_post_meta($post_id, '_welpe_status', true),
            'mother_id' => get_post_meta($post_id, '_welpe_mutter_id', true),
            'father_id' => get_post_meta($post_id, '_welpe_vater_id', true),
            'litter_id' => get_post_meta($post_id, '_welpe_litter_id', true),
            'gallery_ids' => get_post_meta($post_id, '_welpe_gallery', true),
            'thumbnail_url' => get_the_post_thumbnail_url($post_id, 'large'),
            'permalink' => get_permalink($post_id)
        );
    }
    
    /**
     * Single puppy content
     */
    public function single_content($content) {
        global $post;
        
        if (!is_single() || $post->post_type !== 'welpe') {
            return $content;
        }
        
        $puppy_data = $this->get_puppy_data($post->ID);
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/puppy-single.php';
        $puppy_content = ob_get_clean();
        
        return $puppy_content . $content;
    }
    
    /**
     * Single puppy head
     */
    public function single_head() {
        global $post;
        
        if (!is_single() || $post->post_type !== 'welpe') {
            return;
        }
        
        $puppy_data = $this->get_puppy_data($post->ID);
        
        echo "\n<!-- Stammbaum Manager - Puppy Meta Tags -->\n";
        echo '<meta property="og:title" content="' . esc_attr($post->post_title) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:url" content="' . esc_attr(get_permalink($post->ID)) . '">' . "\n";
        
        if ($puppy_data['thumbnail_url']) {
            echo '<meta property="og:image" content="' . esc_attr($puppy_data['thumbnail_url']) . '">' . "\n";
        }
    }
    
    /**
     * AJAX: Get puppy details
     */
    public function ajax_get_puppy_details() {
        Stammbaum_Core::verify_ajax_nonce();
        
        $puppy_id = intval($_POST['puppy_id']);
        $puppy_data = $this->get_puppy_data($puppy_id);
        
        if ($puppy_data) {
            wp_send_json_success($puppy_data);
        } else {
            wp_send_json_error(array('message' => __('Welpe nicht gefunden', 'stammbaum-manager')));
        }
    }
    
    /**
     * Shortcode: Puppies list
     */
    public function shortcode_puppies_list($atts) {
        $atts = shortcode_atts(array(
            'status' => '',
            'limit' => 12,
            'litter_id' => 0
        ), $atts);
        
        $args = array(
            'post_type' => 'welpe',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        if (!empty($atts['status'])) {
            $args['meta_query'] = array(
                array(
                    'key' => '_welpe_status',
                    'value' => $atts['status']
                )
            );
        }
        
        if (!empty($atts['litter_id'])) {
            $args['meta_query'][] = array(
                'key' => '_welpe_litter_id',
                'value' => intval($atts['litter_id'])
            );
        }
        
        $puppies = new WP_Query($args);
        
        ob_start();
        include STAMMBAUM_MANAGER_PLUGIN_DIR . 'templates/frontend/puppies-list.php';
        return ob_get_clean();
    }
    
    /**
     * Shortcode: WhatsApp button
     */
    public function shortcode_whatsapp_button($atts) {
        $settings = Stammbaum_Core::get_settings();
        
        if (!$settings['enable_whatsapp'] || empty($settings['whatsapp_number'])) {
            return '';
        }
        
        $atts = shortcode_atts(array(
            'text' => __('WhatsApp Chat', 'stammbaum-manager'),
            'message' => ''
        ), $atts);
        
        $whatsapp_url = 'https://wa.me/' . $settings['whatsapp_number'];
        
        if (!empty($atts['message'])) {
            $whatsapp_url .= '?text=' . urlencode($atts['message']);
        }
        
        return '<a href="' . esc_url($whatsapp_url) . '" target="_blank" class="whatsapp-button">💬 ' . esc_html($atts['text']) . '</a>';
    }
}

