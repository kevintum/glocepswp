<?php
/**
 * Flexible Content Block: Contact Map Section
 * 
 * Matches the static HTML contact.html contact-map section exactly.
 * Full-width map with overlay card showing location details.
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$embed_url = get_sub_field('embed_url');
$location_name = get_sub_field('location_name') ?: 'GLOCEPS Headquarters';
$location_address = get_sub_field('location_address') ?: 'Runda Drive, Nairobi';
$directions_url = get_sub_field('directions_url') ?: 'https://maps.google.com/?q=Runda+Drive+Nairobi';

// Default embed URL if not set
if (!$embed_url) {
    $embed_url = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.9084668361367!2d36.79544847496567!3d-1.2316069356316045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f17352ff2bfe9%3A0x7a94a1f7e07e8d65!2sRunda%20Dr%2C%20Nairobi!5e0!3m2!1sen!2ske';
}
?>

<section class="contact-map">
    <div class="contact-map__wrapper">
        <iframe
            src="<?php echo esc_url($embed_url); ?>"
            width="100%"
            height="100%"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="<?php echo esc_attr($location_name); ?>"
        ></iframe>
    </div>
    
    <div class="contact-map__overlay">
        <div class="contact-map__card">
            <div class="contact-map__card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <div class="contact-map__card-content">
                <?php if ($location_name) : ?>
                    <strong><?php echo esc_html($location_name); ?></strong>
                <?php endif; ?>
                <?php if ($location_address) : ?>
                    <span><?php echo esc_html($location_address); ?></span>
                <?php endif; ?>
                <?php if ($directions_url) : ?>
                    <a href="<?php echo esc_url($directions_url); ?>" target="_blank" rel="noopener" class="btn btn--outline btn--sm">
                        <?php esc_html_e('Get Directions', 'gloceps'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
