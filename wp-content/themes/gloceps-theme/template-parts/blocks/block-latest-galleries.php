<?php
/**
 * Latest Photo Galleries Block
 * 
 * @package GLOCEPS
 */

$section_title = get_sub_field('section_title') ?: 'Photo Galleries';
$section_description = get_sub_field('section_description') ?: 'Highlights from our events';
$count = get_sub_field('count') ?: 3;

// Query latest galleries
$galleries_query = new WP_Query(array(
    'post_type' => 'gallery',
    'posts_per_page' => $count,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<?php if ($galleries_query->have_posts()) : ?>
<section class="section section--dark" style="padding-top: var(--space-12); padding-bottom: var(--space-12);">
    <div class="container">
        <div class="section-header section-header--with-link">
            <div>
                <h2 class="section-header__title"><?php echo esc_html($section_title); ?></h2>
                <p class="section-header__description"><?php echo esc_html($section_description); ?></p>
            </div>
            <a href="<?php echo esc_url(get_post_type_archive_link('gallery')); ?>" class="btn btn--outline-light"><?php esc_html_e('View All Galleries', 'gloceps'); ?> →</a>
        </div>
        <div class="gallery-preview-grid">
            <?php 
            $is_first = true;
            while ($galleries_query->have_posts()) : $galleries_query->the_post(); 
                $gallery_images = get_field('gallery_images');
                $image_count = $gallery_images ? count($gallery_images) : 0;
                $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
                
                // If no featured image, use first gallery image
                if (!$featured_image && $gallery_images && !empty($gallery_images[0])) {
                    $featured_image = $gallery_images[0]['sizes']['large'] ?? $gallery_images[0]['url'] ?? '';
                }
                
                if (!$featured_image) {
                    $featured_image = gloceps_get_favicon_url(192);
                }
            ?>
            <a href="<?php the_permalink(); ?>" class="gallery-preview-card<?php echo $is_first ? ' gallery-preview-card--large' : ''; ?>">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php the_title_attribute(); ?>" />
                <div class="gallery-preview-card__overlay">
                    <span class="gallery-preview-card__count"><?php echo esc_html($image_count); ?> <?php esc_html_e('photos', 'gloceps'); ?></span>
                    <h3 class="gallery-preview-card__title"><?php the_title(); ?></h3>
                </div>
            </a>
            <?php 
                $is_first = false;
            endwhile; 
            ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>

