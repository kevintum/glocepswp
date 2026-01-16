<?php
/**
 * Custom Order Completion Email Template
 * 
 * This template is used for the custom order completion email sent to customers.
 * Variables available:
 * - $order: WC_Order object
 * - $order_number: Order number
 * - $order_date: Formatted order date
 * - $order_total: Formatted order total
 * - $billing_email: Customer email
 * - $publications: Array of publications with download links
 *
 * @package GLOCEPS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Email styling
$email_bg_color = '#f5f5f5';
$email_content_bg = '#ffffff';
$primary_color = '#3898b2';
$text_color = '#333333';
$border_color = '#e0e0e0';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( sprintf( __( 'Your GLOCEPS Publications - Order #%s', 'gloceps' ), $order_number ) ); ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: <?php echo esc_attr( $email_bg_color ); ?>;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: <?php echo esc_attr( $email_bg_color ); ?>;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: <?php echo esc_attr( $email_content_bg ); ?>; border-radius: 8px; overflow: hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: center; background-color: <?php echo esc_attr( $primary_color ); ?>;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600;"><?php esc_html_e( 'Thank You for Your Purchase!', 'gloceps' ); ?></h1>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 16px; line-height: 1.6;">
                                <?php 
                                if ( $order->get_billing_first_name() ) {
                                    printf( esc_html__( 'Hi %s,', 'gloceps' ), esc_html( $order->get_billing_first_name() ) );
                                } else {
                                    esc_html_e( 'Hi,', 'gloceps' );
                                }
                                ?>
                            </p>
                            
                            <p style="margin: 0 0 30px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 16px; line-height: 1.6;">
                                <?php esc_html_e( 'Your order has been successfully processed. Your publications are ready for download.', 'gloceps' ); ?>
                            </p>
                            
                            <!-- Order Details -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 30px; background-color: #f9f9f9; border-radius: 6px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px;"><?php esc_html_e( 'Order Number:', 'gloceps' ); ?></strong>
                                                    <span style="color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; margin-left: 10px;">#<?php echo esc_html( $order_number ); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px;"><?php esc_html_e( 'Date:', 'gloceps' ); ?></strong>
                                                    <span style="color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; margin-left: 10px;"><?php echo esc_html( $order_date ); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px;"><?php esc_html_e( 'Total Paid:', 'gloceps' ); ?></strong>
                                                    <span style="color: <?php echo esc_attr( $primary_color ); ?>; font-size: 16px; font-weight: 600; margin-left: 10px;"><?php echo wp_kses_post( $order_total ); ?></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Publications -->
                            <?php if ( ! empty( $publications ) ) : ?>
                                <h2 style="margin: 0 0 20px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 20px; font-weight: 600;">
                                    <?php esc_html_e( 'Your Publications', 'gloceps' ); ?>
                                </h2>
                                
                                <?php foreach ( $publications as $publication ) : ?>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 20px; border: 1px solid <?php echo esc_attr( $border_color ); ?>; border-radius: 6px; overflow: hidden;">
                                        <tr>
                                            <td style="padding: 20px;">
                                                <p style="margin: 0 0 10px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 16px; font-weight: 600;">
                                                    <?php echo esc_html( $publication['name'] ); ?>
                                                </p>
                                                
                                                <p style="margin: 10px 0 0; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                                    <?php esc_html_e( 'Your publication PDF has been attached to this email. Please check your email attachments to download the file.', 'gloceps' ); ?>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <!-- Download Info -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #e8f4f8; border-left: 4px solid <?php echo esc_attr( $primary_color ); ?>; border-radius: 4px;">
                                <p style="margin: 0 0 10px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                    <strong><?php esc_html_e( 'Your Publications:', 'gloceps' ); ?></strong>
                                </p>
                                <p style="margin: 0; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                    <?php esc_html_e( 'All publication PDFs have been attached to this email. Please check your email attachments to download your files. If you need to download them again, you can use the resend publications page.', 'gloceps' ); ?>
                                </p>
                            </div>
                            
                            <!-- Receipt Info -->
                            <p style="margin: 30px 0 0; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                <?php 
                                $receipt_url = add_query_arg( array(
                                    'order_id' => $order->get_id(),
                                    'key' => $order->get_order_key(),
                                ), home_url( '/order-receipt/' ) );
                                printf(
                                    esc_html__( 'You can view and download your receipt %s.', 'gloceps' ),
                                    '<a href="' . esc_url( $receipt_url ) . '" style="color: ' . esc_attr( $primary_color ) . '; text-decoration: underline;">' . esc_html__( 'here', 'gloceps' ) . '</a>'
                                );
                                ?>
                            </p>
                            
                            <!-- Help Section -->
                            <div style="margin: 30px 0 0; padding-top: 30px; border-top: 1px solid <?php echo esc_attr( $border_color ); ?>;">
                                <p style="margin: 0 0 10px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                    <strong><?php esc_html_e( 'Need Help?', 'gloceps' ); ?></strong>
                                </p>
                                <p style="margin: 0 0 10px; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                    <?php 
                                    printf(
                                        esc_html__( 'If you didn\'t receive this email or need to resend your publications, please visit %s.', 'gloceps' ),
                                        '<a href="' . esc_url( home_url( '/resend-publications/' ) ) . '" style="color: ' . esc_attr( $primary_color ) . '; text-decoration: underline;">' . esc_html__( 'our resend page', 'gloceps' ) . '</a>'
                                    );
                                    ?>
                                </p>
                                <p style="margin: 0; color: <?php echo esc_attr( $text_color ); ?>; font-size: 14px; line-height: 1.6;">
                                    <?php 
                                    printf(
                                        esc_html__( 'For any other questions, contact us at %s or call %s.', 'gloceps' ),
                                        '<a href="mailto:info@gloceps.org" style="color: ' . esc_attr( $primary_color ) . '; text-decoration: underline;">info@gloceps.org</a>',
                                        '<a href="tel:+254112401331" style="color: ' . esc_attr( $primary_color ) . '; text-decoration: underline;">+254 112 401 331</a>'
                                    );
                                    ?>
                                </p>
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background-color: #f9f9f9; border-top: 1px solid <?php echo esc_attr( $border_color ); ?>;">
                            <p style="margin: 0 0 10px; color: #999999; font-size: 12px;">
                                <?php esc_html_e( '©', 'gloceps' ); ?> <?php echo date( 'Y' ); ?> GLOCEPS – <?php esc_html_e( 'Global Centre for Policy and Strategy', 'gloceps' ); ?>
                            </p>
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                <?php esc_html_e( 'Runda Drive, Nairobi, Kenya', 'gloceps' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

