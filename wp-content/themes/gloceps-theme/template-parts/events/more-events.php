<?php
/**
 * More Events Section
 * 
 * Matches event-single.html more-events section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get related/upcoming events (exclude current event)
$current_id = get_the_ID();
$today = date('Y-m-d');

$args = array(
    'post_type' => 'event',
    'posts_per_page' => 3,
    'post__not_in' => array($current_id),
    'meta_query' => array(
        array(
            'key' => 'event_date',
            'value' => $today,
            'compare' => '>=',
        ),
    ),
    'orderby' => 'meta_value',
    'meta_key' => 'event_date',
    'order' => 'ASC',
);

$events = get_posts($args);

// If not enough upcoming events, get any recent events
if (!$events || count($events) < 3) {
    $args_fallback = array(
        'post_type' => 'event',
        'posts_per_page' => 3,
        'post__not_in' => array($current_id),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $events = get_posts($args_fallback);
}
?>

<?php if ($events && count($events) > 0) : ?>
    <section class="more-events">
        <div class="container">
            <div class="more-events__header">
                <h2 class="more-events__title"><?php esc_html_e('More Events', 'gloceps'); ?></h2>
                <a href="<?php echo esc_url(get_post_type_archive_link('event')); ?>" class="more-events__link">
                    <?php esc_html_e('View All Events', 'gloceps'); ?> →
                </a>
            </div>
            <div class="more-events__grid">
                <?php foreach ($events as $event) : 
                    setup_postdata($event);
                    $event_date = get_field('event_date', $event->ID);
                    $event_time = get_field('event_time', $event->ID);
                    $is_virtual = get_field('is_virtual', $event->ID);
                    $venue_name = get_field('venue_name', $event->ID);
                    $event_types = get_the_terms($event->ID, 'event_type');
                    $event_type = $event_types && !is_wp_error($event_types) ? $event_types[0]->name : '';
                    $event_status = ($event_date && $event_date >= $today) ? 'upcoming' : 'past';
                    $featured_image = get_the_post_thumbnail_url($event->ID, 'medium');
                    // Use favicon as placeholder if no featured image
                    if (!$featured_image) {
                        $featured_image = gloceps_get_favicon_url(192);
                    }
                ?>
                    <article class="event-card <?php echo $event_status === 'past' ? 'event-card--past' : ''; ?>">
                        <a href="<?php echo esc_url(get_permalink($event->ID)); ?>" class="event-card__image">
                            <?php if ($featured_image) : ?>
                                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr(get_the_title($event->ID)); ?>" class="<?php echo !get_the_post_thumbnail_url($event->ID, 'medium') ? 'event-card__image--placeholder-icon' : ''; ?>" />
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
                                <a href="<?php echo esc_url(get_permalink($event->ID)); ?>">
                                    <?php echo esc_html(get_the_title($event->ID)); ?>
                                </a>
                            </h3>
                            <div class="event-card__footer">
                                <div class="event-card__location">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <span><?php echo $is_virtual ? esc_html__('Virtual', 'gloceps') : esc_html($venue_name ?: 'TBA'); ?></span>
                                </div>
                                <a href="<?php echo esc_url(get_permalink($event->ID)); ?>" class="event-card__cta">
                                    <?php echo $event_status === 'upcoming' ? esc_html__('Register', 'gloceps') : esc_html__('View Recap', 'gloceps'); ?> →
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; 
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
<?php endif; ?>
