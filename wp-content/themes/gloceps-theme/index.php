<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();
?>

<main>
    <?php if ( have_posts() ) : ?>
        
        <section class="section">
            <div class="container">
                <div class="section-header reveal">
                    <h1 class="section-header__title">
                        <?php
                        if ( is_home() && ! is_front_page() ) {
                            single_post_title();
                        } elseif ( is_archive() ) {
                            the_archive_title();
                        } elseif ( is_search() ) {
                            printf( esc_html__( 'Search Results for: %s', 'gloceps' ), '<span>' . get_search_query() . '</span>' );
                        } else {
                            esc_html_e( 'Latest Posts', 'gloceps' );
                        }
                        ?>
                    </h1>
                    <?php the_archive_description( '<p class="section-header__description">', '</p>' ); ?>
                </div>

                <div class="publications-grid reveal stagger-children">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/components/post-card' );
                    endwhile;
                    ?>
                </div>

                <?php gloceps_pagination(); ?>
            </div>
        </section>

    <?php else : ?>

        <section class="section">
            <div class="container">
                <div class="section-header section-header--center">
                    <h1 class="section-header__title"><?php esc_html_e( 'Nothing Found', 'gloceps' ); ?></h1>
                    <p class="section-header__description">
                        <?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'gloceps' ); ?>
                    </p>
                </div>
                <?php get_search_form(); ?>
            </div>
        </section>

    <?php endif; ?>
</main>

<?php
get_footer();

