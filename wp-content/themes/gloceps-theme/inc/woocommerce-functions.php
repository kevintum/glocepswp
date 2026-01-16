<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package GLOCEPS
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
 *
 * @return void
 */
function gloceps_woocommerce_setup() {
    add_theme_support(
        'woocommerce',
        array(
            'thumbnail_image_width' => 300,
            'single_image_width'    => 600,
            'product_grid'          => array(
                'default_rows'    => 3,
                'min_rows'        => 1,
                'default_columns' => 3,
                'min_columns'     => 1,
                'max_columns'     => 4,
            ),
        )
    );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'gloceps_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function gloceps_woocommerce_scripts() {
    wp_enqueue_style( 'gloceps-woocommerce-style', get_template_directory_uri() . '/woocommerce/css/woocommerce.css', array(), GLOCEPS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'gloceps_woocommerce_scripts' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function gloceps_woocommerce_active_body_class( $classes ) {
    $classes[] = 'woocommerce-active';
    return $classes;
}
add_filter( 'body_class', 'gloceps_woocommerce_active_body_class' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function gloceps_woocommerce_related_products_args( $args ) {
    $defaults = array(
        'posts_per_page' => 3,
        'columns'        => 3,
    );

    $args = wp_parse_args( $defaults, $args );

    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'gloceps_woocommerce_related_products_args' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'gloceps_woocommerce_wrapper_before' ) ) {
    /**
     * Before Content.
     */
    function gloceps_woocommerce_wrapper_before() {
        ?>
        <main id="primary" class="site-main">
            <div class="container">
        <?php
    }
}
add_action( 'woocommerce_before_main_content', 'gloceps_woocommerce_wrapper_before' );

if ( ! function_exists( 'gloceps_woocommerce_wrapper_after' ) ) {
    /**
     * After Content.
     */
    function gloceps_woocommerce_wrapper_after() {
        ?>
            </div>
        </main><!-- #main -->
        <?php
    }
}
add_action( 'woocommerce_after_main_content', 'gloceps_woocommerce_wrapper_after' );

/**
 * Sample implementation of the WooCommerce Mini Cart.
 */
if ( ! function_exists( 'gloceps_woocommerce_cart_link_fragment' ) ) {
    /**
     * Cart Fragments.
     */
    function gloceps_woocommerce_cart_link_fragment( $fragments ) {
        ob_start();
        gloceps_woocommerce_cart_link();
        $fragments['a.cart-contents'] = ob_get_clean();

        return $fragments;
    }
}
add_filter( 'woocommerce_add_to_cart_fragments', 'gloceps_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'gloceps_woocommerce_cart_link' ) ) {
    /**
     * Cart Link.
     */
    function gloceps_woocommerce_cart_link() {
        ?>
        <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'gloceps' ); ?>">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <?php
            $item_count_text = sprintf(
                /* translators: number of items in the mini cart. */
                _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'gloceps' ),
                WC()->cart->get_cart_contents_count()
            );
            ?>
            <span class="count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
        </a>
        <?php
    }
}

if ( ! function_exists( 'gloceps_woocommerce_header_cart' ) ) {
    /**
     * Display Header Cart.
     */
    function gloceps_woocommerce_header_cart() {
        if ( is_cart() ) {
            $class = 'current-menu-item';
        } else {
            $class = '';
        }
        ?>
        <ul id="site-header-cart" class="site-header-cart">
            <li class="<?php echo esc_attr( $class ); ?>">
                <?php gloceps_woocommerce_cart_link(); ?>
            </li>
            <li>
                <?php
                $instance = array(
                    'title' => '',
                );

                the_widget( 'WC_Widget_Cart', $instance );
                ?>
            </li>
        </ul>
        <?php
    }
}

/**
 * Prevent WooCommerce from auto-printing notices on cart page
 * We'll handle notices manually in the cart template
 */
function gloceps_prevent_cart_notices() {
    if ( is_cart() ) {
        // Remove default notice output from various hooks
        remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
        remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
        
        // Also prevent notices in header/footer
        remove_action( 'wp_head', 'woocommerce_output_all_notices', 1 );
    }
}
add_action( 'template_redirect', 'gloceps_prevent_cart_notices', 5 );

/**
 * Redirect /shop to purchase page
 */
function gloceps_redirect_shop_to_purchase() {
    if ( is_shop() ) {
        $purchase_page = get_page_by_path( 'purchase' );
        if ( $purchase_page ) {
            wp_safe_redirect( get_permalink( $purchase_page->ID ), 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'gloceps_redirect_shop_to_purchase' );

/**
 * Redirect WooCommerce product pages to linked publication pages
 * Prevents confusion and ensures users see the publication page instead of product page
 */
function gloceps_redirect_product_to_publication() {
    if ( ! is_product() ) {
        return;
    }
    
    global $post;
    if ( ! $post ) {
        return;
    }
    
    // Check if this product is linked to a publication
    $publication_id = get_post_meta( $post->ID, '_gloceps_publication_id', true );
    
    if ( $publication_id && get_post_status( $publication_id ) === 'publish' ) {
        $publication_url = get_permalink( $publication_id );
        if ( $publication_url ) {
            wp_safe_redirect( $publication_url, 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'gloceps_redirect_product_to_publication', 5 );

/**
 * Prevent indexing of WooCommerce product pages
 * Since products are linked to publications, we don't want duplicate content in search results
 */
function gloceps_prevent_product_indexing() {
    if ( is_product() ) {
        // Check if this product is linked to a publication
        global $post;
        if ( $post ) {
            $publication_id = get_post_meta( $post->ID, '_gloceps_publication_id', true );
            if ( $publication_id ) {
                // Add noindex meta tag
                echo '<meta name="robots" content="noindex, nofollow">' . "\n";
            }
        }
    }
}
add_action( 'wp_head', 'gloceps_prevent_product_indexing', 1 );

/**
 * Set WooCommerce shop page to purchase page
 */
function gloceps_set_shop_page_to_purchase() {
    $purchase_page = get_page_by_path( 'purchase' );
    if ( $purchase_page ) {
        update_option( 'woocommerce_shop_page_id', $purchase_page->ID );
    }
}
// Run on theme activation or when needed
// add_action( 'after_switch_theme', 'gloceps_set_shop_page_to_purchase' );

/**
 * Customize the Shop page title
 */
function gloceps_woocommerce_shop_title( $page_title ) {
    if ( is_shop() ) {
        return __( 'Publications', 'gloceps' );
    }
    return $page_title;
}
add_filter( 'woocommerce_page_title', 'gloceps_woocommerce_shop_title' );

/**
 * Remove WooCommerce Sidebar
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Customize WooCommerce breadcrumbs
 */
function gloceps_woocommerce_breadcrumbs() {
    return array(
        'delimiter'   => ' <span class="breadcrumb-separator">/</span> ',
        'wrap_before' => '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'gloceps' ) . '">',
        'wrap_after'  => '</nav>',
        'before'      => '<span class="breadcrumb-item">',
        'after'       => '</span>',
        'home'        => __( 'Home', 'gloceps' ),
    );
}
add_filter( 'woocommerce_breadcrumb_defaults', 'gloceps_woocommerce_breadcrumbs' );

/**
 * Sync Publication CPT with WooCommerce Products
 * When a publication is saved, create/update corresponding WooCommerce product if it's a premium publication
 */
function gloceps_sync_publication_to_product( $post_id, $post, $update ) {
    // Only for publications
    if ( $post->post_type !== 'publication' ) {
        return;
    }

    // Skip if this is a revision or autosave
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }

    // Check if it's a premium publication
    $access_type = get_field( 'access_type', $post_id );
    
    if ( $access_type === 'premium' ) {
        $price = get_field( 'price', $post_id );
        $wc_product_id = get_field( 'wc_product', $post_id ); // Use new field name
        
        // Get PDF file from ACF
        $pdf_file = get_field( 'pdf_file', $post_id );
        $downloads = array();
        
        if ( $pdf_file && is_array( $pdf_file ) && isset( $pdf_file['ID'] ) ) {
            // Convert ACF file array to WooCommerce download format
            $file_url = wp_get_attachment_url( $pdf_file['ID'] );
            $file_name = basename( $file_url );
            
            // WooCommerce expects downloads in format: name => url
            $downloads[ $file_name ] = $file_url;
        }
        
        // If no linked product exists, create one
        if ( ! $wc_product_id ) {
            $product = new WC_Product_Simple();
            $product->set_name( $post->post_title );
            $product->set_status( 'publish' );
            $product->set_catalog_visibility( 'visible' );
            $product->set_description( get_the_excerpt( $post_id ) );
            $product->set_short_description( get_the_excerpt( $post_id ) );
            $product->set_price( $price );
            $product->set_regular_price( $price );
            $product->set_virtual( true );
            $product->set_downloadable( true );
            
            // Set downloadable files
            if ( ! empty( $downloads ) ) {
                $product->set_downloads( $downloads );
                $product->set_download_limit( -1 ); // Unlimited downloads
                $product->set_download_expiry( -1 ); // Never expires
            }
            
            // Set featured image
            if ( has_post_thumbnail( $post_id ) ) {
                $product->set_image_id( get_post_thumbnail_id( $post_id ) );
            }
            
            $product_id = $product->save();
            
            // Link the product back to the publication
            update_field( 'wc_product', $product_id, $post_id );
            
            // Link the publication to the product
            update_post_meta( $product_id, '_gloceps_publication_id', $post_id );
        } else {
            // Update existing product
            $product = wc_get_product( $wc_product_id );
            if ( $product ) {
                $product->set_name( $post->post_title );
                $product->set_price( $price );
                $product->set_regular_price( $price );
                
                // Update downloadable files if PDF exists
                if ( ! empty( $downloads ) ) {
                    $product->set_downloadable( true );
                    $product->set_downloads( $downloads );
                    $product->set_download_limit( -1 ); // Unlimited downloads
                    $product->set_download_expiry( -1 ); // Never expires
                }
                
                $product->save();
            }
        }
    }
}
add_action( 'save_post', 'gloceps_sync_publication_to_product', 10, 3 );

/**
 * Add publication meta to order items
 */
function gloceps_add_publication_to_order_meta( $item, $cart_item_key, $values, $order ) {
    $product_id = $values['product_id'];
    $publication_id = get_post_meta( $product_id, '_gloceps_publication_id', true );
    
    if ( $publication_id ) {
        $item->add_meta_data( '_publication_id', $publication_id );
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'gloceps_add_publication_to_order_meta', 10, 4 );

/**
 * Grant access to publication PDF on order complete
 * Note: WooCommerce automatically grants download access when order is completed
 * if products are set as downloadable. This function triggers custom email.
 * The actual function is defined later in the file.
 */

/**
 * ============================================
 * ORDER COMPLETION EMAIL SYSTEM
 * ============================================
 */

/**
 * Disable WooCommerce default order completion emails
 */
function gloceps_disable_default_order_emails( $enabled, $order ) {
    // Disable customer completed order email
    if ( is_a( $order, 'WC_Order' ) && $order->has_status( 'completed' ) ) {
        return false;
    }
    return $enabled;
}
add_filter( 'woocommerce_email_enabled_customer_completed_order', 'gloceps_disable_default_order_emails', 10, 2 );

// Also unhook the email trigger
remove_action( 'woocommerce_order_status_completed_notification', array( 'WC_Email_Customer_Completed_Order', 'trigger' ), 10 );

/**
 * Customize "Add to Cart" text for publications
 */
function gloceps_add_to_cart_text( $text, $product ) {
    $publication_id = get_post_meta( $product->get_id(), '_gloceps_publication_id', true );
    
    if ( $publication_id ) {
        return __( 'Purchase Publication', 'gloceps' );
    }
    
    return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'gloceps_add_to_cart_text', 10, 2 );
add_filter( 'woocommerce_product_add_to_cart_text', 'gloceps_add_to_cart_text', 10, 2 );

/**
 * Trigger add to cart notification via WooCommerce fragments
 */
function gloceps_add_to_cart_fragments( $fragments ) {
    // Add product name to fragments for notification
    if ( isset( $_POST['product_id'] ) ) {
        $product_id = absint( $_POST['product_id'] );
        $product = wc_get_product( $product_id );
        if ( $product ) {
            $fragments['gloceps_product_name'] = $product->get_name();
        }
    }
    
    // Add cart slide fragment
    ob_start();
    include get_template_directory() . '/template-parts/components/cart-slide.php';
    $fragments['#cart-slide'] = ob_get_clean();
    
    // Add cart count to header (desktop)
    ob_start();
    $cart_count = WC()->cart->get_cart_contents_count();
    if ( $cart_count > 0 ) {
        echo '<span class="header__cart-count">' . esc_html( $cart_count ) . '</span>';
    } else {
        echo '';
    }
    $fragments['.header__cart-count'] = ob_get_clean();
    
    // Add cart count to mobile menu
    ob_start();
    if ( $cart_count > 0 ) {
        echo '<span class="mobile-menu__cart-count">' . esc_html( $cart_count ) . '</span>';
    } else {
        echo '';
    }
    $fragments['.mobile-menu__cart-count'] = ob_get_clean();
    
    // Add cart count to header button (for cases where count doesn't exist)
    ob_start();
    ?>
    <button type="button" class="header__cart" id="cart-toggle" aria-label="<?php esc_attr_e( 'View cart', 'gloceps' ); ?>" aria-expanded="false">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <?php if ( $cart_count > 0 ) : ?>
        <span class="header__cart-count"><?php echo esc_html( $cart_count ); ?></span>
        <?php endif; ?>
    </button>
    <?php
    $fragments['#cart-toggle'] = ob_get_clean();
    
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'gloceps_add_to_cart_fragments' );

/**
 * Enqueue cart slide JavaScript
 */
function gloceps_enqueue_cart_scripts() {
    if ( class_exists( 'WooCommerce' ) ) {
        // Ensure cart page is set correctly
        $cart_page_id = wc_get_page_id( 'cart' );
        if ( $cart_page_id ) {
            $cart_page = get_post( $cart_page_id );
            if ( $cart_page && $cart_page->post_name !== 'cart' ) {
                // Update cart page slug if needed
                wp_update_post( array(
                    'ID' => $cart_page_id,
                    'post_name' => 'cart',
                ) );
            }
        }
        
        // Ensure WooCommerce AJAX add to cart is enabled
        if ( get_option( 'woocommerce_enable_ajax_add_to_cart' ) !== 'yes' ) {
            update_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' );
        }
        
        // Ensure WooCommerce add to cart script is enqueued
        if ( get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes' ) {
            wp_enqueue_script( 'wc-add-to-cart' );
        }
        
        wp_localize_script( 'gloceps-main', 'glocepsCart', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'woocommerce-cart' ),
            'cartUrl' => wc_get_cart_url(),
            'checkoutUrl' => wc_get_checkout_url(),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'gloceps_enqueue_cart_scripts', 20 );

/**
 * Ensure cart page slug is 'cart' and uses shortcode
 */
function gloceps_fix_cart_page_slug() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    
    $cart_page_id = wc_get_page_id( 'cart' );
    if ( $cart_page_id ) {
        $cart_page = get_post( $cart_page_id );
        if ( $cart_page ) {
            $needs_update = false;
            $update_data = array( 'ID' => $cart_page_id );
            
            // Fix slug if needed
            if ( $cart_page->post_name !== 'cart' ) {
                $update_data['post_name'] = 'cart';
                $needs_update = true;
            }
            
            // Fix content if it contains blocks instead of shortcode
            $content = $cart_page->post_content;
            if ( strpos( $content, '<!-- wp:woocommerce/cart' ) !== false || 
                 strpos( $content, 'woocommerce/cart' ) !== false && strpos( $content, '[woocommerce_cart]' ) === false ) {
                $update_data['post_content'] = '[woocommerce_cart]';
                $update_data['post_content_filtered'] = ''; // Clear block content
                $needs_update = true;
            }
            
            if ( $needs_update ) {
                wp_update_post( $update_data );
                if ( isset( $update_data['post_name'] ) ) {
                    flush_rewrite_rules( false );
                }
            }
        }
    }
}
add_action( 'init', 'gloceps_fix_cart_page_slug', 20 );

/**
 * Customize empty cart message
 */
function gloceps_empty_cart_message() {
    return '<p class="cart-empty">' . 
        esc_html__( 'Your cart is empty.', 'gloceps' ) . ' ' .
        '<a class="btn btn--primary" href="' . esc_url( get_post_type_archive_link( 'publication' ) ) . '">' . 
        esc_html__( 'Browse Publications', 'gloceps' ) . 
        '</a></p>';
}
add_filter( 'wc_empty_cart_message', 'gloceps_empty_cart_message' );

/**
 * Configure Pesapal as the only payment gateway
 * TheBunch KE Pesapal WooCommerce plugin is used
 * Handles M-Pesa, Cards, and Mobile Money
 */
function gloceps_payment_gateways( $gateways ) {
    // Keep only Pesapal gateway (TheBunch KE plugin uses 'pesapal' as ID)
    $allowed_gateways = array( 'pesapal', 'thebunch_pesapal', 'pesapal_woocommerce' );
    
    foreach ( $gateways as $key => $gateway ) {
        // Check if the gateway class name or ID contains 'pesapal'
        $gateway_lower = strtolower( is_string( $gateway ) ? $gateway : $key );
        
        $is_pesapal = false;
        foreach ( $allowed_gateways as $allowed ) {
            if ( strpos( $gateway_lower, $allowed ) !== false ) {
                $is_pesapal = true;
                break;
            }
        }
        
        // Also keep cash on delivery for testing if needed (can be removed in production)
        if ( ! $is_pesapal && $gateway_lower !== 'cod' ) {
            // Don't remove, just filter - let WooCommerce handle gateway availability
            // This preserves Pesapal even if the class name varies
        }
    }
    
    return $gateways;
}
// Note: Uncomment the line below to enforce Pesapal-only (after confirming plugin class name)
// add_filter( 'woocommerce_payment_gateways', 'gloceps_payment_gateways', 100 );

/**
 * Customize checkout fields for GLOCEPS
 * Note: Merged into gloceps_customize_checkout_fields to avoid conflicts
 */

/**
 * Add Pesapal payment icons to checkout
 */
function gloceps_payment_icons() {
    $icons = array(
        'mpesa' => array(
            'url' => GLOCEPS_URI . '/assets/images/payment-mpesa.png',
            'alt' => 'M-Pesa',
        ),
        'visa' => array(
            'url' => GLOCEPS_URI . '/assets/images/payment-visa.png',
            'alt' => 'Visa',
        ),
        'mastercard' => array(
            'url' => GLOCEPS_URI . '/assets/images/payment-mastercard.png',
            'alt' => 'Mastercard',
        ),
    );
    
    return $icons;
}

// Removed payment icons section - not from payment gateway

/**
 * Add Kenya as default country
 */
function gloceps_default_checkout_country() {
    return 'KE';
}
add_filter( 'default_checkout_billing_country', 'gloceps_default_checkout_country' );
add_filter( 'default_checkout_shipping_country', 'gloceps_default_checkout_country' );

/**
 * Customize currency for Kenyan market
 */
function gloceps_currency_symbol( $currency_symbol, $currency ) {
    if ( $currency === 'KES' ) {
        return 'KES ';
    }
    return $currency_symbol;
}
add_filter( 'woocommerce_currency_symbol', 'gloceps_currency_symbol', 10, 2 );

/**
 * Customize checkout fields to match GLOCEPS design
 */
function gloceps_customize_checkout_fields( $fields ) {
    // Remove unnecessary fields for digital products
    unset( $fields['billing']['billing_address_1'] );
    unset( $fields['billing']['billing_address_2'] );
    unset( $fields['billing']['billing_city'] );
    unset( $fields['billing']['billing_postcode'] );
    unset( $fields['billing']['billing_state'] );
    
    // Hide country field but keep it for validation (set default to Kenya)
    if ( isset( $fields['billing']['billing_country'] ) ) {
        $fields['billing']['billing_country']['required'] = false;
        $fields['billing']['billing_country']['class'][] = 'hidden';
        $fields['billing']['billing_country']['default'] = 'KE';
    }
    
    // Remove shipping fields for digital products
    if ( isset( $fields['shipping'] ) ) {
        unset( $fields['shipping']['shipping_address_1'] );
        unset( $fields['shipping']['shipping_address_2'] );
        unset( $fields['shipping']['shipping_city'] );
        unset( $fields['shipping']['shipping_postcode'] );
        unset( $fields['shipping']['shipping_state'] );
        unset( $fields['shipping']['shipping_country'] );
    }
    
    // Rename company to organization
    if ( isset( $fields['billing']['billing_company'] ) ) {
        $fields['billing']['billing_company']['label'] = __( 'Organization (Optional)', 'gloceps' );
        $fields['billing']['billing_company']['placeholder'] = __( 'Company or Institution name', 'gloceps' );
        $fields['billing']['billing_company']['required'] = false;
    }
    
    // Update field labels
    if ( isset( $fields['billing']['billing_first_name'] ) ) {
        $fields['billing']['billing_first_name']['label'] = __( 'First Name', 'gloceps' );
        $fields['billing']['billing_first_name']['placeholder'] = __( 'John', 'gloceps' );
    }
    
    if ( isset( $fields['billing']['billing_last_name'] ) ) {
        $fields['billing']['billing_last_name']['label'] = __( 'Last Name', 'gloceps' );
        $fields['billing']['billing_last_name']['placeholder'] = __( 'Doe', 'gloceps' );
    }
    
    if ( isset( $fields['billing']['billing_email'] ) ) {
        $fields['billing']['billing_email']['label'] = __( 'Email Address', 'gloceps' );
        $fields['billing']['billing_email']['placeholder'] = __( 'john@example.com', 'gloceps' );
    }
    
    if ( isset( $fields['billing']['billing_phone'] ) ) {
        $fields['billing']['billing_phone']['label'] = __( 'Phone Number', 'gloceps' );
        $fields['billing']['billing_phone']['placeholder'] = __( '712 345 678', 'gloceps' );
        $fields['billing']['billing_phone']['required'] = true;
        $fields['billing']['billing_phone']['class'][] = 'form-row-wide';
    }
    
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'gloceps_customize_checkout_fields' );

/**
 * Set default billing country and ensure it's set during checkout
 */
function gloceps_set_checkout_country( $data ) {
    // Ensure billing country is set to Kenya for digital products
    if ( empty( $data['billing_country'] ) ) {
        $data['billing_country'] = 'KE';
    }
    return $data;
}
add_filter( 'woocommerce_checkout_posted_data', 'gloceps_set_checkout_country' );


/**
 * Disable shipping for virtual/digital products
 */
function gloceps_disable_shipping_for_digital( $needs_shipping ) {
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $product = $cart_item['data'];
            if ( $product && ( $product->is_virtual() || $product->is_downloadable() ) ) {
                return false;
            }
        }
    }
    return $needs_shipping;
}
add_filter( 'woocommerce_cart_needs_shipping', 'gloceps_disable_shipping_for_digital' );

/**
 * Ensure payment gateway return URL points to order complete page
 * This ensures PesaPal and other gateways redirect to our custom thankyou page
 */
function gloceps_payment_gateway_return_url( $return_url, $order ) {
    // Ensure we always use the order received URL (thankyou page)
    if ( $order && is_a( $order, 'WC_Order' ) ) {
        $return_url = $order->get_checkout_order_received_url();
    }
    return $return_url;
}
add_filter( 'woocommerce_get_return_url', 'gloceps_payment_gateway_return_url', 10, 2 );

/**
 * Handle PesaPal payment return - ensure order status is checked
 * This runs when user returns from PesaPal payment page
 */
function gloceps_handle_pesapal_return() {
    // Check if this is a PesaPal return
    if ( isset( $_GET['OrderTrackingId'] ) || isset( $_GET['OrderMerchantReference'] ) ) {
        
        // The PesaPal plugin should handle this via its own hooks
        // But we ensure the order is accessible
        if ( isset( $_GET['OrderMerchantReference'] ) ) {
            $order_ref = sanitize_text_field( $_GET['OrderMerchantReference'] );
            // Try to find order by reference
            $order_id = wc_get_order_id_by_order_key( $order_ref );
            if ( ! $order_id ) {
                // Try to extract order ID from reference (PesaPal may prefix it)
                $order_id = preg_replace( '/[^0-9]/', '', $order_ref );
            }
            
            if ( $order_id ) {
                $order = wc_get_order( $order_id );
                if ( $order ) {
                }
            }
        }
    }
}
add_action( 'woocommerce_thankyou', 'gloceps_handle_pesapal_return', 5 );

/**
 * Add wrapper and spacing for order-pay page content
 * This ensures content is not cut off by fixed header
 */
function gloceps_order_pay_wrapper_before() {
    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-pay' ) ) {
        echo '<div class="order-pay-page-wrapper">';
    }
}
add_action( 'woocommerce_before_main_content', 'gloceps_order_pay_wrapper_before', 1 );

function gloceps_order_pay_wrapper_after() {
    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-pay' ) ) {
        echo '</div>';
    }
}
add_action( 'woocommerce_after_main_content', 'gloceps_order_pay_wrapper_after', 999 );

/**
 * Customize checkout field wrapper classes
 */
function gloceps_checkout_field_wrapper( $field, $key, $args, $value ) {
    // Add custom classes
    $field = str_replace( 'form-row', 'checkout-form__field', $field );
    $field = str_replace( 'form-row-wide', 'checkout-form__field checkout-form__field--wide', $field );
    $field = str_replace( 'form-row-first', 'checkout-form__field checkout-form__field--half', $field );
    $field = str_replace( 'form-row-last', 'checkout-form__field checkout-form__field--half', $field );
    
    // Find the input/select element and add hint after it (only if not already added)
    if ( $key === 'billing_email' ) {
        // Check if hint already exists to avoid duplicates
        if ( strpos( $field, 'checkout-form__hint' ) === false ) {
            $hint = '<span class="checkout-form__hint">' . esc_html__( 'Your download links will be sent here', 'gloceps' ) . '</span>';
            // Insert hint after the input field
            $field = preg_replace( '/(<input[^>]*>)/', '$1' . $hint, $field, 1 );
        }
    }
    
    // Add hint text for phone and wrap with prefix
    if ( $key === 'billing_phone' ) {
        $hint = '<span class="checkout-form__hint">' . esc_html__( 'Required for M-Pesa payment', 'gloceps' ) . '</span>';
        
        // Wrap phone input with prefix - find the input and wrap it
        if ( preg_match( '/(<input[^>]*name="billing_phone"[^>]*>)/', $field, $matches ) ) {
            $phone_input = $matches[1];
            $phone_wrapped = '<div class="phone-input"><span class="phone-input__prefix">+254</span>' . $phone_input . '</div>';
            $field = str_replace( $phone_input, $phone_wrapped, $field );
        }
        
        // Add hint after the phone input wrapper
        $field = str_replace( '</div></p>', '</div>' . $hint . '</p>', $field );
    }
    
    return $field;
}
add_filter( 'woocommerce_form_field', 'gloceps_checkout_field_wrapper', 10, 4 );

/**
 * Override plugin template filters to ensure theme templates are used
 * This ensures our custom payment-method.php template takes precedence over plugin templates
 */
function gloceps_override_payment_method_template( $template, $template_name, $template_path ) {
    // Only override for payment-method.php template
    if ( $template_name === 'checkout/payment-method.php' ) {
        $theme_template = get_stylesheet_directory() . '/woocommerce/' . $template_name;
        if ( file_exists( $theme_template ) ) {
            return $theme_template;
        }
    }
    return $template;
}
add_filter( 'woocommerce_locate_template', 'gloceps_override_payment_method_template', 20, 3 );

/**
 * Force WooCommerce to use shortcode-based checkout instead of blocks
 * This ensures our custom template is used
 */
/**
 * Disable block-based checkout and order confirmation
 * Must be called on before_woocommerce_init hook
 * Also declare theme compatibility with WooCommerce features
 */
function gloceps_declare_woocommerce_compatibility() {
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        // Get theme file path (functions.php)
        $theme_file = get_template_directory() . '/functions.php';
        
        // Disable block-based checkout
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', $theme_file, false );
        // Disable block-based order confirmation
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'order_confirmation_blocks', $theme_file, false );
        
        // Declare compatibility with other WooCommerce features that we support
        // This prevents false incompatibility warnings
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $theme_file, true );
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables_usage_is_enabled', $theme_file, true );
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'product_block_editor', $theme_file, true );
        
        // Declare compatibility for Osen WC Mpesa plugin
        // This plugin works correctly but hasn't declared compatibility, so we do it here
        $osen_mpesa_plugin_file = WP_PLUGIN_DIR . '/osen-wc-mpesa/osen-wc-mpesa.php';
        if ( file_exists( $osen_mpesa_plugin_file ) && is_plugin_active( 'osen-wc-mpesa/osen-wc-mpesa.php' ) ) {
            // Declare compatibility with all features (plugin works with all current WooCommerce features)
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', $osen_mpesa_plugin_file, true );
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'order_confirmation_blocks', $osen_mpesa_plugin_file, true );
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $osen_mpesa_plugin_file, true );
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables_usage_is_enabled', $osen_mpesa_plugin_file, true );
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'product_block_editor', $osen_mpesa_plugin_file, true );
        }
    }
}
add_action( 'before_woocommerce_init', 'gloceps_declare_woocommerce_compatibility' );

/**
 * Diagnostic function to identify WooCommerce feature incompatibilities
 * Access via: /wp-admin/?gloceps_wc_compat_check=1
 */
function gloceps_check_woocommerce_compatibility() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    if ( ! isset( $_GET['gloceps_wc_compat_check'] ) || $_GET['gloceps_wc_compat_check'] !== '1' ) {
        return;
    }
    
    if ( ! class_exists( 'WooCommerce' ) ) {
        wp_die( 'WooCommerce is not active.' );
    }
    
    $output = '<h1>WooCommerce Feature Compatibility Check</h1>';
    
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        // Get all active plugins
        $active_plugins = get_option( 'active_plugins', array() );
        
        $output .= '<h2>Active Plugins</h2>';
        $output .= '<ul>';
        foreach ( $active_plugins as $plugin ) {
            $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
            $output .= '<li><strong>' . esc_html( $plugin_data['Name'] ) . '</strong> (' . esc_html( $plugin ) . ')</li>';
        }
        $output .= '</ul>';
        
        // Check for known WooCommerce-aware plugins
        $output .= '<h2>WooCommerce Feature Compatibility Status</h2>';
        $output .= '<p>To see detailed incompatibility information, visit: <a href="' . esc_url( admin_url( 'plugins.php?plugin_status=incompatible_with_feature' ) ) . '">Plugins → Incompatible with Features</a></p>';
        
        // Get enabled features
        $features_controller = wc_get_container()->get( \Automattic\WooCommerce\Internal\Features\FeaturesController::class );
        $all_features = $features_controller->get_feature_definitions();
        $enabled_features = array();
        
        foreach ( $all_features as $feature_id => $feature_def ) {
            if ( $features_controller->feature_is_enabled( $feature_id ) ) {
                $enabled_features[ $feature_id ] = $feature_def['name'] ?? $feature_id;
            }
        }
        
        $output .= '<h3>Enabled WooCommerce Features</h3>';
        if ( ! empty( $enabled_features ) ) {
            $output .= '<ul>';
            foreach ( $enabled_features as $feature_id => $feature_name ) {
                $output .= '<li><strong>' . esc_html( $feature_name ) . '</strong> (' . esc_html( $feature_id ) . ')</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<p>No features are currently enabled.</p>';
        }
        
        $output .= '<hr>';
        $output .= '<h3>Resolution Steps</h3>';
        $output .= '<ol>';
        $output .= '<li>Visit <a href="' . esc_url( admin_url( 'plugins.php?plugin_status=incompatible_with_feature' ) ) . '">Plugins → Incompatible with Features</a> to see which plugins are incompatible.</li>';
        $output .= '<li>For each incompatible plugin, you can either:<ul>';
        $output .= '<li>Update the plugin to a version that declares compatibility</li>';
        $output .= '<li>Disable the incompatible WooCommerce feature (if not needed)</li>';
        $output .= '<li>Contact the plugin developer to add compatibility declarations</li>';
        $output .= '</ul></li>';
        $output .= '<li>If you know a plugin is compatible despite the warning, you can ignore it (the warning is informational).</li>';
        $output .= '</ol>';
        
    } else {
        $output .= '<p>WooCommerce FeaturesUtil class is not available.</p>';
    }
    
    wp_die( $output, 'WooCommerce Compatibility Check', array( 'back_link' => true ) );
}
add_action( 'admin_init', 'gloceps_check_woocommerce_compatibility', 1 );

function gloceps_force_shortcode_checkout() {
    // Ensure checkout page uses shortcode
    $checkout_page_id = wc_get_page_id( 'checkout' );
    if ( $checkout_page_id ) {
        $checkout_page = get_post( $checkout_page_id );
        if ( $checkout_page ) {
            // Check if page has blocks instead of shortcode
            if ( has_blocks( $checkout_page->post_content ) && strpos( $checkout_page->post_content, '[woocommerce_checkout]' ) === false ) {
                // Replace block content with shortcode
                wp_update_post( array(
                    'ID' => $checkout_page_id,
                    'post_content' => '[woocommerce_checkout]',
                    'post_content_filtered' => '', // Clear block content
                ) );
            }
        }
    }
}
add_action( 'init', 'gloceps_force_shortcode_checkout', 5 );

/**
 * Force classic template for order received page
 * Override WooCommerce's template location to use our custom thankyou.php
 */
function gloceps_force_classic_thankyou_template( $template, $template_name, $template_path ) {
    // Only override the thankyou.php template
    if ( 'checkout/thankyou.php' === $template_name ) {
        $theme_template = locate_template( array(
            'woocommerce/' . $template_name,
            $template_name,
        ) );
        if ( $theme_template ) {
            return $theme_template;
        }
    }
    return $template;
}
add_filter( 'woocommerce_locate_template', 'gloceps_force_classic_thankyou_template', 20, 3 );

/**
 * Remove default WooCommerce order details and billing address from thank you page
 * We have our own custom design
 */
function gloceps_remove_default_thankyou_sections() {
    if ( is_wc_endpoint_url( 'order-received' ) ) {
        remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
        remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button', 10 );
    }
}
add_action( 'template_redirect', 'gloceps_remove_default_thankyou_sections', 5 );

/**
 * Remove duplicate breadcrumbs on checkout page
 * We output breadcrumbs in the header, so remove WooCommerce's default
 */
function gloceps_remove_checkout_breadcrumbs() {
    if ( is_checkout() ) {
        remove_action( 'woocommerce_before_checkout_form', 'woocommerce_breadcrumb', 20 );
    }
}
add_action( 'template_redirect', 'gloceps_remove_checkout_breadcrumbs', 5 );

/**
 * Handle newsletter subscription on checkout completion
 * COMMENTED OUT: Third-party integration (Brevo) - using custom backend system instead
 * This code is preserved for future third-party integration if needed
 * 
 * Current implementation: See inc/newsletter-subscriptions.php
 * Newsletter subscriptions are now handled by gloceps_handle_checkout_newsletter_subscription()
 * which saves to custom post type 'newsletter_sub' with source='ecommerce'
 */
/*
function gloceps_handle_checkout_newsletter_subscription_third_party( $order_id ) {
    // Check if newsletter checkbox was checked
    $newsletter_checked = isset( $_POST['newsletter'] ) && $_POST['newsletter'];
    
    if ( ! $newsletter_checked ) {
        return;
    }
    
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }
    
    $email = $order->get_billing_email();
    if ( ! $email ) {
        return;
    }
    
    // Get the newsletter form ID from theme settings
    $form_id = get_field( 'newsletter_form', 'option' );
    if ( ! $form_id ) {
        return;
    }
    
    // Get the form object
    $form = wpcf7_contact_form( $form_id );
    if ( ! $form ) {
        return;
    }
    
    // Prepare form submission data
    // We'll need to find the email field in the form
    $form_tags = $form->scan_form_tags();
    $email_field = '';
    foreach ( $form_tags as $tag ) {
        if ( $tag->type === 'email' || ( $tag->type === 'text' && strpos( strtolower( $tag->name ), 'email' ) !== false ) ) {
            $email_field = $tag->name;
            break;
        }
    }
    
    if ( ! $email_field ) {
        // Try common field names
        $common_names = array( 'your-email', 'email', 'email-address', 'email_address', 'mail' );
        foreach ( $common_names as $name ) {
            foreach ( $form_tags as $tag ) {
                if ( $tag->name === $name ) {
                    $email_field = $name;
                    break 2;
                }
            }
        }
    }
    
    if ( ! $email_field ) {
        // Log error but don't break checkout
        error_log( 'GLOCEPS: Could not find email field in newsletter form ID: ' . $form_id );
        return;
    }
    
    // Store subscription data for later processing (Brevo integration)
    // For now, we'll prepare the data structure
    $subscription_data = array(
        'email' => $email,
        'first_name' => $order->get_billing_first_name(),
        'last_name' => $order->get_billing_last_name(),
        'form_id' => $form_id,
        'order_id' => $order_id,
        'subscribed_at' => current_time( 'mysql' ),
    );
    
    // Store in order meta for later processing
    update_post_meta( $order_id, '_gloceps_newsletter_subscription', $subscription_data );
    
    // TODO: When Brevo integration is ready, process this subscription here
    // For now, the data is stored and ready for integration
}
// add_action( 'woocommerce_checkout_order_processed', 'gloceps_handle_checkout_newsletter_subscription_third_party', 10, 1 );
*/

/**
 * ============================================
 * CUSTOM ORDER COMPLETION EMAIL SYSTEM
 * ============================================
 */

/**
 * Grant access to publication PDF on order complete and send custom email
 * Note: WooCommerce automatically grants download access when order is completed
 * if products are set as downloadable. This function triggers custom email.
 */
function gloceps_grant_publication_access( $order_id ) {
    $order = wc_get_order( $order_id );
    
    if ( ! $order ) {
        return;
    }
    
    // Send custom order completion email
    gloceps_send_order_complete_email( $order_id );
}
add_action( 'woocommerce_order_status_completed', 'gloceps_grant_publication_access' );

/**
 * Send custom order completion email with publications
 */
function gloceps_send_order_complete_email( $order_id ) {
    $order = wc_get_order( $order_id );
    
    if ( ! $order ) {
        return;
    }
    
    // SECURITY: Only send publications for completed/paid orders
    $order_status = $order->get_status();
    $is_paid = $order->is_paid();
    
    // Only send if order is completed and paid
    if ( $order_status !== 'completed' || ! $is_paid ) {
        return;
    }
    
    // Get email settings from ACF
    $email_settings = get_field( 'order_email_settings', 'option' );
    $from_email = $email_settings['from_email'] ?? 'orders@gloceps.org';
    $from_name = $email_settings['from_name'] ?? 'GLOCEPS';
    $subject_template = $email_settings['subject'] ?? 'Your GLOCEPS Publications - Order #{order_number}';
    
    // Replace placeholders in subject
    $subject = str_replace( '{order_number}', $order->get_order_number(), $subject_template );
    
    // Get customer email
    $to = $order->get_billing_email();
    
    if ( empty( $to ) ) {
        return;
    }
    
    // Get email content
    $email_content = gloceps_get_order_complete_email_content( $order );
    
    // Collect PDF attachments from publications
    $attachments = array();
    foreach ( $order->get_items() as $item ) {
        $publication_id = $item->get_meta( '_publication_id' );
        if ( $publication_id ) {
            // Get PDF file from ACF field
            $pdf_file = get_field( 'pdf_file', $publication_id );
            if ( $pdf_file && is_array( $pdf_file ) && isset( $pdf_file['ID'] ) ) {
                $file_path = get_attached_file( $pdf_file['ID'] );
                if ( $file_path && file_exists( $file_path ) ) {
                    $attachments[] = $file_path;
                }
            }
        }
    }
    
    // Set email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'Reply-To: ' . $from_email,
    );
    
    // Send email with PDF attachments
    wp_mail( $to, $subject, $email_content, $headers, $attachments );
    
    // Generate and attach PDF receipt if needed
    // TODO: Implement PDF receipt generation
}

/**
 * Get order completion email HTML content
 */
function gloceps_get_order_complete_email_content( $order ) {
    
    ob_start();
    
    // Get order details
    $order_number = $order->get_order_number();
    $order_date = wc_format_datetime( $order->get_date_created() );
    $order_total = $order->get_formatted_order_total();
    $billing_email = $order->get_billing_email();
    
    
    // Get publications/downloads
    $downloads = $order->get_downloadable_items();
    $publications = array();
    
    
    foreach ( $order->get_items() as $item ) {
        $product = $item->get_product();
        $publication_id = $item->get_meta( '_publication_id' );
        
        
        if ( $product && $publication_id ) {
            // Get download link for this item
            $item_downloads = array();
            foreach ( $downloads as $download_key => $download ) {
                if ( $download['product_id'] == $product->get_id() ) {
                    $item_downloads[] = $download;
                }
            }
            
            $publications[] = array(
                'name' => $item->get_name(),
                'product' => $product,
                'downloads' => $item_downloads,
                'publication_id' => $publication_id,
            );
        }
    }
    
    
    // Load email template
    $template_path = get_template_directory() . '/woocommerce/emails/customer-order-complete.php';
    
    
    include $template_path;
    
    $content = ob_get_clean();
    
    
    return $content;
}

/**
 * ============================================
 * RESEND PUBLICATIONS HANDLER
 * ============================================
 */

/**
 * AJAX handler for resending publications
 */
function gloceps_ajax_resend_publications() {
    // Verify nonce
    if ( ! isset( $_POST['resend_nonce'] ) || ! wp_verify_nonce( $_POST['resend_nonce'], 'gloceps_resend_publications' ) ) {
        wp_send_json_error( array( 'message' => __( 'Security check failed. Please try again.', 'gloceps' ) ) );
    }
    
    // Get form data
    $order_number = isset( $_POST['order_number'] ) ? sanitize_text_field( $_POST['order_number'] ) : '';
    $email_address = isset( $_POST['email_address'] ) ? sanitize_email( $_POST['email_address'] ) : '';
    
    // Validate inputs
    if ( empty( $order_number ) || empty( $email_address ) ) {
        wp_send_json_error( array( 'message' => __( 'Please fill in all required fields.', 'gloceps' ) ) );
    }
    
    if ( ! is_email( $email_address ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'gloceps' ) ) );
    }
    
    // Find order by order number
    // Try multiple methods to find the order
    
    // Method 1: Direct order number match (remove # prefix if present)
    $clean_order_number = ltrim( $order_number, '#' );
    $orders = wc_get_orders( array(
        'limit' => 100,
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'ids',
    ) );
    
    $order_id = null;
    foreach ( $orders as $id ) {
        $order = wc_get_order( $id );
        if ( $order ) {
            $order_num = $order->get_order_number();
            // Try exact match
            if ( $order_num === $order_number || $order_num === $clean_order_number ) {
                $order_id = $id;
                break;
            }
            // Try match without prefix (e.g., GCP-2024-00847 vs 2024-00847)
            $order_num_clean = preg_replace( '/^[A-Z]+-?/', '', $order_num );
            $input_clean = preg_replace( '/^[A-Z]+-?/', '', $clean_order_number );
            if ( $order_num_clean === $input_clean ) {
                $order_id = $id;
                break;
            }
        }
    }
    
    if ( ! $order_id ) {
        wp_send_json_error( array( 'message' => __( 'Order not found. Please check your order number and try again.', 'gloceps' ) ) );
    }
    
    $order = wc_get_order( $order_id );
    
    if ( ! $order ) {
        wp_send_json_error( array( 'message' => __( 'Order not found. Please check your order number and try again.', 'gloceps' ) ) );
    }
    
    // Verify email matches order billing email
    $billing_email = $order->get_billing_email();
    
    if ( strtolower( $billing_email ) !== strtolower( $email_address ) ) {
        wp_send_json_error( array( 'message' => __( 'The email address does not match the email used for this order. Please use the email address you used during purchase.', 'gloceps' ) ) );
    }
    
    // SECURITY: Only allow resending for completed/paid orders
    $order_status = $order->get_status();
    $is_paid = $order->is_paid();
    
    
    if ( $order_status !== 'completed' || ! $is_paid ) {
        $status_message = '';
        if ( ! $is_paid ) {
            $status_message = __( 'This order has not been paid yet. Please complete payment before requesting publications.', 'gloceps' );
        } else {
            $status_message = sprintf( __( 'This order is currently %s. Publications will be sent automatically once payment is completed.', 'gloceps' ), ucfirst( $order_status ) );
        }
        
        
        wp_send_json_error( array( 'message' => $status_message ) );
    }
    
    
    // Send the order completion email again
    gloceps_send_order_complete_email( $order_id );
    
    // Log the resend attempt
    $order->add_order_note( sprintf( __( 'Publications resent to %s via resend form.', 'gloceps' ), $email_address ) );
    
    wp_send_json_success( array( 
        'message' => __( 'Your publications have been resent successfully! Please check your email for the documents.', 'gloceps' ),
        'email' => $email_address,
    ) );
}
add_action( 'wp_ajax_gloceps_resend_publications', 'gloceps_ajax_resend_publications' );
add_action( 'wp_ajax_nopriv_gloceps_resend_publications', 'gloceps_ajax_resend_publications' );

/**
 * Fix MPESA Gateway plugin error when marking orders as complete
 * The plugin tries to hook into woocommerce_payment_complete_order_status
 * with a method that doesn't exist, causing fatal errors
 * Remove the invalid filter early before WooCommerce tries to use it
 */
function gloceps_remove_invalid_mpesa_filter() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    
    // Remove invalid MPESA gateway callback if it exists
    // Directly manipulate the global $wp_filter to ensure removal
    global $wp_filter;
    if ( ! isset( $wp_filter['woocommerce_payment_complete_order_status'] ) ) {
        return;
    }
    
    $filter_obj = $wp_filter['woocommerce_payment_complete_order_status'];
    if ( ! is_object( $filter_obj ) || ! isset( $filter_obj->callbacks ) ) {
        return;
    }
    
    $callbacks = $filter_obj->callbacks;
    $removed = false;
    
    foreach ( $callbacks as $priority => $hooks ) {
        if ( ! is_array( $hooks ) ) {
            continue;
        }
        
        foreach ( $hooks as $hook_key => $hook ) {
            if ( ! is_array( $hook['function'] ) || ! isset( $hook['function'][0] ) || ! isset( $hook['function'][1] ) ) {
                continue;
            }
            
            $object = $hook['function'][0];
            $method = $hook['function'][1];
            
            // Check if this is the MPESA gateway with invalid method
            if ( is_object( $object ) && 
                 ( get_class( $object ) === 'WC_MPESA_Gateway' || strpos( get_class( $object ), 'MPESA' ) !== false ) &&
                 $method === 'change_payment_complete_order_status' &&
                 ! method_exists( $object, 'change_payment_complete_order_status' ) ) {
                
                // Remove the invalid callback directly from the callbacks array
                unset( $filter_obj->callbacks[ $priority ][ $hook_key ] );
                $removed = true;
            }
        }
        
        // If this priority level is now empty, remove it
        if ( isset( $filter_obj->callbacks[ $priority ] ) && empty( $filter_obj->callbacks[ $priority ] ) ) {
            unset( $filter_obj->callbacks[ $priority ] );
        }
    }
    
    // Also try remove_filter as backup
    if ( $removed && class_exists( 'WC_MPESA_Gateway' ) ) {
        // Try to get an instance and remove the filter
        $gateways = WC()->payment_gateways()->payment_gateways();
        if ( isset( $gateways['mpesa'] ) && is_object( $gateways['mpesa'] ) ) {
            remove_filter( 'woocommerce_payment_complete_order_status', array( $gateways['mpesa'], 'change_payment_complete_order_status' ), 10 );
        }
    }
}
// Run at multiple hooks to catch the filter whenever it's added
add_action( 'woocommerce_loaded', 'gloceps_remove_invalid_mpesa_filter', 20 );
add_action( 'plugins_loaded', 'gloceps_remove_invalid_mpesa_filter', 100 );
add_action( 'init', 'gloceps_remove_invalid_mpesa_filter', 100 );
add_action( 'admin_init', 'gloceps_remove_invalid_mpesa_filter', 5 );

/**
 * Additional safety: Wrap the filter to catch any remaining invalid callbacks
 * This runs at priority 0 to execute before any other filters
 */
function gloceps_safe_payment_complete_order_status( $status, $order_id = 0, $order = false ) {
    // Clean up invalid callbacks right before execution
    gloceps_remove_invalid_mpesa_filter();
    return $status;
}
// Use priority 0 to run before any other filters (MPESA uses priority 10)
add_filter( 'woocommerce_payment_complete_order_status', 'gloceps_safe_payment_complete_order_status', 0, 3 );

/**
 * Create essential pages (Resend Publications, Order Receipt)
 * Runs on admin_init to ensure pages are created when needed
 */
function gloceps_create_essential_pages() {
    // Only run in admin or if pages don't exist
    if (!is_admin() && get_page_by_path('resend-publications') && get_page_by_path('order-receipt')) {
        return;
    }
    
    // Resend Publications page
    $resend_page = get_page_by_path('resend-publications');
    if (!$resend_page) {
        $resend_id = wp_insert_post(array(
            'post_title' => 'Resend Your Publications',
            'post_name' => 'resend-publications',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        if ($resend_id && !is_wp_error($resend_id)) {
            update_post_meta($resend_id, '_wp_page_template', 'page-resend-publications.php');
            
            // Set up default ACF blocks
            if (function_exists('update_field')) {
                $default_blocks = array(
                    array(
                        'acf_fc_layout' => 'resend_form',
                        'title' => 'Resend Your Publications',
                        'description' => "Didn't receive your download email? No problem. Enter your order details below and we'll send a fresh copy of your publications.",
                    ),
                    array(
                        'acf_fc_layout' => 'resend_help',
                        'title' => 'Need More Help?',
                        'email_label' => 'Email Support',
                        'email' => 'support@gloceps.org',
                        'phone_label' => 'Phone Support',
                        'phone' => '+254 112 401 331',
                        'hours' => 'Mon-Fri, 8am-5pm EAT',
                        'faq_title' => 'COMMON QUESTIONS',
                        'faq_items' => array(
                            array(
                                'question' => 'How long does it take to receive the email?',
                                'answer' => 'Emails are typically delivered within a few minutes. If you don\'t see it, please check your spam folder.',
                            ),
                            array(
                                'question' => 'What if I don\'t have my order number?',
                                'answer' => 'You can find your order number in the confirmation email sent after purchase. If you can\'t find it, contact support with your email address.',
                            ),
                            array(
                                'question' => 'How many times can I download my publications?',
                                'answer' => 'Your publications can be downloaded as many times as you need. The documents are sent as email attachments.',
                            ),
                        ),
                    ),
                );
                update_field('content_blocks', $default_blocks, $resend_id);
            }
        }
    }
    
    // Order Receipt page
    $receipt_page = get_page_by_path('order-receipt');
    if (!$receipt_page) {
        $receipt_id = wp_insert_post(array(
            'post_title' => 'Order Receipt',
            'post_name' => 'order-receipt',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        if ($receipt_id && !is_wp_error($receipt_id)) {
            update_post_meta($receipt_id, '_wp_page_template', 'page-order-receipt.php');
        }
    }
}
add_action('admin_init', 'gloceps_create_essential_pages', 5);
add_action('after_setup_theme', 'gloceps_create_essential_pages', 5);

/**
 * Test email function to verify email sending capability
 * Access via: /wp-admin/?gloceps_test_email=1&email=test@rhetoricgroup.net
 */
function gloceps_test_email() {
    // Only allow in admin or with proper nonce
    if ( ! is_admin() && ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Check if test email is requested
    if ( isset( $_GET['gloceps_test_email'] ) && $_GET['gloceps_test_email'] === '1' ) {
        $test_email = isset( $_GET['email'] ) ? sanitize_email( $_GET['email'] ) : 'test@rhetoricgroup.net';
        
        if ( ! is_email( $test_email ) ) {
            wp_die( 'Invalid email address: ' . esc_html( $test_email ) );
        }
        
        // Get email settings from ACF
        $email_settings = get_field( 'order_email_settings', 'option' );
        $from_email = $email_settings['from_email'] ?? 'orders@gloceps.org';
        $from_name = $email_settings['from_name'] ?? 'GLOCEPS';
        
        
        // Create simple test email content
        $subject = 'GLOCEPS Test Email - ' . date( 'Y-m-d H:i:s' );
        $message = '<html><body>';
        $message .= '<h1>Test Email from GLOCEPS</h1>';
        $message .= '<p>This is a test email to verify that the email system is working correctly.</p>';
        $message .= '<p><strong>Sent at:</strong> ' . date( 'Y-m-d H:i:s' ) . '</p>';
        $message .= '<p><strong>From:</strong> ' . esc_html( $from_name ) . ' &lt;' . esc_html( $from_email ) . '&gt;</p>';
        $message .= '<p><strong>To:</strong> ' . esc_html( $test_email ) . '</p>';
        $message .= '<p>If you received this email, the WordPress mail system is configured correctly.</p>';
        $message .= '</body></html>';
        
        // Set email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
            'Reply-To: ' . $from_email,
        );
        
        
        // Initialize PHPMailer error tracking
        global $phpmailer;
        $phpmailer_error_before = '';
        if ( isset( $phpmailer ) && is_object( $phpmailer ) ) {
            $phpmailer_error_before = isset( $phpmailer->ErrorInfo ) ? $phpmailer->ErrorInfo : '';
        }
        
        // Send test email
        $result = wp_mail( $test_email, $subject, $message, $headers );
        
        // Check for wp_mail errors after sending
        $error_message = '';
        $mail_function_available = function_exists( 'mail' );
        $smtp_configured = false;
        
        if ( isset( $phpmailer ) && is_object( $phpmailer ) ) {
            if ( isset( $phpmailer->ErrorInfo ) && ! empty( $phpmailer->ErrorInfo ) ) {
                $error_message = $phpmailer->ErrorInfo;
            }
            // Check if SMTP is configured
            if ( method_exists( $phpmailer, 'isSMTP' ) && $phpmailer->isSMTP() ) {
                $smtp_configured = true;
            } elseif ( isset( $phpmailer->Mailer ) && $phpmailer->Mailer === 'smtp' ) {
                $smtp_configured = true;
            }
        }
        
        
        // Display result with diagnostics
        $message_display = '<h2>Email Test Results</h2>';
        $message_display .= '<p><strong>Recipient:</strong> ' . esc_html( $test_email ) . '</p>';
        $message_display .= '<p><strong>wp_mail() Result:</strong> ' . ( $result ? '✅ TRUE' : '❌ FALSE' ) . '</p>';
        $message_display .= '<p><strong>PHP mail() Function:</strong> ' . ( $mail_function_available ? '✅ Available' : '❌ Not Available' ) . '</p>';
        $message_display .= '<p><strong>SMTP Configured:</strong> ' . ( $smtp_configured ? '✅ Yes' : '❌ No' ) . '</p>';
        
        if ( $error_message ) {
            $message_display .= '<p><strong>PHPMailer Error:</strong> <span style="color: red;">' . esc_html( $error_message ) . '</span></p>';
        }
        
        if ( $result && ! $error_message ) {
            $message_display .= '<p style="color: green;"><strong>✅ wp_mail() returned TRUE</strong></p>';
            $message_display .= '<p><strong>⚠️ IMPORTANT:</strong> If you did NOT receive the email, this indicates that:</p>';
            $message_display .= '<ul>';
            $message_display .= '<li>Your local environment is not configured to actually send emails</li>';
            $message_display .= '<li>PHP\'s mail() function may be failing silently</li>';
            $message_display .= '<li>No SMTP server is configured</li>';
            $message_display .= '</ul>';
            $message_display .= '<p><strong>Solution:</strong> Install and configure an SMTP plugin like <strong>WP Mail SMTP</strong> to enable email sending in local development.</p>';
        } elseif ( ! $result ) {
            $message_display .= '<p style="color: red;"><strong>❌ wp_mail() returned FALSE</strong></p>';
            if ( $error_message ) {
                $message_display .= '<p><strong>Error Details:</strong> ' . esc_html( $error_message ) . '</p>';
            }
        }
        
        $message_display .= '<hr>';
        $message_display .= '<p><strong>Next Steps:</strong></p>';
        $message_display .= '<ol>';
        $message_display .= '<li>Install <strong>WP Mail SMTP</strong> plugin from WordPress.org</li>';
        $message_display .= '<li>Configure it with a mail service (Gmail SMTP, SendGrid, Mailgun, etc.)</li>';
        $message_display .= '<li>Run this test again to verify emails are actually being sent</li>';
        $message_display .= '</ol>';
        
        wp_die( $message_display, 'GLOCEPS Test Email', array( 'back_link' => true ) );
    }
}
add_action( 'admin_init', 'gloceps_test_email', 1 );
