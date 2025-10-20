<?php if (!defined('ABSPATH')) exit; ?>
<div class="stammbaum-container">
    <div class="litters-list">
        <?php foreach ($litters as $litter): ?>
            <div class="litter-card">
                <h3><?php echo esc_html($litter['mother_name'] . ' & ' . $litter['father_name']); ?></h3>
                <p><?php _e('Erwarteter Wurftag:', 'stammbaum-manager'); ?> <?php echo Stammbaum_Core::format_date($litter['expected_date']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
