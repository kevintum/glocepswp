<?php
/**
 * Checkout Form - Custom GLOCEPS Design
 * Matches checkout.html static design
 *
 * @package GLOCEPS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Remove WooCommerce breadcrumb and coupon form from hooks (we output them manually)
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'gloceps' ) ) );
    return;
}

?>

<!-- Checkout Header -->
<section class="checkout-header">
    <div class="container">
        <?php gloceps_breadcrumbs(); ?>
        
        <!-- Progress Steps -->
        <div class="checkout-steps">
            <div class="checkout-step checkout-step--completed">
                <span class="checkout-step__number">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <span class="checkout-step__label"><?php esc_html_e( 'Cart', 'gloceps' ); ?></span>
            </div>
            <div class="checkout-step__line checkout-step__line--completed"></div>
            <div class="checkout-step checkout-step--active">
                <span class="checkout-step__number">
                    <span>2</span>
                </span>
                <span class="checkout-step__label"><?php esc_html_e( 'Details', 'gloceps' ); ?></span>
            </div>
            <div class="checkout-step__line"></div>
            <div class="checkout-step">
                <span class="checkout-step__number">
                    <span>3</span>
                </span>
                <span class="checkout-step__label"><?php esc_html_e( 'Payment', 'gloceps' ); ?></span>
            </div>
            <div class="checkout-step__line"></div>
            <div class="checkout-step">
                <span class="checkout-step__number">
                    <span>4</span>
                </span>
                <span class="checkout-step__label"><?php esc_html_e( 'Complete', 'gloceps' ); ?></span>
            </div>
        </div>
    </div>
</section>

<section class="section checkout-section-main">
    <div class="container checkout-container">

        <form name="checkout" method="post" class="checkout woocommerce-checkout checkout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

            <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

            <div class="checkout-layout">
                <!-- Checkout Form -->
                <div class="checkout-form-wrapper">
                    <!-- Return to Cart Link -->
                    <div class="checkout-return">
                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="checkout-return__link">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <?php esc_html_e( 'Return to Cart', 'gloceps' ); ?>
                        </a>
                    </div>
                    
                    <!-- Coupon Form -->
                    <?php 
                    $enable_coupons = get_field( 'checkout_enable_coupons', 'option' );
                    if ( wc_coupons_enabled() && $enable_coupons ) : 
                    ?>
                        <div class="checkout-coupon">
                            <?php woocommerce_checkout_coupon_form(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $checkout->get_checkout_fields() ) : ?>

                        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                        <!-- Contact Information -->
                        <div class="checkout-section" id="customer_details">
                            <h2 class="checkout-section__title">
                                <span class="checkout-section__icon">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                                <?php esc_html_e( 'Contact Information', 'gloceps' ); ?>
                            </h2>
                            <p class="checkout-section__subtitle">
                                <?php esc_html_e( 'Your publications will be delivered to this email address.', 'gloceps' ); ?>
                            </p>
                            
                            <?php do_action( 'woocommerce_checkout_billing' ); ?>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                    <?php endif; ?>

                    <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

                    <!-- Payment Method -->
                    <div class="checkout-section checkout-section--payment">
                        <h2 class="checkout-section__title">
                            <span class="checkout-section__icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </span>
                            <?php esc_html_e( 'Payment Method', 'gloceps' ); ?>
                        </h2>
                        <p class="checkout-section__subtitle">
                            <?php esc_html_e( 'Choose your preferred payment option.', 'gloceps' ); ?>
                        </p>

                        <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                        
                        <!-- Terms & Submit -->
                        <div class="checkout-section checkout-section--terms">
                            <?php
                            woocommerce_form_field( 'terms', array(
                                'type'          => 'checkbox',
                                'class'         => array( 'checkout-form__field checkout-form__field--checkbox' ),
                                'label_class'   => array( 'checkbox-label' ),
                                'input_class'   => array( 'input-checkbox' ),
                                'required'      => true,
                                'label'         => sprintf( __( 'I agree to the %s and %s', 'gloceps' ), '<a href="#">' . __( 'Terms of Service', 'gloceps' ) . '</a>', '<a href="#">' . __( 'Privacy Policy', 'gloceps' ) . '</a>' ),
                            ) );
                            
                            woocommerce_form_field( 'newsletter', array(
                                'type'          => 'checkbox',
                                'class'         => array( 'checkout-form__field checkout-form__field--checkbox' ),
                                'label_class'   => array( 'checkbox-label' ),
                                'input_class'   => array( 'input-checkbox' ),
                                'required'      => false,
                                'label'         => __( 'Send me GLOCEPS research updates and newsletter', 'gloceps' ),
                            ) );
                            ?>
                            
                            <button type="submit" class="btn btn--primary btn--lg btn--full checkout-submit" name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e( 'Place order', 'woocommerce' ); ?>" data-value="<?php esc_attr_e( 'Place order', 'woocommerce' ); ?>">
                                <span class="checkout-submit__text">
                                    <?php 
                                    $total = WC()->cart->get_total( 'edit' );
                                    // Format amount with thousand separators
                                    $formatted_total = number_format( (float) $total, 0, '.', ',' );
                                    printf( esc_html__( 'Pay KES %s', 'gloceps' ), $formatted_total );
                                    ?>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="checkout-summary">
                    <div class="checkout-summary__card">
                        <h3 class="checkout-summary__title"><?php esc_html_e( 'Order Summary', 'gloceps' ); ?></h3>

                        <!-- Order Items -->
                        <div class="checkout-summary__items">
                            <?php
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                    // Get linked publication for type
                                    $publication_id = get_post_meta( $product_id, '_gloceps_publication_id', true );
                                    $pub_type = '';
                                    if ( $publication_id ) {
                                        $pub_types = get_the_terms( $publication_id, 'publication_type' );
                                        if ( $pub_types && ! is_wp_error( $pub_types ) ) {
                                            $pub_type = $pub_types[0]->name;
                                        }
                                    }
                                    $format = $publication_id ? get_field( 'publication_format', $publication_id ) : 'PDF';
                                    ?>
                                    <div class="checkout-summary__item">
                                        <div class="checkout-summary__item-image">
                                            <?php echo $_product->get_image( 'thumbnail' ); ?>
                                        </div>
                                        <div class="checkout-summary__item-details">
                                            <span class="checkout-summary__item-title"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></span>
                                            <span class="checkout-summary__item-type"><?php echo esc_html( $pub_type ? $pub_type . ' • ' . $format : $format ); ?></span>
                                        </div>
                                        <span class="checkout-summary__item-price"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></span>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="checkout-summary__edit"><?php esc_html_e( 'Edit Cart', 'gloceps' ); ?></a>

                        <!-- Totals -->
                        <div class="checkout-summary__totals">
                            <div class="checkout-summary__row">
                                <span><?php esc_html_e( 'Subtotal', 'gloceps' ); ?></span>
                                <span><?php wc_cart_totals_subtotal_html(); ?></span>
                            </div>
                            <div class="checkout-summary__row checkout-summary__row--total">
                                <span><?php esc_html_e( 'Total', 'gloceps' ); ?></span>
                                <div>
                                    <span class="checkout-summary__total-kes"><?php wc_cart_totals_order_total_html(); ?></span>
                                    <?php 
                                    $total = WC()->cart->get_total( 'edit' );
                                    $usd_total = (float) $total / 130;
                                    if ( $usd_total > 0 ) {
                                        echo '<span class="checkout-summary__total-usd">≈ $' . number_format( $usd_total, 0 ) . ' USD</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="checkout-summary__security">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span><?php esc_html_e( 'Your payment is secure and encrypted', 'gloceps' ); ?></span>
                        </div>
                    </div>

                    <!-- What You'll Get -->
                    <?php 
                    $what_youll_receive_title = get_field( 'checkout_what_youll_receive_title', 'option' ) ?: __( 'What you\'ll receive:', 'gloceps' );
                    $what_youll_receive_items = get_field( 'checkout_what_youll_receive_items', 'option' );
                    if ( ! $what_youll_receive_items || empty( $what_youll_receive_items ) ) {
                        // Default items
                        $what_youll_receive_items = array(
                            array( 'text' => __( 'Instant access to download PDF files', 'gloceps' ) ),
                            array( 'text' => __( 'Email with download links', 'gloceps' ) ),
                            array( 'text' => __( 'Receipt for your records', 'gloceps' ) ),
                            array( 'text' => __( 'Ability to re-download anytime', 'gloceps' ) ),
                        );
                    }
                    ?>
                    <div class="checkout-features">
                        <h4><?php echo esc_html( $what_youll_receive_title ); ?></h4>
                        <ul>
                            <?php foreach ( $what_youll_receive_items as $item ) : ?>
                                <li>
                                    <svg width="16" height="16" fill="none" stroke="var(--color-primary)" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <?php echo esc_html( $item['text'] ); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Need Help -->
                    <div class="checkout-help">
                        <p>
                            <?php 
                            // Get help section settings from ACF
                            $help_section = get_field( 'checkout_help_section', 'option' );
                            
                            // Set defaults
                            $help_prefix = $help_section['prefix'] ?? __( 'Need help?', 'gloceps' );
                            $support_text = $help_section['support_text'] ?? __( 'Contact support', 'gloceps' );
                            $support_url = $help_section['support_url'] ?? '';
                            
                            // If no URL provided, try to get contact page URL
                            if ( empty( $support_url ) ) {
                                $contact_page = get_page_by_path( 'contact' );
                                $support_url = $contact_page ? get_permalink( $contact_page->ID ) : '#contact';
                            }
                            
                            $phone_text = $help_section['phone_text'] ?? '+254 112 401 331';
                            $phone_link = $help_section['phone_link'] ?? '+254112401331';
                            
                            // Build the output
                            echo esc_html( $help_prefix ) . ' ';
                            echo '<a href="' . esc_url( $support_url ) . '">' . esc_html( $support_text ) . '</a>';
                            echo ' ' . esc_html__( 'or call', 'gloceps' ) . ' ';
                            echo '<a href="tel:' . esc_attr( $phone_link ) . '">' . esc_html( $phone_text ) . '</a>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

