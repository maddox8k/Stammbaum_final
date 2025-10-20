<?php if (!defined('ABSPATH')) exit; ?>
<div class="stammbaum-container">
    <div class="stammbaum-pedigree">
        <h3><?php _e('Stammbaum', 'stammbaum-manager'); ?></h3>
        <?php if ($pedigree && isset($pedigree['animal'])): ?>
            <div class="stammbaum-animal-card">
                <h4><?php echo esc_html($pedigree['animal']['name']); ?></h4>
                <?php if (!empty($pedigree['animal']['profile_image'])): ?>
                    <img src="<?php echo esc_url($pedigree['animal']['profile_image']); ?>" alt="<?php echo esc_attr($pedigree['animal']['name']); ?>" style="max-width: 200px;">
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
