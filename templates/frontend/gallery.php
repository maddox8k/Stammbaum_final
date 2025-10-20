<?php if (!defined('ABSPATH')) exit; ?>
<div class="stammbaum-container">
    <div class="stammbaum-grid">
        <?php foreach ($animals as $animal): ?>
            <div class="stammbaum-animal-card">
                <h4><?php echo esc_html($animal['name']); ?></h4>
            </div>
        <?php endforeach; ?>
    </div>
</div>
