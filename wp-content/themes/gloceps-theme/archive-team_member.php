<?php
/**
 * Archive template for Team Members
 * 
 * Matches team.html structure exactly
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for team archive
$team_title = get_field('team_intro_title', 'option') ?: 'Our People';
$team_description = get_field('team_intro_description', 'option') ?: 'Meet the distinguished professionals, thought leaders, and experts driving GLOCEPS\' mission to advance policy research and strategic dialogue.';
$items_per_page_raw = get_field('team_items_per_page', 'option');
$items_per_page = $items_per_page_raw ? intval($items_per_page_raw) : 12;

// #region agent log
error_log('TEAM ARCHIVE DEBUG: items_per_page_raw=' . var_export($items_per_page_raw, true) . ', items_per_page=' . $items_per_page);
// #endregion

// Get current category filter from URL
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';

// Get current page for pagination
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

// Get all team categories ordered by frontend_order
$team_categories = get_terms(array(
    'taxonomy' => 'team_category',
    'hide_empty' => true,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'frontend_order',
            'compare' => 'EXISTS',
        ),
        array(
            'key' => 'frontend_order',
            'compare' => 'NOT EXISTS',
        ),
    ),
    'orderby' => 'meta_value_num',
    'meta_key' => 'frontend_order',
    'order' => 'ASC',
));

// Secondary sort by name for categories with same order
if ($team_categories && !is_wp_error($team_categories)) {
    usort($team_categories, function($a, $b) {
        $order_a = get_term_meta($a->term_id, 'frontend_order', true) ?: 999;
        $order_b = get_term_meta($b->term_id, 'frontend_order', true) ?: 999;
        if ($order_a == $order_b) {
            return strcmp($a->name, $b->name);
        }
        return $order_a - $order_b;
    });
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title"><?php echo esc_html($team_title); ?></h1>
            <?php if ($team_description) : ?>
                <p class="page-header__description">
                    <?php echo esc_html($team_description); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<main>
    <!-- Team Category Filters -->
    <?php if ($team_categories && !is_wp_error($team_categories)) : ?>
        <div class="team-filters">
            <div class="container">
                <div class="team-filters__wrapper">
                    <a href="<?php echo esc_url(remove_query_arg(array('category', 'paged'))); ?>" 
                       class="team-filter <?php echo $current_category === 'all' ? 'team-filter--active' : ''; ?>">
                        <?php esc_html_e('All', 'gloceps'); ?>
                    </a>
                    <?php foreach ($team_categories as $category) : ?>
                        <a href="<?php echo esc_url(add_query_arg('category', $category->slug, remove_query_arg('paged'))); ?>" 
                           class="team-filter <?php echo $current_category === $category->slug ? 'team-filter--active' : ''; ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Team Members Grid -->
    <?php
    // Build query args based on category filter
    $query_args = array(
        'post_type' => 'team_member',
        'posts_per_page' => -1, // Get all for grouping
        'orderby' => 'meta_value_num',
        'meta_key' => 'display_order',
        'order' => 'ASC',
    );

    // Add category filter if not "all"
    if ($current_category !== 'all') {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'team_category',
                'field' => 'slug',
                'terms' => $current_category,
            ),
        );
    }

    $team_query = new WP_Query($query_args);

    if ($team_query->have_posts()) :
        // Group by category for display (only needed for "all" view)
        $members_by_category = array();
        $all_members_direct = array(); // For filtered category view
        
        while ($team_query->have_posts()) :
            $team_query->the_post();
            $member_id = get_the_ID();
            
            if ($current_category === 'all') {
                // Group by category for "all" view
                $member_categories = get_the_terms($member_id, 'team_category');
                if ($member_categories && !is_wp_error($member_categories)) {
                    foreach ($member_categories as $cat) {
                        if (!isset($members_by_category[$cat->slug])) {
                            $members_by_category[$cat->slug] = array();
                        }
                        // Only add if not already in array (prevent duplicates from multiple categories)
                        if (!in_array($member_id, $members_by_category[$cat->slug], true)) {
                            $members_by_category[$cat->slug][] = $member_id;
                        }
                    }
                } else {
                    // Members without category
                    if (!isset($members_by_category['uncategorized'])) {
                        $members_by_category['uncategorized'] = array();
                    }
                    if (!in_array($member_id, $members_by_category['uncategorized'], true)) {
                        $members_by_category['uncategorized'][] = $member_id;
                    }
                }
            } else {
                // For filtered view, collect directly (no grouping needed)
                if (!in_array($member_id, $all_members_direct, true)) {
                    $all_members_direct[] = $member_id;
                }
            }
        endwhile;
        wp_reset_postdata();

        // Display by category if showing all, or just the filtered category
        if ($current_category === 'all') {
            foreach ($team_categories as $category) :
                if (isset($members_by_category[$category->slug]) && !empty($members_by_category[$category->slug])) :
                    $all_members = $members_by_category[$category->slug];
                    $total_members = count($all_members);
                    $total_pages = ceil($total_members / $items_per_page);
                    $current_page = 1; // Always start at page 1 for each category when showing all
                    $offset = 0;
                    $members = array_slice($all_members, $offset, $items_per_page);
                    
                    // #region agent log
                    error_log('TEAM ARCHIVE DEBUG: category=' . $category->slug . ', total_members=' . $total_members . ', items_per_page=' . $items_per_page . ', total_pages=' . $total_pages . ', members_count=' . count($members));
                    // #endregion
        ?>
                    <section class="section team-category-section <?php echo $category->slug === 'founding-council-members' ? '' : 'section--gray'; ?>" 
                             data-category="<?php echo esc_attr($category->slug); ?>"
                             data-total="<?php echo esc_attr($total_members); ?>"
                             data-per-page="<?php echo esc_attr($items_per_page); ?>">
                        <div class="container">
                            <div class="section-header section-header--center reveal">
                                <h2 class="section-header__title"><?php echo esc_html($category->name); ?></h2>
                                <?php if ($category->description) : ?>
                                    <p class="section-header__description"><?php echo esc_html($category->description); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="team-grid team-grid--<?php echo esc_attr(min(5, count($members))); ?> reveal stagger-children" data-page="1">
                                <?php
                                foreach ($members as $member_id) :
                                    $post = get_post($member_id);
                                    setup_postdata($post);
                                    get_template_part('template-parts/components/team-card');
                                endforeach;
                                wp_reset_postdata();
                                ?>
                            </div>

                            <?php if ($total_pages > 1) : ?>
                                <div class="team-pagination" data-category="<?php echo esc_attr($category->slug); ?>">
                                    <nav class="pagination" aria-label="<?php esc_attr_e('Team Members Pagination', 'gloceps'); ?>">
                                        <?php
                                        for ($i = 1; $i <= $total_pages; $i++) :
                                            $is_active = $i === 1;
                                        ?>
                                            <button class="pagination__link <?php echo $is_active ? 'pagination__link--active' : ''; ?>" 
                                                    data-page="<?php echo esc_attr($i); ?>"
                                                    data-category="<?php echo esc_attr($category->slug); ?>"
                                                    <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
                                                <?php echo esc_html($i); ?>
                                            </button>
                                        <?php endfor; ?>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
        <?php
                endif;
            endforeach;
        } else {
            // Show filtered category only with pagination
            $category_obj = get_term_by('slug', $current_category, 'team_category');
            if ($category_obj && !empty($all_members_direct)) :
                // Sort by display_order meta if available
                usort($all_members_direct, function($a, $b) {
                    $order_a = get_post_meta($a, 'display_order', true) ?: 999;
                    $order_b = get_post_meta($b, 'display_order', true) ?: 999;
                    return intval($order_a) - intval($order_b);
                });
                
                $total_members = count($all_members_direct);
                $total_pages = ceil($total_members / $items_per_page);
                $offset = ($paged - 1) * $items_per_page;
                $members = array_slice($all_members_direct, $offset, $items_per_page);
                
                // #region agent log
                error_log('TEAM ARCHIVE FILTERED DEBUG: category=' . $current_category . ', page=' . $paged . ', total_members=' . $total_members . ', items_per_page=' . $items_per_page . ', offset=' . $offset . ', members_count=' . count($members) . ', member_ids=' . implode(',', $members));
                // #endregion
        ?>
                <section class="section team-category-section <?php echo $current_category === 'founding-council-members' ? '' : 'section--gray'; ?>" 
                         data-category="<?php echo esc_attr($current_category); ?>"
                         data-total="<?php echo esc_attr($total_members); ?>"
                         data-per-page="<?php echo esc_attr($items_per_page); ?>">
                    <div class="container">
                        <div class="section-header section-header--center reveal">
                            <h2 class="section-header__title"><?php echo esc_html($category_obj->name); ?></h2>
                            <?php if ($category_obj->description) : ?>
                                <p class="section-header__description"><?php echo esc_html($category_obj->description); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="team-grid team-grid--<?php echo esc_attr(min(5, count($members))); ?> reveal stagger-children" data-page="<?php echo esc_attr($paged); ?>">
                            <?php
                            foreach ($members as $member_id) :
                                $post = get_post($member_id);
                                setup_postdata($post);
                                get_template_part('template-parts/components/team-card');
                            endforeach;
                            wp_reset_postdata();
                            ?>
                        </div>

                        <?php if ($total_pages > 1) : ?>
                            <div class="team-pagination" data-category="<?php echo esc_attr($current_category); ?>">
                                <nav class="pagination" aria-label="<?php esc_attr_e('Team Members Pagination', 'gloceps'); ?>">
                                    <?php if ($paged > 1) : ?>
                                        <a href="<?php echo esc_url(add_query_arg('paged', $paged - 1)); ?>" 
                                           class="pagination__link pagination__link--prev"
                                           data-page="<?php echo esc_attr($paged - 1); ?>"
                                           data-category="<?php echo esc_attr($current_category); ?>">
                                            <?php esc_html_e('Previous', 'gloceps'); ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php
                                    for ($i = 1; $i <= $total_pages; $i++) :
                                        $is_active = $i === $paged;
                                    ?>
                                        <a href="<?php echo esc_url(add_query_arg('paged', $i)); ?>" 
                                           class="pagination__link <?php echo $is_active ? 'pagination__link--active' : ''; ?>" 
                                           data-page="<?php echo esc_attr($i); ?>"
                                           data-category="<?php echo esc_attr($current_category); ?>"
                                           <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
                                            <?php echo esc_html($i); ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($paged < $total_pages) : ?>
                                        <a href="<?php echo esc_url(add_query_arg('paged', $paged + 1)); ?>" 
                                           class="pagination__link pagination__link--next"
                                           data-page="<?php echo esc_attr($paged + 1); ?>"
                                           data-category="<?php echo esc_attr($current_category); ?>">
                                            <?php esc_html_e('Next', 'gloceps'); ?>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
        <?php
            endif;
        }
    else :
    ?>
        <section class="section">
            <div class="container">
                <div class="no-results">
                    <p><?php esc_html_e('No team members found.', 'gloceps'); ?></p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php
get_footer();
