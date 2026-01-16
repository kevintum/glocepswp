<?php
/**
 * Event Header - Split Layout
 * 
 * Matches event-single.html event-header--split section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Variables passed from single-event.php
$event_date = isset($event_date) ? $event_date : get_field('event_date');
$event_end_date = isset($event_end_date) ? $event_end_date : get_field('event_end_date');
$event_time = isset($event_time) ? $event_time : get_field('event_time');
$is_virtual = isset($is_virtual) ? $is_virtual : get_field('is_virtual');
$venue_name = isset($venue_name) ? $venue_name : get_field('venue_name');
$location_city = isset($location_city) ? $location_city : get_field('location_city');
$location_country = isset($location_country) ? $location_country : get_field('location_country');
$registration_link_raw = isset($registration_link) ? $registration_link : get_field('registration_link');
// Ensure registration_link is a string
$registration_link = is_string($registration_link_raw) ? $registration_link_raw : '';
$event_status = isset($event_status) ? $event_status : 'upcoming';
$event_type = isset($event_type) ? $event_type : null;
$date_display = isset($date_display) ? $date_display : '';
$duration_text = isset($duration_text) ? $duration_text : '';

// Get featured image - try WordPress featured image first, then ACF field, then fallback
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
if (!$featured_image) {
    // Try ACF field for event image
    $event_image = get_field('event_image');
    if ($event_image) {
        if (is_array($event_image)) {
            $featured_image = $event_image['url'] ?? '';
        } elseif (is_numeric($event_image)) {
            $featured_image = wp_get_attachment_image_url($event_image, 'full');
        } elseif (is_string($event_image)) {
            $featured_image = $event_image;
        }
    }
}
?>

<header class="event-header--split">
    <div class="event-header__content-block">
        <div class="event-header__content-inner">
            <nav class="event-header__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'gloceps'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'gloceps'); ?></a>
                <span>/</span>
                <a href="<?php echo esc_url(get_post_type_archive_link('event')); ?>"><?php esc_html_e('Events', 'gloceps'); ?></a>
                <?php if ($event_type && is_object($event_type) && isset($event_type->slug)) : ?>
                    <span>/</span>
                    <a href="<?php echo esc_url(add_query_arg('event_type', $event_type->slug, get_post_type_archive_link('event'))); ?>">
                        <?php echo esc_html($event_type->name); ?>
                    </a>
                <?php endif; ?>
            </nav>

            <div class="event-header__badges">
                <?php if ($event_status === 'upcoming') : ?>
                    <span class="event-header__badge event-header__badge--upcoming">
                        <span class="event-header__badge-pulse"></span>
                        <?php esc_html_e('Upcoming', 'gloceps'); ?>
                    </span>
                <?php else : ?>
                    <span class="event-header__badge event-header__badge--past">
                        <?php esc_html_e('Past Event', 'gloceps'); ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($event_type) : ?>
                    <span class="event-header__badge event-header__badge--type">
                        <?php echo esc_html($event_type->name); ?>
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="event-header__title"><?php the_title(); ?></h1>

            <div class="event-header__meta">
                <?php if ($date_display) : ?>
                    <div class="event-header__meta-item event-header__meta-item--date">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <div>
                            <strong><?php echo esc_html($date_display); ?></strong>
                            <?php if ($duration_text) : ?>
                                <span><?php echo esc_html($duration_text); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($event_time) : ?>
                    <div class="event-header__meta-item">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span><?php echo esc_html($event_time); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!$is_virtual && $venue_name) : ?>
                    <div class="event-header__meta-item">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <div>
                            <strong><?php echo esc_html($venue_name); ?></strong>
                            <?php if ($location_city || $location_country) : ?>
                                <span><?php echo esc_html(trim(($location_city ? $location_city : '') . ($location_city && $location_country ? ', ' : '') . ($location_country ? $location_country : ''))); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($is_virtual) : ?>
                    <div class="event-header__meta-item">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span><?php esc_html_e('Virtual Event', 'gloceps'); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="event-header__actions">
                <?php if ($registration_link) : ?>
                    <a href="<?php echo esc_url($registration_link); ?>" class="btn btn--white btn--lg" target="_blank" rel="noopener">
                        <?php esc_html_e('Register Now', 'gloceps'); ?>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                <?php endif; ?>
                
                <div class="event-header__share">
                    <span><?php esc_html_e('Share:', 'gloceps'); ?></span>
                    <button aria-label="<?php esc_attr_e('Share on LinkedIn', 'gloceps'); ?>" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>', '_blank', 'width=600,height=400')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </button>
                    <button aria-label="<?php esc_attr_e('Share on X', 'gloceps'); ?>" onclick="window.open('https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>', '_blank', 'width=600,height=400')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </button>
                    <button aria-label="<?php esc_attr_e('Add to calendar', 'gloceps'); ?>" onclick="window.open('<?php echo esc_url(gloceps_get_calendar_link()); ?>', '_blank')">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                            <line x1="12" y1="14" x2="12" y2="18" />
                            <line x1="10" y1="16" x2="14" y2="16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($featured_image) : ?>
        <div class="event-header__image">
            <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
        </div>
    <?php endif; ?>
</header>

