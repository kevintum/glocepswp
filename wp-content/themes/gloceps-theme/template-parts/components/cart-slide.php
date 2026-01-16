<?php
/**
 * Cart Slide-in Panel
 * 
 * @package GLOCEPS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}
?>

<!-- Cart Slide-in Panel -->
<div id="cart-slide" class="cart-slide" aria-hidden="true">
    <div class="cart-slide__overlay"></div>
    <div class="cart-slide__panel">
        <div class="cart-slide__header">
            <h2 class="cart-slide__title"><?php esc_html_e( 'Your Cart', 'gloceps' ); ?></h2>
            <button class="cart-slide__close" aria-label="<?php esc_attr_e( 'Close cart', 'gloceps' ); ?>">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="cart-slide__content">
            <?php if ( WC()->cart->is_empty() ) : ?>
                <div class="cart-slide__empty">
                    <div class="cart-slide__empty-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="cart-slide__empty-text"><?php esc_html_e( 'Your cart is empty', 'gloceps' ); ?></p>
                    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ?: get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--primary">
                        <?php esc_html_e( 'Browse Publications', 'gloceps' ); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="cart-slide__items">
                    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                        
                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                    ?>
                        <div class="cart-slide__item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
                            <div class="cart-slide__item-image">
                                <?php
                                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'thumbnail' ), $cart_item, $cart_item_key );
                                if ( $product_permalink ) {
                                    printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                } else {
                                    echo $thumbnail;
                                }
                                ?>
                            </div>
                            <div class="cart-slide__item-details">
                                <h3 class="cart-slide__item-title">
                                    <?php
                                    if ( ! $product_permalink ) {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) );
                                    } else {
                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                    }
                                    ?>
                                </h3>
                                <div class="cart-slide__item-meta">
                                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                </div>
                                <div class="cart-slide__item-quantity">
                                    <span class="cart-slide__item-quantity-label"><?php esc_html_e('Qty', 'gloceps'); ?></span>
                                    <span class="cart-slide__item-quantity-value"><?php echo absint($cart_item['quantity']); ?></span>
                                </div>
                                <div class="cart-slide__item-price">
                                    <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                </div>
                            </div>
                            <button class="cart-slide__item-remove" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'gloceps' ); ?>">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-slide__summary">
                    <div class="cart-slide__summary-row">
                        <span><?php esc_html_e( 'Subtotal', 'gloceps' ); ?></span>
                        <span><?php wc_cart_totals_subtotal_html(); ?></span>
                    </div>
                    <div class="cart-slide__summary-total">
                        <span><?php esc_html_e( 'Total', 'gloceps' ); ?></span>
                        <span><?php wc_cart_totals_order_total_html(); ?></span>
                    </div>
                </div>
                
                <div class="cart-slide__actions">
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn--outline btn--full">
                        <?php esc_html_e( 'View Cart', 'gloceps' ); ?>
                    </a>
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn--primary btn--full">
                        <?php esc_html_e( 'Checkout', 'gloceps' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add to Cart Notification -->
<div id="add-to-cart-notification" class="add-to-cart-notification" aria-hidden="true">
    <div class="add-to-cart-notification__content">
        <div class="add-to-cart-notification__icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="add-to-cart-notification__message">
            <strong><?php esc_html_e( 'Added to cart!', 'gloceps' ); ?></strong>
            <span class="add-to-cart-notification__product-name"></span>
        </div>
        <button class="add-to-cart-notification__close" aria-label="<?php esc_attr_e( 'Close', 'gloceps' ); ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="add-to-cart-notification__actions">
        <button class="add-to-cart-notification__action add-to-cart-notification__action--close">
            <?php esc_html_e( 'Shop', 'gloceps' ); ?>
        </button>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="add-to-cart-notification__action add-to-cart-notification__action--cart">
            <?php esc_html_e( 'View Cart', 'gloceps' ); ?>
        </a>
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="add-to-cart-notification__action add-to-cart-notification__action--checkout">
            <?php esc_html_e( 'Checkout', 'gloceps' ); ?>
        </a>
    </div>
</div>

