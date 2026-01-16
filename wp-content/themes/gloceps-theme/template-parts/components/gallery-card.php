<?php
/**
 * Gallery Card Component
 *
 * @package GLOCEPS
 */

$gallery_images = get_field('gallery_images');
$image_count = $gallery_images ? count($gallery_images) : 0;
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');

// If no featured image, use the first gallery image
if (!$featured_image && $gallery_images && !empty($gallery_images[0])) {
    $featured_image = $gallery_images[0]['sizes']['large'];
}

// Get event date for display
$event_date = get_field('event_date');
$date_display = '';
if ($event_date) {
    $date_display = date('F Y', strtotime($event_date));
}

// Generate a gradient color based on post ID for variety
$colors = array(
    array('#0891b2', '#0e7490'), // cyan
    array('#7c3aed', '#5b21b6'), // violet
    array('#059669', '#047857'), // emerald
    array('#dc2626', '#b91c1c'), // red
    array('#2563eb', '#1d4ed8'), // blue
);
$color_index = get_the_ID() % count($colors);
$gradient = $colors[$color_index];

$has_image = !empty($featured_image);
?>

<a href="<?php the_permalink(); ?>" class="gallery-card<?php echo !$has_image ? ' gallery-card--no-image' : ''; ?>" 
   <?php if (!$has_image) : ?>style="background: linear-gradient(135deg, <?php echo esc_attr($gradient[0]); ?> 0%, <?php echo esc_attr($gradient[1]); ?> 100%);"<?php endif; ?>>
    <?php if ($has_image) : ?>
        <img src="<?php echo esc_url($featured_image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
    <?php else : ?>
        <div class="gallery-card__placeholder">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
        </div>
    <?php endif; ?>
    <div class="gallery-card__overlay">
        <span class="gallery-card__count"><?php echo esc_html($image_count); ?> <?php esc_html_e('photos', 'gloceps'); ?></span>
        <h3 class="gallery-card__title"><?php the_title(); ?></h3>
        <?php if ($date_display) : ?>
            <span class="gallery-card__date"><?php echo esc_html($date_display); ?></span>
        <?php endif; ?>
    </div>
</a>

