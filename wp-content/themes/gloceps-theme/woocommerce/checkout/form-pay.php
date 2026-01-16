<?php
/**
 * Pay for order form - Custom GLOCEPS Design
 *
 * @package GLOCEPS
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>

<section class="order-pay">
    <div class="container">
        <div class="order-pay__wrapper">
            
            <!-- Order Details Header -->
            <div class="order-pay__header">
                <div class="order-pay__info">
                    <div class="order-pay__info-item">
                        <span class="order-pay__label"><?php esc_html_e( 'ORDER NUMBER', 'gloceps' ); ?></span>
                        <span class="order-pay__value"><?php echo esc_html( $order->get_order_number() ); ?></span>
                    </div>
                    <div class="order-pay__info-item">
                        <span class="order-pay__label"><?php esc_html_e( 'DATE', 'gloceps' ); ?></span>
                        <span class="order-pay__value"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
                    </div>
                    <div class="order-pay__info-item">
                        <span class="order-pay__label"><?php esc_html_e( 'TOTAL', 'gloceps' ); ?></span>
                        <span class="order-pay__value order-pay__value--highlight"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
                    </div>
                    <div class="order-pay__info-item">
                        <span class="order-pay__label"><?php esc_html_e( 'PAYMENT METHOD', 'gloceps' ); ?></span>
                        <span class="order-pay__value"><?php echo esc_html( $order->get_payment_method_title() ); ?></span>
                    </div>
                </div>
            </div>

            <?php do_action( 'woocommerce_before_pay_order_form', $order ); ?>

            <form id="order_review" method="post" class="order-pay__form">

                <?php if ( count( $order->get_items() ) > 0 ) : ?>
                <div class="order-pay__items">
                    <h3><?php esc_html_e( 'Order Items', 'gloceps' ); ?></h3>
                    <table class="order-pay__table">
                        <thead>
                            <tr>
                                <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                                <th class="product-quantity"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
                                <th class="product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                                <?php
                                if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
                                    continue;
                                }
                                ?>
                                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                                    <td class="product-name">
                                        <?php
                                        echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
                                        do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                                        wc_display_item_meta( $item );
                                        do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
                                        ?>
                                    </td>
                                    <td class="product-quantity"><?php echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); ?></td>
                                    <td class="product-subtotal"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php if ( $totals ) : ?>
                        <tfoot>
                            <?php foreach ( $totals as $total ) : ?>
                                <tr>
                                    <th scope="row" colspan="2"><?php echo $total['label']; ?></th>
                                    <td class="product-total"><?php echo $total['value']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
                <?php endif; ?>

                <?php
                /**
                 * Triggered from within the checkout/form-pay.php template, immediately before the payment section.
                 */
                do_action( 'woocommerce_pay_order_before_payment' ); 
                ?>

                <div id="payment" class="order-pay__payment">
                    <?php if ( $order->needs_payment() ) : ?>
                        <ul class="wc_payment_methods payment_methods methods">
                            <?php
                            if ( ! empty( $available_gateways ) ) {
                                foreach ( $available_gateways as $gateway ) {
                                    wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                                }
                            } else {
                                echo '<li>';
                                wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ), 'notice' );
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <input type="hidden" name="woocommerce_pay" value="1" />

                        <?php wc_get_template( 'checkout/terms.php' ); ?>

                        <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

                        <?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="btn btn--primary btn--lg" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); ?>

                        <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

                        <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
                    </div>
                </div>

            </form>

            <?php do_action( 'woocommerce_after_pay_order_form', $order ); ?>

        </div>
    </div>
</section>

