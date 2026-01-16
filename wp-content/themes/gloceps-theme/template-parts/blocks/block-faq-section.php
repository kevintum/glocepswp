<?php
/**
 * Flexible Content Block: FAQ Section
 * 
 * Matches the static HTML contact.html FAQ section exactly.
 * Grid layout with question/answer cards.
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get block fields
$title = get_sub_field('title') ?: 'Frequently Asked Questions';
$description = get_sub_field('description');
$faqs = get_sub_field('faqs');
?>

<section class="section section--gray">
    <div class="container">
        <div class="section-header section-header--center">
            <?php if ($title) : ?>
                <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($description) : ?>
                <p class="section-header__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($faqs && is_array($faqs)) : ?>
            <div class="faq-grid">
                <?php foreach ($faqs as $faq) : 
                    $question = $faq['question'] ?? '';
                    $answer = $faq['answer'] ?? '';
                    
                    if (!$question) continue;
                ?>
                    <div class="faq-item">
                        <h3 class="faq-item__question"><?php echo esc_html($question); ?></h3>
                        <div class="faq-item__answer"><?php echo wp_kses_post($answer); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
