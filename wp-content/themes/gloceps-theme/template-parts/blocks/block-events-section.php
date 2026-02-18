<?php
/**
 * Block: Events Section
 * 
 * Displays upcoming or recent events
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Upcoming';
$title = get_sub_field('title') ?: 'Events & Dialogues';
$description = get_sub_field('description') ?: 'Join our policy dialogues, roundtables, and expert discussions shaping regional discourse on critical issues.';
$background_style = get_sub_field('background_style') ?: 'default';
$count = get_sub_field('count') ?: 3;
$only_upcoming = get_sub_field('only_upcoming');

// Build query args
$args = array(
    'post_type' => 'event',
    'posts_per_page' => $count,
    'orderby' => 'meta_value',
    'meta_key' => 'event_date',
    'order' => $only_upcoming ? 'ASC' : 'DESC',
);

// Filter for upcoming events only
if ($only_upcoming) {
    $args['meta_query'] = array(
        array(
            'key' => 'event_date',
            'value' => date('Y-m-d'),
            'compare' => '>=',
            'type' => 'DATE',
        ),
    );
}

$events = new WP_Query($args);
?>

<section class="section events-section section--bg-<?php echo esc_attr($background_style); ?>">
    <div class="container">
        <div class="section-header section-header--center reveal">
            <?php if ($eyebrow) : ?>
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="section-header__title"><?php echo wp_kses_post($title); ?></h2>
            <?php endif; ?>

            <?php if ($description) : ?>
                <p class="section-header__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($events->have_posts()) : ?>
            <div class="events-list reveal">
                <?php while ($events->have_posts()) : $events->the_post(); 
                    $event_date = get_field('event_date');
                    $event_location = get_field('event_location');
                    $event_format = get_field('event_format'); // in-person, virtual, hybrid
                    $event_types = get_the_terms(get_the_ID(), 'event_type');
                    $event_type_name = $event_types && !is_wp_error($event_types) && !empty($event_types) ? $event_types[0]->name : '';
                    
                    // Parse date - ACF date field can be in Ymd format or Y-m-d format
                    $day = '';
                    $month = '';
                    if ($event_date) {
                        // Try Ymd format first (ACF default)
                        $date_obj = DateTime::createFromFormat('Ymd', $event_date);
                        if (!$date_obj) {
                            // Try Y-m-d format
                            $date_obj = DateTime::createFromFormat('Y-m-d', $event_date);
                        }
                        if (!$date_obj) {
                            // Try strtotime as fallback
                            $date_obj = $event_date ? new DateTime($event_date) : null;
                        }
                        if ($date_obj) {
                            $day = $date_obj->format('d');
                            $month = $date_obj->format('M');
                        }
                    }
                    
                    // Location/format text
                    $location_text = '';
                    if ($event_format === 'virtual') {
                        $location_text = 'Virtual';
                    } elseif ($event_format === 'hybrid') {
                        $location_text = 'Hybrid Event';
                    } elseif ($event_location) {
                        $location_text = $event_location;
                    }
                ?>
                    <article class="event-item">
                        <div class="event-item__date">
                            <span class="event-item__day"><?php echo esc_html($day); ?></span>
                            <span class="event-item__month"><?php echo esc_html($month); ?></span>
                        </div>
                        <div class="event-item__content">
                            <?php if ($event_type_name) : ?>
                                <span class="event-item__type"><?php echo esc_html($event_type_name); ?></span>
                            <?php endif; ?>
                            <h3 class="event-item__title">
                                <a href="<?php echo esc_url(get_permalink()); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html(get_the_title()); ?></a>
                            </h3>
                            <?php if ($location_text) : ?>
                                <div class="event-item__location">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span><?php echo esc_html($location_text); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="event-item__action">
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn--secondary btn--sm">Register</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="text-center" style="margin-top: var(--space-12);">
                <a href="<?php echo esc_url(get_post_type_archive_link('event')); ?>" class="btn btn--ghost">
                    View All Events →
                </a>
            </div>
        <?php else : ?>
            <p class="no-results text-center">No upcoming events at this time.</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>

