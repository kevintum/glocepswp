<?php
/**
 * Search Results Template
 * Displays search results with filtering by content type
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get search query and filters
$search_query = get_search_query();
$filter_post_types = isset($_GET['post_type']) ? array_map('sanitize_text_field', (array)$_GET['post_type']) : array();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Define available post types with labels
$available_post_types = array(
    'publication' => __('Publications', 'gloceps'),
    'event' => __('Events', 'gloceps'),
    'team_member' => __('Team Members', 'gloceps'),
    'video' => __('Videos', 'gloceps'),
    'podcast' => __('Podcasts', 'gloceps'),
    'gallery' => __('Galleries', 'gloceps'),
    'article' => __('Articles', 'gloceps'),
    'post' => __('Blog Posts', 'gloceps'),
    'page' => __('Pages', 'gloceps'),
);

// Build query args
$query_args = array(
    'posts_per_page' => 20,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
);

// Set post types
if (!empty($filter_post_types)) {
    $query_args['post_type'] = $filter_post_types;
} else {
    // Include all post types if no filter
    $query_args['post_type'] = array_keys($available_post_types);
}

// Add search query (only if provided)
if (!empty($search_query)) {
    $query_args['s'] = $search_query;
}

// Perform the search
$search_query_obj = new WP_Query($query_args);

// Get total results count
$total_results = $search_query_obj->found_posts;

// Get counts per post type for filter display
$post_type_counts = array();
foreach ($available_post_types as $post_type => $label) {
    $count_args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'fields' => 'ids',
    );
    if (!empty($search_query)) {
        $count_args['s'] = $search_query;
    }
    $count_query = new WP_Query($count_args);
    $post_type_counts[$post_type] = $count_query->found_posts;
    wp_reset_postdata();
}
?>

<?php
$header_attrs = gloceps_get_page_header_attrs(false, 'page-header--search');
?>
<section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title">
                <?php 
                if (!empty($search_query)) {
                    printf(esc_html__('Search Results for "%s"', 'gloceps'), esc_html($search_query));
                } else {
                    esc_html_e('Search', 'gloceps');
                }
                ?>
            </h1>
            <?php if (empty($search_query)) : ?>
            <p class="page-header__description">
                <?php esc_html_e('Browse all content or use the search bar to find specific items.', 'gloceps'); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<main>
    <section class="section search-page">
        <div class="container">
            
            <!-- Search Bar -->
            <div class="search-page__bar reveal">
                <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-page__form">
                    <div class="search-page__input-wrapper">
                        <svg class="search-page__icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                        <input 
                            type="search" 
                            class="search-page__input" 
                            placeholder="<?php esc_attr_e('Type here to search', 'gloceps'); ?>" 
                            name="s"
                            value="<?php echo esc_attr($search_query); ?>"
                            autocomplete="off"
                        />
                    </div>
                    <?php 
                    // Preserve post type filters in search form
                    foreach ($filter_post_types as $post_type) {
                        echo '<input type="hidden" name="post_type[]" value="' . esc_attr($post_type) . '">';
                    }
                    ?>
                    <button type="submit" class="search-page__submit">
                        <?php esc_html_e('Search', 'gloceps'); ?>
                    </button>
                </form>
            </div>

            <!-- Filters -->
            <div class="search-page__filters reveal">
                <div class="search-page__filter-group">
                    <button class="search-page__filter-toggle" aria-expanded="false">
                        <span><?php esc_html_e('Content Type', 'gloceps'); ?></span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="search-page__filter-options">
                        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" id="searchFiltersForm">
                            <?php if (!empty($search_query)) : ?>
                            <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">
                            <?php endif; ?>
                            
                            <?php foreach ($available_post_types as $post_type => $label) : 
                                $count = isset($post_type_counts[$post_type]) ? $post_type_counts[$post_type] : 0;
                                // Show all types when no search query, or show types with results when searching
                                if ($count === 0 && !empty($search_query)) continue;
                            ?>
                            <label class="search-page__filter-checkbox">
                                <input 
                                    type="checkbox" 
                                    name="post_type[]" 
                                    value="<?php echo esc_attr($post_type); ?>" 
                                    <?php checked(in_array($post_type, $filter_post_types)); ?>
                                    class="search-filter-checkbox"
                                />
                                <span class="search-page__filter-checkbox-mark"></span>
                                <span class="search-page__filter-label"><?php echo esc_html($label); ?></span>
                                <span class="search-page__filter-count">(<?php echo esc_html($count); ?>)</span>
                            </label>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="search-page__results-header reveal">
                <p class="search-page__results-count">
                    <?php 
                    if (!empty($search_query)) {
                        printf(
                            esc_html(_n('%d RESULT', '%d RESULTS', $total_results, 'gloceps')),
                            number_format_i18n($total_results)
                        );
                    } else {
                        printf(
                            esc_html(_n('%d ITEM', '%d ITEMS', $total_results, 'gloceps')),
                            number_format_i18n($total_results)
                        );
                    }
                    ?>
                </p>
            </div>

            <!-- Results -->
            <div class="search-page__results">
                <?php if ($search_query_obj->have_posts()) : ?>
                    <div class="search-results-list">
                        <?php while ($search_query_obj->have_posts()) : $search_query_obj->the_post(); 
                            $post_type = get_post_type();
                            $post_type_label = isset($available_post_types[$post_type]) ? $available_post_types[$post_type] : ucfirst($post_type);
                            $excerpt = get_the_excerpt();
                            if (empty($excerpt)) {
                                $excerpt = wp_trim_words(get_the_content(), 30);
                            }
                        ?>
                        <article class="search-result-item reveal">
                            <div class="search-result-item__content">
                                <div class="search-result-item__header">
                                    <span class="search-result-item__type"><?php echo esc_html($post_type_label); ?></span>
                                    <h2 class="search-result-item__title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                </div>
                                <?php if (!empty($excerpt)) : ?>
                                <p class="search-result-item__excerpt">
                                    <?php echo esc_html($excerpt); ?>
                                </p>
                                <?php endif; ?>
                                <div class="search-result-item__meta">
                                    <time class="search-result-item__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo esc_html(get_the_date('j F Y')); ?>
                                    </time>
                                    <?php if (get_post_type() === 'publication') : 
                                        $publication_type = get_the_terms(get_the_ID(), 'publication_type');
                                        if ($publication_type && !is_wp_error($publication_type)) :
                                            $type = $publication_type[0];
                                    ?>
                                    <span class="search-result-item__category">
                                        <?php echo esc_html($type->name); ?>
                                    </span>
                                    <?php 
                                        endif;
                                    endif; 
                                    ?>
                                </div>
                            </div>
                        </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($search_query_obj->max_num_pages > 1) : ?>
                    <nav class="search-page__pagination reveal">
                        <?php
                        echo paginate_links(array(
                            'total' => $search_query_obj->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '«',
                            'next_text' => '»',
                            'type' => 'list',
                        ));
                        ?>
                    </nav>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="search-page__no-results reveal">
                        <p class="search-page__no-results-text">
                            <?php 
                            if (!empty($search_query)) {
                                esc_html_e('No results found. Try adjusting your search terms or filters.', 'gloceps');
                            } else {
                                esc_html_e('No content available.', 'gloceps');
                            }
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </section>
</main>

<?php
wp_reset_postdata();
get_footer();
