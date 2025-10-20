<?php if (!defined('ABSPATH')) exit; ?>
<div class="welpe-single-container">
    <div class="welpe-single-header">
        <h2><?php _e('Welpen-Details', 'stammbaum-manager'); ?></h2>
        <span class="welpe-status-badge status-<?php echo esc_attr($puppy_data['status']); ?>">
            <?php echo Stammbaum_Core::get_status_label($puppy_data['status'], 'puppy'); ?>
        </span>
    </div>
    
    <div class="welpe-single-info">
        <?php if ($puppy_data['birth_date']): ?>
            <p><strong><?php _e('Geburtsdatum:', 'stammbaum-manager'); ?></strong> <?php echo Stammbaum_Core::format_date($puppy_data['birth_date']); ?></p>
            <p><strong><?php _e('Alter:', 'stammbaum-manager'); ?></strong> <?php echo esc_html($puppy_data['age']); ?></p>
        <?php endif; ?>
        
        <?php if ($puppy_data['gender']): ?>
            <p><strong><?php _e('Geschlecht:', 'stammbaum-manager'); ?></strong> <?php echo esc_html($puppy_data['gender']); ?></p>
        <?php endif; ?>
        
        <?php if ($puppy_data['color']): ?>
            <p><strong><?php _e('Farbe:', 'stammbaum-manager'); ?></strong> <?php echo esc_html($puppy_data['color']); ?></p>
        <?php endif; ?>
        
        <?php if ($puppy_data['price']): ?>
            <p><strong><?php _e('Preis:', 'stammbaum-manager'); ?></strong> <?php echo esc_html($puppy_data['price']); ?> €</p>
        <?php endif; ?>
    </div>
</div>
