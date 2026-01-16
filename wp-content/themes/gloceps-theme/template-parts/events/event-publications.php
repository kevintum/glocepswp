<?php
/**
 * Event Related Publications Section
 * 
 * Matches event-single.html event-publications section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$related_publications = isset($related_publications) ? $related_publications : get_field('related_publications');
?>

<div class="event-publications">
    <h2><?php esc_html_e('Related Publications', 'gloceps'); ?></h2>
    
    <?php if ($related_publications && is_array($related_publications) && count($related_publications) > 0) : ?>
        <div class="event-publications__list">
            <?php foreach ($related_publications as $publication) : 
                $pub_id = is_object($publication) ? $publication->ID : $publication;
                $pub_types = get_the_terms($pub_id, 'publication_type');
                $pub_type = $pub_types && !is_wp_error($pub_types) ? $pub_types[0]->name : '';
            ?>
                <a href="<?php echo esc_url(get_permalink($pub_id)); ?>" class="event-publication">
                    <?php if ($pub_type) : ?>
                        <span class="event-publication__type"><?php echo esc_html($pub_type); ?></span>
                    <?php endif; ?>
                    <span class="event-publication__title"><?php echo esc_html(get_the_title($pub_id)); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

