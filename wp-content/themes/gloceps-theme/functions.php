<?php
/**
 * GLOCEPS Theme Functions
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define theme constants
 */
define( 'GLOCEPS_VERSION', '1.0.0' );
define( 'GLOCEPS_DIR', get_template_directory() );
define( 'GLOCEPS_URI', get_template_directory_uri() );

/**
 * Theme Setup
 */
function gloceps_theme_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support( 'post-thumbnails' );

    // Add custom image sizes
    add_image_size( 'gloceps-hero', 1920, 1080, true );
    add_image_size( 'gloceps-card', 600, 400, true );
    add_image_size( 'gloceps-thumbnail', 400, 300, true );
    add_image_size( 'gloceps-team', 400, 400, true );
    add_image_size( 'gloceps-publication', 600, 800, true );

    // Register navigation menus
    register_nav_menus(
        array(
            'primary'        => esc_html__( 'Primary Navigation', 'gloceps' ),
            'mobile'         => esc_html__( 'Mobile Navigation', 'gloceps' ),
            'footer-about'   => esc_html__( 'Footer - About', 'gloceps' ),
            'footer-research' => esc_html__( 'Footer - Research', 'gloceps' ),
            'footer-connect' => esc_html__( 'Footer - Connect', 'gloceps' ),
            'footer-legal'   => esc_html__( 'Footer - Legal', 'gloceps' ),
        )
    );

    // Add support for HTML5 markup
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Add custom logo support
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 100,
            'width'       => 300,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    // Add WooCommerce support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Add excerpt support to pages
    add_post_type_support( 'page', 'excerpt' );
}
add_action( 'after_setup_theme', 'gloceps_theme_setup' );

/**
 * Enqueue scripts and styles
 */
function gloceps_scripts() {
    // Google Fonts - DM Sans and Fraunces
    wp_enqueue_style(
        'gloceps-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&display=swap',
        array(),
        null
    );
    
    // Effra Font (Self-hosted) - Load if Effra is selected in theme settings
    // Check if Effra is selected for any font option
    $load_effra = false;
    if ( function_exists( 'get_field' ) ) {
        $typography = get_field( 'typography_settings', 'option' );
        if ( $typography ) {
            $heading_font = isset( $typography['heading_font'] ) ? $typography['heading_font'] : '';
            $body_font = isset( $typography['body_font'] ) ? $typography['body_font'] : '';
            $site_title_font = isset( $typography['site_title_font'] ) ? $typography['site_title_font'] : '';
            
            if ( $heading_font === 'Effra' || $body_font === 'Effra' || $site_title_font === 'Effra' ) {
                $load_effra = true;
            }
        }
    }
    
    // Load Effra font CSS if needed
    if ( $load_effra ) {
        wp_enqueue_style(
            'gloceps-effra-font',
            GLOCEPS_URI . '/assets/css/effra-fonts.css',
            array(),
            GLOCEPS_VERSION
        );
        // Load Effra-specific overrides - load after all stylesheets
        wp_enqueue_style(
            'gloceps-effra-overrides',
            GLOCEPS_URI . '/assets/css/effra-overrides.css',
            array( 'gloceps-effra-font', 'gloceps-style', 'gloceps-blocks' ),
            GLOCEPS_VERSION
        );
    }

    // Main stylesheet
    wp_enqueue_style(
        'gloceps-style',
        GLOCEPS_URI . '/assets/css/styles.css',
        array(),
        GLOCEPS_VERSION . '.' . time() // Add timestamp for cache busting
    );

    // Block components stylesheet
    wp_enqueue_style(
        'gloceps-blocks',
        GLOCEPS_URI . '/assets/css/blocks.css',
        array( 'gloceps-style' ),
        GLOCEPS_VERSION
    );
    
    // Checkout override stylesheet (only on checkout page)
    if ( is_checkout() ) {
        wp_enqueue_style(
            'gloceps-checkout',
            GLOCEPS_URI . '/assets/css/checkout-override.css',
            array( 'gloceps-style' ),
            GLOCEPS_VERSION
        );
    }

    // No special scripts needed - using Google Docs Viewer iframe for mobile

    // Main JavaScript - ensure jQuery is loaded first
    wp_enqueue_script(
        'gloceps-main',
        GLOCEPS_URI . '/assets/js/main.js',
        array( 'jquery' ),
        GLOCEPS_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script(
        'gloceps-main',
        'glocepsAjax',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'gloceps_nonce' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'gloceps_scripts' );

/**
 * Register Custom Post Types
 */
function gloceps_register_post_types() {
    
    // Publications CPT
    register_post_type(
        'publication',
        array(
            'labels'             => array(
                'name'               => _x( 'Publications', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Publication', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Publications', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'publication', 'gloceps' ),
                'add_new_item'       => __( 'Add New Publication', 'gloceps' ),
                'edit_item'          => __( 'Edit Publication', 'gloceps' ),
                'new_item'           => __( 'New Publication', 'gloceps' ),
                'view_item'          => __( 'View Publication', 'gloceps' ),
                'search_items'       => __( 'Search Publications', 'gloceps' ),
                'not_found'          => __( 'No publications found', 'gloceps' ),
                'not_found_in_trash' => __( 'No publications found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'publications',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-media-document',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
            'show_in_rest'       => true,
        )
    );

    // Events CPT
    register_post_type(
        'event',
        array(
            'labels'             => array(
                'name'               => _x( 'Events', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Event', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Events', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'event', 'gloceps' ),
                'add_new_item'       => __( 'Add New Event', 'gloceps' ),
                'edit_item'          => __( 'Edit Event', 'gloceps' ),
                'new_item'           => __( 'New Event', 'gloceps' ),
                'view_item'          => __( 'View Event', 'gloceps' ),
                'search_items'       => __( 'Search Events', 'gloceps' ),
                'not_found'          => __( 'No events found', 'gloceps' ),
                'not_found_in_trash' => __( 'No events found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'events' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true,
        )
    );

    // Team Members CPT
    // Note: Archive page enabled, but single pages are redirected via template_redirect hook
    register_post_type(
        'team_member',
        array(
            'labels'             => array(
                'name'               => _x( 'Team Members', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Team Member', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Team', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'team member', 'gloceps' ),
                'add_new_item'       => __( 'Add New Team Member', 'gloceps' ),
                'edit_item'          => __( 'Edit Team Member', 'gloceps' ),
                'new_item'           => __( 'New Team Member', 'gloceps' ),
                'view_item'          => __( 'View Team Member', 'gloceps' ),
                'search_items'       => __( 'Search Team Members', 'gloceps' ),
                'not_found'          => __( 'No team members found', 'gloceps' ),
                'not_found_in_trash' => __( 'No team members found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true, // Enable for archive, single pages redirected via template_redirect
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'team' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'show_in_rest'       => true,
        )
    );

    // Videos CPT
    // Note: Archive page enabled, but single pages are redirected via template_redirect hook
    register_post_type(
        'video',
        array(
            'labels'             => array(
                'name'               => _x( 'Videos', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Video', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Videos', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'video', 'gloceps' ),
                'add_new_item'       => __( 'Add New Video', 'gloceps' ),
                'edit_item'          => __( 'Edit Video', 'gloceps' ),
                'new_item'           => __( 'New Video', 'gloceps' ),
                'view_item'          => __( 'View Video', 'gloceps' ),
                'search_items'       => __( 'Search Videos', 'gloceps' ),
                'not_found'          => __( 'No videos found', 'gloceps' ),
                'not_found_in_trash' => __( 'No videos found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true, // Enable for archive, single pages redirected via template_redirect
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'videos',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 8,
            'menu_icon'          => 'dashicons-video-alt3',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true,
        )
    );

    // Podcasts CPT
    // Note: Archive page enabled, but single pages are redirected via template_redirect hook
    register_post_type(
        'podcast',
        array(
            'labels'             => array(
                'name'               => _x( 'Podcasts', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Podcast', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Podcasts', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'podcast', 'gloceps' ),
                'add_new_item'       => __( 'Add New Podcast', 'gloceps' ),
                'edit_item'          => __( 'Edit Podcast', 'gloceps' ),
                'new_item'           => __( 'New Podcast', 'gloceps' ),
                'view_item'          => __( 'View Podcast', 'gloceps' ),
                'search_items'       => __( 'Search Podcasts', 'gloceps' ),
                'not_found'          => __( 'No podcasts found', 'gloceps' ),
                'not_found_in_trash' => __( 'No podcasts found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true, // Enable for archive, single pages redirected via template_redirect
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'podcasts',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 9,
            'menu_icon'          => 'dashicons-microphone',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true,
        )
    );

    // Galleries CPT
    register_post_type(
        'gallery',
        array(
            'labels'             => array(
                'name'               => _x( 'Galleries', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Gallery', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Galleries', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'gallery', 'gloceps' ),
                'add_new_item'       => __( 'Add New Gallery', 'gloceps' ),
                'edit_item'          => __( 'Edit Gallery', 'gloceps' ),
                'new_item'           => __( 'New Gallery', 'gloceps' ),
                'view_item'          => __( 'View Gallery', 'gloceps' ),
                'search_items'       => __( 'Search Galleries', 'gloceps' ),
                'not_found'          => __( 'No galleries found', 'gloceps' ),
                'not_found_in_trash' => __( 'No galleries found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'galleries',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 10,
            'menu_icon'          => 'dashicons-format-gallery',
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'show_in_rest'       => true,
        )
    );

    // Articles CPT (for Media Articles)
    register_post_type(
        'article',
        array(
            'labels'             => array(
                'name'               => _x( 'Articles', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Article', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Articles', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'article', 'gloceps' ),
                'add_new_item'       => __( 'Add New Article', 'gloceps' ),
                'edit_item'          => __( 'Edit Article', 'gloceps' ),
                'new_item'           => __( 'New Article', 'gloceps' ),
                'view_item'          => __( 'View Article', 'gloceps' ),
                'search_items'       => __( 'Search Articles', 'gloceps' ),
                'not_found'          => __( 'No articles found', 'gloceps' ),
                'not_found_in_trash' => __( 'No articles found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'articles',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 11,
            'menu_icon'          => 'dashicons-admin-post',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
            'show_in_rest'       => true,
        )
    );

    // Jobs/Vacancies CPT
    register_post_type(
        'vacancy',
        array(
            'labels'             => array(
                'name'               => _x( 'Jobs', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Job', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Jobs', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'job', 'gloceps' ),
                'add_new_item'       => __( 'Add New Job', 'gloceps' ),
                'edit_item'          => __( 'Edit Job', 'gloceps' ),
                'new_item'           => __( 'New Job', 'gloceps' ),
                'view_item'          => __( 'View Job', 'gloceps' ),
                'search_items'       => __( 'Search Jobs', 'gloceps' ),
                'not_found'          => __( 'No jobs found', 'gloceps' ),
                'not_found_in_trash' => __( 'No jobs found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'vacancies',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 12,
            'menu_icon'          => 'dashicons-businessperson',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true,
        )
    );

    // Speeches CPT
    register_post_type(
        'speech',
        array(
            'labels'             => array(
                'name'               => _x( 'Speeches', 'post type general name', 'gloceps' ),
                'singular_name'      => _x( 'Speech', 'post type singular name', 'gloceps' ),
                'menu_name'          => _x( 'Speeches', 'admin menu', 'gloceps' ),
                'add_new'            => _x( 'Add New', 'speech', 'gloceps' ),
                'add_new_item'       => __( 'Add New Speech', 'gloceps' ),
                'edit_item'          => __( 'Edit Speech', 'gloceps' ),
                'new_item'           => __( 'New Speech', 'gloceps' ),
                'view_item'          => __( 'View Speech', 'gloceps' ),
                'search_items'       => __( 'Search Speeches', 'gloceps' ),
                'not_found'          => __( 'No speeches found', 'gloceps' ),
                'not_found_in_trash' => __( 'No speeches found in Trash', 'gloceps' ),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 
                'slug' => 'speeches',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 12,
            'menu_icon'          => 'dashicons-microphone',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true,
        )
    );
}
add_action( 'init', 'gloceps_register_post_types' );

/**
 * Register Custom Taxonomies
 */
function gloceps_register_taxonomies() {
    
    // Publication Type
    register_taxonomy(
        'publication_type',
        'publication',
        array(
            'labels'            => array(
                'name'              => _x( 'Publication Types', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Publication Type', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Publication Types', 'gloceps' ),
                'all_items'         => __( 'All Publication Types', 'gloceps' ),
                'edit_item'         => __( 'Edit Publication Type', 'gloceps' ),
                'update_item'       => __( 'Update Publication Type', 'gloceps' ),
                'add_new_item'      => __( 'Add New Publication Type', 'gloceps' ),
                'new_item_name'     => __( 'New Publication Type Name', 'gloceps' ),
                'menu_name'         => __( 'Publication Types', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'publication-type' ),
            'show_in_rest'      => true,
        )
    );

    // Research Pillar (shared across multiple CPTs)
    // Note: Taxonomy archive URLs redirect to /research/{term-slug}/ pages
    register_taxonomy(
        'research_pillar',
        array( 'publication', 'event', 'video', 'podcast', 'article' ),
        array(
            'labels'            => array(
                'name'              => _x( 'Research Pillars', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Research Pillar', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Research Pillars', 'gloceps' ),
                'all_items'         => __( 'All Research Pillars', 'gloceps' ),
                'edit_item'         => __( 'Edit Research Pillar', 'gloceps' ),
                'update_item'       => __( 'Update Research Pillar', 'gloceps' ),
                'add_new_item'      => __( 'Add New Research Pillar', 'gloceps' ),
                'new_item_name'     => __( 'New Research Pillar Name', 'gloceps' ),
                'menu_name'         => __( 'Research Pillars', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true, // Keep public so rewrite rules are registered
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'research-pillar' ), // Keep rewrite for redirects to work
            'show_in_rest'      => true,
        )
    );

    // Event Type
    register_taxonomy(
        'event_type',
        'event',
        array(
            'labels'            => array(
                'name'              => _x( 'Event Types', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Event Type', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Event Types', 'gloceps' ),
                'all_items'         => __( 'All Event Types', 'gloceps' ),
                'edit_item'         => __( 'Edit Event Type', 'gloceps' ),
                'update_item'       => __( 'Update Event Type', 'gloceps' ),
                'add_new_item'      => __( 'Add New Event Type', 'gloceps' ),
                'new_item_name'     => __( 'New Event Type Name', 'gloceps' ),
                'menu_name'         => __( 'Event Types', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'event-type' ),
            'show_in_rest'      => true,
        )
    );

    // Team Category
    // Note: Taxonomy archives disabled - team categories used for filtering only
    register_taxonomy(
        'team_category',
        'team_member',
        array(
            'labels'            => array(
                'name'              => _x( 'Team Categories', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Team Category', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Team Categories', 'gloceps' ),
                'all_items'         => __( 'All Team Categories', 'gloceps' ),
                'edit_item'         => __( 'Edit Team Category', 'gloceps' ),
                'update_item'       => __( 'Update Team Category', 'gloceps' ),
                'add_new_item'      => __( 'Add New Team Category', 'gloceps' ),
                'new_item_name'     => __( 'New Team Category Name', 'gloceps' ),
                'menu_name'         => __( 'Team Categories', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => false, // Disable public archives
            'publicly_queryable' => false, // Disable public querying
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => false, // Disable rewrite to prevent archive URLs
            'show_in_rest'      => true,
        )
    );

    // Video Category
    register_taxonomy(
        'video_category',
        'video',
        array(
            'labels'            => array(
                'name'              => _x( 'Video Categories', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Video Category', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Video Categories', 'gloceps' ),
                'all_items'         => __( 'All Video Categories', 'gloceps' ),
                'edit_item'         => __( 'Edit Video Category', 'gloceps' ),
                'update_item'       => __( 'Update Video Category', 'gloceps' ),
                'add_new_item'      => __( 'Add New Video Category', 'gloceps' ),
                'new_item_name'     => __( 'New Video Category Name', 'gloceps' ),
                'menu_name'         => __( 'Video Categories', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'video-category' ),
            'show_in_rest'      => true,
        )
    );

    // Gallery Category
    register_taxonomy(
        'gallery_category',
        'gallery',
        array(
            'labels'            => array(
                'name'              => _x( 'Gallery Categories', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Gallery Category', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Gallery Categories', 'gloceps' ),
                'all_items'         => __( 'All Gallery Categories', 'gloceps' ),
                'edit_item'         => __( 'Edit Gallery Category', 'gloceps' ),
                'update_item'       => __( 'Update Gallery Category', 'gloceps' ),
                'add_new_item'      => __( 'Add New Gallery Category', 'gloceps' ),
                'new_item_name'     => __( 'New Gallery Category Name', 'gloceps' ),
                'menu_name'         => __( 'Gallery Categories', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'gallery-category' ),
            'show_in_rest'      => true,
        )
    );

    // Podcast Category
    register_taxonomy(
        'podcast_category',
        'podcast',
        array(
            'labels'            => array(
                'name'              => _x( 'Podcast Categories', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Podcast Category', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Podcast Categories', 'gloceps' ),
                'all_items'         => __( 'All Podcast Categories', 'gloceps' ),
                'edit_item'         => __( 'Edit Podcast Category', 'gloceps' ),
                'update_item'       => __( 'Update Podcast Category', 'gloceps' ),
                'add_new_item'      => __( 'Add New Podcast Category', 'gloceps' ),
                'new_item_name'     => __( 'New Podcast Category Name', 'gloceps' ),
                'menu_name'         => __( 'Podcast Categories', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'podcast-category' ),
            'show_in_rest'      => true,
        )
    );

    // Article Category
    register_taxonomy(
        'article_category',
        'article',
        array(
            'labels'            => array(
                'name'              => _x( 'Article Categories', 'taxonomy general name', 'gloceps' ),
                'singular_name'     => _x( 'Article Category', 'taxonomy singular name', 'gloceps' ),
                'search_items'      => __( 'Search Article Categories', 'gloceps' ),
                'all_items'         => __( 'All Article Categories', 'gloceps' ),
                'edit_item'         => __( 'Edit Article Category', 'gloceps' ),
                'update_item'       => __( 'Update Article Category', 'gloceps' ),
                'add_new_item'      => __( 'Add New Article Category', 'gloceps' ),
                'new_item_name'     => __( 'New Article Category Name', 'gloceps' ),
                'menu_name'         => __( 'Article Categories', 'gloceps' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'article-category' ),
            'show_in_rest'      => true,
        )
    );
}
add_action( 'init', 'gloceps_register_taxonomies' );

/**
 * Insert default taxonomy terms
 */
function gloceps_insert_default_terms() {
    // Check if we've already inserted default terms
    if ( get_option( 'gloceps_default_terms_inserted' ) ) {
        return;
    }

    // Publication Types
    $publication_types = array(
        'Daily Influential Briefs',
        'Weekly Influential Briefs',
        'Special Focus',
        'Influential Bulletins',
        'Policy Papers',
        'Conference Papers & Proceedings',
        'Research Papers',
        'Mainstream Media Articles',
        'What Others Say',
    );

    foreach ( $publication_types as $type ) {
        if ( ! term_exists( $type, 'publication_type' ) ) {
            wp_insert_term( $type, 'publication_type' );
        }
    }

    // Research Pillars
    $research_pillars = array(
        'Foreign Policy',
        'Security & Defence',
        'Governance & Ethics',
        'Development',
        'Transnational Organised Crimes',
    );

    foreach ( $research_pillars as $pillar ) {
        if ( ! term_exists( $pillar, 'research_pillar' ) ) {
            wp_insert_term( $pillar, 'research_pillar' );
        }
    }

    // Event Types
    $event_types = array(
        'Conference',
        'Workshop',
        'Roundtable',
        'Webinar',
        'Official Event',
        'Diplomatic',
    );

    foreach ( $event_types as $type ) {
        if ( ! term_exists( $type, 'event_type' ) ) {
            wp_insert_term( $type, 'event_type' );
        }
    }

    // Team Categories
    $team_categories = array(
        'Founding Council Members',
        'Council of Advisors',
        'Leadership',
        'Research',
        'Operations',
    );

    foreach ( $team_categories as $category ) {
        if ( ! term_exists( $category, 'team_category' ) ) {
            wp_insert_term( $category, 'team_category' );
        }
    }

    // Video Categories
    $video_categories = array(
        'Conferences',
        'Interviews',
        'Documentaries',
        'Webinars',
    );

    foreach ( $video_categories as $category ) {
        if ( ! term_exists( $category, 'video_category' ) ) {
            wp_insert_term( $category, 'video_category' );
        }
    }

    update_option( 'gloceps_default_terms_inserted', true );
}
add_action( 'init', 'gloceps_insert_default_terms', 11 );

/**
 * Custom walker for primary navigation with dropdowns
 */
class GLOCEPS_Nav_Walker extends Walker_Nav_Menu {
    
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<div class="nav__dropdown">';
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</div>';
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'nav__item';

        if ( in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-parent', $classes ) ) {
            $active_class = 'nav__link--active';
        } else {
            $active_class = '';
        }

        $has_children = in_array( 'menu-item-has-children', $classes );
        
        // Add nav__item--has-dropdown class for CSS targeting
        if ( $has_children ) {
            $classes[] = 'nav__item--has-dropdown';
        }

        // Get target and rel attributes
        $target = ! empty( $item->target ) ? esc_attr( $item->target ) : '';
        $rel = ! empty( $item->xfn ) ? esc_attr( $item->xfn ) : '';
        
        // Add rel="noopener noreferrer" for security when target="_blank"
        if ( $target === '_blank' && empty( $rel ) ) {
            $rel = 'noopener noreferrer';
        }
        
        // Build attributes string
        $attributes = 'href="' . esc_url( $item->url ) . '"';
        $attributes .= ' class="nav__link ' . esc_attr( $active_class ) . '"';
        if ( $target ) {
            $attributes .= ' target="' . $target . '"';
        }
        if ( $rel ) {
            $attributes .= ' rel="' . $rel . '"';
        }
        
        if ( $depth === 0 ) {
            $output .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= '<a ' . $attributes . '>';
            $output .= esc_html( $item->title );
            
            if ( $has_children ) {
                $output .= '<svg class="nav__chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">';
                $output .= '<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>';
                $output .= '</svg>';
            }
            
            $output .= '</a>';
        } else {
            // For dropdown links, also include target and rel
            $dropdown_attributes = 'href="' . esc_url( $item->url ) . '" class="nav__dropdown-link"';
            if ( $target ) {
                $dropdown_attributes .= ' target="' . $target . '"';
            }
            if ( $rel ) {
                $dropdown_attributes .= ' rel="' . $rel . '"';
            }
            $output .= '<a ' . $dropdown_attributes . '>';
            $output .= esc_html( $item->title );
            $output .= '</a>';
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</li>';
        }
    }
}

/**
 * Register widget areas
 */
function gloceps_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Sidebar', 'gloceps' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'gloceps' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer Widget Area', 'gloceps' ),
            'id'            => 'footer-widgets',
            'description'   => esc_html__( 'Add widgets to the footer area.', 'gloceps' ),
            'before_widget' => '<div id="%1$s" class="footer__widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer__widget-title">',
            'after_title'   => '</h4>',
        )
    );
}
add_action( 'widgets_init', 'gloceps_widgets_init' );

/**
 * Get research pillar color
 */
function gloceps_get_pillar_color( $pillar_slug ) {
    $colors = array(
        'foreign-policy'                => '#3f93c1',
        'security-defence'              => '#c4a35a',
        'governance-ethics'             => '#70b544',
        'development'                   => '#a855f7',
        'transnational-organised-crimes' => '#ef4444',
    );
    
    return isset( $colors[ $pillar_slug ] ) ? $colors[ $pillar_slug ] : '#3f93c1';
}

/**
 * Include additional theme files
 * Note: ACF options pages are registered in inc/acf-fields.php
 */
require_once GLOCEPS_DIR . '/inc/template-functions.php';
require_once GLOCEPS_DIR . '/inc/template-tags.php';
require_once GLOCEPS_DIR . '/inc/nav-walkers.php';
require_once GLOCEPS_DIR . '/inc/newsletter-subscriptions.php';

/**
 * Enqueue admin scripts for ACF Flexible Content enhancements
 */
function gloceps_admin_scripts($hook) {
    // Load on post edit pages (both classic and block editor)
    if (!in_array($hook, array('post.php', 'post-new.php'))) {
        return;
    }
    
    // Check if we're in block editor - ACF flexible content works in both
    $is_block_editor = function_exists('get_current_screen') && get_current_screen() && get_current_screen()->is_block_editor();
    
    // Ensure jQuery UI Sortable is loaded for ACF drag-and-drop
    wp_enqueue_script('jquery-ui-sortable');
    
    // Enqueue our ACF enhancements script
    // Use higher priority dependencies to ensure ACF is loaded
    wp_enqueue_script(
        'gloceps-admin-acf',
        GLOCEPS_URI . '/assets/js/admin-acf.js',
        array('jquery', 'jquery-ui-sortable', 'acf-input', 'acf'),
        GLOCEPS_VERSION,
        true
    );
    
    // Enqueue ACF admin styles
    wp_enqueue_style(
        'gloceps-admin-acf',
        GLOCEPS_URI . '/assets/css/admin-acf.css',
        array('acf-input'),
        GLOCEPS_VERSION
    );
    
    // Add inline script to check if script loaded
    wp_add_inline_script('gloceps-admin-acf', '
        console.log("GLOCEPS ACF Admin script loaded", {
            hook: "' . esc_js($hook) . '",
            is_block_editor: ' . ($is_block_editor ? 'true' : 'false') . ',
            acf_defined: typeof acf !== "undefined",
            jquery_ui_sortable: typeof jQuery.fn.sortable !== "undefined"
        });
    ', 'after');
}
add_action('admin_enqueue_scripts', 'gloceps_admin_scripts', 20);
require_once GLOCEPS_DIR . '/inc/acf-fields.php';

/**
 * Check for WooCommerce
 */
if ( class_exists( 'WooCommerce' ) ) {
    require_once GLOCEPS_DIR . '/inc/woocommerce-functions.php';
}

/**
 * Setup Research Page (runs on admin_init if requested)
 */
add_action('admin_init', function() {
    if (isset($_GET['setup_research_page']) && $_GET['setup_research_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-research-page.php';
        gloceps_setup_research_page();
        exit;
    }
    
    if (isset($_GET['setup_foreign_policy_page']) && $_GET['setup_foreign_policy_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-foreign-policy-page.php';
        gloceps_setup_foreign_policy_page();
        exit;
    }
    
    if (isset($_GET['setup_security_defence_page']) && $_GET['setup_security_defence_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-security-defence-page.php';
        gloceps_setup_security_defence_page();
        exit;
    }
    
    if (isset($_GET['setup_governance_ethics_page']) && $_GET['setup_governance_ethics_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-governance-ethics-page.php';
        gloceps_setup_governance_ethics_page();
        exit;
    }
    
    if (isset($_GET['setup_development_page']) && $_GET['setup_development_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-development-page.php';
        gloceps_setup_development_page();
        exit;
    }
    
    if (isset($_GET['setup_transnational_organised_crimes_page']) && $_GET['setup_transnational_organised_crimes_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-transnational-organised-crimes-page.php';
        gloceps_setup_transnational_organised_crimes_page();
        exit;
    }
    
    if (isset($_GET['setup_about_page']) && $_GET['setup_about_page'] === '1' && current_user_can('manage_options')) {
        require_once GLOCEPS_DIR . '/setup-about-page.php';
        gloceps_setup_about_page();
        exit;
    }
}, 1);


/**
 * Output dynamic CSS based on theme settings
 */
function gloceps_output_dynamic_css() {
    if ( ! function_exists( 'get_field' ) ) {
        return;
    }
    
    $css = '';
    
    // Typography Settings
    $typography = get_field( 'typography_settings', 'option' );
    if ( $typography ) {
        $heading_font = isset( $typography['heading_font'] ) ? $typography['heading_font'] : 'Fraunces';
        $body_font = isset( $typography['body_font'] ) ? $typography['body_font'] : 'DM Sans';
        $site_title_font = isset( $typography['site_title_font'] ) ? $typography['site_title_font'] : 'Tahoma';
        $h1_size = isset( $typography['heading_size_h1'] ) ? $typography['heading_size_h1'] : 'text-5xl';
        $h2_size = isset( $typography['heading_size_h2'] ) ? $typography['heading_size_h2'] : 'text-4xl';
        $h3_size = isset( $typography['heading_size_h3'] ) ? $typography['heading_size_h3'] : 'text-3xl';
        
        // Map font names to CSS font-family values
        $font_map = array(
            'Effra' => '"effra", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
            'Fraunces' => '"Fraunces", "Georgia", serif',
            'DM Sans' => '"DM Sans", -apple-system, BlinkMacSystemFont, sans-serif',
            'Georgia' => 'Georgia, serif',
            'Playfair Display' => '"Playfair Display", "Georgia", serif',
            'Merriweather' => '"Merriweather", "Georgia", serif',
            'Inter' => '"Inter", -apple-system, BlinkMacSystemFont, sans-serif',
            'Open Sans' => '"Open Sans", -apple-system, BlinkMacSystemFont, sans-serif',
            'Lato' => '"Lato", -apple-system, BlinkMacSystemFont, sans-serif',
            'Roboto' => '"Roboto", -apple-system, BlinkMacSystemFont, sans-serif',
            'Tahoma' => 'Tahoma, Verdana, sans-serif',
            'Arial' => 'Arial, Helvetica, sans-serif',
        );
        
        $heading_font_family = isset( $font_map[ $heading_font ] ) ? $font_map[ $heading_font ] : $font_map['Fraunces'];
        $body_font_family = isset( $font_map[ $body_font ] ) ? $font_map[ $body_font ] : $font_map['DM Sans'];
        $site_title_font_family = isset( $font_map[ $site_title_font ] ) ? $font_map[ $site_title_font ] : $font_map['Tahoma'];
        
        // For Effra, use optimized font sizes (Effra works well at standard sizes)
        // Effra is a humanist sans-serif, similar to other sans-serif fonts in sizing
        $size_map = array(
            'text-2xl' => 'clamp(1.25rem, 1.5vw, 1.5rem)',
            'text-3xl' => 'clamp(1.5rem, 2vw, 1.875rem)',
            'text-4xl' => 'clamp(1.875rem, 2.5vw, 2.25rem)',
            'text-5xl' => 'clamp(2.25rem, 3.5vw, 3rem)',
            'text-6xl' => 'clamp(2.75rem, 5vw, 4rem)',
            'text-7xl' => 'clamp(3.5rem, 7vw, 5.5rem)',
        );
        
        $h1_size_value = isset( $size_map[ $h1_size ] ) ? $size_map[ $h1_size ] : $size_map['text-5xl'];
        $h2_size_value = isset( $size_map[ $h2_size ] ) ? $size_map[ $h2_size ] : $size_map['text-4xl'];
        $h3_size_value = isset( $size_map[ $h3_size ] ) ? $size_map[ $h3_size ] : $size_map['text-3xl'];
        
        $css .= ':root {';
        $css .= '--font-display: ' . $heading_font_family . ';';
        $css .= '--font-primary: ' . $body_font_family . ';';
        $css .= '--font-site-title: ' . $site_title_font_family . ';';
        $css .= '--h1-size: ' . $h1_size_value . ';';
        $css .= '--h2-size: ' . $h2_size_value . ';';
        $css .= '--h3-size: ' . $h3_size_value . ';';
        $css .= '}';
        
        $css .= 'h1 { font-size: var(--h1-size) !important; }';
        $css .= 'h2 { font-size: var(--h2-size) !important; }';
        $css .= 'h3 { font-size: var(--h3-size) !important; }';
        
        // Apply site title font
        $css .= '.header__logo-text strong { font-family: var(--font-site-title) !important; }';
    }
    
    // Text Truncation Settings
    $truncation = get_field( 'text_truncation', 'option' );
    if ( $truncation && isset( $truncation['enable_truncation'] ) && $truncation['enable_truncation'] ) {
        $title_limit = isset( $truncation['title_word_limit'] ) ? intval( $truncation['title_word_limit'] ) : 10;
        $desc_limit = isset( $truncation['description_word_limit'] ) ? intval( $truncation['description_word_limit'] ) : 20;
        
        // Add CSS variables for truncation
        $css .= ':root {';
        $css .= '--title-word-limit: ' . $title_limit . ';';
        $css .= '--desc-word-limit: ' . $desc_limit . ';';
        $css .= '--truncation-enabled: 1;';
        $css .= '}';
        
        // Enable truncation CSS
        $css .= '.podcast-card__title, .article-card__title, .video-card__title, .speech-card__title, .event-card__title, .publication-card__title, .gallery-card__title, .pillar-card__title, .other-pillar-card__title, .theme-card__title {';
        $css .= 'display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word;';
        $css .= '}';
        
        $css .= '.podcast-card__excerpt, .article-card__excerpt, .video-card__excerpt, .speech-card__excerpt, .event-card__excerpt, .publication-card__excerpt, .gallery-card__excerpt {';
        $css .= 'display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word;';
        $css .= '}';
    } else {
        // Disable truncation
        $css .= ':root {';
        $css .= '--truncation-enabled: 0;';
        $css .= '}';
        
        // Remove truncation CSS
        $css .= '.podcast-card__title, .article-card__title, .video-card__title, .speech-card__title, .event-card__title, .publication-card__title, .gallery-card__title, .pillar-card__title, .other-pillar-card__title, .theme-card__title {';
        $css .= 'display: block; overflow: visible; text-overflow: clip;';
        $css .= '}';
        
        $css .= '.podcast-card__excerpt, .article-card__excerpt, .video-card__excerpt, .speech-card__excerpt, .event-card__excerpt, .publication-card__excerpt, .gallery-card__excerpt {';
        $css .= 'display: block; overflow: visible; text-overflow: clip;';
        $css .= '}';
    }
    
    // Color Settings
    $colors = get_field( 'color_settings', 'option' );
    if ( $colors ) {
        $primary = isset( $colors['primary_color'] ) ? $colors['primary_color'] : '#3f93c1';
        $secondary = isset( $colors['secondary_color'] ) ? $colors['secondary_color'] : '#70b544';
        $tertiary = isset( $colors['tertiary_color'] ) ? $colors['tertiary_color'] : '#c4a35a';
        
        // Calculate darker/lighter variants
        $primary_dark = gloceps_darken_color( $primary, 20 );
        $primary_light = gloceps_lighten_color( $primary, 20 );
        $primary_muted = gloceps_hex_to_rgba( $primary, 0.15 );
        
        // Calculate secondary color variants
        $secondary_dark = gloceps_darken_color( $secondary, 20 );
        $secondary_light = gloceps_lighten_color( $secondary, 20 );
        
        $primary_rgb = gloceps_hex_to_rgb( $primary );
        $secondary_rgb = gloceps_hex_to_rgb( $secondary );
        
        $css .= ':root {';
        $css .= '--color-primary: ' . esc_attr( $primary ) . ';';
        $css .= '--color-primary-dark: ' . esc_attr( $primary_dark ) . ';';
        $css .= '--color-primary-light: ' . esc_attr( $primary_light ) . ';';
        $css .= '--color-primary-muted: ' . esc_attr( $primary_muted ) . ';';
        $css .= '--color-secondary: ' . esc_attr( $secondary ) . ';';
        $css .= '--color-secondary-dark: ' . esc_attr( $secondary_dark ) . ';';
        $css .= '--color-secondary-light: ' . esc_attr( $secondary_light ) . ';';
        $css .= '--color-tertiary: ' . esc_attr( $tertiary ) . ';';
        $css .= '--gradient-primary-secondary: linear-gradient(135deg, ' . esc_attr( $primary ) . ' 0%, ' . esc_attr( $secondary ) . ' 100%);';
        $css .= '--gradient-primary-secondary-subtle: linear-gradient(135deg, rgba(' . $primary_rgb . ', 0.08) 0%, rgba(' . $secondary_rgb . ', 0.08) 100%);';
        $css .= '}';
    }
    
    // Footer Background Colors
    $footer_bg = get_field( 'footer_backgrounds', 'option' );
    if ( $footer_bg ) {
        $cta_bg = isset( $footer_bg['footer_cta_bg'] ) ? $footer_bg['footer_cta_bg'] : '#1a1a1a';
        $main_bg = isset( $footer_bg['footer_main_bg'] ) ? $footer_bg['footer_main_bg'] : '#0d0d0d';
        $bottom_bg = isset( $footer_bg['footer_bottom_bg'] ) ? $footer_bg['footer_bottom_bg'] : '#141414';
        
        $css .= '.footer__cta { background-color: ' . esc_attr( $cta_bg ) . ' !important; }';
        $css .= '.footer__main { background-color: ' . esc_attr( $main_bg ) . ' !important; }';
        $css .= '.footer__bottom { background-color: ' . esc_attr( $bottom_bg ) . ' !important; }';
    }
    
    if ( ! empty( $css ) ) {
        echo '<style id="gloceps-dynamic-css">' . $css . '</style>';
    }
}
add_action( 'wp_head', 'gloceps_output_dynamic_css', 100 );

/**
 * Add noindex meta tag for disabled single pages (video, podcast, speech)
 * This prevents search engines from indexing these pages even if redirects fail
 */
function gloceps_add_noindex_for_disabled_singles() {
    // Only run on frontend
    if ( is_admin() ) {
        return;
    }
    
    // Add noindex for video, podcast, and speech single pages
    if ( is_singular( 'video' ) || is_singular( 'podcast' ) || is_singular( 'speech' ) ) {
        echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
    }
}
add_action( 'wp_head', 'gloceps_add_noindex_for_disabled_singles', 1 );

/**
 * Helper function to darken a hex color
 */
function gloceps_darken_color( $hex, $percent = 20 ) {
    $hex = str_replace( '#', '', $hex );
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    
    $r = max( 0, min( 255, $r - ( $r * $percent / 100 ) ) );
    $g = max( 0, min( 255, $g - ( $g * $percent / 100 ) ) );
    $b = max( 0, min( 255, $b - ( $b * $percent / 100 ) ) );
    
    return '#' . str_pad( dechex( round( $r ) ), 2, '0', STR_PAD_LEFT ) . 
           str_pad( dechex( round( $g ) ), 2, '0', STR_PAD_LEFT ) . 
           str_pad( dechex( round( $b ) ), 2, '0', STR_PAD_LEFT );
}

/**
 * Helper function to lighten a hex color
 */
function gloceps_lighten_color( $hex, $percent = 20 ) {
    $hex = str_replace( '#', '', $hex );
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    
    $r = max( 0, min( 255, $r + ( ( 255 - $r ) * $percent / 100 ) ) );
    $g = max( 0, min( 255, $g + ( ( 255 - $g ) * $percent / 100 ) ) );
    $b = max( 0, min( 255, $b + ( ( 255 - $b ) * $percent / 100 ) ) );
    
    return '#' . str_pad( dechex( round( $r ) ), 2, '0', STR_PAD_LEFT ) . 
           str_pad( dechex( round( $g ) ), 2, '0', STR_PAD_LEFT ) . 
           str_pad( dechex( round( $b ) ), 2, '0', STR_PAD_LEFT );
}

/**
 * Helper function to convert hex to rgba
 */
function gloceps_hex_to_rgba( $hex, $alpha = 1 ) {
    $hex = str_replace( '#', '', $hex );
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    
    return 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
}

/**
 * Helper function to convert hex to RGB string (for CSS gradients)
 */
function gloceps_hex_to_rgb( $hex ) {
    $hex = str_replace( '#', '', $hex );
    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    
    return $r . ', ' . $g . ', ' . $b;
}

/**
 * Enhance search query to include all post types by default
 */
function gloceps_enhance_search_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        // If no post_type is specified, include all custom post types
        if ( empty( $query->get( 'post_type' ) ) ) {
            $query->set( 'post_type', array(
                'publication',
                'event',
                'team_member',
                'video',
                'podcast',
                'gallery',
                'article',
                'speech',
                'post',
                'page',
            ) );
        }
        
        // Increase posts per page for search results
        if ( $query->get( 'posts_per_page' ) == get_option( 'posts_per_page' ) ) {
            $query->set( 'posts_per_page', 20 );
        }
    }
}
add_action( 'pre_get_posts', 'gloceps_enhance_search_query' );

/**
 * Fix pagination for custom post type archives
 * Ensures paged query parameter is recognized for archive pages
 */
function gloceps_fix_cpt_archive_pagination_request( $query_vars ) {
    // Check if this is a request to any custom post type archive with paged parameter
    if ( ! is_admin() && isset( $_SERVER['REQUEST_URI'] ) ) {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Define custom post types and their archive slugs
        $cpt_archives = array(
            'publication' => '/publications',
            'video' => '/videos',
            'podcast' => '/podcasts',
            'article' => '/articles',
            'gallery' => '/galleries',
            'speech' => '/speeches',
        );
        
        // Check each post type
        foreach ( $cpt_archives as $post_type => $archive_slug ) {
            if ( strpos( $request_uri, $archive_slug ) !== false || 
                 ( isset( $query_vars['post_type'] ) && $query_vars['post_type'] === $post_type ) ) {
                
                // If paged is in GET but not in query_vars, add it
                if ( isset( $_GET['paged'] ) && ! isset( $query_vars['paged'] ) ) {
                    $paged = absint( $_GET['paged'] );
                    if ( $paged > 0 ) {
                        $query_vars['paged'] = $paged;
                    }
                }
                
                // Ensure post_type is set
                if ( ! isset( $query_vars['post_type'] ) ) {
                    $query_vars['post_type'] = $post_type;
                }
                
                break; // Found matching post type, no need to continue
            }
        }
    }
    
    return $query_vars;
}
add_filter( 'request', 'gloceps_fix_cpt_archive_pagination_request', 10, 1 );

/**
 * Fix pagination for custom post type archives in main query
 */
function gloceps_fix_cpt_archive_pagination( $query ) {
    // Only run on frontend, main query
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    
    // Define custom post types and their archive slugs
    $cpt_archives = array(
        'publication' => '/publications',
        'video' => '/videos',
        'podcast' => '/podcasts',
        'article' => '/articles',
        'gallery' => '/galleries',
        'speech' => '/speeches',
    );
    
    // Check each post type
    foreach ( $cpt_archives as $post_type => $archive_slug ) {
        if ( $query->is_post_type_archive( $post_type ) || 
             ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], $archive_slug ) !== false ) ) {
            
            // Set post type explicitly
            $query->set( 'post_type', $post_type );
            
            // Handle paged parameter from query string
            if ( isset( $_GET['paged'] ) ) {
                $paged = absint( $_GET['paged'] );
                if ( $paged > 0 ) {
                    $query->set( 'paged', $paged );
                }
            }
            
            break; // Found matching post type, no need to continue
        }
    }
}
add_action( 'pre_get_posts', 'gloceps_fix_cpt_archive_pagination', 5 );

/**
 * Redirect research_pillar taxonomy archive URLs to /research/{term-slug}/ pages
 * 
 * Handles redirects from /research-pillar/{term-slug}/ to /research/{term-slug}/
 * This ensures old taxonomy archive URLs redirect to the proper custom pages
 */
function gloceps_redirect_research_pillar_archives() {
    // Only run on frontend
    if ( is_admin() ) {
        return;
    }
    
    // Check if this is a research_pillar taxonomy archive request
    if ( is_tax( 'research_pillar' ) ) {
        $term = get_queried_object();
        
        if ( $term && ! is_wp_error( $term ) ) {
            // First, try to find a page at /research/{term-slug}/
            $page = get_page_by_path( 'research/' . $term->slug );
            
            if ( $page ) {
                // Page exists, redirect to it
                wp_safe_redirect( get_permalink( $page->ID ), 301 );
                exit;
            } else {
                // Page doesn't exist yet, but we still want to prevent the taxonomy archive
                // Redirect to the research parent page or home
                $research_page = get_page_by_path( 'research' );
                if ( $research_page ) {
                    wp_safe_redirect( get_permalink( $research_page->ID ), 301 );
                } else {
                    wp_safe_redirect( home_url( '/' ), 301 );
                }
                exit;
            }
        }
    }
}
add_action( 'template_redirect', 'gloceps_redirect_research_pillar_archives' );

/**
 * Redirect disabled single post pages to appropriate archive pages
 * 
 * Handles redirects for:
 * - Team member single pages -> /team/ archive
 * - Video single pages -> /videos/ archive (or home if no archive)
 * - Podcast single pages -> /podcasts/ archive (or home if no archive)
 * - Speech single pages -> /speeches/ archive (or home if no archive)
 * - Team category taxonomy archives -> /team/ archive
 */
function gloceps_redirect_disabled_single_pages() {
    // Only run on frontend
    if ( is_admin() ) {
        return;
    }
    
    // IMPORTANT: Do NOT redirect the archive page itself
    // Only redirect single post pages, not the archive
    // Check for archive pages first - must check before is_singular() to prevent false positives
    global $wp_query;
    
    // Check if this is an archive page for video, podcast, or speech
    if ( is_post_type_archive( 'team_member' ) || 
         is_post_type_archive( 'video' ) || 
         is_post_type_archive( 'podcast' ) ||
         is_post_type_archive( 'speech' ) ) {
        return; // Allow archive pages to load normally
    }
    
    // Additional check: if query var indicates archive and we're not singular
    $post_type = get_query_var( 'post_type' );
    if ( ( $post_type === 'video' || $post_type === 'podcast' || $post_type === 'speech' ) && 
         ! is_singular() && 
         ( is_archive() || ( isset( $wp_query->is_archive ) && $wp_query->is_archive ) ) ) {
        return; // Allow archive pages to load normally
    }
    
    // Check REQUEST_URI to see if we're on an archive page
    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        $request_uri = $_SERVER['REQUEST_URI'];
        // Remove query string
        $request_uri = strtok( $request_uri, '?' );
        // Check if URI matches archive patterns
        if ( preg_match( '#^/videos/?$#', $request_uri ) || 
             preg_match( '#^/podcasts/?$#', $request_uri ) ||
             preg_match( '#^/speeches/?$#', $request_uri ) ||
             preg_match( '#^/videos/page/#', $request_uri ) ||
             preg_match( '#^/podcasts/page/#', $request_uri ) ||
             preg_match( '#^/speeches/page/#', $request_uri ) ) {
            return; // Allow archive pages to load normally
        }
    }
    
    // Redirect team member single pages to /team/ archive
    if ( is_singular( 'team_member' ) ) {
        $team_archive = get_post_type_archive_link( 'team_member' );
        if ( $team_archive ) {
            wp_safe_redirect( $team_archive, 301 );
            exit;
        } else {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }
    
    // Redirect video single pages to /videos/ archive
    if ( is_singular( 'video' ) ) {
        $video_archive = get_post_type_archive_link( 'video' );
        if ( $video_archive ) {
            wp_safe_redirect( $video_archive, 301 );
            exit;
        } else {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }
    
    // Redirect podcast single pages to /podcasts/ archive
    if ( is_singular( 'podcast' ) ) {
        $podcast_archive = get_post_type_archive_link( 'podcast' );
        if ( $podcast_archive ) {
            wp_safe_redirect( $podcast_archive, 301 );
            exit;
        } else {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }
    
    // Redirect speech single pages to /speeches/ archive
    if ( is_singular( 'speech' ) ) {
        $speech_archive = get_post_type_archive_link( 'speech' );
        if ( $speech_archive ) {
            wp_safe_redirect( $speech_archive, 301 );
            exit;
        } else {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }
    
    // Redirect team category taxonomy archives to /team/ archive
    if ( is_tax( 'team_category' ) ) {
        $team_archive = get_post_type_archive_link( 'team_member' );
        if ( $team_archive ) {
            wp_safe_redirect( $team_archive, 301 );
            exit;
        } else {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'gloceps_redirect_disabled_single_pages', 5 );

/**
 * Fix 404 errors on paginated custom post type archive pages
 * Handles cases where WordPress doesn't recognize /{post-type}/?paged=X as valid
 */
function gloceps_fix_cpt_archive_404() {
    // Only run on frontend
    if ( is_admin() ) {
        return;
    }
    
    global $wp_query;
    
    // Check if this is a 404 and might be a custom post type archive pagination
    if ( is_404() && isset( $_SERVER['REQUEST_URI'] ) ) {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Define custom post types and their archive slugs
        $cpt_archives = array(
            'publication' => '/publications',
            'video' => '/videos',
            'podcast' => '/podcasts',
            'article' => '/articles',
            'gallery' => '/galleries',
        );
        
        // Check each post type
        foreach ( $cpt_archives as $post_type => $archive_slug ) {
            if ( strpos( $request_uri, $archive_slug ) !== false ) {
                // Parse the URL to check for paged parameter
                $parsed_url = parse_url( $request_uri );
                $path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
                
                // Check if path matches archive or archive/page/X/ format
                $is_archive = false;
                $paged = 1;
                
                // Check base archive or with query string
                if ( $path === $archive_slug . '/' || $path === $archive_slug ) {
                    $is_archive = true;
                    if ( isset( $_GET['paged'] ) ) {
                        $paged = absint( $_GET['paged'] );
                    }
                } elseif ( preg_match( '#^' . preg_quote( $archive_slug, '#' ) . '/page/(\d+)/?$#', $path, $matches ) ) {
                    // Pretty permalink format: /{archive}/page/2/
                    $is_archive = true;
                    $paged = absint( $matches[1] );
                }
                
                if ( $is_archive ) {
                    // This is a valid archive request, prevent 404
                    
                    // Reset query flags
                    $wp_query->is_404 = false;
                    $wp_query->is_archive = true;
                    $wp_query->is_post_type_archive = true;
                    $wp_query->is_home = false;
                    $wp_query->is_front_page = false;
                    
                    // Set query vars properly
                    $wp_query->set( 'post_type', $post_type );
                    $wp_query->set( 'paged', $paged );
                    
                    // Get posts per page (use theme setting for publications, default for others)
                    $posts_per_page = get_option( 'posts_per_page' );
                    if ( $post_type === 'publication' && function_exists( 'get_field' ) ) {
                        $posts_per_page = absint( get_field( 'publications_per_page', 'option' ) ) ?: 12;
                    }
                    $wp_query->set( 'posts_per_page', $posts_per_page );
                    
                    // Clear any existing query results
                    $wp_query->posts = array();
                    $wp_query->post_count = 0;
                    
                    // Build query args
                    $query_args = array(
                        'post_type' => $post_type,
                        'paged' => $paged,
                        'posts_per_page' => $posts_per_page,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    );
                    
                    // Create a new query to get the posts
                    $archive_query = new WP_Query( $query_args );
                    
                    // Transfer results to main query
                    $wp_query->posts = $archive_query->posts;
                    $wp_query->post_count = $archive_query->post_count;
                    $wp_query->found_posts = $archive_query->found_posts;
                    $wp_query->max_num_pages = $archive_query->max_num_pages;
                    $wp_query->query_vars = array_merge( $wp_query->query_vars, $query_args );
                    
                    wp_reset_postdata();
                    
                    break; // Found matching post type, no need to continue
                }
            }
        }
    }
}
add_action( 'template_redirect', 'gloceps_fix_cpt_archive_404', 1 );

/**
 * Add Frontend Order column to Team Category taxonomy
 */
function gloceps_add_team_category_order_column( $columns ) {
    $columns['frontend_order'] = __( 'Frontend Order', 'gloceps' );
    return $columns;
}
add_filter( 'manage_edit-team_category_columns', 'gloceps_add_team_category_order_column' );

/**
 * Display Frontend Order value in Team Category column
 */
function gloceps_team_category_order_column_content( $content, $column_name, $term_id ) {
    if ( 'frontend_order' === $column_name ) {
        $order = get_term_meta( $term_id, 'frontend_order', true );
        $order = $order ? intval( $order ) : 0;
        $content = '<span class="frontend-order-value">' . esc_html( $order ) . '</span>';
    }
    return $content;
}
add_filter( 'manage_team_category_custom_column', 'gloceps_team_category_order_column_content', 10, 3 );

/**
 * Add Frontend Order field to Team Category edit form
 */
function gloceps_team_category_add_order_field( $term ) {
    $order = get_term_meta( $term->term_id, 'frontend_order', true );
    $order = $order ? intval( $order ) : 0;
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="frontend_order"><?php esc_html_e( 'Frontend Order', 'gloceps' ); ?></label>
        </th>
        <td>
            <input type="number" name="frontend_order" id="frontend_order" value="<?php echo esc_attr( $order ); ?>" min="0" step="1" />
            <p class="description"><?php esc_html_e( 'Lower numbers appear first on the frontend. Categories with the same order will be sorted by name.', 'gloceps' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'team_category_edit_form_fields', 'gloceps_team_category_add_order_field' );
add_action( 'team_category_add_form_fields', 'gloceps_team_category_add_order_field' );

/**
 * Save Frontend Order for Team Category
 */
function gloceps_team_category_save_order( $term_id ) {
    if ( isset( $_POST['frontend_order'] ) ) {
        $order = intval( $_POST['frontend_order'] );
        update_term_meta( $term_id, 'frontend_order', $order );
    }
}
add_action( 'edited_team_category', 'gloceps_team_category_save_order' );
add_action( 'created_team_category', 'gloceps_team_category_save_order' );

/**
 * AJAX handler for team pagination
 */
function gloceps_ajax_team_pagination() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gloceps_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed' ) );
    }

    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';
    $page = isset( $_POST['page'] ) ? max( 1, intval( $_POST['page'] ) ) : 1;
    $items_per_page = get_field( 'team_items_per_page', 'option' ) ?: 12;

    // Build query args
    $query_args = array(
        'post_type' => 'team_member',
        'posts_per_page' => -1,
        'orderby' => 'meta_value_num',
        'meta_key' => 'display_order',
        'order' => 'ASC',
    );

    if ( $category !== 'all' ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'team_category',
                'field' => 'slug',
                'terms' => $category,
            ),
        );
    }

    $team_query = new WP_Query( $query_args );

    if ( ! $team_query->have_posts() ) {
        wp_send_json_error( array( 'message' => 'No members found' ) );
    }

    // Collect member IDs directly (no grouping needed when filtering by category)
    $all_members = array();
    while ( $team_query->have_posts() ) {
        $team_query->the_post();
        $member_id = get_the_ID();
        // Only add if not already in array (prevent duplicates)
        if ( ! in_array( $member_id, $all_members, true ) ) {
            $all_members[] = $member_id;
        }
    }
    wp_reset_postdata();

    // Sort by display_order meta if available
    usort( $all_members, function( $a, $b ) {
        $order_a = get_post_meta( $a, 'display_order', true ) ?: 999;
        $order_b = get_post_meta( $b, 'display_order', true ) ?: 999;
        return intval( $order_a ) - intval( $order_b );
    } );

    if ( empty( $all_members ) ) {
        wp_send_json_error( array( 'message' => 'No members found for category' ) );
    }

    $total_members = count( $all_members );
    $total_pages = ceil( $total_members / $items_per_page );
    $offset = ( $page - 1 ) * $items_per_page;
    $members = array_slice( $all_members, $offset, $items_per_page );

    // #region agent log
    error_log( 'TEAM AJAX DEBUG: category=' . $category . ', page=' . $page . ', total_members=' . $total_members . ', items_per_page=' . $items_per_page . ', offset=' . $offset . ', members_count=' . count( $members ) . ', member_ids=' . implode( ',', $members ) );
    // #endregion

    // Render team cards
    ob_start();
    global $post;
    foreach ( $members as $member_id ) {
        $post = get_post( $member_id );
        if ( ! $post ) {
            continue;
        }
        setup_postdata( $post );
        get_template_part( 'template-parts/components/team-card' );
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( array(
        'html' => $html,
        'page' => $page,
        'total_pages' => $total_pages,
        'total_members' => $total_members,
    ) );
}
add_action( 'wp_ajax_gloceps_team_pagination', 'gloceps_ajax_team_pagination' );
add_action( 'wp_ajax_nopriv_gloceps_team_pagination', 'gloceps_ajax_team_pagination' );
