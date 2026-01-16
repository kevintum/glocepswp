<?php
/**
 * Media Categories Grid Block
 * 
 * @package GLOCEPS
 */

$videos_title = get_sub_field('videos_title') ?: 'Videos';
$videos_description = get_sub_field('videos_description') ?: 'Expert interviews, panel discussions & webinars';
$videos_image = get_sub_field('videos_image');

$podcasts_title = get_sub_field('podcasts_title') ?: 'Podcasts';
$podcasts_description = get_sub_field('podcasts_description') ?: 'In-depth policy discussions & analysis';
$podcasts_image = get_sub_field('podcasts_image');

$gallery_title = get_sub_field('gallery_title') ?: 'Photo Gallery';
$gallery_description = get_sub_field('gallery_description') ?: 'Event highlights & official engagements';
$gallery_image = get_sub_field('gallery_image');

$articles_title = get_sub_field('articles_title') ?: 'Articles';
$articles_description = get_sub_field('articles_description') ?: 'Opinion, commentary & news coverage';
$articles_image = get_sub_field('articles_image');

// Get image URLs
$videos_url = '';
if ($videos_image && is_array($videos_image)) {
    $videos_url = $videos_image['url'] ?? '';
} elseif ($videos_image) {
    $videos_url = wp_get_attachment_image_url($videos_image, 'large');
}

$podcasts_url = '';
if ($podcasts_image && is_array($podcasts_image)) {
    $podcasts_url = $podcasts_image['url'] ?? '';
} elseif ($podcasts_image) {
    $podcasts_url = wp_get_attachment_image_url($podcasts_image, 'large');
}

$gallery_url = '';
if ($gallery_image && is_array($gallery_image)) {
    $gallery_url = $gallery_image['url'] ?? '';
} elseif ($gallery_image) {
    $gallery_url = wp_get_attachment_image_url($gallery_image, 'large');
}

$articles_url = '';
if ($articles_image && is_array($articles_image)) {
    $articles_url = $articles_image['url'] ?? '';
} elseif ($articles_image) {
    $articles_url = wp_get_attachment_image_url($articles_image, 'large');
}
?>

<section class="section media-categories-section">
    <div class="container">
        <div class="media-categories-grid">
            <!-- Videos -->
            <a href="<?php echo esc_url(get_post_type_archive_link('video')); ?>" class="media-category-card media-category-card--videos">
                <div class="media-category-card__bg">
                    <?php if ($videos_url) : ?>
                    <img src="<?php echo esc_url($videos_url); ?>" alt="<?php echo esc_attr($videos_title); ?>" />
                    <?php endif; ?>
                </div>
                <div class="media-category-card__content">
                    <div class="media-category-card__icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </div>
                    <h3 class="media-category-card__title"><?php echo esc_html($videos_title); ?></h3>
                    <p class="media-category-card__description"><?php echo esc_html($videos_description); ?></p>
                    <span class="media-category-card__cta">
                        <?php esc_html_e('Browse Videos', 'gloceps'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </span>
                </div>
            </a>

            <!-- Podcasts -->
            <a href="<?php echo esc_url(get_post_type_archive_link('podcast')); ?>" class="media-category-card media-category-card--podcasts">
                <div class="media-category-card__bg">
                    <?php if ($podcasts_url) : ?>
                    <img src="<?php echo esc_url($podcasts_url); ?>" alt="<?php echo esc_attr($podcasts_title); ?>" />
                    <?php endif; ?>
                </div>
                <div class="media-category-card__content">
                    <div class="media-category-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" y1="19" x2="12" y2="23"></line>
                        </svg>
                    </div>
                    <h3 class="media-category-card__title"><?php echo esc_html($podcasts_title); ?></h3>
                    <p class="media-category-card__description"><?php echo esc_html($podcasts_description); ?></p>
                    <span class="media-category-card__cta">
                        <?php esc_html_e('Listen Now', 'gloceps'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </span>
                </div>
            </a>

            <!-- Photo Gallery -->
            <a href="<?php echo esc_url(get_post_type_archive_link('gallery')); ?>" class="media-category-card media-category-card--gallery">
                <div class="media-category-card__bg">
                    <?php if ($gallery_url) : ?>
                    <img src="<?php echo esc_url($gallery_url); ?>" alt="<?php echo esc_attr($gallery_title); ?>" />
                    <?php endif; ?>
                </div>
                <div class="media-category-card__content">
                    <div class="media-category-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                    <h3 class="media-category-card__title"><?php echo esc_html($gallery_title); ?></h3>
                    <p class="media-category-card__description"><?php echo esc_html($gallery_description); ?></p>
                    <span class="media-category-card__cta">
                        <?php esc_html_e('View Gallery', 'gloceps'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </span>
                </div>
            </a>

            <!-- Articles -->
            <a href="<?php echo esc_url(get_post_type_archive_link('article')); ?>" class="media-category-card media-category-card--articles">
                <div class="media-category-card__bg">
                    <?php if ($articles_url) : ?>
                    <img src="<?php echo esc_url($articles_url); ?>" alt="<?php echo esc_attr($articles_title); ?>" />
                    <?php endif; ?>
                </div>
                <div class="media-category-card__content">
                    <div class="media-category-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <h3 class="media-category-card__title"><?php echo esc_html($articles_title); ?></h3>
                    <p class="media-category-card__description"><?php echo esc_html($articles_description); ?></p>
                    <span class="media-category-card__cta">
                        <?php esc_html_e('Read Articles', 'gloceps'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m12 5 7 7-7 7"></path>
                        </svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>

