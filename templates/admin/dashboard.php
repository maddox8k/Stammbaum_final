<?php
/**
 * Admin Dashboard Template
 */

if (!defined('ABSPATH')) exit;

// Get statistics
global $wpdb;
$animals_table = $wpdb->prefix . 'stammbaum_animals';
$litters_table = $wpdb->prefix . 'breeding_litters';
$applications_table = $wpdb->prefix . 'breeding_applications';

$total_animals = $wpdb->get_var("SELECT COUNT(*) FROM {$animals_table}");
$total_litters = $wpdb->get_var("SELECT COUNT(*) FROM {$litters_table}");
$total_applications = $wpdb->get_var("SELECT COUNT(*) FROM {$applications_table}");
$pending_applications = $wpdb->get_var("SELECT COUNT(*) FROM {$applications_table} WHERE status = 'pending'");

$total_puppies = wp_count_posts('welpe')->publish;

// Get recent animals
$recent_animals = $wpdb->get_results("SELECT * FROM {$animals_table} ORDER BY created_at DESC LIMIT 5");

// Get recent litters
$recent_litters = $wpdb->get_results("SELECT * FROM {$litters_table} ORDER BY created_at DESC LIMIT 5");

// Get recent applications
$recent_applications = $wpdb->get_results("SELECT * FROM {$applications_table} ORDER BY submitted_at DESC LIMIT 5");
?>

<div class="wrap stammbaum-admin-wrap">
    <h1><?php _e('Stammbaum Manager Dashboard', 'stammbaum-manager'); ?></h1>
    
    <!-- Statistics Cards -->
    <div class="stammbaum-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin: 20px 0;">
        
        <div class="stammbaum-card" style="text-align: center; padding: 30px;">
            <div style="font-size: 48px; color: #2271b1; margin-bottom: 10px;">🐕</div>
            <h2 style="margin: 0; font-size: 36px;"><?php echo $total_animals; ?></h2>
            <p style="margin: 5px 0 0 0; color: #666;"><?php _e('Tiere', 'stammbaum-manager'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=stammbaum-animals'); ?>" class="button" style="margin-top: 10px;">
                <?php _e('Verwalten', 'stammbaum-manager'); ?>
            </a>
        </div>
        
        <div class="stammbaum-card" style="text-align: center; padding: 30px;">
            <div style="font-size: 48px; color: #2271b1; margin-bottom: 10px;">👶</div>
            <h2 style="margin: 0; font-size: 36px;"><?php echo $total_puppies; ?></h2>
            <p style="margin: 5px 0 0 0; color: #666;"><?php _e('Welpen', 'stammbaum-manager'); ?></p>
            <a href="<?php echo admin_url('edit.php?post_type=welpe'); ?>" class="button" style="margin-top: 10px;">
                <?php _e('Verwalten', 'stammbaum-manager'); ?>
            </a>
        </div>
        
        <div class="stammbaum-card" style="text-align: center; padding: 30px;">
            <div style="font-size: 48px; color: #2271b1; margin-bottom: 10px;">🐾</div>
            <h2 style="margin: 0; font-size: 36px;"><?php echo $total_litters; ?></h2>
            <p style="margin: 5px 0 0 0; color: #666;"><?php _e('Würfe', 'stammbaum-manager'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=stammbaum-litters'); ?>" class="button" style="margin-top: 10px;">
                <?php _e('Verwalten', 'stammbaum-manager'); ?>
            </a>
        </div>
        
        <div class="stammbaum-card" style="text-align: center; padding: 30px;">
            <div style="font-size: 48px; color: #d63638; margin-bottom: 10px;">📋</div>
            <h2 style="margin: 0; font-size: 36px;"><?php echo $pending_applications; ?></h2>
            <p style="margin: 5px 0 0 0; color: #666;"><?php _e('Offene Anmeldungen', 'stammbaum-manager'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=stammbaum-waitlist'); ?>" class="button button-primary" style="margin-top: 10px;">
                <?php _e('Bearbeiten', 'stammbaum-manager'); ?>
            </a>
        </div>
        
    </div>
    
    <!-- Two Column Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
        
        <!-- Recent Animals -->
        <div class="stammbaum-card">
            <h2><?php _e('Neueste Tiere', 'stammbaum-manager'); ?></h2>
            <?php if ($recent_animals): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'stammbaum-manager'); ?></th>
                            <th><?php _e('Geschlecht', 'stammbaum-manager'); ?></th>
                            <th><?php _e('Typ', 'stammbaum-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_animals as $animal): ?>
                            <tr>
                                <td><strong><?php echo esc_html($animal->name); ?></strong></td>
                                <td><?php echo esc_html($animal->gender); ?></td>
                                <td><?php echo esc_html($animal->animal_type); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="text-align: right; margin-top: 10px;">
                    <a href="<?php echo admin_url('admin.php?page=stammbaum-animals'); ?>" class="button">
                        <?php _e('Alle Tiere anzeigen', 'stammbaum-manager'); ?> →
                    </a>
                </p>
            <?php else: ?>
                <p><?php _e('Noch keine Tiere vorhanden.', 'stammbaum-manager'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=stammbaum-animals'); ?>" class="button button-primary">
                    <?php _e('Erstes Tier hinzufügen', 'stammbaum-manager'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Recent Litters -->
        <div class="stammbaum-card">
            <h2><?php _e('Neueste Würfe', 'stammbaum-manager'); ?></h2>
            <?php if ($recent_litters): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Mutter & Vater', 'stammbaum-manager'); ?></th>
                            <th><?php _e('Erwarteter Termin', 'stammbaum-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_litters as $litter): ?>
                            <tr>
                                <td><strong><?php echo esc_html($litter->mother_name . ' & ' . $litter->father_name); ?></strong></td>
                                <td><?php echo esc_html(Stammbaum_Core::format_date($litter->expected_date)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="text-align: right; margin-top: 10px;">
                    <a href="<?php echo admin_url('admin.php?page=stammbaum-litters'); ?>" class="button">
                        <?php _e('Alle Würfe anzeigen', 'stammbaum-manager'); ?> →
                    </a>
                </p>
            <?php else: ?>
                <p><?php _e('Noch keine Würfe geplant.', 'stammbaum-manager'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=stammbaum-litters'); ?>" class="button button-primary">
                    <?php _e('Ersten Wurf planen', 'stammbaum-manager'); ?>
                </a>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Recent Applications -->
    <div class="stammbaum-card" style="margin-top: 20px;">
        <h2><?php _e('Neueste Wartelisten-Anmeldungen', 'stammbaum-manager'); ?></h2>
        <?php if ($recent_applications): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'stammbaum-manager'); ?></th>
                        <th><?php _e('E-Mail', 'stammbaum-manager'); ?></th>
                        <th><?php _e('Telefon', 'stammbaum-manager'); ?></th>
                        <th><?php _e('Status', 'stammbaum-manager'); ?></th>
                        <th><?php _e('Datum', 'stammbaum-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_applications as $app): ?>
                        <tr>
                            <td><strong><?php echo esc_html($app->applicant_name); ?></strong></td>
                            <td><?php echo esc_html($app->applicant_email); ?></td>
                            <td><?php echo esc_html($app->applicant_phone); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($app->status); ?>">
                                    <?php echo Stammbaum_Core::get_status_label($app->status, 'application'); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(Stammbaum_Core::format_date($app->submitted_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="text-align: right; margin-top: 10px;">
                <a href="<?php echo admin_url('admin.php?page=stammbaum-waitlist'); ?>" class="button">
                    <?php _e('Alle Anmeldungen anzeigen', 'stammbaum-manager'); ?> →
                </a>
            </p>
        <?php else: ?>
            <p><?php _e('Noch keine Wartelisten-Anmeldungen vorhanden.', 'stammbaum-manager'); ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Quick Links -->
    <div class="stammbaum-card" style="margin-top: 20px; background: #f0f6fc;">
        <h2><?php _e('Schnellzugriff', 'stammbaum-manager'); ?></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
            <a href="<?php echo admin_url('admin.php?page=stammbaum-animals'); ?>" class="button button-large">
                🐕 <?php _e('Neues Tier hinzufügen', 'stammbaum-manager'); ?>
            </a>
            <a href="<?php echo admin_url('post-new.php?post_type=welpe'); ?>" class="button button-large">
                👶 <?php _e('Neuen Welpen hinzufügen', 'stammbaum-manager'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=stammbaum-litters'); ?>" class="button button-large">
                🐾 <?php _e('Neuen Wurf planen', 'stammbaum-manager'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=stammbaum-settings'); ?>" class="button button-large">
                ⚙️ <?php _e('Einstellungen', 'stammbaum-manager'); ?>
            </a>
        </div>
    </div>
    
</div>

<style>
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}
.status-pending { background: #ffc107; color: #000; }
.status-confirmed { background: #4caf50; color: #fff; }
.status-approved { background: #2196f3; color: #fff; }
.status-rejected { background: #f44336; color: #fff; }
</style>

