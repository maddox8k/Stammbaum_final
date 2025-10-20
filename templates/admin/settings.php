<?php
if (!defined('ABSPATH')) exit;

$settings = Stammbaum_Core::get_settings();

if (isset($_POST['save_settings']) && wp_verify_nonce($_POST['settings_nonce'], 'stammbaum_settings')) {
    $new_settings = array(
        'email_notifications' => isset($_POST['email_notifications']),
        'max_applications' => intval($_POST['max_applications']),
        'require_approval' => isset($_POST['require_approval']),
        'enable_whatsapp' => isset($_POST['enable_whatsapp']),
        'whatsapp_number' => sanitize_text_field($_POST['whatsapp_number']),
        'enable_social_sharing' => isset($_POST['enable_social_sharing']),
        'enable_favorites' => isset($_POST['enable_favorites']),
        'show_anfrage_button' => isset($_POST['show_anfrage_button']),
        'currency' => sanitize_text_field($_POST['currency']),
        'currency_symbol' => sanitize_text_field($_POST['currency_symbol'])
    );
    
    Stammbaum_Core::update_settings($new_settings);
    echo '<div class="notice notice-success"><p>' . __('Einstellungen gespeichert!', 'stammbaum-manager') . '</p></div>';
    $settings = $new_settings;
}
?>

<div class="wrap stammbaum-admin-wrap">
    <h1><?php _e('Stammbaum Manager Einstellungen', 'stammbaum-manager'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('stammbaum_settings', 'settings_nonce'); ?>
        
        <div class="stammbaum-card">
            <h2><?php _e('E-Mail-Benachrichtigungen', 'stammbaum-manager'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Benachrichtigungen aktivieren', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="email_notifications" value="1" <?php checked($settings['email_notifications']); ?>>
                            <?php _e('E-Mail-Benachrichtigungen bei neuen Wartelisten-Anmeldungen senden', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="stammbaum-card">
            <h2><?php _e('Wartelisten-Einstellungen', 'stammbaum-manager'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Maximale Anmeldungen', 'stammbaum-manager'); ?></th>
                    <td>
                        <input type="number" name="max_applications" value="<?php echo esc_attr($settings['max_applications']); ?>" class="small-text">
                        <p class="description"><?php _e('Maximale Anzahl an Wartelisten-Anmeldungen pro Wurf (0 = unbegrenzt)', 'stammbaum-manager'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Genehmigungspflicht', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="require_approval" value="1" <?php checked($settings['require_approval']); ?>>
                            <?php _e('Anmeldungen müssen manuell genehmigt werden', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="stammbaum-card">
            <h2><?php _e('WhatsApp-Integration', 'stammbaum-manager'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('WhatsApp aktivieren', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_whatsapp" value="1" <?php checked($settings['enable_whatsapp']); ?>>
                            <?php _e('WhatsApp-Button auf Welpen-Seiten anzeigen', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('WhatsApp-Nummer', 'stammbaum-manager'); ?></th>
                    <td>
                        <input type="text" name="whatsapp_number" value="<?php echo esc_attr($settings['whatsapp_number']); ?>" class="regular-text" placeholder="+491234567890">
                        <p class="description"><?php _e('Ihre WhatsApp-Nummer im internationalen Format (z.B. +491234567890)', 'stammbaum-manager'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="stammbaum-card">
            <h2><?php _e('Frontend-Funktionen', 'stammbaum-manager'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Social Media Sharing', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_social_sharing" value="1" <?php checked($settings['enable_social_sharing']); ?>>
                            <?php _e('Social Media Teilen-Buttons anzeigen', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Favoriten-Funktion', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_favorites" value="1" <?php checked($settings['enable_favorites']); ?>>
                            <?php _e('Favoriten-Funktion für Welpen aktivieren', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Anfrage-Button', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="show_anfrage_button" value="1" <?php checked($settings['show_anfrage_button']); ?>>
                            <?php _e('Anfrage-Button auf Welpen-Seiten anzeigen', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="stammbaum-card">
            <h2><?php _e('Währung', 'stammbaum-manager'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Währung', 'stammbaum-manager'); ?></th>
                    <td>
                        <input type="text" name="currency" value="<?php echo esc_attr($settings['currency']); ?>" class="regular-text" placeholder="EUR">
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Währungssymbol', 'stammbaum-manager'); ?></th>
                    <td>
                        <input type="text" name="currency_symbol" value="<?php echo esc_attr($settings['currency_symbol']); ?>" class="small-text" placeholder="€">
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <button type="submit" name="save_settings" class="button button-primary button-large">
                <?php _e('Einstellungen speichern', 'stammbaum-manager'); ?>
            </button>
        </p>
    </form>
</div>
