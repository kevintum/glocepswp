<?php
/**
 * Setup Essential Pages
 * 
 * Creates Resend Publications and Order Receipt pages
 * Run this once via: wp eval-file setup-essential-pages.php
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/../../../../wp-load.php';
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
                    'description' => "Didn't receive your download email? No problem. Enter your order details below and we'll send a fresh copy of your download links.",
                ),
                array(
                    'acf_fc_layout' => 'resend_help',
                    'title' => 'Need More Help?',
                    'email_label' => 'Email Support',
                    'email' => 'support@gloceps.org',
                    'phone_label' => 'Phone Support',
                    'phone' => '+254 112 401 331',
                    'hours' => 'Mon-Fri, 8am-5pm EAT',
                ),
                array(
                    'acf_fc_layout' => 'resend_faq',
                    'title' => 'COMMON QUESTIONS',
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
                            'answer' => 'Your download links are valid indefinitely. You can download your publications as many times as you need.',
                        ),
                    ),
                ),
            );
            update_field('content_blocks', $default_blocks, $resend_id);
        }
        
        echo "✓ Created Resend Publications page (ID: $resend_id)\n";
    } else {
        echo "✗ Failed to create Resend Publications page\n";
    }
} else {
    echo "✓ Resend Publications page already exists (ID: {$resend_page->ID})\n";
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
        echo "✓ Created Order Receipt page (ID: $receipt_id)\n";
    } else {
        echo "✗ Failed to create Order Receipt page\n";
    }
} else {
    echo "✓ Order Receipt page already exists (ID: {$receipt_page->ID})\n";
}

// Flush rewrite rules
flush_rewrite_rules();
echo "✓ Flushed rewrite rules\n";

echo "\nDone! Pages are now available.\n";

