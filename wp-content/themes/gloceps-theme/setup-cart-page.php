<?php
/**
 * Setup Cart Page
 * Updates the cart page to use the shortcode instead of blocks
 *
 * Run this once: php setup-cart-page.php
 */

require_once __DIR__ . '/../../../wp-load.php';

if (!class_exists('WooCommerce')) {
    die('WooCommerce is not active.');
}

$cart_page_id = wc_get_page_id('cart');

if (!$cart_page_id) {
    die('Cart page not found.');
}

// Update the cart page content to use shortcode
$updated = wp_update_post(array(
    'ID' => $cart_page_id,
    'post_content' => '[woocommerce_cart]',
    'post_content_filtered' => '', // Clear any block content
));

if (is_wp_error($updated)) {
    die('Error updating cart page: ' . $updated->get_error_message());
}

echo "Cart page updated successfully!\n";
echo "Page ID: {$cart_page_id}\n";
echo "Page URL: " . get_permalink($cart_page_id) . "\n";

