<?php
/**
 * Setup Development Pillar Page
 * 
 * Creates the Development page with default ACF Flexible Content blocks
 * Based on development.html structure
 * 
 * Usage: Visit /wp-admin/?setup_development_page=1
 */

if (!defined('ABSPATH')) {
    require_once(ABSPATH . 'wp-load.php');
}

function gloceps_setup_development_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Get or create Research parent page
    $research_page = get_page_by_path('research');
    if (!$research_page) {
        wp_die('Research page not found. Please create it first.');
    }

    // Check if Development page already exists
    $page_slug = 'development';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>Development page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'Development',
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

        echo '<h2>Development page created (ID: ' . $page_id . ')</h2>';
    }

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Pillar Hero Split
            array(
                'acf_fc_layout' => 'pillar_hero_split',
                'title' => 'Development',
                'description' => 'Analyzing national and global development agenda within the ambit of Sustainable Development Goals (SDGs), advancing policy debates for social and economic transformation.',
                'cta_text' => 'Explore Our Work',
                'cta_link' => '#focus-areas',
                'image' => '', // Will need to be uploaded manually
            ),
            
            // Block 2: Pillar Introduction
            array(
                'acf_fc_layout' => 'pillar_intro',
                'lead_text' => 'The pillar analyses national and global development agenda within the ambit of Sustainable Development Goals (SDGs). It seeks to advance policy debates contributing to substantive social and economic transformation of nations.',
                'text' => '<p>While providing a trusted space for policy dialogues and sharing of policy best practices, the pillar helps to isolate and confront development challenges facing nations globally.</p><p>The pillar helps to identify and overcome developmental challenges facing nations globally through research, advisory, and inclusive policy engagement.</p>',
                'stats' => array(
                    array(
                        'value' => '22+',
                        'label' => 'Development Studies',
                    ),
                    array(
                        'value' => '17',
                        'label' => 'SDGs Addressed',
                    ),
                    array(
                        'value' => '35+',
                        'label' => 'Policy Dialogues',
                    ),
                ),
            ),
            
            // Block 3: Focus Areas (How We Support)
            array(
                'acf_fc_layout' => 'focus_areas',
                'eyebrow' => 'OUR PROSPECTS',
                'title' => 'How We Support Development',
                'description' => 'The pillar helps to identify and overcome developmental challenges facing nations globally through three key approaches.',
                'items' => array(
                    array(
                        'number' => '01',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Advisory Roles',
                        'description' => 'We build a vibrant and experienced community of practice to inform and provide technical support and advisory services to government, business and private sector and public benefit organizations.',
                        'bullets' => array(
                            array('text' => 'Technical support to government'),
                            array('text' => 'Business sector advisory'),
                            array('text' => 'Public benefit organization support'),
                        ),
                    ),
                    array(
                        'number' => '02',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Inclusivity',
                        'description' => 'We convene and manage inclusive dialogues bringing together key stakeholders, policy influential and communities to interact with the Centre\'s knowledge products.',
                        'bullets' => array(
                            array('text' => 'Multi-stakeholder dialogues'),
                            array('text' => 'Community engagement'),
                            array('text' => 'Knowledge product dissemination'),
                        ),
                    ),
                    array(
                        'number' => '03',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Equity',
                        'description' => 'We generate broad-based national consensus on what a more equitable, sustainable development and transformation path could look like for a nation. We therefore support pro-poor development research and policy advisory through cross-cutting policy research on development regionally and globally.',
                        'bullets' => array(
                            array('text' => 'Pro-poor development research'),
                            array('text' => 'Equitable transformation pathways'),
                            array('text' => 'Cross-cutting policy research'),
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
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>',
                        'title' => 'Sustainable Development Goals',
                        'description' => 'Research aligned with the UN\'s 2030 Agenda for Sustainable Development.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>',
                        'title' => 'Economic Transformation',
                        'description' => 'Advancing policies for substantive social and economic transformation.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>',
                        'title' => 'Poverty Reduction',
                        'description' => 'Pro-poor development research and equitable growth strategies.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>',
                        'title' => 'Human Capital Development',
                        'description' => 'Education, health, and skills development for sustainable growth.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>',
                        'title' => 'Infrastructure Development',
                        'description' => 'Research on critical infrastructure needs for national development.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>',
                        'title' => 'Climate & Environment',
                        'description' => 'Sustainable development pathways addressing environmental challenges.',
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
                'filter_by_pillar' => 'development',
                'style' => 'dark',
                'view_all_link' => array(
                    'url' => home_url('/publications/?pillar=development'),
                    'title' => 'View All Development Publications',
                    'target' => '',
                ),
            ),
            
            // Block 6: Pillar Contributors
            array(
                'acf_fc_layout' => 'team_grid',
                'eyebrow' => 'OUR EXPERTS',
                'title' => 'Pillar Contributors',
                'category' => '',
                'pillar_slug' => 'development',
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

        echo '<h3>✅ Default blocks added to Development page</h3>';
        echo '<p><strong>Note:</strong> You will need to upload images for:</p>';
        echo '<ul>';
        echo '<li>Hero image (development/infrastructure related)</li>';
        echo '<li>Focus Areas images (3 images for the "How We Support" section)</li>';
        echo '</ul>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit Development Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View Development Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

// Run if accessed directly
if (isset($_GET['setup_development_page']) && $_GET['setup_development_page'] === '1') {
    gloceps_setup_development_page();
    exit;
}


