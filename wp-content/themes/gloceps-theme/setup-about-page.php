<?php
/**
 * Setup About Page
 * 
 * Creates the About page with default ACF Flexible Content blocks
 * Based on about.html structure
 * 
 * Usage: Visit /wp-admin/?setup_about_page=1
 */

if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

function gloceps_setup_about_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Check if About page already exists
    $page_slug = 'about';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>About page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'About',
            'post_name'     => $page_slug,
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
        );

        $page_id = wp_insert_post($page_data);

        if (is_wp_error($page_id)) {
            echo '<h2>Error creating page</h2>';
            echo '<p>' . $page_id->get_error_message() . '</p>';
            return;
        }

        echo '<h2>About page created (ID: ' . $page_id . ')</h2>';
    }

    // Set page template
    update_post_meta($page_id, '_wp_page_template', 'page-templates/template-about.php');

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Page Header (handled by template, but we can add a hero block if needed)
            // Block 2: Who We Are
            array(
                'acf_fc_layout' => 'who_we_are',
                'eyebrow' => 'Who We Are',
                'title' => 'The Global Centre for Policy and Strategy',
                'lead_text' => 'A leading centre of excellence in policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.',
                'content' => '<p>The Global Centre for Policy and Strategy (GLOCEPS) is a leading centre of excellence in policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.</p><p>We conduct cutting-edge research, provide strategic advisory services, and facilitate policy dialogues that shape regional and global discourse on critical issues affecting the region.</p>',
                'image' => '', // Will need to be uploaded manually
                'cta' => array(
                    'url' => home_url('/publications/'),
                    'title' => 'Explore Our Publications',
                    'target' => '',
                ),
                'anchor_id' => 'who-we-are',
            ),
            
            // Block 3: Mission & Vision
            array(
                'acf_fc_layout' => 'mission_vision',
                'mission_title' => 'Our Mission',
                'mission_text' => 'To advance peace, security, and development in Eastern Africa through evidence-based policy research, strategic advisory, and inclusive policy dialogues that inform decision-making at local, regional, and global levels.',
                'vision_title' => 'Our Vision',
                'vision_text' => 'To be the premier centre of excellence in policy influence and strategy formulation, recognized for our transformative research, innovative solutions, and commitment to advancing sustainable peace and development in Eastern Africa.',
                'anchor_id' => 'mission-vision',
            ),
            
            // Block 4: Goals Strip
            array(
                'acf_fc_layout' => 'goals_strip',
                'eyebrow' => 'Strategic Direction',
                'title' => 'Our Goals',
                'goals' => array(
                    array('text' => 'Conduct cutting-edge research that addresses critical policy challenges in Eastern Africa'),
                    array('text' => 'Provide strategic advisory services to governments, institutions, and organizations'),
                    array('text' => 'Facilitate inclusive policy dialogues that bring together diverse stakeholders'),
                    array('text' => 'Build capacity and strengthen policy research capabilities across the region'),
                    array('text' => 'Advance peace, security, and sustainable development through evidence-based solutions'),
                ),
                'anchor_id' => 'goals',
            ),
            
            // Block 5: Timeline (The GLOCEPS Journey)
            array(
                'acf_fc_layout' => 'timeline',
                'eyebrow' => 'Our Story',
                'title' => 'The GLOCEPS Journey',
                'items' => array(
                    array(
                        'year' => '2020',
                        'title' => 'Foundation',
                        'description' => 'GLOCEPS was established as a response to the growing need for evidence-based policy research and strategic advisory services in Eastern Africa.',
                        'is_future' => false,
                    ),
                    array(
                        'year' => '2021',
                        'title' => 'First Major Research Initiative',
                        'description' => 'Launched our flagship research program focusing on peace, security, and development challenges in the region.',
                        'is_future' => false,
                    ),
                    array(
                        'year' => '2022',
                        'title' => 'Regional Expansion',
                        'description' => 'Expanded our reach across Eastern Africa, establishing partnerships with key institutions and organizations.',
                        'is_future' => false,
                    ),
                    array(
                        'year' => '2023',
                        'title' => 'Policy Impact',
                        'description' => 'Our research and advisory services began to influence policy decisions at national and regional levels.',
                        'is_future' => false,
                    ),
                    array(
                        'year' => '2024',
                        'title' => 'Digital Transformation',
                        'description' => 'Launched our digital platform to make our research and publications more accessible to a global audience.',
                        'is_future' => false,
                    ),
                    array(
                        'year' => '2025',
                        'title' => 'Future Vision',
                        'description' => 'Continuing to expand our impact and reach, with plans for new research initiatives and strategic partnerships.',
                        'is_future' => true,
                    ),
                ),
                'anchor_id' => 'journey',
            ),
            
            // Block 6: Values Grid (Our Approach)
            array(
                'acf_fc_layout' => 'values_grid',
                'eyebrow' => 'What We Do',
                'title' => 'Our Approach',
                'description' => 'We combine rigorous research, strategic thinking, and inclusive engagement to deliver solutions that make a difference.',
                'items' => array(
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124a6.68 6.68 0 01.22-.128c.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                        'title' => 'Evidence-Based Research',
                        'text' => 'We conduct rigorous, data-driven research that informs policy decisions and strategic planning.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>',
                        'title' => 'Inclusive Engagement',
                        'text' => 'We bring together diverse stakeholders to ensure all voices are heard in policy dialogues.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>',
                        'title' => 'Strategic Advisory',
                        'text' => 'We provide expert guidance to help organizations navigate complex policy challenges.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>',
                        'title' => 'Capacity Building',
                        'text' => 'We strengthen research and policy capabilities across the region through training and mentorship.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>',
                        'title' => 'Regional Focus',
                        'text' => 'We specialize in Eastern Africa, bringing deep regional knowledge and understanding to our work.',
                    ),
                    array(
                        'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/></svg>',
                        'title' => 'Innovation',
                        'text' => 'We embrace new technologies and methodologies to enhance the impact of our research and services.',
                    ),
                ),
                'anchor_id' => 'our-approach',
            ),
            
            // Block 7: Team Grid (Our People)
            array(
                'acf_fc_layout' => 'team_grid',
                'eyebrow' => 'Our People',
                'title' => 'Meet Our Team',
                'description' => 'Our team of experts brings together diverse backgrounds and deep expertise to deliver impactful research and strategic advisory services.',
                'category' => '', // Will need to be set based on team categories
                'count' => 8,
                'show_filters' => 0, // Hide filters by default
                'simple_layout' => 0, // Use full team cards with bio links
                'view_all_link' => array(
                    'url' => home_url('/team/'),
                    'title' => 'View Full Team',
                    'target' => '',
                ),
                'secondary_button' => array(
                    'url' => home_url('/team/?filter=advisors'),
                    'title' => 'Council of Advisors',
                    'target' => '',
                ),
                'anchor_id' => 'team',
            ),
            
            // Block 8: Partners Section
            array(
                'acf_fc_layout' => 'partners_section',
                'eyebrow' => 'Collaborations',
                'title' => 'Our Partners',
                'description' => 'We work alongside leading institutions, governments, and organizations to advance policy research and strategic dialogue across the region.',
                'logos' => array(), // Will need to be added manually
                'anchor_id' => 'partners',
            ),
        );

        update_field('content_blocks', $blocks, $page_id);

        echo '<h3>✅ Default blocks added to About page</h3>';
        echo '<p><strong>Note:</strong> You will need to:</p>';
        echo '<ul>';
        echo '<li>Upload images for the "Who We Are" section</li>';
        echo '<li>Set the team category for the "Our People" section</li>';
        echo '<li>Add partner logos to the "Our Partners" section</li>';
        echo '<li>Review and customize all content blocks</li>';
        echo '</ul>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit About Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View About Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

