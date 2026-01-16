<?php
/**
 * Media Hero Block
 * 
 * @package GLOCEPS
 */

$title = get_sub_field('title') ?: 'Media Centre';
$description = get_sub_field('description') ?: 'Explore our multimedia content — from expert interviews and policy podcasts to event galleries and in-depth analysis.';
$background_image = get_sub_field('background_image');
$stats = get_sub_field('stats');

// Get background image URL
$bg_url = '';
if ($background_image && is_array($background_image)) {
    $bg_url = $background_image['url'] ?? '';
} elseif ($background_image) {
    $bg_url = wp_get_attachment_image_url($background_image, 'full');
}
?>

<section class="media-hero">
    <div class="media-hero__bg">
        <?php if ($bg_url) : ?>
        <img src="<?php echo esc_url($bg_url); ?>" alt="<?php echo esc_attr($title); ?>" />
        <?php endif; ?>
        <div class="media-hero__overlay"></div>
    </div>
    <div class="container">
        <nav class="page-header__breadcrumb page-header__breadcrumb--light" aria-label="Breadcrumb">
            <?php gloceps_breadcrumbs(); ?>
        </nav>
        <div class="media-hero__content">
            <h1 class="media-hero__title"><?php echo esc_html($title); ?></h1>
            <p class="media-hero__description">
                <?php echo esc_html($description); ?>
            </p>
            <?php if ($stats && have_rows('stats')) : ?>
            <div class="media-hero__stats">
                <?php while (have_rows('stats')) : the_row(); ?>
                <div class="media-hero__stat">
                    <span class="media-hero__stat-number"><?php echo esc_html(get_sub_field('number')); ?></span>
                    <span class="media-hero__stat-label"><?php echo esc_html(get_sub_field('label')); ?></span>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
