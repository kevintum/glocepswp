<?php
/**
 * Archive template for Publications
 * Matches publications.html structure exactly
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for publications archive
$publications_title = get_field('publications_intro_title', 'option') ?: 'Publications';
$publications_description = get_field('publications_intro_description', 'option') ?: 'Explore our research papers, policy briefs, bulletins, and analysis on policy and strategy across Eastern Africa. Free resources and premium publications available.';
$publications_per_page = absint(get_field('publications_per_page', 'option')) ?: 12;

// Get filter parameters from URL
$filter_type = isset($_GET['type']) ? array_map('sanitize_text_field', (array)$_GET['type']) : array();
$filter_pillar = isset($_GET['pillar']) ? array_map('sanitize_text_field', (array)$_GET['pillar']) : array();
$filter_access = isset($_GET['access']) ? array_map('sanitize_text_field', (array)$_GET['access']) : array();
$filter_year = isset($_GET['year']) ? array_map('sanitize_text_field', (array)$_GET['year']) : array();
$filter_format = isset($_GET['format']) ? array_map('sanitize_text_field', (array)$_GET['format']) : array();
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$sort_by = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'newest';
$view_mode = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'grid';
// Get paged from query var or GET parameter (for custom post type archives)
$paged = max(1, get_query_var('paged') ?: (isset($_GET['paged']) ? absint($_GET['paged']) : 1));

// Get all taxonomies for filters
$publication_types = get_terms(array(
    'taxonomy' => 'publication_type',
    'hide_empty' => true,
    'orderby' => 'term_id',
));

$research_pillars = get_terms(array(
    'taxonomy' => 'research_pillar',
    'hide_empty' => true,
    'orderby' => 'term_id',
));

// Get unique years from publications
$years_query = new WP_Query(array(
    'post_type' => 'publication',
    'posts_per_page' => -1,
    'fields' => 'ids',
));
$years = array();
if ($years_query->have_posts()) {
    foreach ($years_query->posts as $post_id) {
        $year = get_the_date('Y', $post_id);
        if (!in_array($year, $years)) {
            $years[] = $year;
        }
    }
}
rsort($years);
wp_reset_postdata();

// Build query args
$query_args = array(
    'post_type' => 'publication',
    'posts_per_page' => $publications_per_page,
    'paged' => $paged,
);

// Search
if ($search_query) {
    $query_args['s'] = $search_query;
}

// Sort
switch ($sort_by) {
    case 'oldest':
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'ASC';
        break;
    case 'title-az':
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'ASC';
        break;
    case 'title-za':
        $query_args['orderby'] = 'title';
        $query_args['order'] = 'DESC';
        break;
    default: // newest
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'DESC';
}

// Build tax_query
$tax_query = array('relation' => 'AND');

if (!empty($filter_type)) {
    $tax_query[] = array(
        'taxonomy' => 'publication_type',
        'field' => 'slug',
        'terms' => $filter_type,
    );
}

if (!empty($filter_pillar)) {
    $tax_query[] = array(
        'taxonomy' => 'research_pillar',
        'field' => 'slug',
        'terms' => $filter_pillar,
    );
}

if (count($tax_query) > 1) {
    $query_args['tax_query'] = $tax_query;
}

// Build meta_query for access type, format, and year
$meta_query = array('relation' => 'AND');

if (!empty($filter_access)) {
    $meta_query[] = array(
        'key' => 'access_type',
        'value' => $filter_access,
        'compare' => 'IN',
    );
}

if (!empty($filter_format)) {
    $meta_query[] = array(
        'key' => 'publication_format',
        'value' => $filter_format,
        'compare' => 'IN',
    );
}

if (count($meta_query) > 1) {
    $query_args['meta_query'] = $meta_query;
}

// Year filter (using date_query)
if (!empty($filter_year)) {
    $query_args['date_query'] = array(
        array(
            'year' => $filter_year,
            'compare' => 'IN',
        ),
    );
}

$publications_query = new WP_Query($query_args);
?>

<!-- Page Header -->
<?php
$header_attrs = gloceps_get_page_header_attrs(false);
?>
<section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title"><?php echo esc_html($publications_title); ?></h1>
            <?php if ($publications_description) : ?>
            <p class="page-header__description">
                <?php echo esc_html($publications_description); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<main>
    <section class="section publications-archive">
        <div class="container">
            
            <!-- Publications Toolbar -->
            <div class="publications-toolbar reveal">
                <div class="publications-search">
                    <svg class="publications-search__icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                    <form method="get" action="<?php echo esc_url(get_post_type_archive_link('publication')); ?>" class="publications-search__form">
                        <input 
                            type="search" 
                            class="publications-search__input" 
                            placeholder="<?php esc_attr_e('Search publications...', 'gloceps'); ?>" 
                            name="s"
                            value="<?php echo esc_attr($search_query); ?>"
                            id="publicationSearch"
                        />
                        <?php
                        // Preserve filter parameters
                        foreach ($filter_type as $type) {
                            echo '<input type="hidden" name="type[]" value="' . esc_attr($type) . '">';
                        }
                        foreach ($filter_pillar as $pillar) {
                            echo '<input type="hidden" name="pillar[]" value="' . esc_attr($pillar) . '">';
                        }
                        foreach ($filter_access as $access) {
                            echo '<input type="hidden" name="access[]" value="' . esc_attr($access) . '">';
                        }
                        foreach ($filter_year as $year) {
                            echo '<input type="hidden" name="year[]" value="' . esc_attr($year) . '">';
                        }
                        foreach ($filter_format as $format) {
                            echo '<input type="hidden" name="format[]" value="' . esc_attr($format) . '">';
                        }
                        if ($sort_by) {
                            echo '<input type="hidden" name="sort" value="' . esc_attr($sort_by) . '">';
                        }
                        if ($view_mode) {
                            echo '<input type="hidden" name="view" value="' . esc_attr($view_mode) . '">';
                        }
                        ?>
                    </form>
                </div>
                
                <div class="publications-toolbar__right">
                    <div class="publications-sort">
                        <label for="sortSelect" class="publications-sort__label"><?php esc_html_e('Sort by:', 'gloceps'); ?></label>
                        <select id="sortSelect" class="publications-sort__select">
                            <option value="newest" <?php selected($sort_by, 'newest'); ?>><?php esc_html_e('Newest First', 'gloceps'); ?></option>
                            <option value="oldest" <?php selected($sort_by, 'oldest'); ?>><?php esc_html_e('Oldest First', 'gloceps'); ?></option>
                            <option value="title-az" <?php selected($sort_by, 'title-az'); ?>><?php esc_html_e('Title A-Z', 'gloceps'); ?></option>
                            <option value="title-za" <?php selected($sort_by, 'title-za'); ?>><?php esc_html_e('Title Z-A', 'gloceps'); ?></option>
                        </select>
                    </div>
                    
                    <div class="publications-view-toggle">
                        <button class="view-toggle__btn <?php echo $view_mode === 'grid' ? 'view-toggle__btn--active' : ''; ?>" data-view="grid" aria-label="<?php esc_attr_e('Grid view', 'gloceps'); ?>">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1" />
                                <rect x="14" y="3" width="7" height="7" rx="1" />
                                <rect x="3" y="14" width="7" height="7" rx="1" />
                                <rect x="14" y="14" width="7" height="7" rx="1" />
                            </svg>
                        </button>
                        <button class="view-toggle__btn <?php echo $view_mode === 'list' ? 'view-toggle__btn--active' : ''; ?>" data-view="list" aria-label="<?php esc_attr_e('List view', 'gloceps'); ?>">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="publications-layout">
                <!-- Filters Sidebar -->
                <aside class="publications-filters reveal">
                    <div class="publications-filters__header">
                        <h3 class="publications-filters__title"><?php esc_html_e('Filters', 'gloceps'); ?></h3>
                        <a href="<?php echo esc_url(get_post_type_archive_link('publication')); ?>" class="publications-filters__clear" id="clearFilters"><?php esc_html_e('Clear All', 'gloceps'); ?></a>
                    </div>

                    <form method="get" action="<?php echo esc_url(get_post_type_archive_link('publication')); ?>" id="publicationsFiltersForm">
                        <?php if ($search_query) : ?>
                        <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">
                        <?php endif; ?>
                        <?php if ($sort_by) : ?>
                        <input type="hidden" name="sort" value="<?php echo esc_attr($sort_by); ?>">
                        <?php endif; ?>
                        <?php if ($view_mode) : ?>
                        <input type="hidden" name="view" value="<?php echo esc_attr($view_mode); ?>">
                        <?php endif; ?>

                        <!-- Filter: Publication Type -->
                        <div class="filter-group">
                            <h4 class="filter-group__title">
                                <?php esc_html_e('Publication Type', 'gloceps'); ?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h4>
                            <div class="filter-group__options">
                                <?php if ($publication_types && !is_wp_error($publication_types)) : ?>
                                    <?php foreach ($publication_types as $type) : 
                                        $count_args = array(
                                            'post_type' => 'publication',
                                            'tax_query' => array(array(
                                                'taxonomy' => 'publication_type',
                                                'field' => 'term_id',
                                                'terms' => $type->term_id,
                                            )),
                                        );
                                        $count = gloceps_get_publication_count($count_args);
                                    ?>
                                    <label class="filter-checkbox">
                                        <input type="checkbox" name="type[]" value="<?php echo esc_attr($type->slug); ?>" <?php checked(in_array($type->slug, $filter_type)); ?> />
                                        <span class="filter-checkbox__mark"></span>
                                        <span class="filter-checkbox__label"><?php echo esc_html($type->name); ?></span>
                                        <span class="filter-checkbox__count">(<?php echo esc_html($count); ?>)</span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Filter: Research Pillar -->
                        <div class="filter-group">
                            <h4 class="filter-group__title">
                                <?php esc_html_e('Research Pillar', 'gloceps'); ?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h4>
                            <div class="filter-group__options">
                                <?php if ($research_pillars && !is_wp_error($research_pillars)) : ?>
                                    <?php foreach ($research_pillars as $pillar) : 
                                        $count_args = array(
                                            'post_type' => 'publication',
                                            'tax_query' => array(array(
                                                'taxonomy' => 'research_pillar',
                                                'field' => 'term_id',
                                                'terms' => $pillar->term_id,
                                            )),
                                        );
                                        $count = gloceps_get_publication_count($count_args);
                                    ?>
                                    <label class="filter-checkbox">
                                        <input type="checkbox" name="pillar[]" value="<?php echo esc_attr($pillar->slug); ?>" <?php checked(in_array($pillar->slug, $filter_pillar)); ?> />
                                        <span class="filter-checkbox__mark"></span>
                                        <span class="filter-checkbox__label"><?php echo esc_html($pillar->name); ?></span>
                                        <span class="filter-checkbox__count">(<?php echo esc_html($count); ?>)</span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Filter: Access Type -->
                        <div class="filter-group">
                            <h4 class="filter-group__title">
                                <?php esc_html_e('Access Type', 'gloceps'); ?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h4>
                            <div class="filter-group__options">
                                <?php
                                $free_count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(array(
                                        'key' => 'access_type',
                                        'value' => 'free',
                                        'compare' => '=',
                                    )),
                                );
                                $free_count = gloceps_get_publication_count($free_count_args);
                                
                                $premium_count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(array(
                                        'key' => 'access_type',
                                        'value' => 'premium',
                                        'compare' => '=',
                                    )),
                                );
                                $premium_count = gloceps_get_publication_count($premium_count_args);
                                ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="access[]" value="free" <?php checked(in_array('free', $filter_access)); ?> />
                                    <span class="filter-checkbox__mark"></span>
                                    <span class="filter-checkbox__label"><?php esc_html_e('Free Access', 'gloceps'); ?></span>
                                    <span class="filter-checkbox__count">(<?php echo esc_html($free_count); ?>)</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="access[]" value="premium" <?php checked(in_array('premium', $filter_access)); ?> />
                                    <span class="filter-checkbox__mark"></span>
                                    <span class="filter-checkbox__label"><?php esc_html_e('Premium', 'gloceps'); ?></span>
                                    <span class="filter-checkbox__count">(<?php echo esc_html($premium_count); ?>)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Filter: Year -->
                        <div class="filter-group">
                            <h4 class="filter-group__title">
                                <?php esc_html_e('Year', 'gloceps'); ?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h4>
                            <div class="filter-group__options">
                                <?php foreach ($years as $year) : 
                                    $count_args = array(
                                        'post_type' => 'publication',
                                        'date_query' => array(array('year' => $year)),
                                    );
                                    $count = gloceps_get_publication_count($count_args);
                                ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="year[]" value="<?php echo esc_attr($year); ?>" <?php checked(in_array($year, $filter_year)); ?> />
                                    <span class="filter-checkbox__mark"></span>
                                    <span class="filter-checkbox__label"><?php echo esc_html($year); ?></span>
                                    <span class="filter-checkbox__count">(<?php echo esc_html($count); ?>)</span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Filter: Format -->
                        <div class="filter-group">
                            <h4 class="filter-group__title">
                                <?php esc_html_e('Format', 'gloceps'); ?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </h4>
                            <div class="filter-group__options">
                                <?php
                                $pdf_count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(array(
                                        'key' => 'publication_format',
                                        'value' => 'pdf',
                                        'compare' => '=',
                                    )),
                                );
                                $pdf_count = gloceps_get_publication_count($pdf_count_args);
                                
                                $article_count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(array(
                                        'key' => 'publication_format',
                                        'value' => 'article',
                                        'compare' => '=',
                                    )),
                                );
                                $article_count = gloceps_get_publication_count($article_count_args);
                                ?>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="format[]" value="pdf" <?php checked(in_array('pdf', $filter_format)); ?> />
                                    <span class="filter-checkbox__mark"></span>
                                    <span class="filter-checkbox__label"><?php esc_html_e('PDF Document', 'gloceps'); ?></span>
                                    <span class="filter-checkbox__count">(<?php echo esc_html($pdf_count); ?>)</span>
                                </label>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="format[]" value="article" <?php checked(in_array('article', $filter_format)); ?> />
                                    <span class="filter-checkbox__mark"></span>
                                    <span class="filter-checkbox__label"><?php esc_html_e('Online Article', 'gloceps'); ?></span>
                                    <span class="filter-checkbox__count">(<?php echo esc_html($article_count); ?>)</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="publications-filters__apply btn btn--primary btn--block">
                            <?php esc_html_e('Apply Filters', 'gloceps'); ?>
                        </button>
                    </form>
                </aside>

                <!-- Publications Content -->
                <div class="publications-content">
                    <div class="publications-results reveal">
                        <?php
                        $total = $publications_query->found_posts;
                        $start = ($paged - 1) * $publications_per_page + 1;
                        $end = min($paged * $publications_per_page, $total);
                        ?>
                        <p class="publications-results__count">
                            <?php
                            printf(
                                esc_html__('Showing %1$s-%2$s of %3$s publications', 'gloceps'),
                                '<strong>' . esc_html($start) . '</strong>',
                                '<strong>' . esc_html($end) . '</strong>',
                                '<strong>' . esc_html($total) . '</strong>'
                            );
                            ?>
                        </p>
                        <div class="publications-results__active-filters" id="activeFilters">
                            <!-- Active filter tags will be inserted here by JavaScript -->
                        </div>
                    </div>

                    <?php if ($publications_query->have_posts()) : ?>
                    <div class="publications-grid <?php echo $view_mode === 'list' ? 'publications-grid--list' : ''; ?> reveal stagger-children" id="publicationsGrid">
                        <?php
                        while ($publications_query->have_posts()) :
                            $publications_query->the_post();
                            get_template_part('template-parts/components/publication-card');
                        endwhile;
                        ?>
                    </div>

                    <?php
                    // Custom pagination
                    $total_pages = $publications_query->max_num_pages;
                    if ($total_pages > 1) :
                        $current_page = max(1, $paged);
                        
                        // Build base URL from archive permalink
                        $base_url = get_post_type_archive_link('publication');
                        
                        // Build query args array for all filters
                        $query_args = array();
                        
                        // Add search
                        if ($search_query) {
                            $query_args['s'] = $search_query;
                        }
                        
                        // Add sort
                        if ($sort_by && $sort_by !== 'newest') {
                            $query_args['sort'] = $sort_by;
                        }
                        
                        // Add view mode
                        if ($view_mode && $view_mode !== 'grid') {
                            $query_args['view'] = $view_mode;
                        }
                        
                        // Add filters
                        if (!empty($filter_type)) {
                            $query_args['type'] = $filter_type;
                        }
                        if (!empty($filter_pillar)) {
                            $query_args['pillar'] = $filter_pillar;
                        }
                        if (!empty($filter_access)) {
                            $query_args['access'] = $filter_access;
                        }
                        if (!empty($filter_year)) {
                            $query_args['year'] = $filter_year;
                        }
                        if (!empty($filter_format)) {
                            $query_args['format'] = $filter_format;
                        }
                        
                        // Helper function to build pagination URL with filters
                        $get_page_url = function($page_num) use ($base_url, $query_args) {
                            global $wp_rewrite;
                            
                            // Build URL based on permalink structure
                            if ($wp_rewrite->using_permalinks()) {
                                // Using pretty permalinks: /publications/page/2/
                                if ($page_num == 1) {
                                    $page_url = trailingslashit($base_url);
                                } else {
                                    $page_url = trailingslashit($base_url) . $wp_rewrite->pagination_base . '/' . $page_num . '/';
                                }
                            } else {
                                // Using query strings: /publications/?paged=2
                                if ($page_num == 1) {
                                    $page_url = $base_url;
                                } else {
                                    $page_url = add_query_arg('paged', $page_num, $base_url);
                                }
                            }
                            
                            // Add filter query args
                            if (!empty($query_args)) {
                                $page_url = add_query_arg($query_args, $page_url);
                            }
                            
                            return $page_url;
                        };
                    ?>
                    <div class="pagination">
                        <?php
                        // Previous page URL
                        if ($current_page > 1) {
                            $prev_url = $get_page_url($current_page - 1);
                        } else {
                            $prev_url = '#';
                        }
                        ?>
                        <a href="<?php echo esc_url($prev_url); ?>"
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
                                    $page_url = $get_page_url($i);
                            ?>
                                <a href="<?php echo esc_url($page_url); ?>"
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
                        <?php
                        // Next page URL
                        if ($current_page < $total_pages) {
                            $next_url = $get_page_url($current_page + 1);
                        } else {
                            $next_url = '#';
                        }
                        ?>
                        <a href="<?php echo esc_url($next_url); ?>"
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
                        <p><?php esc_html_e('No publications found. Try adjusting your filters.', 'gloceps'); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    // CTA Section Settings
    $cta_title = get_field('publications_cta_title', 'option') ?: __('Access Premium Research', 'gloceps');
    $cta_description = get_field('publications_cta_description', 'option') ?: __('Get exclusive access to in-depth policy papers, research reports, and conference proceedings. Support evidence-based policy making in Eastern Africa.', 'gloceps');
    $cta_bg_image = get_field('publications_cta_bg_image', 'option');
    $cta_primary_label = get_field('publications_cta_primary_label', 'option') ?: __('Browse Premium Publications', 'gloceps');
    $cta_primary_link = get_field('publications_cta_primary_link', 'option');
    $cta_show_secondary = get_field('publications_cta_show_secondary', 'option');
    $cta_secondary_label = get_field('publications_cta_secondary_label', 'option') ?: __('Get in Touch', 'gloceps');
    $cta_secondary_link = get_field('publications_cta_secondary_link', 'option');
    
    // Auto-generate primary link if not set (link to publications with premium filter)
    if (empty($cta_primary_link)) {
        $cta_primary_link = add_query_arg('access', 'premium', get_post_type_archive_link('publication'));
    }
    
    // Auto-generate secondary link if not set (link to contact page)
    if (empty($cta_secondary_link)) {
        $contact_page = get_page_by_path('contact');
        $cta_secondary_link = $contact_page ? get_permalink($contact_page) : '#contact';
    }
    
    // Default to showing secondary button if not set
    if ($cta_show_secondary === null) {
        $cta_show_secondary = true;
    }
    ?>
    
    <!-- CTA Section -->
    <section class="section publications-cta publications-cta--enhanced">
        <?php if ($cta_bg_image && !empty($cta_bg_image['url'])) : ?>
        <div class="publications-cta__bg">
            <img src="<?php echo esc_url($cta_bg_image['url']); ?>" alt="<?php echo esc_attr($cta_bg_image['alt'] ?: $cta_title); ?>" />
        </div>
        <div class="publications-cta__overlay"></div>
        <?php endif; ?>
        
        <div class="container">
            <div class="publications-cta__inner reveal">
                <div class="publications-cta__content">
                    <?php if ($cta_title) : ?>
                    <h2 class="publications-cta__title"><?php echo esc_html($cta_title); ?></h2>
                    <?php endif; ?>
                    
                    <?php if ($cta_description) : ?>
                    <p class="publications-cta__text"><?php echo esc_html($cta_description); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="publications-cta__actions">
                    <?php if ($cta_primary_label && $cta_primary_link) : ?>
                    <a href="<?php echo esc_url($cta_primary_link); ?>" class="btn btn--white btn--lg">
                        <?php echo esc_html($cta_primary_label); ?>
                        <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($cta_show_secondary && $cta_secondary_label && $cta_secondary_link) : ?>
                    <a href="<?php echo esc_url($cta_secondary_link); ?>" class="btn btn--outline-light btn--lg">
                        <?php echo esc_html($cta_secondary_label); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Publications Archive JavaScript
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Sort dropdown change
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('sort', this.value);
                url.searchParams.set('paged', '1'); // Reset to first page
                window.location.href = url.toString();
            });
        }
        
        // View toggle
        const viewToggleBtns = document.querySelectorAll('.view-toggle__btn');
        viewToggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                const url = new URL(window.location.href);
                url.searchParams.set('view', view);
                url.searchParams.set('paged', '1'); // Reset to first page
                window.location.href = url.toString();
            });
        });
        
        // Auto-submit filters on checkbox change (optional - can be removed if manual Apply button is preferred)
        // Uncomment if you want instant filtering:
        /*
        const filterCheckboxes = document.querySelectorAll('.filter-checkbox input[type="checkbox"]');
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('publicationsFiltersForm').submit();
            });
        });
        */
    });
})();
</script>

<?php
get_footer();
?>
