<?php
/**
 * Setup Security & Defence Pillar Page
 * 
 * Creates the Security & Defence page with default ACF Flexible Content blocks
 * Based on security-defence.html structure
 * 
 * Usage: Visit /wp-admin/?setup_security_defence_page=1
 */

if (!defined('ABSPATH')) {
    require_once(ABSPATH . 'wp-load.php');
}

function gloceps_setup_security_defence_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Get or create Research parent page
    $research_page = get_page_by_path('research');
    if (!$research_page) {
        wp_die('Research page not found. Please create it first.');
    }

    // Check if Security & Defence page already exists
    $page_slug = 'security-defence';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>Security & Defence page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'Security & Defence',
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

        echo '<h2>Security & Defence page created (ID: ' . $page_id . ')</h2>';
    }

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Pillar Hero Split
            array(
                'acf_fc_layout' => 'pillar_hero_split',
                'title' => 'Security & Defence',
                'description' => 'Understanding emerging threats and opportunities in defence and security realm locally and globally, focusing on the security-development nexus and strategic national interests.',
                'cta_text' => 'Explore Our Work',
                'cta_link' => '#focus-areas',
                'image' => '', // Will need to be uploaded manually
            ),
            
            // Block 2: Pillar Introduction
            array(
                'acf_fc_layout' => 'pillar_intro',
                'lead_text' => 'The pillar focuses on understanding of emerging threats and opportunities in defence and security realm locally and globally.',
                'text' => '<p>It focuses on the security-development nexus, political violence, ethno-national conflicts, security sector reforms, radicalization and violent extremism, counterterrorism and multilateralism.</p><p>As we consistently examine shifting dynamics in defence and security, we support strategy formulation through evidence-based research and expert analysis.</p>',
                'stats' => array(
                    array(
                        'value' => '25+',
                        'label' => 'Research Publications',
                    ),
                    array(
                        'value' => '12',
                        'label' => 'Partner Institutions',
                    ),
                    array(
                        'value' => '30+',
                        'label' => 'Security Experts',
                    ),
                ),
            ),
            
            // Block 3: Focus Areas (How We Support)
            array(
                'acf_fc_layout' => 'focus_areas',
                'eyebrow' => 'OUR PROSPECTS',
                'title' => 'How We Support Security Strategy',
                'description' => 'As we consistently examine shifting dynamics in defence and security, we support strategy formulation through three key approaches.',
                'items' => array(
                    array(
                        'number' => '01',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Knowledge Integration',
                        'description' => 'We undertake, produce and disseminate cutting edge research that supports effective national policy making and strengthen the capacity of national security and defence.',
                        'bullets' => array(
                            array('text' => 'Cutting-edge security research'),
                            array('text' => 'National policy support'),
                            array('text' => 'Defence capacity strengthening'),
                        ),
                    ),
                    array(
                        'number' => '02',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Better Options',
                        'description' => 'We assist the public, private, defence and security decision makers in understanding the practices that drive better security management.',
                        'bullets' => array(
                            array('text' => 'Decision-maker advisory'),
                            array('text' => 'Security management best practices'),
                            array('text' => 'Public-private sector coordination'),
                        ),
                    ),
                    array(
                        'number' => '03',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Practical Application',
                        'description' => 'We establish a platform for national defence and security researchers to link with practitioners for in-depth knowledge transfer and collaborative research.',
                        'bullets' => array(
                            array('text' => 'Research-practitioner linkages'),
                            array('text' => 'Knowledge transfer programs'),
                            array('text' => 'Collaborative research initiatives'),
                        ),
                    ),
                ),
            ),
            
            // Block 4: Key Areas of Focus Grid
            array(
                'acf_fc_layout' => 'key_areas_grid',
                'eyebrow' => 'RESEARCH THEMES',
                'title' => 'Key Areas of Focus',
                'items' => array(
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z"/></svg>',
                        'title' => 'Security-Development Nexus',
                        'description' => 'Examining the interconnection between security and sustainable development outcomes.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>',
                        'title' => 'Political Violence',
                        'description' => 'Understanding causes, patterns and prevention of political violence in the region.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>',
                        'title' => 'Ethno-National Conflicts',
                        'description' => 'Analyzing ethnic and national identity-based conflicts and resolution strategies.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>',
                        'title' => 'Security Sector Reforms',
                        'description' => 'Supporting transformation and modernization of security institutions.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
                        'title' => 'Counterterrorism',
                        'description' => 'Developing strategies to prevent and respond to terrorism threats.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>',
                        'title' => 'Multilateralism',
                        'description' => 'Promoting cooperative security through regional and international partnerships.',
                    ),
                ),
            ),
            
            // Block 5: Latest Publications
            array(
                'acf_fc_layout' => 'publications_feed',
                'eyebrow' => 'FROM THIS PILLAR',
                'title' => 'Latest Publications',
                'count' => 3,
                'show_filter' => 0,
                'filter_by_pillar' => 'security-defence',
                'style' => 'dark',
                'view_all_link' => array(
                    'url' => home_url('/publications/?pillar=security-defence'),
                    'title' => 'View All Security & Defence Publications',
                    'target' => '',
                ),
            ),
            
            // Block 6: Pillar Contributors
            array(
                'acf_fc_layout' => 'team_grid',
                'eyebrow' => 'OUR EXPERTS',
                'title' => 'Pillar Contributors',
                'category' => '',
                'pillar_slug' => 'security-defence',
                'count' => 4,
                'simple_layout' => 1,
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
        );

        update_field('content_blocks', $blocks, $page_id);

        echo '<h3>✅ Default blocks added to Security & Defence page</h3>';
        echo '<p><strong>Note:</strong> You will need to upload images for:</p>';
        echo '<ul>';
        echo '<li>Hero image (security/defence related)</li>';
        echo '<li>Focus Areas images (3 images for the "How We Support" section)</li>';
        echo '</ul>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit Security & Defence Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View Security & Defence Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

// Run if accessed directly
if (isset($_GET['setup_security_defence_page']) && $_GET['setup_security_defence_page'] === '1') {
    gloceps_setup_security_defence_page();
    exit;
}


