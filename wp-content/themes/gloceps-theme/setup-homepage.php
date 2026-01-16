<?php
/**
 * Setup Homepage Blocks
 * 
 * This script programmatically sets up the homepage with all required blocks.
 * Run this once via: wp eval-file setup-homepage.php
 * Or access via browser: http://gloceps.local/wp-content/themes/gloceps-theme/setup-homepage.php
 * 
 * @package GLOCEPS
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!function_exists('acf_get_field')) {
    die('ACF is not active. Please activate Advanced Custom Fields Pro.');
}

// Get the front page (homepage)
$front_page_id = get_option('page_on_front');

// If no front page is set, try to find or create a "Home" page
if (!$front_page_id) {
    $home_page = get_page_by_path('home');
    
    if (!$home_page) {
        // Create a Home page
        $page_data = array(
            'post_title'    => 'Home',
            'post_name'     => 'home',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
        );
        
        $home_page_id = wp_insert_post($page_data);
        
        if (is_wp_error($home_page_id)) {
            die('Error creating Home page: ' . $home_page_id->get_error_message());
        }
        
        // Set as front page
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page_id);
        
        $front_page_id = $home_page_id;
        echo "✅ Created Home page and set as front page (ID: {$front_page_id})\n";
    } else {
        $front_page_id = $home_page->ID;
        // Set as front page
        update_option('show_on_front', 'page');
        update_option('page_on_front', $front_page_id);
        echo "✅ Found Home page and set as front page (ID: {$front_page_id})\n";
    }
} else {
    $front_page = get_post($front_page_id);
    echo "✅ Using existing front page: {$front_page->post_title} (ID: {$front_page_id})\n";
}

// Define the blocks in order (matching the static HTML design)
$blocks = array(
    // Block 1: Hero with Video Background
    array(
        'acf_fc_layout' => 'hero_video',
        'title' => 'Research. <em>Knowledge.</em> Influence.',
        'subtitle' => 'The Global Centre for Policy and Strategy (GLOCEPS) provides strategic linkage between experience and research, bringing together outstanding professionals, thought leaders, and academia to advance key issues on peace and security.',
        'button1_text' => 'Explore Our Work',
        'button1_link' => array(
            'url' => '#research',
            'title' => 'Explore Our Work',
            'target' => '',
        ),
        'video_file' => '', // Upload video later via admin
        'poster_image' => '', // Upload poster image later via admin
    ),
    
    // Block 2: Research Focus Areas (Five Pillars) - Homepage version with images
    array(
        'acf_fc_layout' => 'home_pillars',
        'eyebrow' => 'Research Focus Areas',
        'title' => 'Our Five Pillars',
        'description' => 'GLOCEPS work cuts across five interconnected pillars addressing the most pressing challenges facing Eastern Africa and the broader region.',
    ),
    
    // Block 3: Featured Publication (Featured Insight)
    array(
        'acf_fc_layout' => 'featured_publication',
        'publication' => '', // Will auto-select most recent premium publication
    ),
    
    // Block 4: Latest Research Publications (Editorial Listing)
    array(
        'acf_fc_layout' => 'latest_publications',
        'eyebrow' => 'Latest Research',
        'title' => 'Publications',
        'description' => 'Access our latest policy briefs, research papers, and strategic analyses shaping discourse on regional and global issues.',
        'count' => 5,
        'view_all_link' => array(
            'url' => home_url('/purchase/'),
            'title' => 'View All Publications',
            'target' => '',
        ),
    ),
    
    // Block 5: Impact Statistics
    array(
        'acf_fc_layout' => 'impact_stats',
        'show_eyebrow' => 0, // Hide eyebrow for homepage
        'show_title' => 0, // Hide title for homepage (stats only)
        'title_color' => 'white',
        'stats' => array(
            array(
                'value' => '50',
                'suffix' => '+',
                'label' => 'Research Publications',
            ),
            array(
                'value' => '12',
                'suffix' => '',
                'label' => 'Countries Engaged',
            ),
            array(
                'value' => '35',
                'suffix' => '+',
                'label' => 'Policy Dialogues',
            ),
            array(
                'value' => '20',
                'suffix' => '+',
                'label' => 'Expert Fellows',
            ),
        ),
    ),
    
    // Block 6: Events Section
    array(
        'acf_fc_layout' => 'events_section',
        'eyebrow' => 'Upcoming',
        'title' => 'Events & Dialogues',
        'description' => 'Join our policy dialogues, roundtables, and expert discussions shaping regional discourse on critical issues.',
        'count' => 3,
        'only_upcoming' => 1,
    ),
    
    // Block 7: CTA Section (Access Our Research)
    array(
        'acf_fc_layout' => 'cta_section',
        'style' => 'default',
        'title' => 'Access Our Research',
        'description' => 'Explore in-depth analysis and strategic insights from GLOCEPS. Download free briefs or purchase comprehensive research papers to support evidence-based policy making in Eastern Africa.',
        'background_image' => '', // Upload background image later
        'primary_button' => array(
            'title' => 'Browse Publications',
            'url' => home_url('/purchase/'),
            'target' => '',
        ),
        'secondary_button' => array(
            'title' => 'Subscribe to Updates',
            'url' => '#',
            'target' => '',
        ),
    ),
);

// Update the page with blocks
update_field('content_blocks', $blocks, $front_page_id);

echo "\n✅ Homepage blocks have been configured successfully!\n";
echo "Page ID: {$front_page_id}\n";
echo "Page URL: " . get_permalink($front_page_id) . "\n";
echo "\n📋 Blocks added (in order):\n";
foreach ($blocks as $index => $block) {
    $layout_name = ucwords(str_replace('_', ' ', $block['acf_fc_layout']));
    echo ($index + 1) . ". {$layout_name}\n";
}

echo "\n📝 Next Steps:\n";
echo "1. Edit the homepage in WordPress admin to:\n";
echo "   - Upload hero video file and poster image\n";
echo "   - Add images to Research Pillars (via ACF on taxonomy terms)\n";
echo "   - Select a featured publication (or leave empty for auto-select)\n";
echo "   - Upload CTA section background image\n";
echo "2. Visit: " . admin_url('post.php?post=' . $front_page_id . '&action=edit') . "\n";
echo "3. View page: " . get_permalink($front_page_id) . "\n";
