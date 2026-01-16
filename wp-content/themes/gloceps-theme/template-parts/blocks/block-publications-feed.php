<?php
/**
 * Block: Publications Feed
 * 
 * Displays a grid of recent publications
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'From This Pillar';
$title = get_sub_field('title') ?: 'Publications';
$count = get_sub_field('count') ?: 6;
$show_filter = get_sub_field('show_filter');
$view_all_link = get_sub_field('view_all_link');
$style = get_sub_field('style') ?: 'default';

// Get publication types for filter
$pub_types = get_terms(array(
    'taxonomy' => 'publication_type',
    'hide_empty' => true,
));

// Query publications
$args = array(
    'post_type' => 'publication',
    'posts_per_page' => $count,
    'orderby' => 'date',
    'order' => 'DESC',
);

// Filter by research pillar if set
$filter_pillar = get_sub_field('filter_by_pillar');
if ($filter_pillar) {
    // Try to get term by slug
    $pillar_term = get_term_by('slug', $filter_pillar, 'research_pillar');
    if ($pillar_term && !is_wp_error($pillar_term)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'research_pillar',
                'field' => 'term_id',
                'terms' => $pillar_term->term_id,
            ),
        );
    }
}

$publications = new WP_Query($args);
?>

<section class="section publications-feed <?php echo $style === 'dark' ? 'publications-feed--dark' : ''; ?>" id="publications">
    <div class="container">
        <div class="section-header section-header--with-action reveal">
            <div>
                <?php if ($eyebrow) : ?>
                    <div class="section-header__eyebrow">
                        <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($title) : ?>
                    <h2 class="section-header__title"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>
            </div>

            <?php if ($view_all_link) : ?>
                <a href="<?php echo esc_url($view_all_link['url']); ?>" class="btn btn--secondary">
                    <?php echo esc_html($view_all_link['title'] ?: 'View All'); ?>
                    <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>

        <?php if ($show_filter && !empty($pub_types) && !is_wp_error($pub_types)) : ?>
            <div class="publications-feed__filter reveal">
                <div class="filter-tags">
                    <button class="filter-tag filter-tag--active" data-filter="all">All</button>
                    <?php foreach ($pub_types as $type) : ?>
                        <button class="filter-tag" data-filter="<?php echo esc_attr($type->slug); ?>">
                            <?php echo esc_html($type->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($publications->have_posts()) : ?>
            <div class="publications-feed__grid reveal stagger-children">
                <?php while ($publications->have_posts()) : $publications->the_post(); ?>
                    <?php get_template_part('template-parts/components/publication-card'); ?>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="no-results">No publications found.</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>

