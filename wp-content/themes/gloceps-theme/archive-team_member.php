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

// Get current category filter from URL
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';

// Get all team categories
$team_categories = get_terms(array(
    'taxonomy' => 'team_category',
    'hide_empty' => true,
    'orderby' => 'term_id',
));
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
                    <a href="<?php echo esc_url(remove_query_arg('category')); ?>" 
                       class="team-filter <?php echo $current_category === 'all' ? 'team-filter--active' : ''; ?>">
                        <?php esc_html_e('All', 'gloceps'); ?>
                    </a>
                    <?php foreach ($team_categories as $category) : ?>
                        <a href="<?php echo esc_url(add_query_arg('category', $category->slug)); ?>" 
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
        'posts_per_page' => -1,
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
        // Group by category for display
        $members_by_category = array();
        while ($team_query->have_posts()) :
            $team_query->the_post();
            $member_categories = get_the_terms(get_the_ID(), 'team_category');
            if ($member_categories && !is_wp_error($member_categories)) {
                foreach ($member_categories as $cat) {
                    if (!isset($members_by_category[$cat->slug])) {
                        $members_by_category[$cat->slug] = array();
                    }
                    $members_by_category[$cat->slug][] = get_the_ID();
                }
            } else {
                // Members without category
                if (!isset($members_by_category['uncategorized'])) {
                    $members_by_category['uncategorized'] = array();
                }
                $members_by_category['uncategorized'][] = get_the_ID();
            }
        endwhile;
        wp_reset_postdata();

        // Display by category if showing all, or just the filtered category
        if ($current_category === 'all') {
            foreach ($team_categories as $category) :
                if (isset($members_by_category[$category->slug]) && !empty($members_by_category[$category->slug])) :
                    $members = $members_by_category[$category->slug];
        ?>
                    <section class="section <?php echo $category->slug === 'founding-council-members' ? '' : 'section--gray'; ?>">
                        <div class="container">
                            <div class="section-header section-header--center reveal">
                                <h2 class="section-header__title"><?php echo esc_html($category->name); ?></h2>
                                <?php if ($category->description) : ?>
                                    <p class="section-header__description"><?php echo esc_html($category->description); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="team-grid team-grid--<?php echo esc_attr(min(5, count($members))); ?> reveal stagger-children">
                                <?php
                                foreach ($members as $member_id) :
                                    $post = get_post($member_id);
                                    setup_postdata($post);
                                    get_template_part('template-parts/components/team-card');
                                endforeach;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    </section>
        <?php
                endif;
            endforeach;
        } else {
            // Show filtered category only
            $category_obj = get_term_by('slug', $current_category, 'team_category');
            if ($category_obj && isset($members_by_category[$current_category])) :
                $members = $members_by_category[$current_category];
        ?>
                <section class="section <?php echo $current_category === 'founding-council-members' ? '' : 'section--gray'; ?>">
                    <div class="container">
                        <div class="section-header section-header--center reveal">
                            <h2 class="section-header__title"><?php echo esc_html($category_obj->name); ?></h2>
                            <?php if ($category_obj->description) : ?>
                                <p class="section-header__description"><?php echo esc_html($category_obj->description); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="team-grid team-grid--<?php echo esc_attr(min(5, count($members))); ?> reveal stagger-children">
                            <?php
                            foreach ($members as $member_id) :
                                $post = get_post($member_id);
                                setup_postdata($post);
                                get_template_part('template-parts/components/team-card');
                            endforeach;
                            wp_reset_postdata();
                            ?>
                        </div>
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
