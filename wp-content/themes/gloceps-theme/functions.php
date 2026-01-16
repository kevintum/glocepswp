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
        GLOCEPS_VERSION
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
            'rewrite'            => array( 'slug' => 'publications' ),
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
            'publicly_queryable' => true,
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
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'videos' ),
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
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'podcasts' ),
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
            'rewrite'            => array( 'slug' => 'galleries' ),
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
            'rewrite'            => array( 'slug' => 'articles' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 11,
            'menu_icon'          => 'dashicons-admin-post',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
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
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'research-pillar' ),
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
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'team-category' ),
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

        if ( $depth === 0 ) {
            $output .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav__link ' . esc_attr( $active_class ) . '">';
            $output .= esc_html( $item->title );
            
            if ( $has_children ) {
                $output .= '<svg class="nav__chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">';
                $output .= '<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>';
                $output .= '</svg>';
            }
            
            $output .= '</a>';
        } else {
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav__dropdown-link">';
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
