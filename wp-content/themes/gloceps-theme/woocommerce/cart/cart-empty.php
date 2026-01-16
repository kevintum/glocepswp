<?php
/**
 * Empty Cart Page - Custom GLOCEPS Design
 *
 * @package GLOCEPS
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<!-- Cart Hero -->
<section class="cart-hero">
    <div class="container">
        <?php gloceps_breadcrumbs(); ?>
        <h1 class="cart-hero__title"><?php esc_html_e( 'Your Cart', 'gloceps' ); ?></h1>
        <p class="cart-hero__subtitle">
            <?php esc_html_e( 'Review your selected publications before checkout.', 'gloceps' ); ?>
        </p>
    </div>
</section>

<!-- Cart Content -->
<section class="section">
    <div class="container">
        <div class="cart-empty">
            <div class="cart-empty__icon">
                <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="cart-empty__title"><?php esc_html_e( 'Your cart is empty', 'gloceps' ); ?></h2>
            <p class="cart-empty__message">
                <?php esc_html_e( 'Browse our publications and find research papers that interest you.', 'gloceps' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/store/' ) ); ?>" class="btn btn--primary btn--lg">
                <?php esc_html_e( 'Browse Publications', 'gloceps' ); ?>
            </a>
        </div>
    </div>
</section>

<?php
do_action( 'woocommerce_after_cart' );

