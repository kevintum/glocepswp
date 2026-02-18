<?php
/**
 * Template Name: Contact Page
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get contact details
$phone = get_field( 'footer_phone', 'option' ) ?: '+254 112 401 331';
$email = get_field( 'footer_email', 'option' ) ?: 'info@gloceps.org';
$address = get_field( 'footer_address', 'option' ) ?: 'Runda Drive, Nairobi, Kenya';

while ( have_posts() ) :
    the_post();
?>

<!-- Page Header -->
<?php
$header_attrs = gloceps_get_page_header_attrs();
?>
<section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title"><?php the_title(); ?></h1>
            <p class="page-header__description">
                <?php echo has_excerpt() ? wp_kses_post( get_the_excerpt() ) : esc_html__( 'Get in touch with GLOCEPS. We\'re here to collaborate on policy research and strategic initiatives.', 'gloceps' ); ?>
            </p>
        </div>
    </div>
</section>

<main>
    <section class="section contact-section">
        <div class="container">
            <div class="contact-layout">
                <!-- Contact Info -->
                <div class="contact-info reveal">
                    <div class="contact-info__card">
                        <h2 class="contact-info__title"><?php esc_html_e( 'Contact Information', 'gloceps' ); ?></h2>
                        <p class="contact-info__description">
                            <?php esc_html_e( 'Reach out to us for research collaborations, speaking engagements, or to learn more about our work.', 'gloceps' ); ?>
                        </p>

                        <div class="contact-info__items">
                            <div class="contact-info__item">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <strong><?php esc_html_e( 'Address', 'gloceps' ); ?></strong>
                                    <p><?php echo esc_html( $address ); ?></p>
                                </div>
                            </div>

                            <div class="contact-info__item">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <div>
                                    <strong><?php esc_html_e( 'Phone', 'gloceps' ); ?></strong>
                                    <p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
                                </div>
                            </div>

                            <div class="contact-info__item">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <strong><?php esc_html_e( 'Email', 'gloceps' ); ?></strong>
                                    <p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info__social">
                            <strong><?php esc_html_e( 'Follow Us', 'gloceps' ); ?></strong>
                            <?php gloceps_social_links(); ?>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form reveal">
                    <h2 class="contact-form__title"><?php esc_html_e( 'Send us a Message', 'gloceps' ); ?></h2>
                    
                    <?php 
                    // If Contact Form 7 is active and shortcode is set
                    $contact_form = get_field( 'contact_form_shortcode' );
                    if ( $contact_form ) {
                        echo do_shortcode( $contact_form );
                    } else {
                    ?>
                    <form class="contact-form__form" method="post" action="#">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><?php esc_html_e( 'Full Name', 'gloceps' ); ?> *</label>
                                <input type="text" id="name" name="name" required />
                            </div>
                            <div class="form-group">
                                <label for="email"><?php esc_html_e( 'Email Address', 'gloceps' ); ?> *</label>
                                <input type="email" id="email" name="email" required />
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="organization"><?php esc_html_e( 'Organization', 'gloceps' ); ?></label>
                                <input type="text" id="organization" name="organization" />
                            </div>
                            <div class="form-group">
                                <label for="subject"><?php esc_html_e( 'Subject', 'gloceps' ); ?> *</label>
                                <select id="subject" name="subject" required>
                                    <option value=""><?php esc_html_e( 'Select a topic', 'gloceps' ); ?></option>
                                    <option value="collaboration"><?php esc_html_e( 'Research Collaboration', 'gloceps' ); ?></option>
                                    <option value="speaking"><?php esc_html_e( 'Speaking Engagement', 'gloceps' ); ?></option>
                                    <option value="publications"><?php esc_html_e( 'Publications Inquiry', 'gloceps' ); ?></option>
                                    <option value="media"><?php esc_html_e( 'Media Inquiry', 'gloceps' ); ?></option>
                                    <option value="other"><?php esc_html_e( 'Other', 'gloceps' ); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><?php esc_html_e( 'Message', 'gloceps' ); ?> *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn--primary btn--lg">
                            <?php esc_html_e( 'Send Message', 'gloceps' ); ?>
                            <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <?php 
    $map_embed = get_field( 'map_embed' );
    if ( $map_embed ) :
    ?>
    <section class="section section--compact contact-map">
        <div class="container--wide">
            <div class="map-container">
                <?php echo wp_kses_post( $map_embed ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
endwhile;

get_footer();

