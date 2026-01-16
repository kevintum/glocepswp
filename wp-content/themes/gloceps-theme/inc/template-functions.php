<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package GLOCEPS
 */

/**
 * Get header class based on current page
 * 
 * @return string
 */
function gloceps_get_header_class() {
    $classes = array( 'header' );
    
    // Check if this is the Media page or Purchase page (by slug or template)
    $is_media_page = false;
    $is_purchase_page = false;
    if (is_page()) {
        $page_slug = get_post_field('post_name', get_the_ID());
        if ($page_slug === 'media') {
            $is_media_page = true;
        } elseif ($page_slug === 'purchase' || $page_slug === 'shop') {
            $is_purchase_page = true;
        }
    }
    
    // Check if this is a research pillar page (child of Research page)
    $is_pillar_page = false;
    if (is_page()) {
        $page = get_queried_object();
        if ($page && $page->post_parent) {
            $parent = get_post($page->post_parent);
            if ($parent && $parent->post_name === 'research') {
                $is_pillar_page = true;
            }
        }
    }
    
    // Pages with transparent/overlay header (shows light logo)
    if ( is_front_page() || is_singular('event') ) {
        $classes[] = 'header--transparent';
        $classes[] = 'header--overlay';
        if ( is_front_page() ) {
            $classes[] = 'header--split';
        }
    } elseif ( $is_media_page || is_singular('publication') || $is_purchase_page || $is_pillar_page ) {
        // Media page, single publications, purchase page, and pillar pages have special header styling
        $classes[] = 'header--split';
        if ($is_purchase_page) {
            // Purchase page has split header but not on primary background
            // (it has its own hero section)
        } else {
            $classes[] = 'header--on-primary';
        }
    } else {
        // All other pages have dark header (shows dark logo on light background)
        $classes[] = 'header--dark';
    }
    
    // Add scrolled class will be handled by JavaScript
    
    return implode( ' ', $classes );
}

/**
 * Display the site logo with dark/light variants
 * Checks ACF Theme Settings first, then falls back to WordPress Customizer
 */
function gloceps_logo() {
    // Get ACF logos from Theme Settings
    $acf_logo = function_exists('get_field') ? get_field('header_logo', 'option') : null;
    $acf_logo_light = function_exists('get_field') ? get_field('header_logo_light', 'option') : null;
    
    // Get WordPress Customizer logo as fallback
    $wp_logo_id = get_theme_mod( 'custom_logo' );
    
    // Determine which logos to use
    $logo_url = null;
    $logo_light_url = null;
    
    // Priority 1: ACF Theme Settings logos
    if ( $acf_logo && is_array($acf_logo) && !empty($acf_logo['url']) ) {
        $logo_url = $acf_logo['url'];
    }
    if ( $acf_logo_light && is_array($acf_logo_light) && !empty($acf_logo_light['url']) ) {
        $logo_light_url = $acf_logo_light['url'];
    }
    
    // Priority 2: WordPress Customizer logo
    if ( !$logo_url && $wp_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $wp_logo_id, 'full' );
    }
    
    // Priority 3: Theme bundled logo
    if ( !$logo_url ) {
        $logo_file = GLOCEPS_DIR . '/assets/images/glocep-logo.png';
        if ( file_exists( $logo_file ) ) {
            $logo_url = GLOCEPS_URI . '/assets/images/glocep-logo.png';
        }
    }
    
    // Use same logo for light version if not set
    if ( !$logo_light_url ) {
        $logo_light_url = $logo_url;
    }
    
    $site_name = get_bloginfo( 'name' );
    $site_tagline = get_bloginfo( 'description' );
    
    // Get header settings
    $show_site_title = function_exists('get_field') ? get_field('header_show_site_title', 'option') : true;
    $show_tagline = function_exists('get_field') ? get_field('header_show_tagline', 'option') : true;
    $logo_size = function_exists('get_field') ? get_field('header_logo_size', 'option') : '2';
    
    // Default to true if not set (backward compatibility)
    if ( $show_site_title === null ) {
        $show_site_title = true;
    }
    if ( $show_tagline === null ) {
        $show_tagline = true;
    }
    
    // Add logo size class
    $logo_size_class = 'header__logo-img--size-' . esc_attr( $logo_size );
    
    ?>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo" rel="home">
        <?php if ( $logo_url ) : ?>
            <!-- Dark logo (default) - shown on light backgrounds -->
            <img src="<?php echo esc_url( $logo_url ); ?>" 
                 alt="<?php echo esc_attr( $site_name ); ?>" 
                 class="header__logo-img header__logo-img--dark <?php echo esc_attr( $logo_size_class ); ?>" />
            
            <!-- Light logo - shown on dark/transparent headers -->
            <img src="<?php echo esc_url( $logo_light_url ); ?>" 
                 alt="<?php echo esc_attr( $site_name ); ?>" 
                 class="header__logo-img header__logo-img--light <?php echo esc_attr( $logo_size_class ); ?>" />
        <?php endif; ?>
        
        <?php if ( $show_site_title || $show_tagline ) : ?>
        <div class="header__logo-text">
            <?php if ( $show_site_title && $site_name ) : ?>
                <strong><?php echo esc_html( $site_name ); ?></strong>
            <?php endif; ?>
            <?php if ( $show_tagline && $site_tagline ) : ?>
                <span><?php echo esc_html( $site_tagline ); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </a>
    <?php
}

/**
 * Get favicon/site icon URL for use as placeholder image
 * 
 * @param int $size Size of the icon (default 192)
 * @return string URL of the site icon
 */
function gloceps_get_favicon_url( $size = 192 ) {
    // Try WordPress site icon first
    $site_icon_id = get_option( 'site_icon' );
    if ( $site_icon_id ) {
        $icon_url = get_site_icon_url( $size );
        if ( $icon_url ) {
            return $icon_url;
        }
    }
    
    // Try ACF theme settings favicon
    if ( function_exists( 'get_field' ) ) {
        $acf_favicon = get_field( 'favicon', 'option' );
        if ( $acf_favicon ) {
            if ( is_array( $acf_favicon ) && !empty( $acf_favicon['url'] ) ) {
                return $acf_favicon['url'];
            } elseif ( is_numeric( $acf_favicon ) ) {
                return wp_get_attachment_image_url( $acf_favicon, array( $size, $size ) );
            } elseif ( is_string( $acf_favicon ) ) {
                return $acf_favicon;
            }
        }
    }
    
    // Fallback to default WordPress favicon location
    return get_site_icon_url( $size, includes_url( 'images/w-logo-blue-white-bg.png' ) );
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function gloceps_body_classes( $classes ) {
    // Add class for order-pay pages
    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-pay' ) ) {
        $classes[] = 'woocommerce-order-pay';
    }
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }

    // Add class for dark header pages
    if ( is_front_page() || is_singular( 'publication' ) ) {
        $classes[] = 'has-dark-header';
    }
    
    // Add class when Effra font is selected
    if ( function_exists( 'get_field' ) ) {
        $typography = get_field( 'typography_settings', 'option' );
        if ( $typography ) {
            $heading_font = isset( $typography['heading_font'] ) ? $typography['heading_font'] : '';
            $body_font = isset( $typography['body_font'] ) ? $typography['body_font'] : '';
            $site_title_font = isset( $typography['site_title_font'] ) ? $typography['site_title_font'] : '';
            
            if ( $heading_font === 'Effra' || $body_font === 'Effra' || $site_title_font === 'Effra' ) {
                $classes[] = 'font-effra-active';
            }
        }
    }

    return $classes;
}
add_filter( 'body_class', 'gloceps_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function gloceps_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'gloceps_pingback_header' );

/**
 * Output Google Analytics code from theme settings
 */
function gloceps_google_analytics() {
    $ga_code = get_field( 'google_analytics_code', 'option' );
    if ( $ga_code ) {
        echo $ga_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
add_action( 'wp_head', 'gloceps_google_analytics', 20 );

/**
 * Display breadcrumbs
 */
function gloceps_breadcrumbs() {
    if ( is_front_page() ) {
        return;
    }
    
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'gloceps' ) . '">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'gloceps' ) . '</a>';
    echo '<span class="breadcrumbs__separator">/</span>';
    
    if ( is_archive() ) {
        if ( is_post_type_archive() ) {
            echo '<span class="breadcrumbs__current">' . esc_html( post_type_archive_title( '', false ) ) . '</span>';
        } elseif ( is_tax() ) {
            $term = get_queried_object();
            $post_type = get_post_type();
            if ( $post_type ) {
                $post_type_obj = get_post_type_object( $post_type );
                if ( $post_type_obj ) {
                    echo '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '">' . esc_html( $post_type_obj->labels->name ) . '</a>';
                    echo '<span class="breadcrumbs__separator">/</span>';
                }
            }
            echo '<span class="breadcrumbs__current">' . esc_html( $term->name ) . '</span>';
        } else {
            echo '<span class="breadcrumbs__current">' . esc_html( get_the_archive_title() ) . '</span>';
        }
    } elseif ( is_singular() ) {
        $post_type = get_post_type();
        if ( $post_type && $post_type !== 'page' && $post_type !== 'post' ) {
            $post_type_obj = get_post_type_object( $post_type );
            if ( $post_type_obj ) {
                echo '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '">' . esc_html( $post_type_obj->labels->name ) . '</a>';
                echo '<span class="breadcrumbs__separator">/</span>';
            }
        }
        echo '<span class="breadcrumbs__current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_page() ) {
        global $post;
        
        // Special handling for checkout page - add purchase and cart
        if ( function_exists( 'is_checkout' ) && is_checkout() ) {
            $purchase_page = get_page_by_path( 'purchase' );
            $cart_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'cart' ) : 0;
            
            if ( $purchase_page ) {
                echo '<a href="' . esc_url( get_permalink( $purchase_page->ID ) ) . '">' . esc_html( get_the_title( $purchase_page->ID ) ) . '</a>';
                echo '<span class="breadcrumbs__separator">/</span>';
            }
            
            if ( $cart_page_id ) {
                echo '<a href="' . esc_url( get_permalink( $cart_page_id ) ) . '">' . esc_html__( 'Cart', 'gloceps' ) . '</a>';
                echo '<span class="breadcrumbs__separator">/</span>';
            }
        } elseif ( $post->post_parent ) {
            $ancestors = get_post_ancestors( $post->ID );
            $ancestors = array_reverse( $ancestors );
            foreach ( $ancestors as $ancestor ) {
                echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
                echo '<span class="breadcrumbs__separator">/</span>';
            }
        }
        echo '<span class="breadcrumbs__current">' . esc_html( get_the_title() ) . '</span>';
    }
    
    echo '</nav>';
}

/**
 * Display pagination
 */
function gloceps_pagination() {
    $args = array(
        'type'      => 'list',
        'prev_text' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg><span>' . esc_html__( 'Previous', 'gloceps' ) . '</span>',
        'next_text' => '<span>' . esc_html__( 'Next', 'gloceps' ) . '</span><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>',
    );
    
    echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'gloceps' ) . '">';
    the_posts_pagination( $args );
    echo '</nav>';
}

/**
 * Get team members by category
 * 
 * @param string $category_slug The team category slug
 * @param int $limit Number of members to retrieve (-1 for all)
 * @return WP_Query
 */
function gloceps_get_team_members( $category_slug = '', $limit = -1 ) {
    $args = array(
        'post_type'      => 'team_member',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );
    
    if ( $category_slug ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'team_category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        );
    }
    
    return new WP_Query( $args );
}

/**
 * Display social links
 */
function gloceps_social_links() {
    $social_links = array(
        'linkedin' => array(
            'url'   => get_field( 'social_linkedin', 'option' ),
            'label' => 'LinkedIn',
            'icon'  => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
        ),
        'twitter' => array(
            'url'   => get_field( 'social_twitter', 'option' ),
            'label' => 'X / Twitter',
            'icon'  => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
        ),
        'facebook' => array(
            'url'   => get_field( 'social_facebook', 'option' ),
            'label' => 'Facebook',
            'icon'  => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
        ),
        'youtube' => array(
            'url'   => get_field( 'social_youtube', 'option' ),
            'label' => 'YouTube',
            'icon'  => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
        ),
    );
    
    echo '<div class="social-links">';
    foreach ( $social_links as $network => $data ) {
        if ( ! empty( $data['url'] ) ) {
            printf(
                '<a href="%s" aria-label="%s" class="social-links__item social-links__item--%s" target="_blank" rel="noopener noreferrer"><svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">%s</svg></a>',
                esc_url( $data['url'] ),
                esc_attr( $data['label'] ),
                esc_attr( $network ),
                $data['icon']
            );
        }
    }
    echo '</div>';
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Output format
 * @return string
 */
function gloceps_format_date( $date, $format = 'F j, Y' ) {
    if ( empty( $date ) ) {
        return '';
    }
    
    $timestamp = strtotime( $date );
    return date_i18n( $format, $timestamp );
}

/**
 * Get reading time estimate
 * 
 * @param int $post_id Post ID
 * @return string
 */
function gloceps_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 200 ); // Average reading speed
    
    if ( $reading_time < 1 ) {
        $reading_time = 1;
    }
    
    return sprintf(
        /* translators: %d: number of minutes */
        _n( '%d min read', '%d min read', $reading_time, 'gloceps' ),
        $reading_time
    );
}

/**
 * Truncate text to a specified length
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to append
 * @return string
 */
function gloceps_truncate( $text, $length = 150, $suffix = '...' ) {
    if ( strlen( $text ) <= $length ) {
        return $text;
    }
    
    return substr( $text, 0, $length ) . $suffix;
}

/**
 * Get publication access badge
 * 
 * @param int $post_id Publication post ID
 * @return string
 */
function gloceps_get_access_badge( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $access_type = get_field( 'access_type', $post_id );
    
    $badge_class = 'badge';
    $badge_text = '';
    
    switch ( $access_type ) {
        case 'free':
            $badge_class .= ' badge--success';
            $badge_text = __( 'Free', 'gloceps' );
            break;
        case 'premium':
            $badge_class .= ' badge--premium';
            $badge_text = __( 'Premium', 'gloceps' );
            break;
        case 'members':
            $badge_class .= ' badge--info';
            $badge_text = __( 'Members Only', 'gloceps' );
            break;
    }
    
    if ( $badge_text ) {
        return '<span class="' . esc_attr( $badge_class ) . '">' . esc_html( $badge_text ) . '</span>';
    }
    
    return '';
}

/**
 * Check if current user has access to publication
 * 
 * @param int $post_id Publication post ID
 * @return bool
 */
function gloceps_user_has_access( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $access_type = get_field( 'access_type', $post_id );
    
    // Free publications are always accessible
    if ( $access_type === 'free' ) {
        return true;
    }
    
    // Admins always have access
    if ( current_user_can( 'manage_options' ) ) {
        return true;
    }
    
    // For premium publications, check if user has purchased
    if ( $access_type === 'premium' && class_exists( 'WooCommerce' ) ) {
        $linked_product = get_field( 'linked_product', $post_id );
        
        if ( $linked_product && is_user_logged_in() ) {
            return wc_customer_bought_product( '', get_current_user_id(), $linked_product );
        }
    }
    
    return false;
}

/**
 * Register custom image sizes
 */
function gloceps_custom_image_sizes() {
    add_image_size( 'gloceps-hero', 1920, 1080, true );
    add_image_size( 'gloceps-featured', 800, 600, true );
    add_image_size( 'gloceps-card', 400, 300, true );
    add_image_size( 'gloceps-team', 300, 300, true );
    add_image_size( 'gloceps-publication', 280, 380, true );
}
add_action( 'after_setup_theme', 'gloceps_custom_image_sizes' );

/**
 * Add custom image sizes to media selector
 */
function gloceps_custom_image_sizes_names( $sizes ) {
    return array_merge( $sizes, array(
        'gloceps-hero'        => __( 'Hero Image', 'gloceps' ),
        'gloceps-featured'    => __( 'Featured Image', 'gloceps' ),
        'gloceps-card'        => __( 'Card Image', 'gloceps' ),
        'gloceps-team'        => __( 'Team Photo', 'gloceps' ),
        'gloceps-publication' => __( 'Publication Cover', 'gloceps' ),
    ) );
}
add_filter( 'image_size_names_choose', 'gloceps_custom_image_sizes_names' );

/**
 * Get featured publications
 * 
 * @param int $limit Number of publications to retrieve
 * @return WP_Query
 */
function gloceps_get_featured_publications( $limit = 1 ) {
    $args = array(
        'post_type'      => 'publication',
        'posts_per_page' => $limit,
        'meta_query'     => array(
            array(
                'key'     => 'is_featured',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
    );
    
    $query = new WP_Query( $args );
    
    // Fallback to latest publication if no featured ones
    if ( ! $query->have_posts() ) {
        $args = array(
            'post_type'      => 'publication',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $query = new WP_Query( $args );
    }
    
    return $query;
}

/**
 * Get latest publications
 * 
 * @param int $limit Number of publications to retrieve
 * @param string $type Publication type slug (optional)
 * @return WP_Query
 */
function gloceps_get_latest_publications( $limit = 5, $type = '' ) {
    $args = array(
        'post_type'      => 'publication',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    
    if ( $type ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'publication_type',
                'field'    => 'slug',
                'terms'    => $type,
            ),
        );
    }
    
    return new WP_Query( $args );
}

/**
 * Get upcoming events
 * 
 * @param int $limit Number of events to retrieve
 * @return WP_Query
 */
function gloceps_get_upcoming_events( $limit = 3 ) {
    $today = date( 'Y-m-d' );
    
    $args = array(
        'post_type'      => 'event',
        'posts_per_page' => $limit,
        'meta_key'       => 'event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => 'event_date',
                'value'   => $today,
                'compare' => '>=',
                'type'    => 'DATE',
            ),
        ),
    );
    
    return new WP_Query( $args );
}

/**
 * Get research pillar page URL
 * 
 * @param string $pillar_slug Research pillar slug
 * @return string
 */
function gloceps_get_pillar_page( $pillar_slug ) {
    // First, try to find a page with the pillar slug
    $page = get_page_by_path( 'research/' . $pillar_slug );
    
    if ( $page ) {
        return get_permalink( $page->ID );
    }
    
    // Fallback to the taxonomy archive
    $term = get_term_by( 'slug', $pillar_slug, 'research_pillar' );
    
    if ( $term && ! is_wp_error( $term ) ) {
        return get_term_link( $term );
    }
    
    // Final fallback to publications filtered by pillar
    return add_query_arg( 'research_pillar', $pillar_slug, get_post_type_archive_link( 'publication' ) );
}

/**
 * Format price in KES
 * 
 * @param float $price Price value
 * @return string
 */
function gloceps_format_price( $price ) {
    return 'KES ' . number_format( (float) $price, 0, '.', ',' );
}

/**
 * Generate Google Calendar link for event
 * 
 * @return string Google Calendar URL
 */
function gloceps_get_calendar_link() {
    if (!is_singular('event')) {
        return '#';
    }
    
    $event_date = get_field('event_date');
    $event_end_date = get_field('event_end_date');
    $event_time = get_field('event_time');
    $venue_name = get_field('venue_name');
    $location_city = get_field('location_city');
    $location_country = get_field('location_country');
    
    if (!$event_date) {
        return '#';
    }
    
    // Format dates for Google Calendar
    $start_datetime = $event_date . 'T090000'; // Default to 9 AM if no time specified
    $end_datetime = ($event_end_date ?: $event_date) . 'T170000'; // Default to 5 PM
    
    // If time is specified, try to parse it
    if ($event_time) {
        // Simple parsing - assumes format like "9:00 AM - 5:00 PM"
        if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)/i', $event_time, $start_match)) {
            $start_hour = (int)$start_match[1];
            $start_min = $start_match[2];
            if (strtoupper($start_match[3]) === 'PM' && $start_hour < 12) {
                $start_hour += 12;
            }
            if (strtoupper($start_match[3]) === 'AM' && $start_hour == 12) {
                $start_hour = 0;
            }
            $start_datetime = $event_date . sprintf('T%02d%02d00', $start_hour, $start_min);
        }
        
        if (preg_match('/-.*?(\d{1,2}):(\d{2})\s*(AM|PM)/i', $event_time, $end_match)) {
            $end_hour = (int)$end_match[1];
            $end_min = $end_match[2];
            if (strtoupper($end_match[3]) === 'PM' && $end_hour < 12) {
                $end_hour += 12;
            }
            if (strtoupper($end_match[3]) === 'AM' && $end_hour == 12) {
                $end_hour = 0;
            }
            $end_date = $event_end_date ?: $event_date;
            $end_datetime = $end_date . sprintf('T%02d%02d00', $end_hour, $end_min);
        }
    }
    
    $title = urlencode(get_the_title());
    $location = '';
    if ($venue_name) {
        $location = $venue_name;
        if ($location_city) {
            $location .= ', ' . $location_city;
        }
        if ($location_country) {
            $location .= ', ' . $location_country;
        }
    }
    $location = urlencode($location);
    $details = urlencode(get_permalink());
    
    return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$start_datetime}/{$end_datetime}&details={$details}&location={$location}";
}

/**
 * Generate Table of Contents from publication content
 * Extracts headings (h2, h3, h4) and creates anchor links
 * 
 * @param int $post_id Publication post ID
 * @return array Array of TOC items with title, id, level
 */
function gloceps_generate_publication_toc( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field( 'post_content', $post_id );
    if ( empty( $content ) ) {
        return array();
    }
    
    $toc = array();
    $pattern = '/<h([2-4])[^>]*>(.*?)<\/h[2-4]>/i';
    
    if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        $index = 0;
        foreach ( $matches as $match ) {
            $level = (int) $match[1];
            $title = strip_tags( $match[2] );
            $title = trim( $title );
            
            if ( empty( $title ) ) {
                continue;
            }
            
            // Generate ID from title
            $id = 'section-' . sanitize_title( $title ) . '-' . $index;
            
            $toc[] = array(
                'id'    => $id,
                'title' => $title,
                'level' => $level,
            );
            
            $index++;
        }
    }
    
    return $toc;
}

/**
 * Get publication author information
 * Returns author info based on author_type (team or guest)
 * 
 * @param int $post_id Publication post ID
 * @return array Author information array
 */
function gloceps_get_publication_author( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $author_type = get_field( 'author_type', $post_id ) ?: 'team';
    $author_info = array(
        'type'  => $author_type,
        'name'  => '',
        'title' => '',
        'bio'   => '',
        'image' => '',
        'link'  => '',
    );
    
    if ( $author_type === 'team' ) {
        $team_member = get_field( 'team_member', $post_id );
        if ( $team_member && is_object( $team_member ) ) {
            $author_info['name'] = get_the_title( $team_member->ID );
            $author_info['title'] = get_field( 'job_title', $team_member->ID ) ?: '';
            $author_info['bio'] = get_field( 'bio', $team_member->ID ) ?: '';
            $author_info['image'] = get_the_post_thumbnail_url( $team_member->ID, 'thumbnail' ) ?: gloceps_get_favicon_url( 80 );
            $author_info['link'] = get_post_type_archive_link( 'team_member' );
        }
    } else {
        // Guest author
        $author_info['name'] = get_field( 'guest_author_name', $post_id ) ?: get_the_author();
        $author_info['title'] = get_field( 'guest_author_title', $post_id ) ?: '';
        $author_info['bio'] = get_field( 'guest_author_bio', $post_id ) ?: '';
        $guest_image = get_field( 'guest_author_image', $post_id );
        if ( $guest_image && is_array( $guest_image ) ) {
            $author_info['image'] = $guest_image['url'] ?? ( $guest_image['sizes']['thumbnail'] ?? '' );
        }
        if ( ! $author_info['image'] ) {
            $author_info['image'] = gloceps_get_favicon_url( 80 );
        }
    }
    
    return $author_info;
}

/**
 * Get publication price
 * Returns price from ACF field or WooCommerce product
 * 
 * @param int $post_id Publication post ID
 * @return float|false Price or false if free
 */
function gloceps_get_publication_price( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $access_type = get_field( 'access_type', $post_id );
    if ( $access_type !== 'premium' ) {
        return false;
    }
    
    // Check if linked to WooCommerce product
    $wc_product_id = get_field( 'wc_product', $post_id );
    if ( $wc_product_id && class_exists( 'WooCommerce' ) ) {
        $product = wc_get_product( $wc_product_id );
        if ( $product ) {
            return (float) $product->get_price();
        }
    }
    
    // Fallback to ACF price field
    $price = get_field( 'price', $post_id );
    return $price ? (float) $price : false;
}

/**
 * Check if publication is premium
 * 
 * @param int $post_id Publication post ID
 * @return bool
 */
function gloceps_is_premium_publication( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    return get_field( 'access_type', $post_id ) === 'premium';
}

/**
 * Track publication download
 * Increments download count
 * 
 * @param int $post_id Publication post ID
 */
function gloceps_track_publication_download( $post_id ) {
    $current_count = (int) get_field( 'download_count', $post_id );
    update_field( 'download_count', $current_count + 1, $post_id );
}

/**
 * Auto-populate publication page count from PDF
 * Called on save_post hook
 * 
 * @param int $post_id Publication post ID
 */
function gloceps_auto_populate_publication_details( $post_id ) {
    // Only run for publications
    if ( get_post_type( $post_id ) !== 'publication' ) {
        return;
    }
    
    // Auto-populate page count from PDF if available
    $pdf_file = get_field( 'pdf_file', $post_id );
    $page_count = get_field( 'page_count', $post_id );
    
    if ( $pdf_file && is_array( $pdf_file ) && empty( $page_count ) ) {
        $pdf_path = get_attached_file( $pdf_file['ID'] );
        if ( $pdf_path && file_exists( $pdf_path ) ) {
            // Try to get page count using PDF parser if available
            // For now, we'll leave it manual or use a library
            // This is a placeholder for future enhancement
        }
    }
    
    // Auto-calculate reading time if not set
    $reading_time = get_field( 'reading_time', $post_id );
    if ( empty( $reading_time ) ) {
        $content = get_post_field( 'post_content', $post_id );
        $word_count = str_word_count( strip_tags( $content ) );
        $estimated_time = max( 1, round( $word_count / 200 ) ); // 200 words per minute
        update_field( 'reading_time', $estimated_time, $post_id );
    }
}

// Hook to auto-populate details on save
add_action( 'save_post', 'gloceps_auto_populate_publication_details', 20, 1 );

/**
 * Get publication count for filter labels
 * 
 * @param array $args Query arguments
 * @return int Number of publications matching the query
 */
function gloceps_get_publication_count($args = array()) {
    // Preserve all query args including date_query
    $query_args = array_merge($args, array('posts_per_page' => -1, 'fields' => 'ids'));
    $count_query = new WP_Query($query_args);
    $count = $count_query->found_posts;
    wp_reset_postdata();
    return $count;
}

/**
 * AJAX handler for tracking publication downloads
 */
function gloceps_ajax_track_download() {
    check_ajax_referer('gloceps_download_nonce', 'nonce');
    $post_id = intval($_POST['post_id']);
    if ($post_id && get_post_type($post_id) === 'publication') {
        gloceps_track_publication_download($post_id);
        wp_send_json_success();
    }
    wp_send_json_error();
}
add_action('wp_ajax_gloceps_track_download', 'gloceps_ajax_track_download');
add_action('wp_ajax_nopriv_gloceps_track_download', 'gloceps_ajax_track_download');
