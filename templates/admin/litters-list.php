<?php
/**
 * Admin Litters List Template - Modern Glass Design
 */

if (!defined('ABSPATH')) exit;

$litters_module = Stammbaum_Litters::get_instance();
$litters = $litters_module->get_litters();

// Get all animals for dropdowns
$animals_module = Stammbaum_Animals::get_instance();
$all_animals = $animals_module->get_all_animals();
$females = array_filter($all_animals, function($animal) {
    return $animal['gender'] === 'female';
});
$males = array_filter($all_animals, function($animal) {
    return $animal['gender'] === 'male';
});
?>

<div class="wrap stammbaum-admin-wrap">
    <h1 class="wp-heading-inline">🐾 <?php _e('Würfe', 'stammbaum-manager'); ?></h1>
    <a href="#" class="page-title-action" id="add-new-litter"><?php _e('Neuen Wurf hinzufügen', 'stammbaum-manager'); ?></a>
    <hr class="wp-header-end" style="border: none; margin: 20px 0;">
    
    <?php if ($litters): ?>
        <div class="stammbaum-card">
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
                                    ✏️ <?php _e('Bearbeiten', 'stammbaum-manager'); ?>
                                </button>
                                <button class="button button-small button-link-delete delete-litter" data-id="<?php echo $litter['id']; ?>">
                                    🗑️ <?php _e('Löschen', 'stammbaum-manager'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="stammbaum-card" style="text-align: center; padding: 80px 40px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7)); border: 2px dashed rgba(99, 102, 241, 0.3);">
            <div style="font-size: 80px; margin-bottom: 20px; opacity: 0.5;">🐾</div>
            <h2 style="color: #475569; font-weight: 600; margin-bottom: 10px;"><?php _e('Noch keine Würfe geplant', 'stammbaum-manager'); ?></h2>
            <p style="color: #94a3b8; margin-bottom: 25px;"><?php _e('Planen Sie Ihren ersten Wurf.', 'stammbaum-manager'); ?></p>
            <button class="button button-primary button-large" id="add-first-litter">
                ➕ <?php _e('Ersten Wurf planen', 'stammbaum-manager'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Litter Form Modal -->
<div id="litter-modal" class="stammbaum-modal" style="display: none;">
    <div class="stammbaum-modal-content" style="max-width: 900px;">
        <span class="stammbaum-modal-close">&times;</span>
        <h2 id="litter-modal-title">🐾 <?php _e('Wurf hinzufügen/bearbeiten', 'stammbaum-manager'); ?></h2>
        <form id="litter-form">
            <input type="hidden" id="litter-id" name="litter_id">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <!-- Left Column -->
                <div>
                    <h3 style="color: #475569; margin-bottom: 15px;">👨‍👩‍👧 Elterntiere</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="mother-id" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Mutter *', 'stammbaum-manager'); ?>
                        </label>
                        <select id="mother-id" name="mother_id" required style="width: 100%;">
                            <option value=""><?php _e('Bitte wählen', 'stammbaum-manager'); ?></option>
                            <?php foreach ($females as $female): ?>
                                <option value="<?php echo $female['id']; ?>">
                                    <?php echo esc_html($female['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="father-id" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Vater *', 'stammbaum-manager'); ?>
                        </label>
                        <select id="father-id" name="father_id" required style="width: 100%;">
                            <option value=""><?php _e('Bitte wählen', 'stammbaum-manager'); ?></option>
                            <?php foreach ($males as $male): ?>
                                <option value="<?php echo $male['id']; ?>">
                                    <?php echo esc_html($male['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <h3 style="color: #475569; margin: 25px 0 15px;">📅 Termine</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="expected-date" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Erwarteter Termin *', 'stammbaum-manager'); ?>
                        </label>
                        <input type="date" id="expected-date" name="expected_date" required style="width: 100%;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="actual-date" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Tatsächlicher Termin', 'stammbaum-manager'); ?>
                        </label>
                        <input type="date" id="actual-date" name="actual_date" style="width: 100%;">
                    </div>
                </div>
                
                <!-- Right Column -->
                <div>
                    <h3 style="color: #475569; margin-bottom: 15px;">📊 Details</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="litter-status" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Status', 'stammbaum-manager'); ?>
                        </label>
                        <select id="litter-status" name="status" style="width: 100%;">
                            <option value="planned"><?php _e('Geplant', 'stammbaum-manager'); ?></option>
                            <option value="active"><?php _e('Aktiv', 'stammbaum-manager'); ?></option>
                            <option value="born"><?php _e('Geboren', 'stammbaum-manager'); ?></option>
                            <option value="closed"><?php _e('Abgeschlossen', 'stammbaum-manager'); ?></option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="expected-puppies" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Erwartete Welpenanzahl', 'stammbaum-manager'); ?>
                        </label>
                        <input type="number" id="expected-puppies" name="expected_puppies" min="1" max="20" style="width: 100%;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="genetics" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Genetik', 'stammbaum-manager'); ?>
                        </label>
                        <input type="text" id="genetics" name="genetics" placeholder="z.B. at/at, ky/ky" style="width: 100%;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="colors" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                            <?php _e('Erwartete Farben', 'stammbaum-manager'); ?>
                        </label>
                        <input type="text" id="colors" name="colors" placeholder="z.B. Schwarz, Braun" style="width: 100%;">
                    </div>
                </div>
                
            </div>
            
            <div style="margin-top: 20px;">
                <label for="notes" style="display: block; font-weight: 600; color: #475569; margin-bottom: 8px;">
                    <?php _e('Notizen', 'stammbaum-manager'); ?>
                </label>
                <textarea id="notes" name="notes" rows="4" style="width: 100%;" placeholder="<?php _e('Zusätzliche Informationen...', 'stammbaum-manager'); ?>"></textarea>
            </div>
            
            <p class="submit" style="text-align: right; margin-top: 25px;">
                <button type="button" class="button button-large stammbaum-modal-close" style="margin-right: 10px;">
                    ❌ <?php _e('Abbrechen', 'stammbaum-manager'); ?>
                </button>
                <button type="submit" class="button button-primary button-large">
                    ✅ <?php _e('Speichern', 'stammbaum-manager'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Open modal for new litter
    $('#add-new-litter, #add-first-litter').on('click', function(e) {
        e.preventDefault();
        $('#litter-form')[0].reset();
        $('#litter-id').val('');
        $('#litter-modal-title').text('🐾 <?php _e('Neuen Wurf hinzufügen', 'stammbaum-manager'); ?>');
        $('#litter-modal').fadeIn();
    });
    
    // Close modal
    $('.stammbaum-modal-close').on('click', function() {
        $('#litter-modal').fadeOut();
    });
    
    // Edit litter
    $('.edit-litter').on('click', function() {
        var litterId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stammbaum_get_litter',
                nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>',
                litter_id: litterId
            },
            success: function(response) {
                if (response.success) {
                    var litter = response.data;
                    $('#litter-id').val(litter.id);
                    $('#mother-id').val(litter.mother_id);
                    $('#father-id').val(litter.father_id);
                    $('#expected-date').val(litter.expected_date);
                    $('#actual-date').val(litter.actual_date);
                    $('#litter-status').val(litter.status);
                    $('#expected-puppies').val(litter.expected_puppies);
                    $('#genetics').val(litter.genetics);
                    $('#colors').val(litter.colors);
                    $('#notes').val(litter.notes);
                    $('#litter-modal-title').text('🐾 <?php _e('Wurf bearbeiten', 'stammbaum-manager'); ?>');
                    $('#litter-modal').fadeIn();
                }
            }
        });
    });
    
    // Save litter
    $('#litter-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serializeArray();
        var data = {
            action: 'stammbaum_save_litter',
            nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>'
        };
        
        $.each(formData, function(i, field) {
            data[field.name] = field.value;
        });
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    alert('✅ <?php _e('Wurf gespeichert!', 'stammbaum-manager'); ?>');
                    location.reload();
                } else {
                    alert('❌ <?php _e('Fehler beim Speichern', 'stammbaum-manager'); ?>');
                }
            }
        });
    });
    
    // Delete litter
    $('.delete-litter').on('click', function() {
        if (!confirm('<?php _e('Wurf wirklich löschen?', 'stammbaum-manager'); ?>')) {
            return;
        }
        
        var litterId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stammbaum_delete_litter',
                nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>',
                litter_id: litterId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('❌ <?php _e('Fehler beim Löschen', 'stammbaum-manager'); ?>');
                }
            }
        });
    });
});
</script>
