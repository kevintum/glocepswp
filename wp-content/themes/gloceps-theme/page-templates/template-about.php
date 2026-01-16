<?php
/**
 * Template Name: About Page
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while ( have_posts() ) :
    the_post();
?>

<main>
    <?php
    // Render flexible content blocks
    if (function_exists('gloceps_render_blocks')) {
        gloceps_render_blocks();
    } else {
        // Fallback: render blocks manually
        if (function_exists('have_rows') && have_rows('content_blocks')) {
            while (have_rows('content_blocks')) : the_row();
                $layout = get_row_layout();
                $block_file = get_template_directory() . '/template-parts/blocks/block-' . str_replace('_', '-', $layout) . '.php';
                
                if (file_exists($block_file)) {
                    include $block_file;
                }
            endwhile;
        } else {
            // Fallback content if no blocks are set
            ?>
            <section class="page-header">
                <div class="container">
                    <div class="page-header__content">
                        <?php gloceps_breadcrumbs(); ?>
                        <h1 class="page-header__title"><?php the_title(); ?></h1>
                        <p class="page-header__description">
                            <?php echo has_excerpt() ? wp_kses_post( get_the_excerpt() ) : esc_html__( 'A leading centre of excellence in policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.', 'gloceps' ); ?>
                        </p>
                    </div>
                </div>
            </section>
            <?php
        }
    }
    ?>
</main>

<?php
endwhile;

get_footer();
