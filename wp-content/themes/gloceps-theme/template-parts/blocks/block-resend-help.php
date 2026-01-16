<?php
/**
 * Flexible Content Block: Resend Help & FAQ Section
 * 
 * Combined help section with support contact information and FAQ
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$title = get_sub_field('title') ?: 'Need More Help?';
$email_label = get_sub_field('email_label') ?: 'Email Support';
$email = get_sub_field('email') ?: 'support@gloceps.org';
$phone_label = get_sub_field('phone_label') ?: 'Phone Support';
$phone = get_sub_field('phone') ?: '+254 112 401 331';
$hours = get_sub_field('hours') ?: 'Mon-Fri, 8am-5pm EAT';

// FAQ fields
$faq_title = get_sub_field('faq_title') ?: 'COMMON QUESTIONS';
$faq_items = get_sub_field('faq_items');
?>

<div class="resend-publications__help-panel">
    <h2 class="resend-publications__help-title"><?php echo esc_html($title); ?></h2>
    
    <!-- Email Support -->
    <div class="resend-publications__help-item">
        <div class="resend-publications__help-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="resend-publications__help-content">
            <strong><?php echo esc_html($email_label); ?></strong>
            <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
        </div>
    </div>
    
    <!-- Phone Support -->
    <div class="resend-publications__help-item">
        <div class="resend-publications__help-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <div class="resend-publications__help-content">
            <strong><?php echo esc_html($phone_label); ?></strong>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
            <span class="resend-publications__help-hours"><?php echo esc_html($hours); ?></span>
        </div>
    </div>
    
    <?php if ($faq_items && is_array($faq_items) && count($faq_items) > 0) : ?>
    <!-- FAQ Section -->
    <div class="resend-publications__faq">
        <h3 class="resend-publications__faq-title"><?php echo esc_html($faq_title); ?></h3>
        
        <?php foreach ($faq_items as $faq) : 
            $question = $faq['question'] ?? '';
            $answer = $faq['answer'] ?? '';
            
            if (empty($question) || empty($answer)) continue;
        ?>
        <div class="resend-publications__faq-item" aria-expanded="false">
            <button class="resend-publications__faq-question" type="button">
                <svg width="16" height="16" viewBox="0 0 16 16" class="resend-publications__faq-icon">
                    <path d="M6 4l4 4-4 4" fill="currentColor" stroke="none"/>
                </svg>
                <span><?php echo esc_html($question); ?></span>
            </button>
            <div class="resend-publications__faq-answer">
                <p><?php echo esc_html($answer); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

