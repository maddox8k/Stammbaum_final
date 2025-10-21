<?php
/**
 * Admin Animals List Template
 */

if (!defined('ABSPATH')) exit;

// Get animals
$animals_module = Stammbaum_Animals::get_instance();
$animals = $animals_module->get_all_animals();
?>

<div class="wrap stammbaum-admin-wrap">
    <h1 class="wp-heading-inline"><?php _e('Tiere', 'stammbaum-manager'); ?></h1>
    <a href="#" class="page-title-action" id="add-new-animal"><?php _e('Neues Tier hinzufügen', 'stammbaum-manager'); ?></a>
    <hr class="wp-header-end">
    
    <!-- Search Box -->
    <div class="stammbaum-card" style="margin: 20px 0;">
        <input type="text" id="animal-search" class="regular-text" placeholder="<?php _e('Tier suchen...', 'stammbaum-manager'); ?>" style="width: 100%; padding: 10px;">
    </div>
    
    <?php if ($animals): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 60px;"><?php _e('Bild', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Name', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Geschlecht', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Typ', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Geburtsdatum', 'stammbaum-manager'); ?></th>
                    <th><?php _e('Aktionen', 'stammbaum-manager'); ?></th>
                </tr>
            </thead>
            <tbody id="animals-table-body">
                <?php foreach ($animals as $animal): ?>
                    <tr data-animal-id="<?php echo $animal['id']; ?>">
                        <td>
                            <?php if (!empty($animal['profile_image'])): ?>
                                <img src="<?php echo esc_url($animal['profile_image']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">🐕</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo esc_html($animal['name']); ?></strong></td>
                        <td><?php echo esc_html($animal['gender']); ?></td>
                        <td><?php echo $animal['is_breeding_animal'] ? __('Zuchttier', 'stammbaum-manager') : ($animal['is_external'] ? __('Extern', 'stammbaum-manager') : __('Eigenes Tier', 'stammbaum-manager')); ?></td>
                        <td><?php echo $animal['birth_date'] ? Stammbaum_Core::format_date($animal['birth_date']) : '-'; ?></td>
                        <td>
                            <button class="button button-small edit-animal" data-id="<?php echo $animal['id']; ?>">
                                <?php _e('Bearbeiten', 'stammbaum-manager'); ?>
                            </button>
                            <button class="button button-small view-pedigree" data-id="<?php echo $animal['id']; ?>">
                                <?php _e('Stammbaum', 'stammbaum-manager'); ?>
                            </button>
                            <button class="button button-small button-link-delete delete-animal" data-id="<?php echo $animal['id']; ?>">
                                <?php _e('Löschen', 'stammbaum-manager'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="stammbaum-card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 72px; margin-bottom: 20px;">🐕</div>
            <h2><?php _e('Noch keine Tiere vorhanden', 'stammbaum-manager'); ?></h2>
            <p><?php _e('Fügen Sie Ihr erstes Tier hinzu, um zu beginnen.', 'stammbaum-manager'); ?></p>
            <button class="button button-primary button-large" id="add-first-animal" style="margin-top: 20px;">
                <?php _e('Erstes Tier hinzufügen', 'stammbaum-manager'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Animal Form Modal -->
<div id="animal-modal" class="stammbaum-modal" style="display: none;">
    <div class="stammbaum-modal-content" style="max-width: 800px;">
        <span class="stammbaum-modal-close">&times;</span>
        <h2 id="modal-title"><?php _e('Tier hinzufügen/bearbeiten', 'stammbaum-manager'); ?></h2>
        <form id="animal-form">
            <input type="hidden" id="animal-id" name="animal_id">
            
            <table class="form-table">
                <tr>
                    <th><label for="animal-name"><?php _e('Name *', 'stammbaum-manager'); ?></label></th>
                    <td><input type="text" id="animal-name" name="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="animal-gender"><?php _e('Geschlecht *', 'stammbaum-manager'); ?></label></th>
                    <td>
                        <select id="animal-gender" name="gender" required>
                            <option value=""><?php _e('Bitte wählen', 'stammbaum-manager'); ?></option>
                            <option value="male"><?php _e('Männlich', 'stammbaum-manager'); ?></option>
                            <option value="female"><?php _e('Weiblich', 'stammbaum-manager'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Eigenschaften', 'stammbaum-manager'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" id="is-breeding-animal" name="is_breeding_animal" value="1">
                            <?php _e('Zuchttier', 'stammbaum-manager'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" id="is-external" name="is_external" value="1">
                            <?php _e('Externes Tier (z.B. Deckrüde, Großeltern)', 'stammbaum-manager'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><label for="animal-birth-date"><?php _e('Geburtsdatum', 'stammbaum-manager'); ?></label></th>
                    <td><input type="date" id="animal-birth-date" name="birth_date" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="animal-breed"><?php _e('Rasse', 'stammbaum-manager'); ?></label></th>
                    <td><input type="text" id="animal-breed" name="breed" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="animal-color"><?php _e('Farbe', 'stammbaum-manager'); ?></label></th>
                    <td><input type="text" id="animal-color" name="color" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="animal-profile-image"><?php _e('Profilbild URL', 'stammbaum-manager'); ?></label></th>
                    <td>
                        <input type="text" id="animal-profile-image" name="profile_image" class="regular-text">
                        <button type="button" class="button upload-image-button"><?php _e('Bild hochladen', 'stammbaum-manager'); ?></button>
                    </td>
                </tr>
                <tr>
                    <th><label for="animal-description"><?php _e('Beschreibung', 'stammbaum-manager'); ?></label></th>
                    <td><textarea id="animal-description" name="description" rows="4" class="large-text"></textarea></td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary button-large"><?php _e('Speichern', 'stammbaum-manager'); ?></button>
                <button type="button" class="button button-large stammbaum-modal-close"><?php _e('Abbrechen', 'stammbaum-manager'); ?></button>
            </p>
        </form>
    </div>
</div>

<style>
.stammbaum-modal {
    display: none;
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.stammbaum-modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 600px;
    border-radius: 4px;
    position: relative;
}

.stammbaum-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.stammbaum-modal-close:hover,
.stammbaum-modal-close:focus {
    color: #000;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Open modal for new animal
    $('#add-new-animal, #add-first-animal').on('click', function(e) {
        e.preventDefault();
        $('#animal-form')[0].reset();
        $('#animal-id').val('');
        $('#modal-title').text('<?php _e('Neues Tier hinzufügen', 'stammbaum-manager'); ?>');
        $('#animal-modal').fadeIn();
    });
    
    // Close modal
    $('.stammbaum-modal-close').on('click', function() {
        $('#animal-modal').fadeOut();
    });
    
    // Edit animal
    $('.edit-animal').on('click', function() {
        var animalId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stammbaum_get_animal',
                nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>',
                animal_id: animalId
            },
            success: function(response) {
                if (response.success) {
                    var animal = response.data;
                    $('#animal-id').val(animal.id);
                    $('#animal-name').val(animal.name);
                    $('#animal-gender').val(animal.gender);
                    $('#is-breeding-animal').prop('checked', animal.is_breeding_animal == 1);
                    $('#is-external').prop('checked', animal.is_external == 1);
                    $('#animal-birth-date').val(animal.birth_date);
                    $('#animal-breed').val(animal.breed);
                    $('#animal-color').val(animal.color);
                    $('#animal-profile-image').val(animal.profile_image);
                    $('#animal-description').val(animal.description);
                    $('#modal-title').text('<?php _e('Tier bearbeiten', 'stammbaum-manager'); ?>');
                    $('#animal-modal').fadeIn();
                }
            }
        });
    });
    
    // Save animal
    $('#animal-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serializeArray();
        var data = {
            action: 'stammbaum_save_animal',
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
                    alert('<?php _e('Tier gespeichert!', 'stammbaum-manager'); ?>');
                    location.reload();
                } else {
                    alert('<?php _e('Fehler beim Speichern', 'stammbaum-manager'); ?>');
                }
            }
        });
    });
    
    // Delete animal
    $('.delete-animal').on('click', function() {
        if (!confirm('<?php _e('Tier wirklich löschen?', 'stammbaum-manager'); ?>')) {
            return;
        }
        
        var animalId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'stammbaum_delete_animal',
                nonce: '<?php echo wp_create_nonce('stammbaum_manager_admin_nonce'); ?>',
                animal_id: animalId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php _e('Fehler beim Löschen', 'stammbaum-manager'); ?>');
                }
            }
        });
    });
    
    // Search animals
    $('#animal-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('#animals-table-body tr').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(searchTerm) > -1);
        });
    });
});
</script>

