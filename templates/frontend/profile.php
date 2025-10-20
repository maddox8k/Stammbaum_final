<?php if (!defined('ABSPATH')) exit; ?>
<div class="stammbaum-container">
    <div class="stammbaum-profile">
        <h3><?php echo esc_html($animal['name']); ?></h3>
        <?php if (!empty($animal['profile_image'])): ?>
            <img src="<?php echo esc_url($animal['profile_image']); ?>" alt="<?php echo esc_attr($animal['name']); ?>" style="max-width: 400px;">
        <?php endif; ?>
        <p><?php echo esc_html($animal['description']); ?></p>
    </div>
</div>
