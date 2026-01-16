<?php
/**
 * Flexible Content Block: Contact Hero Section
 * 
 * Matches the static HTML contact.html contact-hero section exactly.
 * Includes left column with contact info and right column with form.
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$label = get_sub_field('label') ?: 'Get In Touch';
$title = get_sub_field('title') ?: "Let's Start a <em>Conversation</em>";
$description = get_sub_field('description');
$use_theme_info_field = get_sub_field('use_theme_contact_info');
// Default to true if field is not set (null), only disable if explicitly set to false
$use_theme_info = ($use_theme_info_field === false) ? false : true;
$show_social_field = get_sub_field('show_social');
// Default to true if field is not set (null)
$show_social = ($show_social_field === false) ? false : true;
$form_title = get_sub_field('form_title') ?: 'Send Us a Message';
$form_subtitle = get_sub_field('form_subtitle') ?: "Fill out the form below and we'll get back to you within 24-48 hours.";
$cf7_shortcode = get_sub_field('cf7_shortcode');

// Get contact info from theme settings (default behavior - always fetch for display)
$address = get_field('contact_address', 'option');
$phone = get_field('contact_phone', 'option');
$email = get_field('contact_email', 'option');
$office_hours = get_field('office_hours', 'option');

// Contact info is fetched from Theme Settings

// If use_theme_info is explicitly disabled, clear the values
if (!$use_theme_info) {
    $address = '';
    $phone = '';
    $email = '';
    $office_hours = '';
}

// Get social links from theme settings
$social_linkedin = get_field('social_linkedin', 'option');
$social_twitter = get_field('social_twitter', 'option');
$social_youtube = get_field('social_youtube', 'option');
$social_facebook = get_field('social_facebook', 'option');
?>

<section class="contact-hero">
    <div class="container">
        <div class="contact-hero__grid">
            <!-- Left Column: Content & Contact Info -->
            <div class="contact-hero__content">
                <?php if ($label) : ?>
                    <span class="contact-hero__label"><?php echo esc_html($label); ?></span>
                <?php endif; ?>

                <?php if ($title) : ?>
                    <h1 class="contact-hero__title"><?php echo wp_kses_post($title); ?></h1>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="contact-hero__description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <div class="contact-hero__info">
                    <?php if ($address) : ?>
                        <div class="contact-info-item">
                            <div class="contact-info-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <div class="contact-info-item__content">
                                <strong><?php esc_html_e('Visit Us', 'gloceps'); ?></strong>
                                <span><?php echo nl2br(esc_html($address)); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($phone) : ?>
                        <div class="contact-info-item">
                            <div class="contact-info-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </div>
                            <div class="contact-info-item__content">
                                <strong><?php esc_html_e('Call Us', 'gloceps'); ?></strong>
                                <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($email) : ?>
                        <div class="contact-info-item">
                            <div class="contact-info-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div class="contact-info-item__content">
                                <strong><?php esc_html_e('Email Us', 'gloceps'); ?></strong>
                                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($office_hours) : ?>
                        <div class="contact-info-item">
                            <div class="contact-info-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div class="contact-info-item__content">
                                <strong><?php esc_html_e('Office Hours', 'gloceps'); ?></strong>
                                <span><?php echo esc_html($office_hours); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($show_social && ($social_linkedin || $social_twitter || $social_youtube || $social_facebook)) : ?>
                    <div class="contact-hero__social">
                        <span><?php esc_html_e('Follow Us', 'gloceps'); ?></span>
                        <div class="contact-hero__social-links">
                            <?php if ($social_linkedin) : ?>
                                <a href="<?php echo esc_url($social_linkedin); ?>" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_twitter) : ?>
                                <a href="<?php echo esc_url($social_twitter); ?>" aria-label="X / Twitter" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_youtube) : ?>
                                <a href="<?php echo esc_url($social_youtube); ?>" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_facebook) : ?>
                                <a href="<?php echo esc_url($social_facebook); ?>" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Contact Form -->
            <div class="contact-hero__form-wrapper">
                <?php if ($cf7_shortcode) : ?>
                    <div class="contact-form">
                        <?php if ($form_title) : ?>
                            <h2 class="contact-form__title"><?php echo esc_html($form_title); ?></h2>
                        <?php endif; ?>
                        <?php if ($form_subtitle) : ?>
                            <p class="contact-form__subtitle"><?php echo esc_html($form_subtitle); ?></p>
                        <?php endif; ?>
                        <?php echo do_shortcode($cf7_shortcode); ?>
                    </div>
                <?php else : ?>
                    <!-- Fallback message for admin/editor -->
                    <div class="contact-form contact-form--placeholder">
                        <h2 class="contact-form__title"><?php echo esc_html($form_title); ?></h2>
                        <p class="contact-form__subtitle"><?php echo esc_html($form_subtitle); ?></p>
                        <div class="contact-form__notice">
                            <p><strong><?php esc_html_e('Contact Form Placeholder', 'gloceps'); ?></strong></p>
                            <p><?php esc_html_e('Add a Contact Form 7 shortcode in the block settings to display the form here.', 'gloceps'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
