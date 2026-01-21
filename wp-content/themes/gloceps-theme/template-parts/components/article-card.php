<?php
/**
 * Template part for displaying an article card
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$categories = get_the_terms(get_the_ID(), 'article_category');
$category_name = $categories && !is_wp_error($categories) ? $categories[0]->name : '';

// Get featured image or placeholder
$thumbnail_url = '';
$is_placeholder = false;
if (has_post_thumbnail()) {
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
} else {
    $thumbnail_url = gloceps_get_favicon_url(192);
    $is_placeholder = true;
}

// Format date
$date_display = date('M j, Y', strtotime(get_the_date('c')));

// Get excerpt
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(get_the_content(), 20, '...');
}
?>

<article class="article-card">
    <div class="article-card__image">
        <a href="<?php the_permalink(); ?>">
            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" <?php echo $is_placeholder ? 'class="is-placeholder"' : ''; ?> />
        </a>
    </div>
    <div class="article-card__content">
        <div class="article-card__meta">
            <?php if ($category_name) : ?>
            <span class="article-card__category"><?php echo esc_html($category_name); ?></span>
            <?php endif; ?>
            <span class="article-card__date"><?php echo esc_html($date_display); ?></span>
        </div>
        <h4 class="article-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <?php if ($excerpt) : ?>
        <p class="article-card__excerpt"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>
        <a href="<?php the_permalink(); ?>" class="article-card__link"><?php esc_html_e('Read More', 'gloceps'); ?> →</a>
    </div>
</article>

