<?php
/**
 * Payment method template - Custom GLOCEPS Design
 *
 * @var WC_Payment_Gateway $gateway
 * @package GLOCEPS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$gateway_title = $gateway->get_title();
// Get description directly from gateway settings to bypass conditional filters
// This ensures each gateway shows its own description regardless of which one is selected
$gateway_desc = isset( $gateway->settings['description'] ) ? $gateway->settings['description'] : '';
// If no description in settings, fall back to get_description() (which applies filters)
if ( empty( $gateway_desc ) ) {
    $gateway_desc = $gateway->get_description();
}
// Strip HTML tags from description for card display (keep it clean)
$gateway_desc_clean = $gateway_desc ? wp_strip_all_tags( $gateway_desc ) : '';
// Remove gateway ID prefix if present (e.g., "codPay", "pesapalPay", "cod", "pesapal" at the start)
if ( $gateway_desc_clean ) {
    $gateway_id_lower = strtolower( $gateway->id );
    // Remove patterns like "codPay", "pesapalPay", "cod", "pesapal" at the start (case-insensitive)
    $patterns = array(
        '/^' . preg_quote( $gateway_id_lower, '/' ) . 'pay\s*/i',  // "codPay", "pesapalPay"
        '/^' . preg_quote( $gateway_id_lower, '/' ) . '\s+/i',     // "cod ", "pesapal "
        '/^' . preg_quote( $gateway_id_lower, '/' ) . '$/i',        // Just "cod" or "pesapal"
    );
    foreach ( $patterns as $pattern ) {
        $gateway_desc_clean = preg_replace( $pattern, '', $gateway_desc_clean );
    }
    // Trim any leading/trailing whitespace
    $gateway_desc_clean = trim( $gateway_desc_clean );
}
$gateway_icon = $gateway->get_icon(); // Use WooCommerce default icon
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
    <div class="payment-method-card <?php echo $gateway->chosen ? 'payment-method-card--selected' : ''; ?>">
        <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="payment-method">
            <div class="payment-method__radio">
                <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
                <span class="payment-method__radio-indicator"></span>
            </div>
            <div class="payment-method__content">
                <div class="payment-method__icon">
                    <?php if ( $gateway_icon ) : ?>
                        <?php echo $gateway_icon; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
                    <?php else : ?>
                        <!-- Fallback icon if gateway doesn't provide one -->
                        <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                            <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="payment-method__details">
                    <strong class="payment-method__title"><?php echo esc_html( $gateway_title ); ?></strong>
                    <?php if ( $gateway_desc_clean ) : ?>
                        <span class="payment-method__description"><?php echo esc_html( $gateway_desc_clean ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </label>
    </div>
    
    <?php 
    // Only show payment_box if gateway has actual fields (not just description)
    // Description is already shown in the card, so we don't need payment_box for description-only gateways
    if ( $gateway->has_fields() ) : ?>
        <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
            <?php 
            // For MPesa gateway, we need to output fields manually to avoid duplicate description
            if ( $gateway->id === 'mpesa' ) :
                // Get the phone number from billing fields
                $billing_phone = WC()->checkout->get_value( 'billing_phone' );
                // Remove +254 prefix if present for display
                $display_phone = $billing_phone ? preg_replace( '/^\+?254/', '', $billing_phone ) : '';
                ?>
                <div class="payment-method__fields">
                    <div class="checkout-form__field">
                        <label for="billing_mpesa_phone" class="checkout-form__label">
                            <?php esc_html_e( 'Confirm M-PESA Number', 'gloceps' ); ?>
                        </label>
                        <div class="phone-input">
                            <span class="phone-input__prefix">+254</span>
                            <input 
                                type="text" 
                                class="checkout-form__input" 
                                name="billing_mpesa_phone" 
                                id="billing_mpesa_phone" 
                                value="<?php echo esc_attr( $display_phone ); ?>"
                                placeholder="<?php esc_attr_e( 'Enter your M-PESA number', 'gloceps' ); ?>"
                            />
                        </div>
                        <span class="checkout-form__hint">
                            <?php esc_html_e( 'Confirm the phone number registered with M-PESA', 'gloceps' ); ?>
                        </span>
                    </div>
                </div>
            <?php else : ?>
                <?php 
                // For other gateways (like PesaPal), only output fields if gateway is selected
                // This prevents descriptions from showing for non-selected gateways
                if ( $gateway->chosen ) {
                    ob_start();
                    $gateway->payment_fields();
                    $fields_output = ob_get_clean();
                    
                    // Remove description paragraph if it matches the card description
                    if ( ! empty( $gateway_desc_clean ) ) {
                        // Remove description that matches what's in the card (case-insensitive, flexible matching)
                        $desc_pattern = preg_quote( $gateway_desc_clean, '/' );
                        $fields_output = preg_replace( '/<p[^>]*>.*?' . $desc_pattern . '.*?<\/p>/is', '', $fields_output );
                        
                        // Also try removing any description paragraphs that contain similar text
                        $fields_output = preg_replace( '/<p[^>]*>' . preg_quote( wp_strip_all_tags( $gateway_desc ), '/' ) . '<\/p>/i', '', $fields_output );
                    }
                    
                    // Remove any standalone description paragraphs (usually first paragraph)
                    $fields_output = preg_replace( '/<p[^>]*>.*?description.*?<\/p>/is', '', $fields_output );
                    
                    echo $fields_output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
                }
                ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</li>

