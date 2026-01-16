<?php
/**
 * Event Organizer Card (Sidebar)
 * 
 * Matches event-single.html event-organizer-card section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$organizer_name = isset($organizer_name) ? $organizer_name : (get_field('organizer_name') ?: 'GLOCEPS');
$organizer_description = isset($organizer_description) ? $organizer_description : (get_field('organizer_description') ?: 'Global Centre for Policy and Strategy');

// Get logo from theme options (ACF image field returns array or ID)
$logo_array = get_field('header_logo', 'option');
$logo_url = '';

if ($logo_array) {
    if (is_array($logo_array)) {
        $logo_url = isset($logo_array['url']) ? (string) $logo_array['url'] : '';
    } elseif (is_numeric($logo_array)) {
        // If it's an ID, get the image URL
        $logo_url = wp_get_attachment_image_url((int) $logo_array, 'full');
        if ($logo_url === false) {
            $logo_url = '';
        }
    } elseif (is_string($logo_array)) {
        $logo_url = $logo_array;
    }
}

// Ensure logo_url is always a string
if (!is_string($logo_url) || empty($logo_url)) {
    $logo_url = get_template_directory_uri() . '/assets/images/glocep-logo.png';
}
?>

<div class="event-organizer-card">
    <h3 class="event-organizer-card__title"><?php esc_html_e('Organizer', 'gloceps'); ?></h3>
    <div class="event-organizer-card__info">
        <?php if ($logo_url && is_string($logo_url)) : ?>
            <img
                src="<?php echo esc_url($logo_url); ?>"
                alt="<?php echo esc_attr($organizer_name); ?>"
                class="event-organizer-card__logo"
            />
        <?php endif; ?>
        <div>
            <strong><?php echo esc_html($organizer_name); ?></strong>
            <p><?php echo esc_html($organizer_description); ?></p>
        </div>
    </div>
    <?php 
    $contact_page = get_page_by_path('contact');
    $contact_url = $contact_page ? get_permalink($contact_page->ID) : home_url('/contact');
    if ($contact_url) :
    ?>
        <a href="<?php echo esc_url($contact_url); ?>" class="event-organizer-card__link">
            <?php esc_html_e('Contact Organizer', 'gloceps'); ?> →
        </a>
    <?php endif; ?>
</div>

