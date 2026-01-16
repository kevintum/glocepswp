<?php
/**
 * Template part for displaying an event card in grid layout
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get event data
$event_id = get_the_ID();
$event_date = get_field('event_date', $event_id);
$event_time = get_field('event_time', $event_id);
$is_virtual = get_field('is_virtual', $event_id);
$venue_name = get_field('venue_name', $event_id);
$event_types = get_the_terms($event_id, 'event_type');
$event_type = $event_types && !is_wp_error($event_types) ? $event_types[0]->name : '';

// Determine status
$today = date('Y-m-d');
$event_status = ($event_date && $event_date >= $today) ? 'upcoming' : 'past';

// Get featured image or use favicon placeholder
$featured_image = get_the_post_thumbnail_url($event_id, 'medium');
$is_placeholder = false;
if (!$featured_image) {
    $featured_image = gloceps_get_favicon_url(192);
    $is_placeholder = true;
}
?>

<article class="event-card <?php echo $event_status === 'past' ? 'event-card--past' : ''; ?>">
    <a href="<?php echo esc_url(get_permalink($event_id)); ?>" class="event-card__image">
        <?php if ($featured_image) : ?>
            <img src="<?php echo esc_url($featured_image); ?>" 
                 alt="<?php echo esc_attr(get_the_title($event_id)); ?>" 
                 class="<?php echo $is_placeholder ? 'event-card__image--placeholder-icon' : ''; ?>" />
        <?php endif; ?>
        <span class="event-card__status event-card__status--<?php echo esc_attr($event_status); ?>">
            <?php echo $event_status === 'upcoming' ? esc_html__('Upcoming', 'gloceps') : esc_html__('Past Event', 'gloceps'); ?>
        </span>
    </a>
    <div class="event-card__content">
        <div class="event-card__meta">
            <?php if ($event_type) : ?>
                <span class="event-card__type"><?php echo esc_html($event_type); ?></span>
            <?php endif; ?>
            <?php if ($event_date) : ?>
                <time datetime="<?php echo esc_attr($event_date); ?>" class="event-card__date">
                    <?php echo esc_html(date('M j, Y', strtotime($event_date))); ?>
                </time>
            <?php endif; ?>
        </div>
        <h3 class="event-card__title">
            <a href="<?php echo esc_url(get_permalink($event_id)); ?>">
                <?php echo esc_html(get_the_title($event_id)); ?>
            </a>
        </h3>
        <?php if (has_excerpt($event_id)) : ?>
            <p class="event-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt($event_id), 20)); ?></p>
        <?php endif; ?>
        <div class="event-card__footer">
            <div class="event-card__location">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <span><?php echo $is_virtual ? esc_html__('Virtual', 'gloceps') : esc_html($venue_name ?: 'TBA'); ?></span>
            </div>
            <a href="<?php echo esc_url(get_permalink($event_id)); ?>" class="event-card__cta">
                <?php echo $event_status === 'upcoming' ? esc_html__('Register', 'gloceps') : esc_html__('View Recap', 'gloceps'); ?> →
            </a>
        </div>
    </div>
</article>

