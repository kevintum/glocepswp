<?php
/**
 * Custom 500 Error Page Template
 * 
 * This file is a WordPress drop-in that displays when a fatal PHP error occurs.
 * Place this file in wp-content/ directory (not in the theme).
 *
 * @package GLOCEPS
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    // If WordPress isn't loaded, try to load it
    if ( file_exists( dirname( __FILE__ ) . '/wp-load.php' ) ) {
        require_once dirname( __FILE__ ) . '/wp-load.php';
    } else {
        die( 'WordPress not found' );
    }
}

// Set HTTP status code
http_response_code( 500 );

// Get site info
$site_name = get_bloginfo( 'name' );
$site_url = home_url( '/' );
$contact_email = get_field( 'footer_email', 'option' ) ?: 'info@gloceps.org';
$address = get_field( 'footer_address', 'option' ) ?: 'Runda Drive, Nairobi, Kenya';

// Get logo
$logo_url = get_field( 'header_logo', 'option' );
if ( $logo_url && is_array( $logo_url ) ) {
    $logo_url = $logo_url['url'];
} elseif ( $logo_url ) {
    $logo_url = wp_get_attachment_image_url( $logo_url, 'full' );
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Server Error - <?php echo esc_attr( $site_name ); ?>">
    <title>500 - Server Error | <?php echo esc_html( $site_name ); ?></title>
    <?php
    // Try to enqueue styles if WordPress is loaded
    if ( function_exists( 'wp_head' ) ) {
        wp_head();
    } else {
        // Fallback basic styles
        ?>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
                color: #fff;
            }
        </style>
        <?php
    }
    ?>
</head>
<body class="error-page error-page--500">
    <main class="error-page__main">
        <div class="error-page__content">
            <div class="error-page__illustration">
                <div class="error-page__number" data-number="500">500</div>
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
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                </div>
            </div>

            <h1 class="error-page__title"><?php esc_html_e( 'Server Error', 'gloceps' ); ?></h1>
            <p class="error-page__description">
                <?php esc_html_e( 'Something went wrong on our end. Our team has been notified and we\'re working to fix it. Please try again in a few moments.', 'gloceps' ); ?>
            </p>

            <div class="error-page__actions">
                <button onclick="window.location.reload()" class="btn btn--accent btn--lg">
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
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    <?php esc_html_e( 'Try Again', 'gloceps' ); ?>
                </button>
                <a href="<?php echo esc_url( $site_url ); ?>" class="btn btn--outline-light btn--lg">
                    <?php esc_html_e( 'Go to Homepage', 'gloceps' ); ?>
                </a>
            </div>

            <div class="error-page__status">
                <p class="error-page__status-title"><?php esc_html_e( 'System Status', 'gloceps' ); ?></p>
                <p class="error-page__status-text">
                    <?php
                    printf(
                        esc_html__( 'Our team is aware of this issue. If the problem persists, please contact us at %s', 'gloceps' ),
                        '<a href="mailto:' . esc_attr( $contact_email ) . '">' . esc_html( $contact_email ) . '</a>'
                    );
                    ?>
                </p>
                <div class="error-page__status-indicator"><?php esc_html_e( 'Monitoring active', 'gloceps' ); ?></div>
            </div>
        </div>
    </main>

    <footer class="error-footer">
        <div class="container">
            <div class="error-footer__inner">
                <a href="<?php echo esc_url( $site_url ); ?>" class="error-footer__logo">
                    <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" />
                    <?php endif; ?>
                    <span class="error-footer__logo-text"><?php echo esc_html( $site_name ); ?></span>
                </a>
                <p class="error-footer__copyright">
                    <?php
                    printf(
                        '© %d %s — %s',
                        date( 'Y' ),
                        esc_html( $site_name ),
                        esc_html( $address )
                    );
                    ?>
                </p>
            </div>
        </div>
    </footer>

    <?php
    if ( function_exists( 'wp_footer' ) ) {
        wp_footer();
    }
    ?>
</body>
</html>
