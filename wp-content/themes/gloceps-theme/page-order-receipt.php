<?php
/**
 * Template Name: Order Receipt
 * 
 * Custom receipt page for WooCommerce orders
 * Shows true order status and all order details
 * 
 * Access via: /order-receipt/?order_id=XXX&key=XXX
 *
 * @package GLOCEPS
 */

get_header();

// Get order from query params
$order_id = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
$order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : '';

// Also try to get from query var if rewrite rule is used
if ( ! $order_id && get_query_var( 'order_id' ) ) {
    $order_id = absint( get_query_var( 'order_id' ) );
}
if ( ! $order_key && get_query_var( 'order_key' ) ) {
    $order_key = wc_clean( get_query_var( 'order_key' ) );
}

$order = null;
if ( $order_id > 0 ) {
    $order = wc_get_order( $order_id );
    if ( $order && ! hash_equals( $order->get_order_key(), $order_key ) ) {
        $order = null;
    }
}

if ( ! $order ) {
    ?>
    <section class="order-receipt">
        <div class="container">
            <div class="order-receipt__error">
                <h1><?php esc_html_e( 'Order Not Found', 'gloceps' ); ?></h1>
                <p><?php esc_html_e( 'The order you are looking for could not be found.', 'gloceps' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Return to Home', 'gloceps' ); ?>
                </a>
            </div>
        </div>
    </section>
    <?php
    get_footer();
    return;
}

// Get order status
$order_status = $order->get_status();
$status_label = wc_get_order_status_name( $order_status );
$is_paid = $order->is_paid();
?>

<section class="order-receipt">
    <div class="container">
        <!-- Download PDF Button -->
        <div class="order-receipt__actions">
            <button type="button" class="btn btn--primary btn--lg" id="download-receipt-pdf">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <?php esc_html_e( 'Download PDF Receipt', 'gloceps' ); ?>
            </button>
        </div>
        
        <div class="order-receipt__wrapper" id="receipt-content">
            
            <!-- Receipt Header -->
            <div class="order-receipt__header">
                <div class="order-receipt__logo">
                    <?php
                    // Get logo from theme settings
                    $acf_logo = function_exists('get_field') ? get_field('header_logo', 'option') : null;
                    $logo_url = null;
                    
                    // Priority 1: ACF Theme Settings logo
                    if ( $acf_logo && is_array($acf_logo) && !empty($acf_logo['url']) ) {
                        $logo_url = $acf_logo['url'];
                    }
                    
                    // Priority 2: WordPress Customizer logo
                    if ( !$logo_url ) {
                        $wp_logo_id = get_theme_mod( 'custom_logo' );
                        if ( $wp_logo_id ) {
                            $logo_url = wp_get_attachment_image_url( $wp_logo_id, 'full' );
                        }
                    }
                    
                    // Priority 3: Theme bundled logo
                    if ( !$logo_url ) {
                        $logo_file = GLOCEPS_DIR . '/assets/images/glocep-logo.png';
                        if ( file_exists( $logo_file ) ) {
                            $logo_url = GLOCEPS_URI . '/assets/images/glocep-logo.png';
                        }
                    }
                    
                    // Get site info
                    $site_name = get_bloginfo( 'name' );
                    $site_tagline = get_bloginfo( 'description' );
                    
                    // Get header settings
                    $show_site_title = function_exists('get_field') ? get_field('header_show_site_title', 'option') : true;
                    $show_tagline = function_exists('get_field') ? get_field('header_show_tagline', 'option') : true;
                    
                    // Default to true if not set (backward compatibility)
                    if ( $show_site_title === null ) {
                        $show_site_title = true;
                    }
                    if ( $show_tagline === null ) {
                        $show_tagline = true;
                    }
                    ?>
                    
                    <?php if ( $logo_url ) : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" 
                             alt="<?php echo esc_attr( $site_name ); ?>" 
                             class="order-receipt__logo-img" />
                    <?php endif; ?>
                    
                    <?php if ( ($show_site_title && $site_name) || ($show_tagline && $site_tagline) ) : ?>
                    <div class="order-receipt__logo-text">
                        <?php if ( $show_site_title && $site_name ) : ?>
                            <h2><?php echo esc_html( $site_name ); ?></h2>
                        <?php endif; ?>
                        
                        <?php if ( $show_tagline && $site_tagline ) : ?>
                            <p><?php echo esc_html( $site_tagline ); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="order-receipt__title">
                    <h2><?php esc_html_e( 'Order Receipt', 'gloceps' ); ?></h2>
                    <span class="order-receipt__status order-receipt__status--<?php echo esc_attr( $order_status ); ?>">
                        <?php echo esc_html( $status_label ); ?>
                    </span>
                </div>
            </div>
            
            <!-- Order Information -->
            <div class="order-receipt__info">
                <div class="order-receipt__info-item">
                    <strong><?php esc_html_e( 'Order Number:', 'gloceps' ); ?></strong>
                    <span>#<?php echo esc_html( $order->get_order_number() ); ?></span>
                </div>
                <div class="order-receipt__info-item">
                    <strong><?php esc_html_e( 'Date:', 'gloceps' ); ?></strong>
                    <span><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
                </div>
                <div class="order-receipt__info-item">
                    <strong><?php esc_html_e( 'Payment Method:', 'gloceps' ); ?></strong>
                    <span><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
                </div>
                <div class="order-receipt__info-item">
                    <strong><?php esc_html_e( 'Payment Status:', 'gloceps' ); ?></strong>
                    <span class="order-receipt__payment-status order-receipt__payment-status--<?php echo $is_paid ? 'paid' : 'unpaid'; ?>">
                        <?php echo $is_paid ? esc_html__( 'Paid', 'gloceps' ) : esc_html__( 'Not Paid', 'gloceps' ); ?>
                    </span>
                </div>
            </div>
            
            <!-- Billing Information (Inline) -->
            <div class="order-receipt__billing">
                <h3><?php esc_html_e( 'Billing Information', 'gloceps' ); ?></h3>
                <address class="order-receipt__address">
                    <?php 
                    $billing_fields = array();
                    if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
                        $billing_fields[] = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
                    }
                    if ( $order->get_billing_phone() ) {
                        $billing_fields[] = '<a href="tel:' . esc_attr( $order->get_billing_phone() ) . '">' . esc_html( $order->get_billing_phone() ) . '</a>';
                    }
                    if ( $order->get_billing_email() ) {
                        $billing_fields[] = '<a href="mailto:' . esc_attr( $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a>';
                    }
                    if ( $order->get_billing_company() ) {
                        $billing_fields[] = esc_html( $order->get_billing_company() );
                    }
                    echo implode( ' • ', $billing_fields );
                    ?>
                </address>
            </div>
            
            <!-- Order Items -->
            <div class="order-receipt__items">
                <h3><?php esc_html_e( 'Order Items', 'gloceps' ); ?></h3>
                <table class="order-receipt__table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Product', 'gloceps' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'gloceps' ); ?></th>
                            <th class="text-right"><?php esc_html_e( 'Price', 'gloceps' ); ?></th>
                            <th class="text-right"><?php esc_html_e( 'Total', 'gloceps' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $order->get_items() as $item_id => $item ) : 
                            $product = $item->get_product();
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo wp_kses_post( $item->get_name() ); ?></strong>
                                <?php
                                $meta_data = $item->get_formatted_meta_data();
                                if ( $meta_data ) {
                                    echo '<div class="order-receipt__item-meta">';
                                    foreach ( $meta_data as $meta ) {
                                        echo '<span>' . esc_html( $meta->display_key ) . ': ' . esc_html( $meta->display_value ) . '</span>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html( $item->get_quantity() ); ?></td>
                            <td class="text-right"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
                            <td class="text-right"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item, true ) ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong><?php esc_html_e( 'Subtotal:', 'gloceps' ); ?></strong></td>
                            <td class="text-right"><?php echo wp_kses_post( $order->get_subtotal_to_display() ); ?></td>
                        </tr>
                        <?php if ( $order->get_total_tax() > 0 ) : ?>
                        <tr>
                            <td colspan="3"><strong><?php esc_html_e( 'Tax:', 'gloceps' ); ?></strong></td>
                            <td class="text-right"><?php echo wp_kses_post( wc_price( $order->get_total_tax() ) ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php foreach ( $order->get_fees() as $fee ) : ?>
                        <tr>
                            <td colspan="3"><strong><?php echo esc_html( $fee->get_name() ); ?>:</strong></td>
                            <td class="text-right"><?php echo wp_kses_post( wc_price( $fee->get_total() ) ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="order-receipt__total">
                            <td colspan="3"><strong><?php esc_html_e( 'Total:', 'gloceps' ); ?></strong></td>
                            <td class="text-right"><strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Receipt Footer -->
            <div class="order-receipt__footer">
                <p><?php esc_html_e( 'Thank you for your purchase!', 'gloceps' ); ?></p>
                <p class="order-receipt__note">
                    <?php 
                    if ( ! $is_paid ) {
                        esc_html_e( 'This order has not been paid yet. Payment is required before publications can be accessed.', 'gloceps' );
                    } else {
                        esc_html_e( 'Your publications have been sent to your email address.', 'gloceps' );
                    }
                    ?>
                </p>
            </div>
            
        </div>
    </div>
</section>

<?php
get_footer();

