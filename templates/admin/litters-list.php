<?php
if (!defined('ABSPATH')) exit;

$litters_module = Stammbaum_Litters::get_instance();
$litters = $litters_module->get_litters();
?>

<div class="wrap stammbaum-admin-wrap">
    <h1 class="wp-heading-inline"><?php _e('Würfe', 'stammbaum-manager'); ?></h1>
    <a href="#" class="page-title-action" id="add-new-litter"><?php _e('Neuen Wurf hinzufügen', 'stammbaum-manager'); ?></a>
    <hr class="wp-header-end">
    
    <?php if ($litters): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Mutter', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Vater', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Erwarteter Termin', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Status', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Aktionen', 'stammbaum-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($litters as $litter): ?>
                    <tr>
                        <td><strong><?php echo esc_html($litter['mother_name']); ?></strong></td>
                        <td><?php echo esc_html($litter['father_name']); ?></td>
                        <td><?php echo Stammbaum_Core::format_date($litter['expected_date']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($litter['status']); ?>">
                                <?php echo Stammbaum_Core::get_status_label($litter['status'], 'litter'); ?>
                            </span>
                        </td>
                        <td>
                            <button class="button button-small edit-litter" data-id="<?php echo $litter['id']; ?>">
                                <?php _e('Bearbeiten', 'stammbaum-manager'); ?>
                            </button>
                            <button class="button button-small button-link-delete delete-litter" data-id="<?php echo $litter['id']; ?>">
                                <?php _e('Löschen', 'stammbaum-manager'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="stammbaum-card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 72px; margin-bottom: 20px;">🐾</div>
            <h2><?php _e('Noch keine Würfe geplant', 'stammbaum-manager'); ?></h2>
            <p><?php _e('Planen Sie Ihren ersten Wurf.', 'stammbaum-manager'); ?></p>
            <button class="button button-primary button-large" id="add-first-litter" style="margin-top: 20px;">
                <?php _e('Ersten Wurf planen', 'stammbaum-manager'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>
