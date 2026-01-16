<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();
?>

<main class="error-page">
    <section class="section">
        <div class="container">
            <div class="error-content text-center">
                <span class="error-code">404</span>
                <h1 class="error-title"><?php esc_html_e( 'Page Not Found', 'gloceps' ); ?></h1>
                <p class="error-description">
                    <?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'gloceps' ); ?>
                </p>
                
                <div class="error-actions">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary btn--lg">
                        <?php esc_html_e( 'Back to Home', 'gloceps' ); ?>
                    </a>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--secondary btn--lg">
                        <?php esc_html_e( 'Browse Publications', 'gloceps' ); ?>
                    </a>
                </div>
                
                <div class="error-search">
                    <p><?php esc_html_e( 'Or try searching for what you need:', 'gloceps' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

