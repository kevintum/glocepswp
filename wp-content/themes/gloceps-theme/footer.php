<?php
/**
 * The template for displaying the footer
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

// Get footer settings
$cta_title = get_field( 'footer_cta_title', 'option' ) ?: __( "Let's Shape Policy Together", 'gloceps' );
$cta_text = get_field( 'footer_cta_text', 'option' ) ?: __( 'Partner with GLOCEPS to advance evidence-based policy making across Eastern Africa. We\'re ready to collaborate.', 'gloceps' );
$cta_button_text = get_field( 'footer_cta_button_text', 'option' ) ?: __( 'Start a Conversation', 'gloceps' );
$cta_button_link = get_field( 'footer_cta_button_link', 'option' );
$address = get_field( 'footer_address', 'option' ) ?: 'Runda Drive, Nairobi, Kenya';
$phone = get_field( 'footer_phone', 'option' ) ?: '+254 112 401 331';
$email = get_field( 'footer_email', 'option' ) ?: 'info@gloceps.org';
$footer_description = get_field( 'footer_description', 'option' ) ?: __( 'A leading centre of excellence in policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.', 'gloceps' );
$marquee_items = get_field( 'footer_marquee_text', 'option' );
$copyright_text = get_field( 'footer_copyright', 'option' ) ?: '© {year} {site} — {address}';
$newsletter_form_id = get_field( 'footer_newsletter_form', 'option' );

// Process copyright text
$copyright_text = str_replace( '{year}', date( 'Y' ), $copyright_text );
$copyright_text = str_replace( '{site}', get_bloginfo( 'name' ), $copyright_text );
$copyright_text = str_replace( '{address}', $address, $copyright_text );

// Default marquee items if not set
if ( empty( $marquee_items ) ) {
    $marquee_items = array(
        array( 'text' => 'Research' ),
        array( 'text' => 'Knowledge' ),
        array( 'text' => 'Influence' ),
        array( 'text' => 'Policy' ),
        array( 'text' => 'Strategy' ),
    );
}

$contact_page = get_page_by_path( 'contact' );
$contact_url = $contact_page ? get_permalink( $contact_page ) : '#contact';
if ( $cta_button_link && is_array( $cta_button_link ) ) {
    $contact_url = $cta_button_link['url'];
} elseif ( $cta_button_link && is_string( $cta_button_link ) ) {
    $contact_url = $cta_button_link;
}
?>

<!-- Footer - Modern Awwwards-Inspired -->
<footer class="footer">
    <!-- Large CTA Section -->
    <div class="footer__cta">
        <div class="container">
            <div class="footer__cta-inner">
                <div class="footer__cta-content">
                    <span class="footer__cta-label"><?php esc_html_e( 'Get In Touch', 'gloceps' ); ?></span>
                    <h2 class="footer__cta-title">
                        <?php 
                        // Allow HTML in CTA title for emphasis
                        echo wp_kses_post( str_replace( 'Policy', '<em>Policy</em>', $cta_title ) );
                        ?>
                    </h2>
                    <p class="footer__cta-text"><?php echo esc_html( $cta_text ); ?></p>
                    <a href="<?php echo esc_url( $contact_url ); ?>" class="footer__cta-btn">
                        <?php echo esc_html( $cta_button_text ); ?>
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <?php if ( $newsletter_form_id && function_exists( 'wpcf7_contact_form' ) ) : 
                    $form = wpcf7_contact_form( $newsletter_form_id );
                    if ( $form ) :
                ?>
                <div class="footer__newsletter">
                    <h3 class="footer__newsletter-title"><?php esc_html_e( 'Weekly Insights', 'gloceps' ); ?></h3>
                    <p class="footer__newsletter-text">
                        <?php esc_html_e( 'Subscribe to receive our latest research and analysis directly in your inbox.', 'gloceps' ); ?>
                    </p>
                    <?php echo do_shortcode( '[contact-form-7 id="' . esc_attr( $newsletter_form_id ) . '"]' ); ?>
                </div>
                <?php 
                    endif;
                endif; 
                ?>
            </div>
        </div>
    </div>

    <!-- Scrolling Marquee -->
    <?php if ( ! empty( $marquee_items ) ) : ?>
    <div class="footer__marquee">
        <div class="footer__marquee-track">
            <div class="footer__marquee-item">
                <?php foreach ( $marquee_items as $item ) : ?>
                    <span class="footer__marquee-text"><?php echo esc_html( $item['text'] ); ?></span>
                    <span class="footer__marquee-dot"></span>
                <?php endforeach; ?>
            </div>
            <div class="footer__marquee-item">
                <?php foreach ( $marquee_items as $item ) : ?>
                    <span class="footer__marquee-text"><?php echo esc_html( $item['text'] ); ?></span>
                    <span class="footer__marquee-dot"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Footer Content -->
    <div class="footer__main">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer__logo">
                        <img src="<?php echo esc_url( GLOCEPS_URI . '/assets/images/glocep-logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
                        <span class="footer__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                    </a>
                    <p class="footer__description">
                        <?php echo esc_html( $footer_description ); ?>
                    </p>
                    <?php gloceps_social_links(); ?>
                </div>

                <div class="footer__column">
                    <h4><?php esc_html_e( 'About', 'gloceps' ); ?></h4>
                    <?php
                    if ( has_nav_menu( 'footer-about' ) ) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer-about',
                                'menu_class'     => 'footer__links',
                                'container'      => 'nav',
                                'container_class' => '',
                                'depth'          => 1,
                            )
                        );
                    } else {
                        ?>
                        <nav class="footer__links">
                            <a href="#"><?php esc_html_e( 'Who We Are', 'gloceps' ); ?></a>
                            <a href="#"><?php esc_html_e( 'Mission & Vision', 'gloceps' ); ?></a>
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'team_member' ) ); ?>"><?php esc_html_e( 'Team', 'gloceps' ); ?></a>
                            <a href="#"><?php esc_html_e( 'Governance', 'gloceps' ); ?></a>
                        </nav>
                        <?php
                    }
                    ?>
                </div>

                <div class="footer__column">
                    <h4><?php esc_html_e( 'Research', 'gloceps' ); ?></h4>
                    <?php
                    if ( has_nav_menu( 'footer-research' ) ) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer-research',
                                'menu_class'     => 'footer__links',
                                'container'      => 'nav',
                                'container_class' => '',
                                'depth'          => 1,
                            )
                        );
                    } else {
                        // Display research pillars
                        $pillars = get_terms(
                            array(
                                'taxonomy'   => 'research_pillar',
                                'hide_empty' => false,
                            )
                        );
                        if ( $pillars && ! is_wp_error( $pillars ) ) {
                            echo '<nav class="footer__links">';
                            foreach ( $pillars as $pillar ) {
                                printf(
                                    '<a href="%s">%s</a>',
                                    esc_url( get_term_link( $pillar ) ),
                                    esc_html( $pillar->name )
                                );
                            }
                            echo '</nav>';
                        }
                    }
                    ?>
                </div>

                <div class="footer__column">
                    <h4><?php esc_html_e( 'Connect', 'gloceps' ); ?></h4>
                    <?php
                    if ( has_nav_menu( 'footer-connect' ) ) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer-connect',
                                'menu_class'     => 'footer__links',
                                'container'      => 'nav',
                                'container_class' => '',
                                'depth'          => 1,
                            )
                        );
                    } else {
                        ?>
                        <nav class="footer__links">
                            <a href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Contact Us', 'gloceps' ); ?></a>
                            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Purchase', 'gloceps' ); ?></a>
                            <?php endif; ?>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                        </nav>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer__bottom">
        <div class="container">
            <div class="footer__bottom-inner">
                <p class="footer__copyright">
                    <?php echo esc_html( $copyright_text ); ?>
                </p>
                <div class="footer__bottom-right">
                    <?php
                    if ( has_nav_menu( 'footer-legal' ) ) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer-legal',
                                'menu_class'     => 'footer__legal',
                                'container'      => 'nav',
                                'container_class' => '',
                                'depth'          => 1,
                            )
                        );
                    } else {
                        ?>
                        <nav class="footer__legal">
                            <a href="#"><?php esc_html_e( 'Privacy', 'gloceps' ); ?></a>
                            <a href="#"><?php esc_html_e( 'Terms', 'gloceps' ); ?></a>
                        </nav>
                        <?php
                    }
                    ?>
                    <button class="footer__back-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                        <?php esc_html_e( 'Back to top', 'gloceps' ); ?>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php
// Include cart slide-in panel
if ( class_exists( 'WooCommerce' ) ) {
    get_template_part( 'template-parts/components/cart-slide' );
}
?>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox">
    <button class="lightbox__close" aria-label="<?php esc_attr_e('Close gallery', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <button class="lightbox__nav lightbox__nav--prev" aria-label="<?php esc_attr_e('Previous image', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button class="lightbox__nav lightbox__nav--next" aria-label="<?php esc_attr_e('Next image', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <div class="lightbox__content">
        <img src="" alt="" class="lightbox__image" />
        <div class="lightbox__caption"></div>
    </div>
    <div class="lightbox__counter"></div>
</div>

<!-- Bio Modal -->
<div class="bio-modal" id="bioModal">
    <div class="bio-modal__overlay"></div>
    <div class="bio-modal__panel">
        <button class="bio-modal__close" aria-label="<?php esc_attr_e('Close bio', 'gloceps'); ?>">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="bio-modal__content">
            <div class="bio-modal__header">
                <div class="bio-modal__image" id="bioImage"></div>
                <div class="bio-modal__meta">
                    <span class="bio-modal__role" id="bioRole"></span>
                    <h2 class="bio-modal__name" id="bioName"></h2>
                    <p class="bio-modal__job-title" id="bioJobTitle"></p>
                </div>
            </div>
            <div class="bio-modal__body" id="bioBio"></div>
        </div>
    </div>
</div>

<?php wp_footer(); ?>

</body>
</html>

