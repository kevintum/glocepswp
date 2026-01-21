<?php
/**
 * Event Gallery Section
 * 
 * Displays event photos in a grid with thumbnail carousel navigation
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$gallery = isset($gallery) ? $gallery : get_field('gallery');
?>

<?php if ($gallery && is_array($gallery) && !empty($gallery)) : ?>
<div class="event-gallery">
    <h2><?php esc_html_e('Gallery', 'gloceps'); ?></h2>
    
    <div class="event-gallery__main" data-event-gallery>
        <div class="event-gallery__grid">
            <?php foreach ($gallery as $index => $image) : 
                $image_url = '';
                $image_alt = '';
                $image_caption = '';
                
                if (is_array($image)) {
                    $image_url = $image['url'] ?? $image['sizes']['large'] ?? '';
                    $image_alt = $image['alt'] ?? '';
                    $image_caption = $image['caption'] ?? '';
                } elseif (is_numeric($image)) {
                    $image_url = wp_get_attachment_image_url($image, 'large');
                    $image_alt = get_post_meta($image, '_wp_attachment_image_alt', true);
                    $image_caption = wp_get_attachment_caption($image);
                }
                
                if (!$image_url) continue;
            ?>
                <div class="event-gallery__item" data-gallery-index="<?php echo esc_attr($index); ?>">
                    <a href="<?php echo esc_url($image_url); ?>" class="event-gallery__link" data-event-gallery-image data-image-index="<?php echo esc_attr($index); ?>" data-image-url="<?php echo esc_url($image_url); ?>" data-image-caption="<?php echo esc_attr($image_caption); ?>" data-image-alt="<?php echo esc_attr($image_alt ?: 'Event photo'); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt ?: 'Event photo'); ?>" loading="lazy" />
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if (count($gallery) > 6) : ?>
        <div class="event-gallery__thumbnails-wrapper">
            <button class="event-gallery__thumb-btn event-gallery__thumb-btn--prev" aria-label="Previous thumbnails">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
            <div class="event-gallery__thumbnails" data-event-gallery-thumbnails>
                <div class="event-gallery__thumbnails-track">
                    <?php foreach ($gallery as $index => $image) : 
                        $thumb_url = '';
                        if (is_array($image)) {
                            $thumb_url = $image['sizes']['thumbnail'] ?? $image['url'] ?? '';
                        } elseif (is_numeric($image)) {
                            $thumb_url = wp_get_attachment_image_url($image, 'thumbnail');
                        }
                        
                        if (!$thumb_url) continue;
                    ?>
                        <button class="event-gallery__thumb" data-thumb-index="<?php echo esc_attr($index); ?>" aria-label="View image <?php echo esc_attr($index + 1); ?>">
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy" />
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="event-gallery__thumb-btn event-gallery__thumb-btn--next" aria-label="Next thumbnails">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>
        </div>
    <?php else : ?>
        <div class="event-gallery__thumbnails-static">
            <?php foreach ($gallery as $index => $image) : 
                $thumb_url = '';
                if (is_array($image)) {
                    $thumb_url = $image['sizes']['thumbnail'] ?? $image['url'] ?? '';
                } elseif (is_numeric($image)) {
                    $thumb_url = wp_get_attachment_image_url($image, 'thumbnail');
                }
                
                if (!$thumb_url) continue;
            ?>
                <button class="event-gallery__thumb" data-thumb-index="<?php echo esc_attr($index); ?>" aria-label="View image <?php echo esc_attr($index + 1); ?>">
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy" />
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
