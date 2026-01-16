<?php
/**
 * The header for our theme
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header - Premium Banded Navigation -->
<header class="<?php echo esc_attr( gloceps_get_header_class() ); ?>" id="header">
    <div class="header__bar">
        <div class="container">
            <div class="header__inner">
                <!-- Logo -->
                <?php gloceps_logo(); ?>

                <!-- Navigation Band -->
                <div class="nav__band">
                    <nav class="nav">
                        <?php
                        if ( has_nav_menu( 'primary' ) ) {
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'primary',
                                    'menu_class'     => 'nav__list',
                                    'container'      => false,
                                    'walker'         => new GLOCEPS_Nav_Walker(),
                                    'fallback_cb'    => false,
                                )
                            );
                        } else {
                            // Fallback menu
                            ?>
                            <ul class="nav__list">
                                <li class="nav__item">
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav__link <?php echo is_front_page() ? 'nav__link--active' : ''; ?>">
                                        <?php esc_html_e( 'Home', 'gloceps' ); ?>
                                    </a>
                                </li>
                                <li class="nav__item">
                                    <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="nav__link">
                                        <?php esc_html_e( 'Publications', 'gloceps' ); ?>
                                    </a>
                                </li>
                                <li class="nav__item">
                                    <a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" class="nav__link">
                                        <?php esc_html_e( 'Events', 'gloceps' ); ?>
                                    </a>
                                </li>
                            </ul>
                            <?php
                        }
                        ?>
                        <!-- More Menu (for overflow items) -->
                        <div class="nav__more" id="navMore" style="display: none;">
                            <button class="nav__more-toggle" aria-label="<?php esc_attr_e( 'More menu', 'gloceps' ); ?>" aria-expanded="false">
                                <span><?php esc_html_e( 'More', 'gloceps' ); ?></span>
                                <svg class="nav__chevron" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="nav__dropdown">
                                <ul class="nav__dropdown-list" id="navMoreList"></ul>
                            </div>
                        </div>
                    </nav>

                    <!-- Header Actions -->
                    <div class="header__actions">
                        <button class="header__search" aria-label="<?php esc_attr_e( 'Search', 'gloceps' ); ?>">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                        </button>
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--primary btn--nav">
                            <?php esc_html_e( 'Publications', 'gloceps' ); ?>
                        </a>
                        <?php 
                        $contact_page = get_page_by_path( 'contact' );
                        $contact_url = $contact_page ? get_permalink( $contact_page ) : '#contact';
                        ?>
                        <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--outline-light btn--nav">
                            <?php esc_html_e( 'Contact Us', 'gloceps' ); ?>
                        </a>
                        
                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <button type="button" class="header__cart" id="cart-toggle" aria-label="<?php esc_attr_e( 'View cart', 'gloceps' ); ?>" aria-expanded="false">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <?php 
                            $cart_count = WC()->cart->get_cart_contents_count();
                            if ( $cart_count > 0 ) : 
                            ?>
                            <span class="header__cart-count"><?php echo esc_html( $cart_count ); ?></span>
                            <?php endif; ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mobile Toggle -->
                <button class="nav__toggle" aria-label="<?php esc_attr_e( 'Menu', 'gloceps' ); ?>" id="navToggle">
                    <div class="nav__toggle-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="nav-overlay"></div>

<!-- Mobile Menu -->
<nav class="mobile-menu">
    <div class="mobile-menu__header">
        <button class="mobile-menu__close" aria-label="<?php esc_attr_e( 'Close menu', 'gloceps' ); ?>">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="mobile-menu__links">
        <?php
        if ( has_nav_menu( 'mobile' ) ) {
            wp_nav_menu(
                array(
                    'theme_location' => 'mobile',
                    'menu_class'     => 'mobile-menu__list',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'walker'         => new GLOCEPS_Mobile_Nav_Walker(),
                )
            );
        } elseif ( has_nav_menu( 'primary' ) ) {
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'mobile-menu__list',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'walker'         => new GLOCEPS_Mobile_Nav_Walker(),
                )
            );
        } else {
            // Fallback mobile menu
            ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-menu__link <?php echo is_front_page() ? 'mobile-menu__link--active' : ''; ?>">
                <?php esc_html_e( 'Home', 'gloceps' ); ?>
            </a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="mobile-menu__link">
                <?php esc_html_e( 'Publications', 'gloceps' ); ?>
            </a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" class="mobile-menu__link">
                <?php esc_html_e( 'Events', 'gloceps' ); ?>
            </a>
            <a href="<?php echo esc_url( $contact_url ); ?>" class="mobile-menu__link">
                <?php esc_html_e( 'Contact', 'gloceps' ); ?>
            </a>
            <?php
        }
        ?>
        
        <!-- Mobile Menu CTA Buttons -->
        <div class="mobile-menu__ctas">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--primary btn--mobile-menu">
                <?php esc_html_e( 'Publications', 'gloceps' ); ?>
            </a>
            <?php 
            $contact_page = get_page_by_path( 'contact' );
            $contact_url = $contact_page ? get_permalink( $contact_page ) : '#contact';
            ?>
            <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--outline btn--mobile-menu">
                <?php esc_html_e( 'Contact Us', 'gloceps' ); ?>
            </a>
        </div>
        
        <?php if ( class_exists( 'WooCommerce' ) ) : 
            $cart_count = WC()->cart->get_cart_contents_count();
        ?>
        <div class="mobile-menu__cart">
            <button type="button" class="mobile-menu__cart-btn" id="mobile-cart-toggle" aria-label="<?php esc_attr_e( 'View cart', 'gloceps' ); ?>" aria-expanded="false">
                <div class="mobile-menu__cart-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php if ( $cart_count > 0 ) : ?>
                    <span class="mobile-menu__cart-count"><?php echo esc_html( $cart_count ); ?></span>
                    <?php endif; ?>
                </div>
                <span class="mobile-menu__cart-text"><?php esc_html_e( 'Cart', 'gloceps' ); ?></span>
            </button>
        </div>
        <?php endif; ?>
    </div>
</nav>

