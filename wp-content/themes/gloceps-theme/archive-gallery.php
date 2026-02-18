<?php
/**
 * Archive template for Galleries
 * Matches media-gallery.html structure
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for gallery archive
$gallery_title = get_field('gallery_intro_title', 'option') ?: 'Photo Gallery';
$gallery_description = get_field('gallery_intro_description', 'option') ?: 'Browse photographs from conferences, symposiums, workshops, and official engagements.';

// Get current category filter from URL
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get all gallery categories (if taxonomy exists)
$gallery_categories = get_terms(array(
    'taxonomy' => 'gallery_category',
    'hide_empty' => true,
    'orderby' => 'term_id',
));
?>

<main>
    <!-- Page Header -->
    <?php
    $header_attrs = gloceps_get_page_header_attrs(false);
    ?>
    <section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
        <div class="container">
            <div class="page-header__content">
                <?php gloceps_breadcrumbs(); ?>
                <h1 class="page-header__title"><?php echo esc_html($gallery_title); ?></h1>
                <?php if ($gallery_description) : ?>
                    <p class="page-header__description">
                        <?php echo esc_html($gallery_description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Gallery Category Filters -->
    <?php if ($gallery_categories && !is_wp_error($gallery_categories)) : ?>
        <section class="section section--filters">
            <div class="container">
                <div class="events-tabs">
                    <div class="events-tabs__wrapper">
                        <a href="<?php echo esc_url(remove_query_arg('category')); ?>" 
                           class="events-tab <?php echo $current_category === 'all' ? 'events-tab--active' : ''; ?>">
                            <?php esc_html_e('All Galleries', 'gloceps'); ?>
                        </a>
                        <?php foreach ($gallery_categories as $category) : ?>
                            <a href="<?php echo esc_url(add_query_arg('category', $category->slug)); ?>" 
                               class="events-tab <?php echo $current_category === $category->slug ? 'events-tab--active' : ''; ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Gallery Grid -->
    <section class="section">
        <div class="container">
            <?php
            // Build query args based on category filter
            $query_args = array(
                'post_type' => 'gallery',
                'posts_per_page' => 9,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            // Add category filter if not "all"
            if ($current_category !== 'all' && $gallery_categories && !is_wp_error($gallery_categories)) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'gallery_category',
                        'field' => 'slug',
                        'terms' => $current_category,
                    ),
                );
            }

            $galleries_query = new WP_Query($query_args);

            if ($galleries_query->have_posts()) :
            ?>
                <div class="gallery-grid">
                    <?php while ($galleries_query->have_posts()) : $galleries_query->the_post(); ?>
                        <?php get_template_part('template-parts/components/gallery-card'); ?>
                    <?php endwhile; ?>
                </div>

                <?php
                // Custom pagination matching static HTML
                $total_pages = $galleries_query->max_num_pages;
                if ($total_pages > 1) :
                    $current_page = max(1, $paged);
                    $base_url = remove_query_arg('paged');
                    if ($current_category !== 'all') {
                        $base_url = add_query_arg('category', $current_category, $base_url);
                    }
                ?>
                <div class="pagination">
                    <a href="<?php echo esc_url($current_page > 1 ? add_query_arg('paged', $current_page - 1, $base_url) : '#'); ?>" 
                       class="pagination__btn pagination__btn--prev" 
                       <?php if ($current_page <= 1) : ?>disabled style="pointer-events: none; opacity: 0.5;"<?php endif; ?>>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                        <?php esc_html_e('Previous', 'gloceps'); ?>
                    </a>
                    <div class="pagination__pages">
                        <?php
                        $prev_ellipsis = false;
                        for ($i = 1; $i <= $total_pages; $i++) :
                            if ($i == 1 || $i == $total_pages || ($i >= $current_page - 1 && $i <= $current_page + 1)) :
                                $prev_ellipsis = false;
                        ?>
                            <a href="<?php echo esc_url(add_query_arg('paged', $i, $base_url)); ?>" 
                               class="pagination__page <?php echo $i == $current_page ? 'pagination__page--active' : ''; ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                        <?php
                            elseif (!$prev_ellipsis && ($i < $current_page - 1 || $i > $current_page + 1)) :
                                $prev_ellipsis = true;
                        ?>
                            <span class="pagination__ellipsis">...</span>
                        <?php
                            endif;
                        endfor;
                        ?>
                    </div>
                    <a href="<?php echo esc_url($current_page < $total_pages ? add_query_arg('paged', $current_page + 1, $base_url) : '#'); ?>" 
                       class="pagination__btn pagination__btn--next"
                       <?php if ($current_page >= $total_pages) : ?>disabled style="pointer-events: none; opacity: 0.5;"<?php endif; ?>>
                        <?php esc_html_e('Next', 'gloceps'); ?>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="no-results">
                    <p><?php esc_html_e('No galleries found. Try adjusting your filters.', 'gloceps'); ?></p>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>
</main>

<?php
get_footer();
