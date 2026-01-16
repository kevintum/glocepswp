<?php
/**
 * Template Name: Resend Publications
 * 
 * Page template for resending publication download links
 * Uses ACF Flexible Content blocks for full customization
 *
 * @package GLOCEPS
 */

get_header();

// Check if the page has flexible content blocks
$has_content_blocks = function_exists('have_rows') && have_rows('content_blocks');
?>

<section class="resend-publications">
    <div class="container">
        <div class="resend-publications__layout">
            
            <?php if ($has_content_blocks) : ?>
                <!-- Render Flexible Content Blocks -->
                <?php include get_template_directory() . '/template-parts/blocks/block-renderer.php'; ?>
            <?php else : ?>
                <!-- Fallback: Default content if no blocks are set -->
                <div class="resend-publications__form-panel">
                    <div class="resend-publications__icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="resend-publications__title"><?php esc_html_e('Resend Your Publications', 'gloceps'); ?></h1>
                    <p class="resend-publications__description">
                        <?php esc_html_e("Didn't receive your download email? No problem. Enter your order details below and we'll send a fresh copy of your download links.", 'gloceps'); ?>
                    </p>
                    <p><?php esc_html_e('Please configure this page using the Page Builder blocks in the WordPress admin.', 'gloceps'); ?></p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</section>

<?php
get_footer();

