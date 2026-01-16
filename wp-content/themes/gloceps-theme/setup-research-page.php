<?php
/**
 * Setup Research Page
 * 
 * Creates the Research page with default ACF Flexible Content blocks
 * 
 * This function can be called directly or via admin_init hook
 */

if (!defined('ABSPATH')) {
    // Go up 3 levels from theme directory to reach app/public/
    require_once('../../../wp-load.php');
}

function gloceps_setup_research_page() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to run this script.');
    }

    // Check if page already exists
    $page_slug = 'research';
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        $page_id = $existing_page->ID;
        echo '<h2>Research page already exists (ID: ' . $page_id . ')</h2>';
        echo '<p>Updating with default blocks...</p>';
    } else {
        // Create the page
        $page_data = array(
            'post_title'    => 'Research',
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

        echo '<h2>Research page created (ID: ' . $page_id . ')</h2>';
    }

    // Set up ACF Flexible Content blocks
    if (function_exists('update_field')) {
        $blocks = array(
            // Block 1: Page Header
            array(
                'acf_fc_layout' => 'page_header',
                'title' => '',
                'description' => 'Our research spans five strategic pillars, providing evidence-based analysis and policy recommendations on critical issues affecting Eastern Africa and beyond.',
            ),
            // Block 2: Research Pillars
            array(
                'acf_fc_layout' => 'research_pillars',
                'eyebrow' => '',
                'title' => '',
                'description' => '',
            ),
            // Block 3: Partner With Us CTA
            array(
                'acf_fc_layout' => 'cta_section',
                'style' => 'dark',
                'title' => 'Partner With Us',
                'description' => 'Join GLOCEPS in advancing evidence-based policy making across Eastern Africa. Explore collaboration opportunities or support our research mission.',
                'background_image' => '',
                'primary_button' => array(
                    'url' => home_url('/contact/'),
                    'title' => 'Get In Touch',
                    'target' => '',
                ),
                'secondary_button' => array(
                    'url' => home_url('/contact/'),
                    'title' => 'Support Our Work',
                    'target' => '',
                ),
            ),
        );

        update_field('content_blocks', $blocks, $page_id);

        echo '<h3>✅ Default blocks added to Research page</h3>';
        echo '<p><a href="' . admin_url('post.php?post=' . $page_id . '&action=edit') . '">Edit Research Page</a></p>';
        echo '<p><a href="' . get_permalink($page_id) . '" target="_blank">View Research Page</a></p>';
    } else {
        echo '<h3>⚠️ ACF is not active. Please install Advanced Custom Fields Pro.</h3>';
    }
}

// Function is called via admin_init hook in functions.php

