<?php if (!defined('ABSPATH')) exit; ?>
<div class="stammbaum-container">
    <form id="waitlist-form" class="stammbaum-waitlist-form">
        <h3><?php _e('Wartelisten-Anmeldung', 'stammbaum-manager'); ?></h3>
        <input type="hidden" name="litter_id" value="<?php echo esc_attr($litter['id']); ?>">
        
        <p>
            <label><?php _e('Name:', 'stammbaum-manager'); ?></label>
            <input type="text" name="applicant_name" required>
        </p>
        
        <p>
            <label><?php _e('E-Mail:', 'stammbaum-manager'); ?></label>
            <input type="email" name="applicant_email" required>
        </p>
        
        <p>
            <label><?php _e('Telefon:', 'stammbaum-manager'); ?></label>
            <input type="tel" name="applicant_phone">
        </p>
        
        <p>
            <button type="submit" class="stammbaum-button"><?php _e('Anmelden', 'stammbaum-manager'); ?></button>
        </p>
    </form>
</div>
