<?php
/**
 * Event Speakers Section
 * 
 * Matches event-single.html event-speakers section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$speakers = isset($speakers) ? $speakers : get_field('speakers');
?>

<div class="event-speakers">
    <h2><?php esc_html_e('Featured Speakers', 'gloceps'); ?></h2>
    
    <?php if ($speakers && is_array($speakers) && count($speakers) > 0) : ?>
        <div class="event-speakers__grid">
            <?php foreach ($speakers as $speaker) : 
                $speaker_image_raw = $speaker['image'] ?? '';
                $speaker_name = $speaker['name'] ?? '';
                $speaker_title = $speaker['title'] ?? '';
                
                if (!$speaker_name) continue;
                
                // Handle different image formats (array, ID, or URL string)
                $speaker_image = '';
                if ($speaker_image_raw) {
                    if (is_array($speaker_image_raw)) {
                        $speaker_image = $speaker_image_raw['url'] ?? '';
                    } elseif (is_numeric($speaker_image_raw)) {
                        $speaker_image = wp_get_attachment_image_url($speaker_image_raw, 'thumbnail');
                    } elseif (is_string($speaker_image_raw)) {
                        $speaker_image = $speaker_image_raw;
                    }
                }
            ?>
                <div class="event-speaker">
                    <?php if ($speaker_image && is_string($speaker_image)) : ?>
                        <div class="event-speaker__image">
                            <img src="<?php echo esc_url($speaker_image); ?>" alt="<?php echo esc_attr($speaker_name); ?>" />
                        </div>
                    <?php endif; ?>
                    <div class="event-speaker__info">
                        <h4 class="event-speaker__name"><?php echo esc_html($speaker_name); ?></h4>
                        <?php if ($speaker_title) : ?>
                            <p class="event-speaker__title"><?php echo esc_html($speaker_title); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

