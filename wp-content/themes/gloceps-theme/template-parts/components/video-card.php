<?php
/**
 * Template part for displaying a video card
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$video_source_type = get_field('video_source_type') ?: 'embed';
$video_url = get_field('video_url');
$video_file = get_field('video_file');
$duration = get_field('duration');
$video_thumbnail = get_field('video_thumbnail');
$categories = get_the_terms(get_the_ID(), 'video_category');
$category_name = $categories && !is_wp_error($categories) ? $categories[0]->name : '';

// Get thumbnail - priority: custom thumbnail > featured image > placeholder
$thumbnail_url = '';
if ($video_thumbnail && is_array($video_thumbnail)) {
    $thumbnail_url = $video_thumbnail['url'] ?? ($video_thumbnail['sizes']['large'] ?? '');
} elseif (has_post_thumbnail()) {
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
}

if (!$thumbnail_url) {
    $thumbnail_url = gloceps_get_favicon_url(192);
}

// Format date
$date_display = date('F Y', strtotime(get_the_date('c')));
?>

<a href="#" class="video-card" 
     data-video-type="<?php echo esc_attr($video_source_type); ?>"
     data-video-url="<?php echo esc_attr($video_url ?: ''); ?>"
     data-video-file="<?php echo esc_attr($video_file && is_array($video_file) ? $video_file['url'] : ''); ?>"
     data-video-title="<?php echo esc_attr(get_the_title()); ?>">
    <div class="video-card__thumbnail">
        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
        <div class="video-card__play">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
        </div>
        <?php if ($duration) : ?>
        <span class="video-card__duration"><?php echo esc_html($duration); ?></span>
        <?php endif; ?>
    </div>
    <div class="video-card__content">
        <?php if ($category_name) : ?>
        <span class="video-card__category"><?php echo esc_html($category_name); ?></span>
        <?php endif; ?>
        <h3 class="video-card__title"><?php the_title(); ?></h3>
        <div class="video-card__meta"><?php echo esc_html($date_display); ?></div>
    </div>
</a>

