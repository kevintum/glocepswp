<?php
/**
 * Setup Purchase Page Blocks
 * 
 * This script programmatically sets up the purchase page with all required blocks.
 * Run this once via: wp eval-file setup-purchase-page.php
 * Or access via browser: http://gloceps.local/wp-content/themes/gloceps-theme/setup-purchase-page.php
 * 
 * @package GLOCEPS
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!function_exists('acf_get_field')) {
    die('ACF is not active. Please activate Advanced Custom Fields Pro.');
}

// Get the purchase page
$purchase_page = get_page_by_path('purchase');

if (!$purchase_page) {
    die('Purchase page not found. Please create a page with slug "purchase" first.');
}

$page_id = $purchase_page->ID;

// Define the blocks in order
$blocks = array(
    array(
        'acf_fc_layout' => 'store_hero',
        'eyebrow' => 'Purchase Publications',
        'title' => 'Research That Shapes Policy',
        'description' => 'Access in-depth analysis, strategic insights, and policy recommendations from GLOCEPS experts. Your purchase directly supports independent research advancing peace, security, and development in Eastern Africa.',
    ),
    array(
        'acf_fc_layout' => 'trust_bar',
        'trust_items' => array(
            array('text' => 'Secure Payment via M-Pesa & Card'),
            array('text' => 'Instant PDF Download'),
            array('text' => 'Peer-Reviewed Research'),
            array('text' => 'Invoice Available'),
        ),
    ),
    array(
        'acf_fc_layout' => 'featured_publication',
        'publication' => '', // Will auto-select premium publication
    ),
    array(
        'acf_fc_layout' => 'products_grid',
        'per_page' => 12,
    ),
    array(
        'acf_fc_layout' => 'institutional_subscriptions',
        'eyebrow' => 'For Organisations',
        'title' => 'Institutional Subscriptions',
        'description' => 'Universities, government agencies, and development organisations can access our full catalogue through institutional licensing.',
        'details' => 'Institutional subscribers receive advance access to upcoming publications, priority event invitations, quarterly briefings with GLOCEPS researchers, and custom research commissions at preferred rates.',
        'button' => array(
            'title' => 'Enquire About Licensing',
            'url' => '#',
            'target' => '',
        ),
        'benefits' => array(
            array('text' => 'Unlimited access to all publications for your team'),
            array('text' => 'Early access to new research before public release'),
            array('text' => 'Quarterly briefings with GLOCEPS researchers'),
            array('text' => 'Priority seating at GLOCEPS events and dialogues'),
            array('text' => 'Custom research commissions at preferred rates'),
        ),
    ),
    array(
        'acf_fc_layout' => 'cta_section',
        'style' => 'default',
        'title' => "Can't Find What You're Looking For?",
        'description' => 'Browse our free publications or contact us for custom research commissions tailored to your specific policy needs.',
        'background_image' => '', // Can be set later
        'primary_button' => array(
            'title' => 'Free Publications',
            'url' => '/publications/',
            'target' => '',
        ),
        'secondary_button' => array(
            'title' => 'Commission Research',
            'url' => '/contact/',
            'target' => '',
        ),
    ),
);

// Update the page with blocks
update_field('content_blocks', $blocks, $page_id);

echo "Purchase page blocks have been configured successfully!\n";
echo "Page ID: {$page_id}\n";
echo "Page URL: " . get_permalink($page_id) . "\n";
echo "\nBlocks added:\n";
foreach ($blocks as $index => $block) {
    echo ($index + 1) . ". " . ucwords(str_replace('_', ' ', $block['acf_fc_layout'])) . "\n";
}

