<?php
/**
 * Latest Articles Block
 * 
 * @package GLOCEPS
 */

$section_title = get_sub_field('section_title') ?: 'Latest Articles';
$section_description = get_sub_field('section_description') ?: 'Opinion and analysis from our experts';
$count = get_sub_field('count') ?: 3;

// Query latest articles
$articles_query = new WP_Query(array(
    'post_type' => 'article',
    'posts_per_page' => $count,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<?php if ($articles_query->have_posts()) : ?>
<section class="section" style="padding-top: var(--space-12); padding-bottom: var(--space-12);">
    <div class="container">
        <div class="section-header section-header--with-link">
            <div>
                <h2 class="section-header__title"><?php echo esc_html($section_title); ?></h2>
                <p class="section-header__description"><?php echo esc_html($section_description); ?></p>
            </div>
            <a href="<?php echo esc_url(get_post_type_archive_link('article')); ?>" class="btn btn--ghost"><?php esc_html_e('View All Articles', 'gloceps'); ?> →</a>
        </div>
        <div class="articles-grid">
            <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                <?php get_template_part('template-parts/components/article-card'); ?>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>

