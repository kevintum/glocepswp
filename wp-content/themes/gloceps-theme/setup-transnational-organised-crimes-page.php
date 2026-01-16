<?php
/**
 * Setup Transnational Organised Crimes Pillar Page
 * 
 * Creates the Transnational Organised Crimes page with default ACF Flexible Content blocks
 * Based on transnational-organised-crimes.html structure
 * 
 * Usage: Visit /wp-admin/?setup_transnational_organised_crimes_page=1
 */

if (!defined('ABSPATH')) {
    require_once(ABSPATH . 'wp-load.php');
}

function gloceps_setup_transnational_organised_crimes_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Get or create Research parent page
    $research_page = get_page_by_path('research');
    if (!$research_page) {
        wp_die('Research page not found. Please create it first.');
    }

    // Check if Transnational Organised Crimes page already exists
    $page_slug = 'transnational-organised-crimes';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>Transnational Organised Crimes page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'Transnational Organised Crimes',
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

        echo '<h2>Transnational Organised Crimes page created (ID: ' . $page_id . ')</h2>';
    }

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Pillar Hero Split
            array(
                'acf_fc_layout' => 'pillar_hero_split',
                'title' => 'Transnational Organised Crimes',
                'description' => 'Analyzing threats TOCs pose to peaceful coexistence locally, regionally, and globally—from money laundering to cyber-warfare and maritime security.',
                'cta_text' => 'Explore Our Work',
                'cta_link' => '#focus-areas',
                'image' => '', // Will need to be uploaded manually
            ),
            
            // Block 2: Pillar Introduction
            array(
                'acf_fc_layout' => 'pillar_intro',
                'lead_text' => 'The pillar focuses on the threats transnational organized crimes (TOCs) pose to peaceful coexistence locally, regionally, and globally. It contributes to policy analysis on traditional and emerging TOCs inter alia, money laundering, trafficking, counterfeiting, and wildlife crimes while emphasizing how they undermine development and the rule of law.',
                'text' => '<p>Other research interests include transnational mobility, violent extremism, proliferation of small arms and light weapons, smuggling, cyber-warfare, transboundary conflicts, and maritime security.</p><p>We contribute to the discourse of contemporary and emerging TOCs in Eastern Africa through investigative research, analytical frameworks, global partnerships, and risk management.</p>',
                'stats' => array(
                    array(
                        'value' => '18+',
                        'label' => 'TOC Studies',
                    ),
                    array(
                        'value' => '12',
                        'label' => 'Crime Categories',
                    ),
                    array(
                        'value' => '25+',
                        'label' => 'Partner Agencies',
                    ),
                ),
            ),
            
            // Block 3: Focus Areas (How We Support) - NOTE: This pillar has 4 focus areas, not 3
            array(
                'acf_fc_layout' => 'focus_areas',
                'eyebrow' => 'OUR PROSPECTS',
                'title' => 'Contributing to TOC Discourse',
                'description' => 'We contribute to the discourse of contemporary and emerging TOCs in Eastern Africa through four key approaches.',
                'items' => array(
                    array(
                        'number' => '01',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Investigative Focus',
                        'description' => 'We explore the Eastern Africa region to provide cutting-edge analysis on existing and emerging TOCs in migration, trade, mobility, environment, technology, and pastoralism. These inform opportunities and threats to sustainable development in the region.',
                        'bullets' => array(
                            array('text' => 'Migration and trade analysis'),
                            array('text' => 'Environmental crime monitoring'),
                            array('text' => 'Technology-enabled crime research'),
                        ),
                    ),
                    array(
                        'number' => '02',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Analytical Dimension',
                        'description' => 'We engage seasoned research tools to unveil in-depth assessment of the strategic environment to develop policies and strategies aligned with the goals and objectives of the Eastern Africa law enforcement priorities.',
                        'bullets' => array(
                            array('text' => 'Strategic environment assessment'),
                            array('text' => 'Policy development support'),
                            array('text' => 'Law enforcement alignment'),
                        ),
                    ),
                    array(
                        'number' => '03',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Global Partnerships',
                        'description' => 'We leverage and institutionalize strong, unique, and valuable partnerships, from academia, civil society, non-governmental organizations, government, business, and private sector, to support in-depth analysis of the intricacies of TOCs.',
                        'bullets' => array(
                            array('text' => 'Academic collaborations'),
                            array('text' => 'Civil society engagement'),
                            array('text' => 'Government and business partnerships'),
                        ),
                    ),
                    array(
                        'number' => '04',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Risk Management',
                        'description' => 'We broaden and deepen strategic knowledge and expertise on TOCs threats and concerns to enable long-term strategic interventions to nations. We assess trends and developments related to the various forms of TOCs to identify variations that would affect national security interests. We equally identify associated strategic risks and opportunities.',
                        'bullets' => array(
                            array('text' => 'Trend and threat assessment'),
                            array('text' => 'National security analysis'),
                            array('text' => 'Strategic risk identification'),
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
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>',
                        'title' => 'Money Laundering',
                        'description' => 'Tracking illicit financial flows and their impact on regional economies.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
                        'title' => 'Human Trafficking',
                        'description' => 'Combating modern slavery and forced labor across borders.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>',
                        'title' => 'Wildlife Crimes',
                        'description' => 'Protecting endangered species from poaching and illegal trade.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>',
                        'title' => 'Cyber-Warfare',
                        'description' => 'Addressing digital threats and cyber-enabled crimes.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>',
                        'title' => 'Violent Extremism',
                        'description' => 'Countering radicalization and terrorist financing networks.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>',
                        'title' => 'Maritime Security',
                        'description' => 'Safeguarding regional waters from piracy and illegal activities.',
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
                'filter_by_pillar' => 'transnational-organised-crimes',
                'style' => 'dark',
                'view_all_link' => array(
                    'url' => home_url('/publications/?pillar=transnational-organised-crimes'),
                    'title' => 'View All TOC Publications',
                    'target' => '',
                ),
            ),
            
            // Block 6: Pillar Contributors
            array(
                'acf_fc_layout' => 'team_grid',
                'eyebrow' => 'OUR EXPERTS',
                'title' => 'Pillar Contributors',
                'category' => '',
                'pillar_slug' => 'transnational-organised-crimes',
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

        echo '<h3>✅ Default blocks added to Transnational Organised Crimes page</h3>';
        echo '<p><strong>Note:</strong> You will need to upload images for:</p>';
        echo '<ul>';
        echo '<li>Hero image (TOC/cyber security related)</li>';
        echo '<li>Focus Areas images (4 images for the "Contributing to TOC Discourse" section)</li>';
        echo '</ul>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit Transnational Organised Crimes Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View Transnational Organised Crimes Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

// Run if accessed directly
if (isset($_GET['setup_transnational_organised_crimes_page']) && $_GET['setup_transnational_organised_crimes_page'] === '1') {
    gloceps_setup_transnational_organised_crimes_page();
    exit;
}


