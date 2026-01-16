<?php
/**
 * Flexible Content Block: Resend Publications Form
 * 
 * Form for users to request resend of their publication download links
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$title = get_sub_field('title') ?: 'Resend Your Publications';
$description = get_sub_field('description') ?: "Didn't receive your download email? No problem. Enter your order details below and we'll send a fresh copy of your publications.";
?>

<div class="resend-publications__form-panel">
    <div class="resend-publications__icon">
        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    
    <h1 class="resend-publications__title"><?php echo esc_html($title); ?></h1>
    
    <p class="resend-publications__description">
        <?php echo esc_html($description); ?>
    </p>
    
    <form id="resend-publications-form" class="resend-publications__form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
        <?php wp_nonce_field('gloceps_resend_publications', 'resend_nonce'); ?>
        <input type="hidden" name="action" value="gloceps_resend_publications">
        
        <div class="form-group">
            <label for="order_number" class="form-label">
                <?php esc_html_e('Order Number', 'gloceps'); ?>
                <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="order_number" 
                name="order_number" 
                class="form-input" 
                placeholder="<?php esc_attr_e('e.g., GCP-2024-00847', 'gloceps'); ?>" 
                required
            >
            <span class="form-hint"><?php esc_html_e('Find this in your original confirmation email', 'gloceps'); ?></span>
        </div>
        
        <div class="form-group">
            <label for="email_address" class="form-label">
                <?php esc_html_e('Email Address', 'gloceps'); ?>
                <span class="required">*</span>
            </label>
            <input 
                type="email" 
                id="email_address" 
                name="email_address" 
                class="form-input" 
                placeholder="<?php esc_attr_e('The email used during purchase', 'gloceps'); ?>" 
                required
            >
            <span class="form-hint"><?php esc_html_e('Must match the email used for your order', 'gloceps'); ?></span>
        </div>
        
        <div class="form-messages" id="resend-form-messages"></div>
        
        <button type="submit" class="btn btn--primary btn--lg btn--block resend-publications__submit-btn" id="resend-submit-btn">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <?php esc_html_e('Resend Publications', 'gloceps'); ?>
        </button>
    </form>
</div>

