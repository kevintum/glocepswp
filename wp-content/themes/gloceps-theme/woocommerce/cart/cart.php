<?php
/**
 * Cart Page - Custom GLOCEPS Design
 * Matches the static cart.html design
 *
 * @package GLOCEPS
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<!-- Cart Hero -->
<section class="cart-hero">
    <div class="container">
        <?php gloceps_breadcrumbs(); ?>
        <h1 class="cart-hero__title"><?php esc_html_e('Your Cart', 'gloceps'); ?></h1>
        <p class="cart-hero__subtitle">
            <?php esc_html_e('Review your selected publications before checkout.', 'gloceps'); ?>
        </p>
    </div>
</section>

<!-- Cart Content -->
<section class="section">
    <div class="container">
        <?php if (WC()->cart->is_empty()) : ?>
            <div class="cart-empty">
                <div class="cart-empty__icon">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="cart-empty__title"><?php esc_html_e('Your cart is empty', 'gloceps'); ?></h2>
                <p class="cart-empty__message">
                    <?php esc_html_e('Browse our publications and find research papers that interest you.', 'gloceps'); ?>
                </p>
                <a href="<?php echo esc_url(home_url('/store/')); ?>" class="btn btn--primary btn--lg">
                    <?php esc_html_e('Browse Publications', 'gloceps'); ?>
                </a>
            </div>
            <?php else : ?>
                <!-- WooCommerce Notices -->
                <div class="cart-notices">
                    <?php wc_print_notices(); ?>
                </div>
                
                <form class="woocommerce-cart-form cart-layout" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                    <!-- Cart Items -->
                    <div class="cart-items">
                    <?php do_action('woocommerce_before_cart_contents'); ?>
                    
                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                        
                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    ?>
                    <div class="cart-item woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                        <div class="cart-item__image">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                            if (!$product_permalink) {
                                echo $thumbnail;
                            } else {
                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                            }
                            ?>
                        </div>
                        
                        <div class="cart-item__details">
                            <?php 
                            // Get linked publication
                            $publication_id = get_post_meta($product_id, '_gloceps_publication_id', true);
                            $publication_type_name = '';
                            $format = '';
                            $page_count = '';
                            $pub_date = '';
                            
                            if ($publication_id) {
                                // Get publication type
                                $pub_types = get_the_terms($publication_id, 'publication_type');
                                if ($pub_types && !is_wp_error($pub_types)) {
                                    $publication_type_name = $pub_types[0]->name;
                                }
                                
                                // Get format and page count
                                $format = get_field('publication_format', $publication_id);
                                $page_count = get_field('page_count', $publication_id);
                                
                                // Get publication date
                                $pub_date_field = get_field('publication_date', $publication_id);
                                if ($pub_date_field) {
                                    $pub_date = date_i18n('F Y', strtotime($pub_date_field));
                                } else {
                                    $pub_date = get_the_date('F Y', $publication_id);
                                }
                            } else {
                                // Fallback to product categories
                                $terms = get_the_terms($product_id, 'product_cat');
                                if ($terms && !is_wp_error($terms)) {
                                    $publication_type_name = $terms[0]->name;
                                }
                                if ($_product->is_downloadable()) {
                                    $format = 'PDF';
                                }
                            }
                            
                            if ($publication_type_name) :
                            ?>
                            <span class="cart-item__category"><?php echo esc_html($publication_type_name); ?></span>
                            <?php endif; ?>
                            
                            <h3 class="cart-item__title">
                                <?php
                                if (!$product_permalink) {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                } else {
                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                }
                                ?>
                            </h3>
                            
                            <div class="cart-item__meta">
                                <?php 
                                // First span: Format and pages (uppercase format)
                                $format_parts = array();
                                if ($format) {
                                    $format_parts[] = esc_html(strtoupper($format));
                                }
                                if ($page_count && absint($page_count) > 0) {
                                    $format_parts[] = sprintf(esc_html__('%d pages', 'gloceps'), absint($page_count));
                                }
                                
                                // Always show first span if we have format or page count
                                if (!empty($format_parts)) {
                                    echo '<span>' . implode(' • ', $format_parts) . '</span>';
                                } elseif ($format) {
                                    // If only format, still show it
                                    echo '<span>' . esc_html(strtoupper($format)) . '</span>';
                                }
                                
                                // Second span: Date (separate, always show if available)
                                if ($pub_date) {
                                    echo '<span>' . esc_html($pub_date) . '</span>';
                                }
                                
                                // Fallback if no meta data at all
                                if (empty($format_parts) && !$format && !$pub_date) {
                                    echo wc_get_formatted_cart_item_data($cart_item);
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="cart-item__quantity">
                            <span class="cart-item__quantity-label"><?php esc_html_e('Quantity', 'gloceps'); ?></span>
                            <div class="cart-item__quantity-controls">
                                <?php
                                if ( $_product->is_sold_individually() ) {
                                    $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                } else {
                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                            'min_value'    => '0',
                                            'product_name' => $_product->get_name(),
                                        ),
                                        $_product,
                                        false
                                    );
                                }
                                echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                ?>
                            </div>
                        </div>
                        
                        <div class="cart-item__price">
                            <span class="cart-item__price-amount">
                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                            </span>
                            <?php 
                            // Show USD equivalent if KES
                            $currency = get_woocommerce_currency();
                            if ($currency === 'KES') {
                                // Simple conversion (1 USD ≈ 130 KES) - adjust as needed
                                $price = (float) $_product->get_price();
                                $quantity = absint($cart_item['quantity']);
                                $usd_amount = ($price * $quantity) / 130;
                                if ($usd_amount > 0) {
                                    echo '<span class="cart-item__price-usd">≈ $' . number_format($usd_amount, 0) . ' USD</span>';
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="cart-item__remove-wrapper">
                            <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="cart-item__remove" aria-label="<?php esc_attr_e('Remove this item', 'gloceps'); ?>" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php do_action('woocommerce_cart_contents'); ?>
                    
                    <div class="cart-continue">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) ?: '/store/'); ?>" class="btn btn--ghost">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <?php esc_html_e('Browse More Premium Publications', 'gloceps'); ?>
                        </a>
                    </div>
                    
                    <?php do_action('woocommerce_after_cart_contents'); ?>
                    
                    <div class="cart-actions">
                        <button type="submit" class="btn btn--outline" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                            <?php esc_html_e('Update basket', 'gloceps'); ?>
                        </button>
                    </div>
                    
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </div>
                
                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="cart-summary__card">
                        <h2 class="cart-summary__title"><?php esc_html_e('Order Summary', 'gloceps'); ?></h2>
                        
                        <div class="cart-summary__rows">
                            <div class="cart-summary__row">
                                <span><?php printf(esc_html__('Subtotal (%d items)', 'gloceps'), WC()->cart->get_cart_contents_count()); ?></span>
                                <span><?php wc_cart_totals_subtotal_html(); ?></span>
                            </div>
                            
                            <div class="cart-summary__row cart-summary__row--muted">
                                <span><?php esc_html_e('Delivery', 'gloceps'); ?></span>
                                <span><?php esc_html_e('Digital (Instant)', 'gloceps'); ?></span>
                            </div>
                        </div>
                        
                        <div class="cart-summary__total">
                            <span><?php esc_html_e('Total', 'gloceps'); ?></span>
                            <div class="cart-summary__total-amount">
                                <span class="cart-summary__total-kes"><?php wc_cart_totals_order_total_html(); ?></span>
                                <?php 
                                // Show USD equivalent
                                $total = WC()->cart->get_total('edit');
                                $usd_total = (float) $total / 130; // Simple conversion
                                if ($usd_total > 0) {
                                    echo '<span class="cart-summary__total-usd">≈ $' . number_format($usd_total, 0) . ' USD</span>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn--primary btn--lg btn--full">
                            <?php esc_html_e('Proceed to Checkout', 'gloceps'); ?>
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        
                        <div class="cart-summary__security">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span><?php esc_html_e('Secure checkout with M-Pesa & PayPal', 'gloceps'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="cart-trust">
                        <div class="cart-trust__item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span><?php esc_html_e('Instant PDF Download', 'gloceps'); ?></span>
                        </div>
                        <div class="cart-trust__item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span><?php esc_html_e('Email Delivery', 'gloceps'); ?></span>
                        </div>
                        <div class="cart-trust__item">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span><?php esc_html_e('Secure Payment', 'gloceps'); ?></span>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php do_action('woocommerce_after_cart'); ?>
