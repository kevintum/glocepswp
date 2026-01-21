<?php
/**
 * Archive template for Speeches
 * Similar to podcasts but with download functionality
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for speech archive
$speech_title = get_field('speech_intro_title', 'option') ?: 'Speeches';
$speech_description = get_field('speech_intro_description', 'option') ?: 'Access speeches and statements delivered by GLOCEPS leadership and experts on key policy issues.';

// Get current category filter from URL (if taxonomy exists in future)
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get all speech categories (if taxonomy exists in future)
$speech_categories = get_terms(array(
    'taxonomy' => 'speech_category',
    'hide_empty' => true,
    'orderby' => 'term_id',
));
?>

<main>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header__content">
                <?php gloceps_breadcrumbs(); ?>
                <h1 class="page-header__title"><?php echo esc_html($speech_title); ?></h1>
                <?php if ($speech_description) : ?>
                    <p class="page-header__description">
                        <?php echo esc_html($speech_description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Speech Category Filters (if taxonomy exists) -->
    <?php if ($speech_categories && !is_wp_error($speech_categories) && count($speech_categories) > 0) : ?>
        <section class="section section--filters">
            <div class="container">
                <div class="events-tabs">
                    <div class="events-tabs__wrapper">
                        <a href="<?php echo esc_url(remove_query_arg('category')); ?>" 
                           class="events-tab <?php echo $current_category === 'all' ? 'events-tab--active' : ''; ?>">
                            <?php esc_html_e('All Speeches', 'gloceps'); ?>
                        </a>
                        <?php foreach ($speech_categories as $category) : ?>
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

    <!-- Speech Grid -->
    <section class="section">
        <div class="container">
            <?php
            // Build query args based on category filter
            $query_args = array(
                'post_type' => 'speech',
                'posts_per_page' => 9,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            // Add category filter if not "all" and taxonomy exists
            if ($current_category !== 'all' && $speech_categories && !is_wp_error($speech_categories)) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'speech_category',
                        'field' => 'slug',
                        'terms' => $current_category,
                    ),
                );
            }

            $speeches_query = new WP_Query($query_args);

            if ($speeches_query->have_posts()) :
            ?>
                <div class="speech-grid">
                    <?php while ($speeches_query->have_posts()) : $speeches_query->the_post(); ?>
                        <?php get_template_part('template-parts/components/speech-card'); ?>
                    <?php endwhile; ?>
                </div>

                <?php
                // Custom pagination matching static HTML
                $total_pages = $speeches_query->max_num_pages;
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
                    <p><?php esc_html_e('No speeches found. Try adjusting your filters.', 'gloceps'); ?></p>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>
</main>

<?php
get_footer();
?>
