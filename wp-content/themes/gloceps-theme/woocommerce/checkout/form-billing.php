<?php
/**
 * Checkout billing information form - Custom GLOCEPS Design
 *
 * @package GLOCEPS
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-billing-fields">
	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields( 'billing' );
		
		// Add hidden billing_country field (required by WooCommerce but hidden for digital products)
		if ( isset( $fields['billing_country'] ) ) :
			$field = $fields['billing_country'];
			$field['class'] = array( 'hidden' );
			$field['default'] = 'KE';
			woocommerce_form_field( 'billing_country', $field, $checkout->get_value( 'billing_country' ) ?: 'KE' );
		endif;

		// First name and Last name in a row
		if ( isset( $fields['billing_first_name'] ) || isset( $fields['billing_last_name'] ) ) :
		?>
		<div class="checkout-form__row">
			<?php if ( isset( $fields['billing_first_name'] ) ) : 
				$field = $fields['billing_first_name'];
				$field['class'] = array( 'checkout-form__field' );
				$field['label'] = __( 'First Name', 'gloceps' ); // WooCommerce adds asterisk automatically
				woocommerce_form_field( 'billing_first_name', $field, $checkout->get_value( 'billing_first_name' ) );
			endif; ?>
			<?php if ( isset( $fields['billing_last_name'] ) ) : 
				$field = $fields['billing_last_name'];
				$field['class'] = array( 'checkout-form__field' );
				$field['label'] = __( 'Last Name', 'gloceps' ); // WooCommerce adds asterisk automatically
				woocommerce_form_field( 'billing_last_name', $field, $checkout->get_value( 'billing_last_name' ) );
			endif; ?>
		</div>
		<?php endif; ?>
		
		<?php
		// Email field with hint (hint is added by woocommerce-functions.php filter)
		if ( isset( $fields['billing_email'] ) ) :
			$field = $fields['billing_email'];
			$field['class'] = array( 'checkout-form__field' );
			$field['label'] = __( 'Email Address', 'gloceps' ); // WooCommerce adds asterisk automatically
			woocommerce_form_field( 'billing_email', $field, $checkout->get_value( 'billing_email' ) );
		endif;
		
		// Phone field with hint
		if ( isset( $fields['billing_phone'] ) ) :
			$field = $fields['billing_phone'];
			$field['class'] = array( 'checkout-form__field' );
			$field['label'] = __( 'Phone Number', 'gloceps' ); // Remove asterisk here, WooCommerce adds it
			woocommerce_form_field( 'billing_phone', $field, $checkout->get_value( 'billing_phone' ) );
			?>
			<p class="checkout-form__hint"><?php esc_html_e( 'Required for M-Pesa payment.', 'gloceps' ); ?></p>
			<?php
		endif;
		
		// Company/Organization field (optional)
		if ( isset( $fields['billing_company'] ) ) :
			$field = $fields['billing_company'];
			$field['class'] = array( 'checkout-form__field' );
			$field['label'] = __( 'Organization (Optional)', 'gloceps' );
			$field['required'] = false;
			$field['placeholder'] = __( 'Company or Institution name', 'gloceps' );
			woocommerce_form_field( 'billing_company', $field, $checkout->get_value( 'billing_company' ) );
		endif;
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

