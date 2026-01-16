<?php
/**
 * Setup Foreign Policy Pillar Page
 * 
 * Creates the Foreign Policy page with default ACF Flexible Content blocks
 * Based on foreign-policy.html structure
 * 
 * Usage: Visit /wp-admin/?setup_foreign_policy_page=1
 */

if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

function gloceps_setup_foreign_policy_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Get or create Research parent page
    $research_page = get_page_by_path('research');
    if (!$research_page) {
        wp_die('Research page not found. Please create it first.');
    }

    // Check if Foreign Policy page already exists
    $page_slug = 'foreign-policy';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>Foreign Policy page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'Foreign Policy',
            'post_name'     => $page_slug,
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
            'post_parent'   => $research_page->ID,
        );

        $page_id = wp_insert_post($page_data);

        if (is_wp_error($page_id)) {
            echo '<h2>Error creating page</h2>';
            echo '<p>' . $page_id->get_error_message() . '</p>';
            return;
        }

        echo '<h2>Foreign Policy page created (ID: ' . $page_id . ')</h2>';
    }

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Pillar Hero Split
            array(
                'acf_fc_layout' => 'pillar_hero_split',
                'title' => 'Foreign Policy',
                'description' => 'Journeying the global interplay of balance of power, great power politics, diplomatic networks, strategic interests, and political economics of the twenty-first century.',
                'cta_text' => 'Explore Our Work',
                'cta_link' => array(
                    'url' => home_url('/publications/'),
                    'title' => 'Explore Our Work',
                    'target' => '',
                ),
                'image' => '', // Will need to be uploaded manually
            ),
            
            // Block 2: Pillar Introduction
            array(
                'acf_fc_layout' => 'pillar_intro',
                'lead_text' => 'The pillar champions the global interplay of balance of power, great power politics, diplomatic networks, strategic interests, and political economics of the twenty-first century.',
                'text' => '<p>Our evidence-based research provides policy-relevant insights on international relations, regional integration, and global governance frameworks shaping Eastern Africa\'s engagement with the world.</p><p>Through rigorous analysis and strategic foresight, we support policymakers, diplomats, and institutions in navigating complex geopolitical landscapes.</p>',
                'stats' => array(
                    array(
                        'value' => '15+',
                        'label' => 'Years of Foreign Policy Research',
                    ),
                    array(
                        'value' => '8',
                        'label' => 'Research Pillars',
                    ),
                    array(
                        'value' => '20+',
                        'label' => 'Foreign Policy Experts',
                    ),
                ),
            ),
            
            // Block 3: Focus Areas (How We Support)
            array(
                'acf_fc_layout' => 'focus_areas',
                'title' => 'How We Support Foreign Policy',
                'description' => 'Our comprehensive approach combines research, capacity building, and strategic engagement to advance evidence-based foreign policy.',
                'items' => array(
                    array(
                        'number' => '01',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Global Visibility',
                        'description' => 'We help amplify voices and research findings through strategic communication, media engagement, and international platforms.',
                        'bullets' => array(
                            array('text' => 'Policy briefs & reports'),
                            array('text' => 'Top-tier media coverage'),
                            array('text' => 'International conferences'),
                        ),
                    ),
                    array(
                        'number' => '02',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Capacity Building',
                        'description' => 'We strengthen diplomatic capabilities and research skills through targeted training programs and knowledge exchange initiatives.',
                        'bullets' => array(
                            array('text' => 'Diplomatic skills training'),
                            array('text' => 'Strategic research workshops'),
                            array('text' => 'Policy forum discussions'),
                        ),
                    ),
                    array(
                        'number' => '03',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Analytical Insights',
                        'description' => 'We generate evidence-based insights through rigorous research, data analysis, and strategic foresight methodologies.',
                        'bullets' => array(
                            array('text' => 'Evidence-based research'),
                            array('text' => 'Data-driven analysis'),
                            array('text' => 'Strategic foresight'),
                        ),
                    ),
                ),
            ),
            
            // Block 4: Key Areas of Focus Grid
            array(
                'acf_fc_layout' => 'key_areas_grid',
                'title' => 'Key Areas of Focus',
                'items' => array(
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>',
                        'title' => 'Balance of Power',
                        'description' => 'Analyzing shifting power dynamics and strategic alignments in global politics.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l6 6m0-6l-6 6"/></svg>',
                        'title' => 'Great Power Politics',
                        'description' => 'Examining interactions between major powers and their impact on regional stability.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
                        'title' => 'Diplomatic Networks',
                        'description' => 'Mapping and strengthening diplomatic relationships and multilateral cooperation.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>',
                        'title' => 'Political Economics',
                        'description' => 'Exploring the intersection of economic policy and political strategy.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>',
                        'title' => 'Strategic Interests',
                        'description' => 'Identifying and advancing strategic national and regional interests.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
                        'title' => 'Diaspora Engagement',
                        'description' => 'Leveraging diaspora communities for diplomatic and economic partnerships.',
                    ),
                ),
            ),
            
            // Block 5: Latest Publications
            array(
                'acf_fc_layout' => 'publications_feed',
                'eyebrow' => 'Latest Publications',
                'title' => 'Latest Publications',
                'count' => 3,
                'show_filter' => 0,
                'filter_by_pillar' => 'foreign-policy',
                'style' => 'dark',
                'view_all_link' => array(
                    'url' => home_url('/publications/?pillar=foreign-policy'),
                    'title' => 'View All Foreign Policy Publications',
                    'target' => '',
                ),
            ),
            
            // Block 6: Pillar Contributors
            array(
                'acf_fc_layout' => 'team_grid',
                'eyebrow' => 'OUR EXPERTS',
                'title' => 'Pillar Contributors',
                'category' => '', // Will need to be set based on team categories
                'pillar_slug' => 'foreign-policy', // Filter by pillar
                'count' => 4,
                'simple_layout' => 1, // Use simple layout (no bio link)
                'view_all_link' => array(
                    'url' => home_url('/team/'),
                    'title' => 'View Full Team',
                    'target' => '',
                ),
            ),
            
            // Block 7: Other Research Pillars
            array(
                'acf_fc_layout' => 'other_pillars',
                'eyebrow' => 'EXPLORE MORE',
                'title' => 'Other Research Pillars',
            ),
            
            // Block 8: CTA Section
            array(
                'acf_fc_layout' => 'cta_section',
                'style' => 'primary',
                'title' => 'Interested in Foreign Policy Research?',
                'description' => 'Partner with GLOCEPS to enhance your organization\'s understanding of global politics and diplomatic strategy. Let\'s shape policy together.',
                'background_image' => '',
                'primary_button' => array(
                    'url' => home_url('/contact/'),
                    'title' => 'Learn More',
                    'target' => '',
                ),
                'secondary_button' => array(
                    'url' => home_url('/contact/'),
                    'title' => 'Contact Our Team',
                    'target' => '',
                ),
            ),
        );

        update_field('content_blocks', $blocks, $page_id);

        echo '<h3>✅ Default blocks added to Foreign Policy page</h3>';
        echo '<p><strong>Note:</strong> You will need to upload images for:</p>';
        echo '<ul>';
        echo '<li>Hero image (parliament/assembly hall)</li>';
        echo '<li>Focus Areas images (3 images for the "How We Support" section)</li>';
        echo '</ul>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit Foreign Policy Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View Foreign Policy Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

// Run if accessed directly
if (isset($_GET['setup_foreign_policy_page']) && $_GET['setup_foreign_policy_page'] === '1') {
    gloceps_setup_foreign_policy_page();
    exit;
}

