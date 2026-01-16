<?php
/**
 * Block: Mission & Vision
 * 
 * Two-card layout for Mission and Vision
 * 
 * @package GLOCEPS
 */

$mission_title = get_sub_field('mission_title') ?: 'Our Mission';
$mission_text = get_sub_field('mission_text');
$vision_title = get_sub_field('vision_title') ?: 'Our Vision';
$vision_text = get_sub_field('vision_text');
$anchor_id = get_sub_field('anchor_id') ?: 'mission-vision';
?>

<section class="section mission-vision-section" id="<?php echo esc_attr($anchor_id); ?>">
    <div class="container">
        <div class="mission-vision-grid reveal">
            <div class="mission-vision-card mission-vision-card--vision">
                <div class="mission-vision-card__icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="mission-vision-card__label"><?php echo esc_html($vision_title); ?></span>
                <?php if ($vision_text) : ?>
                    <p class="mission-vision-card__text"><?php echo esc_html($vision_text); ?></p>
                <?php endif; ?>
            </div>

            <div class="mission-vision-card mission-vision-card--mission">
                <div class="mission-vision-card__icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <span class="mission-vision-card__label"><?php echo esc_html($mission_title); ?></span>
                <?php if ($mission_text) : ?>
                    <p class="mission-vision-card__text"><?php echo esc_html($mission_text); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


