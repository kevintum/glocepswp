<?php
/**
 * Latest Articles Block
 * 
 * @package GLOCEPS
 */

$section_title = get_sub_field('section_title') ?: 'Latest Articles';
$section_description = get_sub_field('section_description') ?: 'Opinion and analysis from our experts';
$background_style = get_sub_field('background_style') ?: 'default';
$layout = get_sub_field('layout') ?: 'grid';
$categories = get_sub_field('categories');
$per_page = get_sub_field('per_page') ?: 6;
$max_articles = get_sub_field('max_articles'); // Optional limit for grid layout
$carousel_count = get_sub_field('carousel_count') ?: 6;

// Determine posts per page based on layout
$posts_per_page = ($layout === 'carousel') ? $carousel_count : $per_page;

// Get paged for grid layout pagination
$paged = ($layout === 'grid') ? max(1, get_query_var('paged') ?: (isset($_GET['paged']) ? absint($_GET['paged']) : 1)) : 1;

// Build query args
$query_args = array(
    'post_type' => 'article',
    'posts_per_page' => $posts_per_page,
    'orderby' => 'date',
    'order' => 'DESC',
);

// Handle max_articles limit for grid layout
if ($layout === 'grid' && $max_articles && $max_articles > 0) {
    // Calculate max pages based on limit
    $max_pages = ceil($max_articles / $per_page);
    
    // Don't allow paged to exceed max pages
    if ($paged > $max_pages) {
        $paged = $max_pages;
    }
    
    // Calculate offset for current page
    $offset = ($paged - 1) * $per_page;
    
    // Only fetch what we need for this page, up to the limit
    $posts_needed = min($per_page, $max_articles - $offset);
    
    if ($posts_needed > 0 && $offset < $max_articles) {
        $query_args['offset'] = $offset;
        $query_args['posts_per_page'] = $posts_needed;
        $query_args['no_found_rows'] = true; // Don't count all posts
    } else {
        // No posts to show on this page
        $query_args['post__in'] = array(0); // Return no posts
    }
} else {
    // Normal pagination without limit
    if ($layout === 'grid') {
        $query_args['paged'] = $paged;
    }
}

// Filter by categories if selected
if ($categories && !empty($categories)) {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'article_category',
            'field' => 'term_id',
            'terms' => $categories,
        ),
    );
}

// Query articles
$articles_query = new WP_Query($query_args);

// Override max_num_pages if max_articles limit is set
if ($layout === 'grid' && $max_articles && $max_articles > 0) {
    $max_pages = ceil($max_articles / $per_page);
    $articles_query->max_num_pages = $max_pages;
}
?>

<?php if ($articles_query->have_posts()) : ?>
<section class="section section--bg-<?php echo esc_attr($background_style); ?>" style="padding-top: var(--space-12); padding-bottom: var(--space-12);">
    <div class="container">
        <div class="section-header section-header--with-link">
            <div>
                <h2 class="section-header__title"><?php echo esc_html($section_title); ?></h2>
                <p class="section-header__description"><?php echo esc_html($section_description); ?></p>
            </div>
            <a href="<?php echo esc_url(get_post_type_archive_link('article')); ?>" class="btn btn--ghost"><?php esc_html_e('View All Articles', 'gloceps'); ?> →</a>
        </div>
        
        <?php if ($layout === 'carousel') : ?>
            <div class="articles-carousel-wrapper reveal">
                <button class="articles-carousel__btn articles-carousel__btn--prev" aria-label="Previous articles">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>
                <div class="articles-carousel" data-articles-carousel>
                    <div class="articles-carousel__track">
                        <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                            <div class="articles-carousel__slide">
                                <?php get_template_part('template-parts/components/article-card'); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <button class="articles-carousel__btn articles-carousel__btn--next" aria-label="Next articles">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>
        <?php else : ?>
            <div class="articles-grid reveal">
                <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                    <?php get_template_part('template-parts/components/article-card'); ?>
                <?php endwhile; ?>
            </div>
            
            <?php if ($articles_query->max_num_pages > 1) : ?>
                <div class="articles-pagination">
                    <?php
                    // Get current URL and build base URL for pagination
                    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $base_url = remove_query_arg('paged', $current_url);
                    
                    // Previous link
                    if ($paged > 1) {
                        $prev_url = ($paged == 2) ? $base_url : add_query_arg('paged', $paged - 1, $base_url);
                        echo '<a href="' . esc_url($prev_url) . '" class="articles-pagination__link articles-pagination__link--prev">← Previous</a>';
                    }
                    
                    // Page numbers
                    for ($i = 1; $i <= $articles_query->max_num_pages; $i++) {
                        if ($i == 1) {
                            $url = $base_url;
                        } else {
                            $url = add_query_arg('paged', $i, $base_url);
                        }
                        
                        $class = ($i == $paged) ? 'articles-pagination__link articles-pagination__link--current' : 'articles-pagination__link';
                        echo '<a href="' . esc_url($url) . '" class="' . esc_attr($class) . '">' . esc_html($i) . '</a>';
                    }
                    
                    // Next link
                    if ($paged < $articles_query->max_num_pages) {
                        $next_url = add_query_arg('paged', $paged + 1, $base_url);
                        echo '<a href="' . esc_url($next_url) . '" class="articles-pagination__link articles-pagination__link--next">Next →</a>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>

