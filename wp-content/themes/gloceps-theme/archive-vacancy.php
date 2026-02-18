<?php
/**
 * Archive template for Jobs/Vacancies
 * 
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for vacancies archive
$vacancies_title = get_field('vacancy_intro_title', 'option') ?: 'Career Opportunities';
$vacancies_description = get_field('vacancy_intro_description', 'option') ?: 'Join our team and contribute to advancing policy research and strategic dialogue.';
$items_per_page = get_field('vacancy_items_per_page', 'option') ?: 12;

// Modify main query
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
query_posts(array(
    'post_type' => 'vacancy',
    'posts_per_page' => $items_per_page,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<!-- Page Header -->
<?php
$header_attrs = gloceps_get_page_header_attrs(false);
?>
<section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title"><?php echo esc_html($vacancies_title); ?></h1>
            <?php if ($vacancies_description) : ?>
                <p class="page-header__description">
                    <?php echo esc_html($vacancies_description); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<main>
    <section class="section">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="vacancies-listing">
                    <div class="vacancies-listing__header">
                        <h2 class="vacancies-listing__title"><?php esc_html_e('Open Positions', 'gloceps'); ?></h2>
                        <span class="vacancies-listing__count">
                            <?php 
                            global $wp_query;
                            printf(
                                esc_html__('%d Jobs', 'gloceps'),
                                $wp_query->found_posts
                            );
                            ?>
                        </span>
                    </div>
                    <div class="vacancies-list">
                        <?php
                        while (have_posts()) :
                            the_post();
                            get_template_part('template-parts/components/job-listing-item');
                        endwhile;
                        ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php
                    global $wp_query;
                    $pagination_args = array(
                        'total' => $wp_query->max_num_pages,
                        'current' => max(1, $paged),
                        'prev_text' => '‹ Previous',
                        'next_text' => 'Next ›',
                    );
                    ?>
                    <?php if ($wp_query->max_num_pages > 1) : ?>
                        <nav class="pagination">
                            <?php echo paginate_links($pagination_args); ?>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="vacancies-empty">
                    <div class="vacancies-empty__icon">
                        <svg width="80" height="80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .414-.336.75-.75.75h-4.5a.75.75 0 01-.75-.75v-4.25m0 0h4.5m-4.5 0l-4.5-4.5m4.5 4.5l4.5-4.5M3.75 9.75h13.5m-13.5 0a3 3 0 00-3 3v4.5a3 3 0 003 3h13.5a3 3 0 003-3v-4.5a3 3 0 00-3-3H3.75z" />
                        </svg>
                    </div>
                    <h2 class="vacancies-empty__title"><?php esc_html_e('No Open Positions', 'gloceps'); ?></h2>
                    <p class="vacancies-empty__message">
                        <?php esc_html_e('We don\'t have any job openings at the moment, but we\'re always looking for talented individuals to join our team.', 'gloceps'); ?>
                    </p>
                    <p class="vacancies-empty__submessage">
                        <?php esc_html_e('Check back soon or follow us on social media to stay updated on new opportunities.', 'gloceps'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
wp_reset_query();
get_footer();
