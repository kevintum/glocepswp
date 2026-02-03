<?php
/**
 * Template part for displaying a job listing item (not card-based)
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get job data
$job_id = get_the_ID();
$location = get_field('vacancy_location', $job_id);
$engagement_type = get_field('vacancy_engagement_type', $job_id);
$deadline = get_field('vacancy_deadline', $job_id);
$salary_range = get_field('vacancy_salary_range', $job_id);
$excerpt = has_excerpt($job_id) ? get_the_excerpt($job_id) : '';
$writeup = get_field('vacancy_writeup', $job_id);
if (!$excerpt && $writeup) {
    $excerpt = wp_strip_all_tags($writeup);
    $excerpt = wp_trim_words($excerpt, 30);
}

// Get featured image or use placeholder
$featured_image = get_the_post_thumbnail_url($job_id, 'thumbnail');
$is_placeholder = false;
if (!$featured_image) {
    $featured_image = gloceps_get_favicon_url(80);
    $is_placeholder = true;
}

// Format deadline
$deadline_display = '';
$deadline_status = '';
if ($deadline) {
    $deadline_timestamp = strtotime($deadline);
    $today = strtotime('today');
    if ($deadline_timestamp < $today) {
        $deadline_display = esc_html__('Closed', 'gloceps');
        $deadline_status = 'closed';
    } else {
        $days_remaining = ceil(($deadline_timestamp - $today) / 86400);
        if ($days_remaining <= 7) {
            $deadline_display = sprintf(esc_html__('%d days left', 'gloceps'), $days_remaining);
            $deadline_status = 'urgent';
        } else {
            $deadline_display = date('M j, Y', $deadline_timestamp);
        }
    }
}

// Engagement type labels
$engagement_labels = array(
    'full-time' => 'Full Time',
    'part-time' => 'Part Time',
    'contract' => 'Contract',
    'internship' => 'Internship',
    'consultancy' => 'Consultancy',
    'volunteer' => 'Volunteer',
);
$engagement_display = isset($engagement_labels[$engagement_type]) ? $engagement_labels[$engagement_type] : $engagement_type;

// Posted time
$posted_time = human_time_diff(get_the_time('U'), current_time('timestamp'));
?>

<article class="job-listing-item">
    <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="job-listing-item__link">
        <div class="job-listing-item__image">
            <img src="<?php echo esc_url($featured_image); ?>" 
                 alt="<?php echo esc_attr(get_the_title($job_id)); ?>"
                 <?php echo $is_placeholder ? 'class="is-placeholder"' : ''; ?> />
        </div>
        <div class="job-listing-item__content">
            <div class="job-listing-item__header">
                <h3 class="job-listing-item__title"><?php echo esc_html(get_the_title($job_id)); ?></h3>
                <div class="job-listing-item__badges">
                    <?php if ($engagement_type) : ?>
                        <span class="job-listing-item__badge job-listing-item__badge--type">
                            <?php echo esc_html($engagement_display); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($deadline_status === 'urgent') : ?>
                        <span class="job-listing-item__badge job-listing-item__badge--urgent">
                            <?php esc_html_e('Urgently Hiring', 'gloceps'); ?>
                        </span>
                    <?php elseif ($deadline_status === 'closed') : ?>
                        <span class="job-listing-item__badge job-listing-item__badge--closed">
                            <?php esc_html_e('Closed', 'gloceps'); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($excerpt) : ?>
                <p class="job-listing-item__excerpt"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
            <div class="job-listing-item__meta">
                <?php if ($location) : ?>
                    <span class="job-listing-item__meta-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <?php echo esc_html($location); ?>
                    </span>
                <?php endif; ?>
                <?php if ($salary_range) : ?>
                    <span class="job-listing-item__meta-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="6" width="20" height="12" rx="2" />
                            <path d="M6 10h12M6 14h8" />
                        </svg>
                        KES <?php echo esc_html($salary_range); ?>
                    </span>
                <?php endif; ?>
                <?php if ($deadline && $deadline_status !== 'closed') : ?>
                    <span class="job-listing-item__meta-item job-listing-item__meta-item--deadline">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <?php echo esc_html($deadline_display); ?>
                    </span>
                <?php endif; ?>
                <span class="job-listing-item__meta-item job-listing-item__meta-item--time">
                    <?php printf(esc_html__('Posted %s ago', 'gloceps'), $posted_time); ?>
                </span>
            </div>
        </div>
        <div class="job-listing-item__arrow">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </div>
    </a>
</article>
