<?php
/**
 * Template part for displaying a job card in grid layout
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
$job_type = get_field('vacancy_type', $job_id);
$deadline = get_field('vacancy_deadline', $job_id);
$application_url = get_field('vacancy_application_url', $job_id);
$writeup = get_field('vacancy_writeup', $job_id);
$excerpt = has_excerpt($job_id) ? get_the_excerpt($job_id) : '';
if (!$excerpt && $writeup) {
    $excerpt = wp_strip_all_tags($writeup);
    $excerpt = wp_trim_words($excerpt, 25);
}

// Get featured image or use placeholder
$featured_image = get_the_post_thumbnail_url($job_id, 'medium');
$is_placeholder = false;
if (!$featured_image) {
    $featured_image = gloceps_get_favicon_url(192);
    $is_placeholder = true;
}

// Format deadline
$deadline_display = '';
if ($deadline) {
    $deadline_timestamp = strtotime($deadline);
    $today = strtotime('today');
    if ($deadline_timestamp < $today) {
        $deadline_display = 'Closed';
    } else {
        $deadline_display = date('F j, Y', $deadline_timestamp);
    }
}

// Job type labels
$job_type_labels = array(
    'full-time' => 'Full Time',
    'part-time' => 'Part Time',
    'contract' => 'Contract',
    'internship' => 'Internship',
    'consultancy' => 'Consultancy',
);
$job_type_display = isset($job_type_labels[$job_type]) ? $job_type_labels[$job_type] : $job_type;
?>

<article class="job-card">
    <?php if ($featured_image) : ?>
        <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="job-card__image">
            <img src="<?php echo esc_url($featured_image); ?>" 
                 alt="<?php echo esc_attr(get_the_title($job_id)); ?>"
                 <?php echo $is_placeholder ? 'class="is-placeholder"' : ''; ?> />
        </a>
    <?php endif; ?>
    <div class="job-card__content">
        <div class="job-card__meta">
            <?php if ($job_type) : ?>
                <span class="job-card__type"><?php echo esc_html($job_type_display); ?></span>
            <?php endif; ?>
            <?php if ($deadline_display) : ?>
                <span class="job-card__deadline <?php echo $deadline_display === 'Closed' ? 'job-card__deadline--closed' : ''; ?>">
                    <?php echo $deadline_display === 'Closed' ? esc_html__('Closed', 'gloceps') : esc_html__('Deadline:', 'gloceps') . ' ' . esc_html($deadline_display); ?>
                </span>
            <?php endif; ?>
        </div>
        <h4 class="job-card__title">
            <a href="<?php echo esc_url(get_permalink($job_id)); ?>">
                <?php echo esc_html(get_the_title($job_id)); ?>
            </a>
        </h4>
        <?php if ($excerpt) : ?>
            <p class="job-card__excerpt"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>
        <div class="job-card__footer">
            <?php if ($location) : ?>
                <div class="job-card__location">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span><?php echo esc_html($location); ?></span>
                </div>
            <?php endif; ?>
            <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="job-card__cta">
                <?php esc_html_e('View Details', 'gloceps'); ?> →
            </a>
        </div>
    </div>
</article>
