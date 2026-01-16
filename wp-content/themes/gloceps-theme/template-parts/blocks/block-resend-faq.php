<?php
/**
 * Flexible Content Block: Resend FAQ Section
 * 
 * FAQ section for resend publications page with accordion functionality
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$title = get_sub_field('title') ?: 'COMMON QUESTIONS';
$faq_items = get_sub_field('faq_items');
?>

<?php if ($faq_items && is_array($faq_items) && count($faq_items) > 0) : ?>
<div class="resend-publications__faq">
    <h3 class="resend-publications__faq-title"><?php echo esc_html($title); ?></h3>
    
    <?php foreach ($faq_items as $faq) : 
        $question = $faq['question'] ?? '';
        $answer = $faq['answer'] ?? '';
        
        if (empty($question) || empty($answer)) continue;
    ?>
    <div class="resend-publications__faq-item">
        <button class="resend-publications__faq-question" type="button" aria-expanded="false">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="resend-publications__faq-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
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

