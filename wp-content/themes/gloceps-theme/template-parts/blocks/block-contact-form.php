<?php
/**
 * Flexible Content Block: Contact Form Section
 *
 * @package GLOCEPS
 */

$title = get_sub_field('title');
$subtitle = get_sub_field('subtitle');
$shortcode = get_sub_field('shortcode');
?>

<section class="section contact-form-section">
    <div class="container">
        <div class="contact-form-section__wrapper reveal">
            <div class="contact-form-section__header">
                <?php if ( $title ) : ?>
                    <h2 class="contact-form-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if ( $subtitle ) : ?>
                    <p class="contact-form-section__subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <div class="contact-form-section__form">
                <?php 
                if ( $shortcode ) {
                    echo do_shortcode($shortcode);
                } else {
                    // Fallback form if no shortcode is provided
                ?>
                <form class="contact-form" method="post" action="">
                    <div class="contact-form__row">
                        <div class="contact-form__field">
                            <label for="name"><?php esc_html_e('Full Name', 'gloceps'); ?> *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="contact-form__field">
                            <label for="email"><?php esc_html_e('Email Address', 'gloceps'); ?> *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="contact-form__row">
                        <div class="contact-form__field">
                            <label for="organization"><?php esc_html_e('Organization', 'gloceps'); ?></label>
                            <input type="text" id="organization" name="organization">
                        </div>
                        <div class="contact-form__field">
                            <label for="inquiry_type"><?php esc_html_e('Inquiry Type', 'gloceps'); ?></label>
                            <select id="inquiry_type" name="inquiry_type">
                                <option value=""><?php esc_html_e('Select an option', 'gloceps'); ?></option>
                                <option value="research"><?php esc_html_e('Research Partnership', 'gloceps'); ?></option>
                                <option value="media"><?php esc_html_e('Media Inquiry', 'gloceps'); ?></option>
                                <option value="speaking"><?php esc_html_e('Events & Speaking', 'gloceps'); ?></option>
                                <option value="general"><?php esc_html_e('General Inquiry', 'gloceps'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="contact-form__field">
                        <label for="subject"><?php esc_html_e('Subject', 'gloceps'); ?> *</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="contact-form__field">
                        <label for="message"><?php esc_html_e('Message', 'gloceps'); ?> *</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="btn btn--primary btn--lg">
                        <?php esc_html_e('Send Message', 'gloceps'); ?>
                        <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

