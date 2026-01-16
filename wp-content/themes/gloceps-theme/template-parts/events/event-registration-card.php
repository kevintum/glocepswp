<?php
/**
 * Event Registration Card (Sidebar)
 * 
 * Matches event-single.html event-registration-card section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$registration_link_raw = isset($registration_link) ? $registration_link : get_field('registration_link');
// Ensure registration_link is a string
$registration_link = is_string($registration_link_raw) ? $registration_link_raw : '';
$registration_fee = isset($registration_fee) ? $registration_fee : get_field('registration_fee');
$registration_includes = isset($registration_includes) ? $registration_includes : get_field('registration_includes');
$registration_deadline = isset($registration_deadline) ? $registration_deadline : get_field('registration_deadline');
$early_bird_discount = isset($early_bird_discount) ? $early_bird_discount : get_field('early_bird_discount');
?>

<div class="event-registration-card" id="register">
    <h3 class="event-registration-card__title">
        <?php esc_html_e('Register for This Event', 'gloceps'); ?>
    </h3>
    
    <?php if ($registration_fee || $registration_includes) : ?>
        <div class="event-registration-card__pricing">
            <?php if ($registration_fee) : ?>
                <div class="event-registration-card__price">
                    <span class="event-registration-card__price-label">
                        <?php esc_html_e('Registration Fee', 'gloceps'); ?>
                    </span>
                    <span class="event-registration-card__price-value">
                        <?php echo esc_html($registration_fee); ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if ($registration_includes) : ?>
                <p class="event-registration-card__includes">
                    <?php echo esc_html($registration_includes); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($registration_deadline) : ?>
        <div class="event-registration-card__deadline">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
            <?php esc_html_e('Registration closes:', 'gloceps'); ?>
            <strong><?php echo esc_html(date('F j, Y', strtotime($registration_deadline))); ?></strong>
        </div>
    <?php endif; ?>
    
    <?php if ($registration_link) : ?>
        <a href="<?php echo esc_url($registration_link); ?>" class="btn btn--primary btn--block" target="_blank" rel="noopener">
            <?php esc_html_e('Register Now', 'gloceps'); ?>
        </a>
    <?php endif; ?>
    
    <?php if ($early_bird_discount) : ?>
        <p class="event-registration-card__note">
            <?php echo esc_html($early_bird_discount); ?>
        </p>
    <?php endif; ?>
</div>

