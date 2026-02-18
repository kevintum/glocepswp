<?php
/**
 * Archive template for Events
 * 
 * Matches events.html structure exactly
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for events archive
$events_title = get_field('events_intro_title', 'option') ?: 'Events';
$events_description = get_field('events_intro_description', 'option') ?: 'Join us for webinars, conferences, workshops, and roundtable discussions that bring together thought leaders, policymakers, and practitioners to address critical issues in policy and strategy.';

// Get current filter/tab from URL
$current_tab = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : 'all';
$current_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'all';

// Get all event types for filter
$event_type_terms = get_terms(array(
    'taxonomy' => 'event_type',
    'hide_empty' => true,
));

$today = date('Y-m-d');
?>

<!-- Page Header -->
<?php
$header_attrs = gloceps_get_page_header_attrs(false);
?>
<section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title"><?php echo esc_html($events_title); ?></h1>
            <?php if ($events_description) : ?>
                <p class="page-header__description">
                    <?php echo esc_html($events_description); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<main>
    <section class="section">
        <div class="container">
            
            <!-- Event Tabs -->
            <div class="events-tabs">
                <div class="events-tabs__wrapper">
                    <a href="<?php echo esc_url(remove_query_arg('filter')); ?>" 
                       class="events-tab <?php echo $current_tab === 'all' ? 'events-tab--active' : ''; ?>">
                        <?php esc_html_e('All Events', 'gloceps'); ?>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('filter', 'upcoming')); ?>" 
                       class="events-tab <?php echo $current_tab === 'upcoming' ? 'events-tab--active' : ''; ?>">
                        <?php esc_html_e('Upcoming Events', 'gloceps'); ?>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('filter', 'past')); ?>" 
                       class="events-tab <?php echo $current_tab === 'past' ? 'events-tab--active' : ''; ?>">
                        <?php esc_html_e('Past Events', 'gloceps'); ?>
                    </a>
                </div>
            </div>

            <!-- Filter by Type -->
            <?php if ($event_type_terms && !is_wp_error($event_type_terms)) : ?>
                <div class="events-filters">
                    <div class="events-filters__wrapper">
                        <span class="events-filters__label"><?php esc_html_e('Filter by type:', 'gloceps'); ?></span>
                        <div class="events-filters__tags">
                            <a href="<?php echo esc_url(remove_query_arg('type')); ?>" 
                               class="events-filter-tag <?php echo $current_type === 'all' ? 'events-filter-tag--active' : ''; ?>">
                                <?php esc_html_e('All Types', 'gloceps'); ?>
                            </a>
                            <?php foreach ($event_type_terms as $term) : ?>
                                <a href="<?php echo esc_url(add_query_arg('type', $term->slug)); ?>" 
                                   class="events-filter-tag <?php echo $current_type === $term->slug ? 'events-filter-tag--active' : ''; ?>">
                                    <?php echo esc_html($term->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // Build query args based on filters
            $query_args = array(
                'post_type' => 'event',
                'posts_per_page' => get_option('posts_per_page', 12),
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            );

            // Add date filter based on tab
            if ($current_tab === 'upcoming') {
                $query_args['meta_query'] = array(
                    array(
                        'key' => 'event_date',
                        'value' => $today,
                        'compare' => '>=',
                        'type' => 'DATE',
                    ),
                );
                $query_args['orderby'] = 'meta_value';
                $query_args['meta_key'] = 'event_date';
                $query_args['order'] = 'ASC';
            } elseif ($current_tab === 'past') {
                $query_args['meta_query'] = array(
                    array(
                        'key' => 'event_date',
                        'value' => $today,
                        'compare' => '<',
                        'type' => 'DATE',
                    ),
                );
                $query_args['orderby'] = 'meta_value';
                $query_args['meta_key'] = 'event_date';
                $query_args['order'] = 'DESC';
            } else {
                // All events - order by date
                $query_args['orderby'] = 'meta_value';
                $query_args['meta_key'] = 'event_date';
                $query_args['order'] = 'DESC';
            }

            // Add event type filter
            if ($current_type !== 'all') {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'event_type',
                        'field' => 'slug',
                        'terms' => $current_type,
                    ),
                );
            }

            // Get the next upcoming event for featured section (before main query)
            $featured_id = null;
            if ($current_tab !== 'past') {
                $featured_args = array(
                    'post_type' => 'event',
                    'posts_per_page' => 1,
                    'meta_query' => array(
                        array(
                            'key' => 'event_date',
                            'value' => $today,
                            'compare' => '>=',
                            'type' => 'DATE',
                        ),
                    ),
                    'orderby' => 'meta_value',
                    'meta_key' => 'event_date',
                    'order' => 'ASC',
                );
                
                // Respect type filter for featured event
                if ($current_type !== 'all') {
                    $featured_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'event_type',
                            'field' => 'slug',
                            'terms' => $current_type,
                        ),
                    );
                }
                
                $featured_query = new WP_Query($featured_args);
                if ($featured_query->have_posts()) {
                    $featured_query->the_post();
                    $featured_id = get_the_ID();
                }
                wp_reset_postdata();
            }

            // Exclude featured event from main query
            if ($featured_id) {
                $query_args['post__not_in'] = array($featured_id);
            }

            $events_query = new WP_Query($query_args);
            ?>

            <!-- Featured/Next Event -->
            <?php if ($featured_id) :
                $featured_date = get_field('event_date', $featured_id);
                $featured_end_date = get_field('event_end_date', $featured_id);
                $featured_time = get_field('event_time', $featured_id);
                $featured_venue = get_field('venue_name', $featured_id);
                $featured_description_lead = get_field('description_lead', $featured_id);
                $featured_registration_link = get_field('registration_link', $featured_id);
                $featured_types = get_the_terms($featured_id, 'event_type');
                $featured_type = $featured_types && !is_wp_error($featured_types) ? $featured_types[0]->name : '';
                $featured_image_raw = get_the_post_thumbnail_url($featured_id, 'large');
                $featured_is_placeholder = !$featured_image_raw;
                if (!$featured_image_raw) {
                    $featured_image = gloceps_get_favicon_url(192);
                } else {
                    $featured_image = $featured_image_raw;
                }
                $featured_date_display = $featured_date ? date('F j', strtotime($featured_date)) : '';
                if ($featured_end_date && $featured_end_date != $featured_date) {
                    $featured_date_display .= '-' . date('j, Y', strtotime($featured_end_date));
                } elseif ($featured_date) {
                    $featured_date_display .= ', ' . date('Y', strtotime($featured_date));
                }
            ?>
                <div class="featured-event">
                    <div class="featured-event__card">
                        <div class="featured-event__image">
                            <div class="featured-event__badge">
                                <span class="featured-event__badge-pulse"></span>
                                <span class="eyebrow"><?php esc_html_e('NEXT EVENT', 'gloceps'); ?></span>
                            </div>
                            <?php if ($featured_image) : ?>
                                <img src="<?php echo esc_url($featured_image); ?>" 
                                     alt="<?php echo esc_attr(get_the_title($featured_id)); ?>"
                                     class="<?php echo $featured_is_placeholder ? 'featured-event__image--placeholder-icon' : ''; ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="featured-event__content">
                            <div class="featured-event__meta-row">
                                <?php if ($featured_type) : ?>
                                    <span class="featured-event__type"><?php echo esc_html($featured_type); ?></span>
                                <?php endif; ?>
                                <?php if ($featured_date_display) : ?>
                                    <time class="featured-event__date" datetime="<?php echo esc_attr($featured_date); ?>">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        <?php echo esc_html($featured_date_display); ?>
                                    </time>
                                <?php endif; ?>
                            </div>
                            <h2 class="featured-event__title">
                                <a href="<?php echo esc_url(get_permalink($featured_id)); ?>">
                                    <?php echo esc_html(get_the_title($featured_id)); ?>
                                </a>
                            </h2>
                            <?php if ($featured_description_lead) : ?>
                                <p class="featured-event__description">
                                    <?php echo esc_html(wp_trim_words($featured_description_lead, 20, '...')); ?>
                                </p>
                            <?php endif; ?>
                            <div class="featured-event__details">
                                <?php if ($featured_time) : ?>
                                    <div class="featured-event__detail">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        <span><?php echo esc_html($featured_time); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($featured_venue) : ?>
                                    <div class="featured-event__detail">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span><?php echo esc_html($featured_venue); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="featured-event__actions">
                                <?php if ($featured_registration_link) : ?>
                                    <a href="<?php echo esc_url($featured_registration_link); ?>" class="btn btn--primary" target="_blank" rel="noopener">
                                        <?php esc_html_e('Register Now', 'gloceps'); ?> →
                                    </a>
                                <?php else : ?>
                                    <a href="<?php echo esc_url(get_permalink($featured_id)); ?>" class="btn btn--primary">
                                        <?php esc_html_e('Register Now', 'gloceps'); ?> →
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo esc_url(get_permalink($featured_id)); ?>" class="featured-event__learn-more">
                                    <?php esc_html_e('Learn More', 'gloceps'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- All Events Grid -->
            <?php if ($events_query->have_posts()) : ?>
                <div class="events-listing">
                    <div class="events-listing__header">
                        <h2 class="events-listing__title"><?php esc_html_e('All Events', 'gloceps'); ?></h2>
                        <span class="events-listing__count">
                            <?php 
                            printf(
                                esc_html__('Showing %d of %d events', 'gloceps'),
                                $events_query->post_count,
                                $events_query->found_posts
                            );
                            ?>
                        </span>
                    </div>
                    <div class="events-grid">
                        <?php
                        while ($events_query->have_posts()) :
                            $events_query->the_post();
                            get_template_part('template-parts/components/event-card');
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php
                    $pagination_args = array(
                        'total' => $events_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => '‹ Previous',
                        'next_text' => 'Next ›',
                    );
                    ?>
                    <nav class="pagination">
                        <?php echo paginate_links($pagination_args); ?>
                    </nav>
                </div>
            <?php else : ?>
                <p class="text-center" style="color: var(--color-gray-500); padding: var(--space-16) 0;">
                    <?php esc_html_e('No events found.', 'gloceps'); ?>
                </p>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php
get_footer();
