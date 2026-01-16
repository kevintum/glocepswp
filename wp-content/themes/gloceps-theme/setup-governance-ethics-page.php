<?php
/**
 * Setup Governance & Ethics Pillar Page
 * 
 * Creates the Governance & Ethics page with default ACF Flexible Content blocks
 * Based on governance-ethics.html structure
 * 
 * Usage: Visit /wp-admin/?setup_governance_ethics_page=1
 */

if (!defined('ABSPATH')) {
    require_once(ABSPATH . 'wp-load.php');
}

function gloceps_setup_governance_ethics_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Get or create Research parent page
    $research_page = get_page_by_path('research');
    if (!$research_page) {
        wp_die('Research page not found. Please create it first.');
    }

    // Check if Governance & Ethics page already exists
    $page_slug = 'governance-ethics';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>Governance & Ethics page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'Governance & Ethics',
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

        echo '<h2>Governance & Ethics page created (ID: ' . $page_id . ')</h2>';
    }

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Pillar Hero Split
            array(
                'acf_fc_layout' => 'pillar_hero_split',
                'title' => 'Governance & Ethics',
                'description' => 'Championing good governance and sustainable development, inspiring ethical leadership and strengthening democratic and corporate governance systems.',
                'cta_text' => 'Explore Our Work',
                'cta_link' => '#focus-areas',
                'image' => '', // Will need to be uploaded manually
            ),
            
            // Block 2: Pillar Introduction
            array(
                'acf_fc_layout' => 'pillar_intro',
                'lead_text' => 'The pillar champions good governance and sustainable development locally and globally. It seeks to inspire ethical leadership, strengthen democratic and corporate governance systems to secure peaceful, stable and developed nations.',
                'text' => '<p>Through cutting-edge action oriented research, it redefines public policy, addresses governance issues and strengthens devolution and the rule of law. It further contributes to protection of rights and liberties and reinvigorates anti-corruption efforts in both public and corporate sectors.</p><p>The pillar collaborates with and supports anti-corruption actors, institutions and initiatives to help raise the quality of governance and inspire a new generation of value-driven, ethically-aligned public servants.</p>',
                'stats' => array(
                    array(
                        'value' => '18+',
                        'label' => 'Governance Reports',
                    ),
                    array(
                        'value' => '10',
                        'label' => 'Partner Institutions',
                    ),
                    array(
                        'value' => '25+',
                        'label' => 'Policy Experts',
                    ),
                ),
            ),
            
            // Block 3: Focus Areas (How We Support)
            array(
                'acf_fc_layout' => 'focus_areas',
                'eyebrow' => 'OUR PROSPECTS',
                'title' => 'How We Support Good Governance',
                'description' => 'We collaborate with and support anti-corruption actors, institutions and initiatives to raise the quality of governance through three key approaches.',
                'items' => array(
                    array(
                        'number' => '01',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Good Governance',
                        'description' => 'We generate usable knowledge on the link between good accountable governance, responsive public policy, responsible citizenship and national harmony and prosperity.',
                        'bullets' => array(
                            array('text' => 'Accountable governance research'),
                            array('text' => 'Public policy responsiveness'),
                            array('text' => 'Citizenship and national harmony'),
                        ),
                    ),
                    array(
                        'number' => '02',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Transparency & Accountability',
                        'description' => 'We examine the link between leadership and strong, credible, accountable institutions and unveiling knowledge that positively influences the determination of developmental outcomes.',
                        'bullets' => array(
                            array('text' => 'Leadership and institution building'),
                            array('text' => 'Credible accountability mechanisms'),
                            array('text' => 'Developmental outcome research'),
                        ),
                    ),
                    array(
                        'number' => '03',
                        'image' => '', // Will need to be uploaded manually
                        'title' => 'Unification of Political Risks',
                        'description' => 'We examine the state of constitutionalism in states and provide knowledge that supports the independence of institutions and rule of law, and promote harmony between the arms and levels of government.',
                        'bullets' => array(
                            array('text' => 'Constitutionalism research'),
                            array('text' => 'Institutional independence'),
                            array('text' => 'Intergovernmental harmony'),
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
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.97zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.97z"/></svg>',
                        'title' => 'Ethical Leadership',
                        'description' => 'Inspiring and developing value-driven leaders for public and corporate sectors.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>',
                        'title' => 'Democratic Systems',
                        'description' => 'Strengthening democratic institutions and processes for stable nations.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>',
                        'title' => 'Anti-Corruption',
                        'description' => 'Reinvigorating anti-corruption efforts in public and corporate sectors.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
                        'title' => 'Rule of Law',
                        'description' => 'Strengthening devolution and the rule of law for effective governance.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
                        'title' => 'Rights & Liberties',
                        'description' => 'Contributing to the protection of fundamental rights and civil liberties.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>',
                        'title' => 'Corporate Governance',
                        'description' => 'Enhancing corporate governance systems for sustainable business practices.',
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
                'filter_by_pillar' => 'governance-ethics',
                'style' => 'dark',
                'view_all_link' => array(
                    'url' => home_url('/publications/?pillar=governance-ethics'),
                    'title' => 'View All Governance & Ethics Publications',
                    'target' => '',
                ),
            ),
            
            // Block 6: Pillar Contributors
            array(
                'acf_fc_layout' => 'team_grid',
                'eyebrow' => 'OUR EXPERTS',
                'title' => 'Pillar Contributors',
                'category' => '',
                'pillar_slug' => 'governance-ethics',
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

        echo '<h3>✅ Default blocks added to Governance & Ethics page</h3>';
        echo '<p><strong>Note:</strong> You will need to upload images for:</p>';
        echo '<ul>';
        echo '<li>Hero image (governance/ethics related)</li>';
        echo '<li>Focus Areas images (3 images for the "How We Support" section)</li>';
        echo '</ul>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit Governance & Ethics Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View Governance & Ethics Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

// Run if accessed directly
if (isset($_GET['setup_governance_ethics_page']) && $_GET['setup_governance_ethics_page'] === '1') {
    gloceps_setup_governance_ethics_page();
    exit;
}


