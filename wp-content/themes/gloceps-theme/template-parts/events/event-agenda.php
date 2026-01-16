<?php
/**
 * Event Agenda Section
 * 
 * Matches event-single.html event-agenda section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$agenda = isset($agenda) ? $agenda : get_field('agenda');
?>

<div class="event-agenda">
    <h2><?php esc_html_e('Event Agenda', 'gloceps'); ?></h2>
    
    <?php if ($agenda && is_array($agenda) && count($agenda) > 0) : ?>
        <?php foreach ($agenda as $day) : 
            $day_title = $day['day_title'] ?? '';
            $items = $day['items'] ?? array();
            
            if (!$day_title) continue;
        ?>
            <div class="event-agenda__day">
                <h3 class="event-agenda__day-title"><?php echo esc_html($day_title); ?></h3>
                
                <?php if ($items && is_array($items) && count($items) > 0) : ?>
                    <div class="event-agenda__items">
                        <?php foreach ($items as $item) : 
                            $time = $item['time'] ?? '';
                            $title = $item['title'] ?? '';
                            $description = $item['description'] ?? '';
                            
                            if (!$time || !$title) continue;
                        ?>
                            <div class="event-agenda__item">
                                <div class="event-agenda__time"><?php echo esc_html($time); ?></div>
                                <div class="event-agenda__content">
                                    <h4><?php echo esc_html($title); ?></h4>
                                    <?php if ($description) : ?>
                                        <p><?php echo esc_html($description); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

