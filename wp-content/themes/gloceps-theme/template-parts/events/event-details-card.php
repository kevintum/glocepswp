<?php
/**
 * Event Details Card (Sidebar)
 * 
 * Matches event-single.html event-details-card section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$event_date = isset($event_date) ? $event_date : get_field('event_date');
$event_end_date = isset($event_end_date) ? $event_end_date : get_field('event_end_date');
$event_time = isset($event_time) ? $event_time : get_field('event_time');
$is_virtual = isset($is_virtual) ? $is_virtual : get_field('is_virtual');
$venue_name = isset($venue_name) ? $venue_name : get_field('venue_name');
$location_city = isset($location_city) ? $location_city : get_field('location_city');
$location_country = isset($location_country) ? $location_country : get_field('location_country');
$map_embed_url_raw = isset($map_embed_url) ? $map_embed_url : get_field('map_embed_url');
// Ensure map_embed_url is a string
$map_embed_url = is_string($map_embed_url_raw) ? $map_embed_url_raw : '';
$event_type = isset($event_type) ? $event_type : null;
$date_display = isset($date_display) ? $date_display : '';

// Format date for sidebar
$sidebar_date = '';
if ($event_date) {
    $start = date('F j, Y', strtotime($event_date));
    if ($event_end_date && $event_end_date != $event_date) {
        $end = date('F j, Y', strtotime($event_end_date));
        $sidebar_date = $start . ' - ' . $end;
    } else {
        $sidebar_date = $start;
    }
}
?>

<div class="event-details-card">
    <h3 class="event-details-card__title"><?php esc_html_e('Event Details', 'gloceps'); ?></h3>
    <div class="event-details-card__items">
        <?php if ($event_type && is_object($event_type) && isset($event_type->name)) : ?>
            <div class="event-details-card__item">
                <span class="event-details-card__label"><?php esc_html_e('Event Type', 'gloceps'); ?></span>
                <span class="event-details-card__value"><?php echo esc_html($event_type->name); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($sidebar_date) : ?>
            <div class="event-details-card__item">
                <span class="event-details-card__label"><?php esc_html_e('Date', 'gloceps'); ?></span>
                <span class="event-details-card__value"><?php echo esc_html($sidebar_date); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($event_time) : ?>
            <div class="event-details-card__item">
                <span class="event-details-card__label"><?php esc_html_e('Time', 'gloceps'); ?></span>
                <span class="event-details-card__value"><?php echo esc_html($event_time); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!$is_virtual && $venue_name) : ?>
            <div class="event-details-card__item">
                <span class="event-details-card__label"><?php esc_html_e('Venue', 'gloceps'); ?></span>
                <span class="event-details-card__value"><?php echo esc_html($venue_name); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($location_city || $location_country) : ?>
            <div class="event-details-card__item">
                <span class="event-details-card__label"><?php esc_html_e('Location', 'gloceps'); ?></span>
                <span class="event-details-card__value">
                    <?php echo esc_html(trim(($location_city ? $location_city : '') . ($location_city && $location_country ? ', ' : '') . ($location_country ? $location_country : ''))); ?>
                </span>
            </div>
        <?php endif; ?>
        
        <div class="event-details-card__item">
            <span class="event-details-card__label"><?php esc_html_e('Format', 'gloceps'); ?></span>
            <span class="event-details-card__value">
                <?php echo $is_virtual ? esc_html__('Virtual', 'gloceps') : esc_html__('In-Person', 'gloceps'); ?>
            </span>
        </div>
    </div>
    
    <?php if ($map_embed_url && !$is_virtual) : ?>
        <div class="event-details-card__map">
            <iframe
                src="<?php echo esc_url($map_embed_url); ?>"
                width="100%"
                height="150"
                style="border: 0; border-radius: 8px"
                allowfullscreen=""
                loading="lazy"
            ></iframe>
        </div>
    <?php endif; ?>
</div>

