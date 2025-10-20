<?php if (!defined('ABSPATH')) exit; ?>
<div class="stammbaum-container">
    <div class="welpen-grid">
        <?php if ($puppies->have_posts()): ?>
            <?php while ($puppies->have_posts()): $puppies->the_post(); ?>
                <?php
                $status = get_post_meta(get_the_ID(), '_welpe_status', true);
                $price = get_post_meta(get_the_ID(), '_welpe_preis', true);
                $gender = get_post_meta(get_the_ID(), '_welpe_geschlecht', true);
                ?>
                <div class="welpe-card" data-puppy-id="<?php the_ID(); ?>" data-puppy-url="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="welpe-image"><?php the_post_thumbnail('medium'); ?></div>
                    <?php endif; ?>
                    <div class="welpe-content">
                        <h3><?php the_title(); ?></h3>
                        <p class="welpe-meta">
                            <span class="welpe-status-badge status-<?php echo esc_attr($status); ?>">
                                <?php echo Stammbaum_Core::get_status_label($status, 'puppy'); ?>
                            </span>
                            <?php if ($gender): ?>
                                <span class="welpe-gender"><?php echo esc_html($gender); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if ($price): ?>
                            <p class="welpe-price"><?php echo esc_html($price); ?> €</p>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="welpe-link">Details ansehen</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p><?php _e('Keine Welpen gefunden', 'stammbaum-manager'); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php wp_reset_postdata(); ?>
