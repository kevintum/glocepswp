<?php
/**
 * MPesa Callback Test Script
 * 
 * This script simulates an MPesa STK Push callback for testing purposes.
 * 
 * Usage:
 * 1. Place an order using MPesa gateway
 * 2. Note the order ID and MerchantRequestID from order notes
 * 3. Run this script: php test-mpesa-callback.php
 * 
 * Or access via browser: http://gloceps.local/test-mpesa-callback.php?order_id=XXX&merchant_request_id=XXX
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Get parameters from GET or CLI
if (php_sapi_name() === 'cli') {
    // CLI mode
    $order_id = isset($argv[1]) ? intval($argv[1]) : 0;
    $merchant_request_id = isset($argv[2]) ? $argv[2] : '';
} else {
    // Web mode
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $merchant_request_id = isset($_GET['merchant_request_id']) ? sanitize_text_field($_GET['merchant_request_id']) : '';
}

if (!$order_id) {
    if (php_sapi_name() === 'cli') {
        die("Error: Please provide order_id and optional merchant_request_id.\nUsage: php test-mpesa-callback.php ORDER_ID [MERCHANT_REQUEST_ID]\n");
    } else {
        die('Error: Please provide order_id parameter. Usage: ?order_id=XXX&merchant_request_id=XXX');
    }
}

$order = wc_get_order($order_id);
if (!$order) {
    die('Error: Order not found');
}

// Get MPesa gateway settings
$gateways = WC()->payment_gateways()->payment_gateways();
$gateway = isset($gateways['mpesa']) ? $gateways['mpesa'] : null;

if (!$gateway) {
    die('Error: MPesa gateway not found. Please ensure the gateway is enabled in WooCommerce settings.');
}

$signature = $gateway->get_option('signature');

// Get MerchantRequestID from order meta if not provided
if (empty($merchant_request_id)) {
    $merchant_request_id = get_post_meta($order_id, 'mpesa_request_id', true);
    if (empty($merchant_request_id)) {
        $merchant_request_id = 'test-' . time();
    }
}

// Simulate successful MPesa callback response
$callback_data = array(
    'Body' => array(
        'stkCallback' => array(
            'MerchantRequestID' => $merchant_request_id,
            'CheckoutRequestID' => 'ws_CO_' . time(),
            'ResultCode' => 0, // 0 = Success
            'ResultDesc' => 'The service request is processed successfully.',
            'CallbackMetadata' => array(
                'Item' => array(
                    array('Name' => 'Amount', 'Value' => round($order->get_total())),
                    array('Name' => 'MpesaReceiptNumber', 'Value' => 'TEST' . time()),
                    array('Name' => 'TransactionDate', 'Value' => date('YmdHis')),
                    array('Name' => 'PhoneNumber', 'Value' => preg_replace('/[^0-9]/', '', $order->get_billing_phone())),
                )
            )
        )
    )
);

// Get the callback URL
$callback_url = home_url("wc-api/lipwa?action=reconcile&sign={$signature}&order={$order_id}");

if (php_sapi_name() === 'cli') {
    echo "=== MPesa Callback Test ===\n";
    echo "Order ID: {$order_id}\n";
    echo "Order Status: " . $order->get_status() . "\n";
    echo "MerchantRequestID: {$merchant_request_id}\n";
    echo "Callback URL: {$callback_url}\n";
    echo "Signature: {$signature}\n\n";
    echo "Simulating Callback...\n\n";
} else {
    echo "<h2>MPesa Callback Test</h2>";
    echo "<p><strong>Order ID:</strong> {$order_id}</p>";
    echo "<p><strong>Order Status:</strong> " . $order->get_status() . "</p>";
    echo "<p><strong>MerchantRequestID:</strong> {$merchant_request_id}</p>";
    echo "<p><strong>Callback URL:</strong> <code>{$callback_url}</code></p>";
    echo "<p><strong>Signature:</strong> <code>{$signature}</code></p>";
    echo "<hr>";
    echo "<h3>Simulating Callback...</h3>";
}

// Make the callback request
$response = wp_remote_post($callback_url, array(
    'method' => 'POST',
    'headers' => array(
        'Content-Type' => 'application/json',
    ),
    'body' => json_encode($callback_data),
    'timeout' => 30,
));

if (is_wp_error($response)) {
    if (php_sapi_name() === 'cli') {
        echo "ERROR: " . $response->get_error_message() . "\n";
    } else {
        echo "<p style='color: red;'><strong>Error:</strong> " . $response->get_error_message() . "</p>";
    }
} else {
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    if (php_sapi_name() === 'cli') {
        echo "Response Code: {$response_code}\n";
        echo "Response Body:\n" . $response_body . "\n\n";
    } else {
        echo "<p><strong>Response Code:</strong> {$response_code}</p>";
        echo "<p><strong>Response Body:</strong></p>";
        echo "<pre>" . esc_html($response_body) . "</pre>";
    }
    
    // Refresh order to check status
    $order = wc_get_order($order_id);
    
    if (php_sapi_name() === 'cli') {
        echo "=== Order Status After Callback ===\n";
        echo "Status: " . $order->get_status() . "\n";
        echo "Transaction ID: " . $order->get_transaction_id() . "\n\n";
        
        if ($order->get_status() === 'completed') {
            echo "✓ SUCCESS! Order marked as completed.\n";
        } else {
            echo "⚠ WARNING: Order status is '{$order->get_status()}' (expected 'completed').\n";
        }
        
        echo "\n=== Callback Data Sent ===\n";
        echo json_encode($callback_data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "<hr>";
        echo "<h3>Order Status After Callback:</h3>";
        echo "<p><strong>Status:</strong> " . $order->get_status() . "</p>";
        echo "<p><strong>Transaction ID:</strong> " . $order->get_transaction_id() . "</p>";
        
        if ($order->get_status() === 'completed') {
            echo "<p style='color: green;'><strong>✓ Success!</strong> Order marked as completed.</p>";
        } else {
            echo "<p style='color: orange;'><strong>⚠ Warning:</strong> Order status is '{$order->get_status()}' (expected 'completed').</p>";
        }
        
        echo "<hr>";
        echo "<h3>Callback Data Sent:</h3>";
        echo "<pre>" . esc_html(json_encode($callback_data, JSON_PRETTY_PRINT)) . "</pre>";
    }
}

