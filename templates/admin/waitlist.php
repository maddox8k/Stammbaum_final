<?php
if (!defined('ABSPATH')) exit;

$waitlist_module = Stammbaum_Waitlist::get_instance();
$applications = $waitlist_module->get_applications();
?>

<div class="wrap stammbaum-admin-wrap">
    <h1><?php _e('Wartelisten-Anmeldungen', 'stammbaum-manager'); ?></h1>
    
    <?php if ($applications): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Name', 'stammbaum-manager'); ?></th>
                    <th><?php _e('E-Mail', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Telefon', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Wurf', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Status', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Datum', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Aktionen', 'stammbaum-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><strong><?php echo esc_html($app['applicant_name']); ?></strong></td>
                        <td><?php echo esc_html($app['applicant_email']); ?></td>
                        <td><?php echo esc_html($app['applicant_phone']); ?></td>
                        <td><?php echo esc_html($app['mother_name'] . ' & ' . $app['father_name']); ?></td>
                        <td>
                            <select class="application-status" data-id="<?php echo $app['id']; ?>">
                                <option value="pending" <?php selected($app['status'], 'pending'); ?>><?php _e('Ausstehend', 'stammbaum-manager'); ?></option>
                                <option value="confirmed" <?php selected($app['status'], 'confirmed'); ?>><?php _e('Bestätigt', 'stammbaum-manager'); ?></option>
                                <option value="rejected" <?php selected($app['status'], 'rejected'); ?>><?php _e('Abgelehnt', 'stammbaum-manager'); ?></option>
                            </select>
                        </td>
                        <td><?php echo Stammbaum_Core::format_date($app['submitted_at']); ?></td>
                        <td>
                            <button class="button button-small button-link-delete delete-application" data-id="<?php echo $app['id']; ?>">
                                <?php _e('Löschen', 'stammbaum-manager'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="stammbaum-card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 72px; margin-bottom: 20px;">📋</div>
            <h2><?php _e('Noch keine Anmeldungen', 'stammbaum-manager'); ?></h2>
            <p><?php _e('Wartelisten-Anmeldungen erscheinen hier.', 'stammbaum-manager'); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('.application-status').on('change', function() {
        var appId = $(this).data('id');
        var status = $(this).val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stammbaum_update_application_status',
                nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>',
                application_id: appId,
                status: status,
                notes: ''
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('Status aktualisiert', 'stammbaum-manager'); ?>');
                }
            }
        });
    });
    
    $('.delete-application').on('click', function() {
        if (!confirm('<?php _e('Anmeldung wirklich löschen?', 'stammbaum-manager'); ?>')) {
            return;
        }
        
        var appId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stammbaum_delete_application',
                nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>',
                application_id: appId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
});
</script>
