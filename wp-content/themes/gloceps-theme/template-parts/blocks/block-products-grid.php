<?php
/**
 * Flexible Content Block: Products Grid (Purchase Page)
 * Uses same design as publications archive but only shows premium publications
 *
 * @package GLOCEPS
 */

// Ensure template functions are loaded and function is available
if (!function_exists('gloceps_get_publication_count')) {
    // Try to load from template functions file
    $template_functions_file = get_template_directory() . '/inc/template-functions.php';
    if (file_exists($template_functions_file)) {
        require_once $template_functions_file;
    }
    
    // If still not available, define it here as fallback
    if (!function_exists('gloceps_get_publication_count')) {
        function gloceps_get_publication_count($args = array()) {
            if (!class_exists('WP_Query')) {
                return 0;
            }
            $count_query = new WP_Query(array_merge($args, array('posts_per_page' => -1, 'fields' => 'ids')));
            $count = $count_query->found_posts;
            wp_reset_postdata();
            return $count;
        }
    }
}

$per_page = get_sub_field('per_page') ?: 12;

// Get filter parameters from URL
$filter_type = isset($_GET['type']) ? array_map('sanitize_text_field', (array)$_GET['type']) : array();
$filter_pillar = isset($_GET['pillar']) ? array_map('sanitize_text_field', (array)$_GET['pillar']) : array();
$filter_year = isset($_GET['year']) ? array_map('sanitize_text_field', (array)$_GET['year']) : array();
$filter_format = isset($_GET['format']) ? array_map('sanitize_text_field', (array)$_GET['format']) : array();
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$sort_by = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'newest';
$view_mode = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'grid';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get all taxonomies for filters (only for premium publications)
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

// Get unique years from premium publications only
// Use same logic as publications archive
$years_query = new WP_Query(array(
    'post_type' => 'publication',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_query' => array(
        array(
            'key' => 'access_type',
            'value' => 'premium',
            'compare' => '=',
        ),
    ),
));
$years = array();
if ($years_query->have_posts()) {
    foreach ($years_query->posts as $post_id) {
        // Use WordPress post date (same as publications archive)
        $year = get_the_date('Y', $post_id);
        if ($year && !in_array($year, $years)) {
            $years[] = $year;
        }
    }
}
rsort($years);
wp_reset_postdata();

// Build query args - ONLY PREMIUM PUBLICATIONS
$query_args = array(
    'post_type' => 'publication',
    'posts_per_page' => $per_page,
    'paged' => $paged,
    'meta_query' => array(
        array(
            'key' => 'access_type',
            'value' => 'premium',
            'compare' => '=',
        ),
    ),
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

// Year filter - use date_query to match post date (WordPress post date is source of truth)
// This ensures consistency with publication cards which use get_the_date()
if (!empty($filter_year)) {
    $query_args['date_query'] = array(
        array(
            'year' => $filter_year,
            'compare' => 'IN',
        ),
    );
}

// Format filter (meta query)
if (!empty($filter_format)) {
    $format_meta_query = array('relation' => 'OR');
    foreach ($filter_format as $format) {
        $format_meta_query[] = array(
            'key' => 'publication_format',
            'value' => $format,
            'compare' => '=',
        );
    }
    $query_args['meta_query'][] = $format_meta_query;
}

$publications_query = new WP_Query($query_args);
$total_posts = $publications_query->found_posts;
$total_pages = $publications_query->max_num_pages;
?>

<section class="section section--compact" style="background: var(--color-gray-50);">
    <div class="container">
        <!-- Toolbar: Search, Sort, View Toggle -->
        <div class="publications-toolbar reveal">
            <div class="publications-search">
                <svg class="publications-search__icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="M21 21l-4.35-4.35" />
                </svg>
                <form method="get" action="<?php echo esc_url(get_permalink()); ?>" class="publications-search__form">
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
                    <button type="button" class="view-toggle__btn <?php echo $view_mode === 'grid' ? 'view-toggle__btn--active' : ''; ?>" data-view="grid" aria-label="<?php esc_attr_e('Grid view', 'gloceps'); ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                    </button>
                    <button type="button" class="view-toggle__btn <?php echo $view_mode === 'list' ? 'view-toggle__btn--active' : ''; ?>" data-view="list" aria-label="<?php esc_attr_e('List view', 'gloceps'); ?>">
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
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="publications-filters__clear" id="clearFilters"><?php esc_html_e('Clear All', 'gloceps'); ?></a>
                </div>

                <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="publicationsFiltersForm">
                    <?php if ($search_query) : ?>
                        <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">
                    <?php endif; ?>
                    <?php if ($sort_by) : ?>
                        <input type="hidden" name="sort" value="<?php echo esc_attr($sort_by); ?>">
                    <?php endif; ?>
                    <?php if ($view_mode) : ?>
                        <input type="hidden" name="view" value="<?php echo esc_attr($view_mode); ?>">
                    <?php endif; ?>

                    <!-- Active Filters Display -->
                    <div class="publications-filters__active" id="activeFilters">
                        <?php
                        $active_filters = array();
                        if (!empty($filter_type)) {
                            foreach ($filter_type as $type) {
                                $term = get_term_by('slug', $type, 'publication_type');
                                if ($term) {
                                    $active_filters[] = array('key' => 'type', 'value' => $type, 'label' => $term->name);
                                }
                            }
                        }
                        if (!empty($filter_pillar)) {
                            foreach ($filter_pillar as $pillar) {
                                $term = get_term_by('slug', $pillar, 'research_pillar');
                                if ($term) {
                                    $active_filters[] = array('key' => 'pillar', 'value' => $pillar, 'label' => $term->name);
                                }
                            }
                        }
                        if (!empty($filter_year)) {
                            foreach ($filter_year as $year) {
                                $active_filters[] = array('key' => 'year', 'value' => $year, 'label' => $year);
                            }
                        }
                        if (!empty($filter_format)) {
                            foreach ($filter_format as $format) {
                                $format_label = $format === 'pdf' ? 'PDF Document' : 'Online Article';
                                $active_filters[] = array('key' => 'format', 'value' => $format, 'label' => $format_label);
                            }
                        }
                        if ($search_query) {
                            $active_filters[] = array('key' => 's', 'value' => $search_query, 'label' => 'Search: "' . $search_query . '"');
                        }
                        ?>
                        <?php if (!empty($active_filters)) : ?>
                            <?php foreach ($active_filters as $filter) : ?>
                                <span class="active-filter-tag">
                                    <?php echo esc_html($filter['label']); ?>
                                    <button type="button" data-filter-key="<?php echo esc_attr($filter['key']); ?>" data-filter-value="<?php echo esc_attr($filter['value']); ?>">&times;</button>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Publication Type Filter -->
                    <?php if ($publication_types && !is_wp_error($publication_types)) : ?>
                    <div class="filter-group">
                        <h4 class="filter-group__title">
                            <?php esc_html_e('Publication Type', 'gloceps'); ?>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </h4>
                        <div class="filter-group__options">
                            <?php foreach ($publication_types as $type) : 
                                // Count only premium publications for this type
                                $count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(array(
                                        'key' => 'access_type',
                                        'value' => 'premium',
                                        'compare' => '=',
                                    )),
                                    'tax_query' => array(array(
                                        'taxonomy' => 'publication_type',
                                        'field' => 'term_id',
                                        'terms' => $type->term_id,
                                    )),
                                );
                                $count = function_exists('gloceps_get_publication_count') ? gloceps_get_publication_count($count_args) : 0;
                                if ($count == 0) continue;
                            ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="type[]" value="<?php echo esc_attr($type->slug); ?>" <?php checked(in_array($type->slug, $filter_type)); ?> />
                                <span class="filter-checkbox__mark"></span>
                                <span class="filter-checkbox__label"><?php echo esc_html($type->name); ?></span>
                                <span class="filter-checkbox__count">(<?php echo esc_html($count); ?>)</span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Research Pillar Filter -->
                    <?php if ($research_pillars && !is_wp_error($research_pillars)) : ?>
                    <div class="filter-group">
                        <h4 class="filter-group__title">
                            <?php esc_html_e('Research Pillar', 'gloceps'); ?>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </h4>
                        <div class="filter-group__options">
                            <?php foreach ($research_pillars as $pillar) : 
                                // Count only premium publications for this pillar
                                $count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(array(
                                        'key' => 'access_type',
                                        'value' => 'premium',
                                        'compare' => '=',
                                    )),
                                    'tax_query' => array(array(
                                        'taxonomy' => 'research_pillar',
                                        'field' => 'term_id',
                                        'terms' => $pillar->term_id,
                                    )),
                                );
                                $count = function_exists('gloceps_get_publication_count') ? gloceps_get_publication_count($count_args) : 0;
                                if ($count == 0) continue;
                            ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="pillar[]" value="<?php echo esc_attr($pillar->slug); ?>" <?php checked(in_array($pillar->slug, $filter_pillar)); ?> />
                                <span class="filter-checkbox__mark"></span>
                                <span class="filter-checkbox__label"><?php echo esc_html($pillar->name); ?></span>
                                <span class="filter-checkbox__count">(<?php echo esc_html($count); ?>)</span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Year Filter -->
                    <?php if (!empty($years)) : ?>
                    <div class="filter-group">
                        <h4 class="filter-group__title">
                            <?php esc_html_e('Year', 'gloceps'); ?>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </h4>
                        <div class="filter-group__options">
                            <?php foreach ($years as $year) : 
                                // Count only premium publications for this year
                                // Use date_query to match post date (same as publications archive)
                                $count_args = array(
                                    'post_type' => 'publication',
                                    'meta_query' => array(
                                        array(
                                            'key' => 'access_type',
                                            'value' => 'premium',
                                            'compare' => '=',
                                        ),
                                    ),
                                    'date_query' => array(
                                        array(
                                            'year' => $year,
                                        ),
                                    ),
                                );
                                $count = function_exists('gloceps_get_publication_count') ? gloceps_get_publication_count($count_args) : 0;
                                if ($count == 0) continue;
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
                    <?php endif; ?>

                    <!-- Format Filter -->
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
                                'meta_query' => array(
                                    array(
                                        'key' => 'access_type',
                                        'value' => 'premium',
                                        'compare' => '=',
                                    ),
                                    array(
                                        'key' => 'publication_format',
                                        'value' => 'pdf',
                                        'compare' => '=',
                                    ),
                                ),
                            );
                            $pdf_count = function_exists('gloceps_get_publication_count') ? gloceps_get_publication_count($pdf_count_args) : 0;
                            
                            $article_count_args = array(
                                'post_type' => 'publication',
                                'meta_query' => array(
                                    array(
                                        'key' => 'access_type',
                                        'value' => 'premium',
                                        'compare' => '=',
                                    ),
                                    array(
                                        'key' => 'publication_format',
                                        'value' => 'article',
                                        'compare' => '=',
                                    ),
                                ),
                            );
                            $article_count = function_exists('gloceps_get_publication_count') ? gloceps_get_publication_count($article_count_args) : 0;
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

            <!-- Main Content Area -->
            <div class="publications-main">
                <div class="publications-results">
                    <span class="publications-results__count">
                        <?php
                        $start = ($paged - 1) * $per_page + 1;
                        $end = min($paged * $per_page, $total_posts);
                        printf(
                            esc_html__('Showing %1$d-%2$d of %3$d publications', 'gloceps'),
                            $start,
                            $end,
                            $total_posts
                        );
                        ?>
                    </span>
                </div>

                <?php if ($publications_query->have_posts()) : ?>
                <div id="publicationsGrid" class="publications-grid <?php echo $view_mode === 'list' ? 'publications-grid--list' : ''; ?>">
                    <?php while ($publications_query->have_posts()) : $publications_query->the_post(); ?>
                        <?php get_template_part('template-parts/components/publication-card'); ?>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>

                <?php if ($total_pages > 1) : ?>
                    <?php
                    $base_url = get_permalink();
                    $base_url = add_query_arg('s', $search_query, $base_url);
                    $base_url = add_query_arg('sort', $sort_by, $base_url);
                    $base_url = add_query_arg('view', $view_mode, $base_url);
                    if (!empty($filter_type)) {
                        $base_url = add_query_arg('type', implode(',', $filter_type), $base_url);
                    }
                    if (!empty($filter_pillar)) {
                        $base_url = add_query_arg('pillar', implode(',', $filter_pillar), $base_url);
                    }
                    if (!empty($filter_year)) {
                        $base_url = add_query_arg('year', implode(',', $filter_year), $base_url);
                    }
                    if (!empty($filter_format)) {
                        $base_url = add_query_arg('format', implode(',', $filter_format), $base_url);
                    }
                    $current_page = $paged;
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
                    <p><?php esc_html_e('No premium publications found. Try adjusting your filters.', 'gloceps'); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Products Grid JavaScript (same as publications archive)
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
        
        // Clear filters button
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(window.location.href);
                // Remove all filter parameters
                url.searchParams.delete('type');
                url.searchParams.delete('pillar');
                url.searchParams.delete('year');
                url.searchParams.delete('format');
                url.searchParams.delete('s');
                url.searchParams.set('paged', '1');
                window.location.href = url.toString();
            });
        }
        
        // Active filter tag removal
        const activeFiltersContainer = document.getElementById('activeFilters');
        if (activeFiltersContainer) {
            activeFiltersContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON') {
                    const key = e.target.dataset.filterKey;
                    const valueToRemove = e.target.dataset.filterValue;
                    const url = new URL(window.location.href);
                    
                    if (key === 's') {
                        url.searchParams.delete('s');
                    } else {
                        const currentValues = url.searchParams.get(key);
                        if (currentValues) {
                            const valuesArray = currentValues.split(',');
                            const newValues = valuesArray.filter(v => v !== valueToRemove).join(',');
                            if (newValues) {
                                url.searchParams.set(key, newValues);
                            } else {
                                url.searchParams.delete(key);
                            }
                        }
                    }
                    url.searchParams.set('paged', '1');
                    window.location.href = url.toString();
                }
            });
        }
    });
})();
</script>

