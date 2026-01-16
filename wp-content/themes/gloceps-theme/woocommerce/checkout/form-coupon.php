<?php
/**
 * Checkout coupon form - Custom GLOCEPS Design
 * Based on WooCommerce default template with custom styling
 *
 * @package GLOCEPS
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) {
	return;
}

?>
<div class="checkout-coupon-toggle">
	<a href="#" role="button" aria-label="<?php esc_attr_e( 'Enter your coupon code', 'woocommerce' ); ?>" aria-controls="woocommerce-checkout-form-coupon" aria-expanded="false" class="showcoupon checkout-coupon-toggle__link">
		<?php esc_html_e( 'Have a coupon?', 'woocommerce' ); ?>
		<span class="checkout-coupon-toggle__text"><?php esc_html_e( 'Click here to enter your code', 'woocommerce' ); ?></span>
	</a>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none" id="woocommerce-checkout-form-coupon">

	<p class="form-row form-row-first">
		<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
	</p>

	<p class="form-row form-row-last">
		<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
	</p>

	<div class="clear"></div>
</form>

