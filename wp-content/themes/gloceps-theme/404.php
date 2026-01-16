<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

// Add body class for error page styling
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'error-page';
    return $classes;
} );

get_header();
?>

<main class="error-page__main">
    <div class="error-page__content">
        <div class="error-page__illustration">
            <div class="error-page__number" data-number="404">404</div>
            <div class="error-page__icon">
                <svg
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>
        </div>

        <h1 class="error-page__title"><?php esc_html_e( 'Page Not Found', 'gloceps' ); ?></h1>
        <p class="error-page__description">
            <?php esc_html_e( 'Sorry, we couldn\'t find the page you\'re looking for. It may have been moved, renamed, or might never have existed. Let\'s get you back on track.', 'gloceps' ); ?>
        </p>

        <div class="error-page__actions">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary btn--lg">
                <svg
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                <?php esc_html_e( 'Back to Home', 'gloceps' ); ?>
            </a>
            <?php
            $contact_page = get_page_by_path( 'contact' );
            if ( $contact_page ) :
            ?>
            <a href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>" class="btn btn--outline btn--lg">
                <?php esc_html_e( 'Contact Support', 'gloceps' ); ?>
            </a>
            <?php endif; ?>
        </div>

        <div class="error-page__suggestions">
            <p class="error-page__suggestions-title"><?php esc_html_e( 'Popular Pages', 'gloceps' ); ?></p>
            <div class="error-page__links">
                <?php
                $publications_link = get_post_type_archive_link( 'publication' );
                if ( $publications_link ) :
                ?>
                <a href="<?php echo esc_url( $publications_link ); ?>" class="error-page__link">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                    <?php esc_html_e( 'Publications', 'gloceps' ); ?>
                </a>
                <?php endif; ?>
                
                <?php
                $research_page = get_page_by_path( 'research' );
                if ( $research_page ) :
                ?>
                <a href="<?php echo esc_url( get_permalink( $research_page->ID ) ); ?>" class="error-page__link">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                        />
                    </svg>
                    <?php esc_html_e( 'Research', 'gloceps' ); ?>
                </a>
                <?php endif; ?>
                
                <?php
                $events_link = get_post_type_archive_link( 'event' );
                if ( $events_link ) :
                ?>
                <a href="<?php echo esc_url( $events_link ); ?>" class="error-page__link">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    <?php esc_html_e( 'Events', 'gloceps' ); ?>
                </a>
                <?php endif; ?>
                
                <?php
                $about_page = get_page_by_path( 'about' );
                if ( $about_page ) :
                ?>
                <a href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>" class="error-page__link">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <?php esc_html_e( 'About Us', 'gloceps' ); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<footer class="error-footer">
    <div class="container">
        <div class="error-footer__inner">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="error-footer__logo">
                <?php
                $logo_url = get_field( 'header_logo', 'option' );
                if ( $logo_url && is_array( $logo_url ) ) {
                    $logo_url = $logo_url['url'];
                } elseif ( $logo_url ) {
                    $logo_url = wp_get_attachment_image_url( $logo_url, 'full' );
                }
                if ( $logo_url ) :
                ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
                <?php endif; ?>
                <span class="error-footer__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            </a>
            <p class="error-footer__copyright">
                <?php
                $address = get_field( 'footer_address', 'option' ) ?: 'Runda Drive, Nairobi, Kenya';
                printf(
                    '© %d %s — %s',
                    date( 'Y' ),
                    esc_html( get_bloginfo( 'name' ) ),
                    esc_html( $address )
                );
                ?>
            </p>
        </div>
    </div>
</footer>

<?php
// Don't use get_footer() here since we have a custom simplified footer
// Just call wp_footer() for scripts
wp_footer();
?>
</body>
</html>
