<?php
/**
 * Event Description Section
 * 
 * Matches event-single.html event-description section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$description_lead = isset($description_lead) ? $description_lead : get_field('description_lead');
$key_themes = isset($key_themes) ? $key_themes : get_field('key_themes');
$who_should_attend = isset($who_should_attend) ? $who_should_attend : get_field('who_should_attend');
?>

<div class="event-description">
    <h2><?php esc_html_e('About This Event', 'gloceps'); ?></h2>
    
    <?php if ($description_lead) : ?>
        <p class="event-description__lead">
            <?php echo esc_html($description_lead); ?>
        </p>
    <?php endif; ?>
    
    <?php the_content(); ?>
    
    <?php if ($key_themes && is_array($key_themes) && count($key_themes) > 0) : ?>
        <h3><?php esc_html_e('Key Themes', 'gloceps'); ?></h3>
        <ul>
            <?php foreach ($key_themes as $theme) : 
                $theme_text = is_array($theme) ? ($theme['theme'] ?? '') : $theme;
                if ($theme_text) :
            ?>
                <li><?php echo esc_html($theme_text); ?></li>
            <?php 
                endif;
            endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <?php if ($who_should_attend) : ?>
        <h3><?php esc_html_e('Who Should Attend', 'gloceps'); ?></h3>
        <div class="event-description__who-should-attend">
            <?php echo wp_kses_post($who_should_attend); ?>
        </div>
    <?php endif; ?>
</div>

