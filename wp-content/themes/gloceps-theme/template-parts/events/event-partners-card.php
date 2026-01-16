<?php
/**
 * Event Partners Card (Sidebar)
 * 
 * Matches event-single.html event-partners-card section
 *
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

$partners = isset($partners) ? $partners : get_field('partners');
?>

<?php if ($partners && is_array($partners) && count($partners) > 0) : ?>
    <div class="event-partners-card">
        <h3 class="event-partners-card__title"><?php esc_html_e('Event Partners', 'gloceps'); ?></h3>
        <div class="event-partners-card__logos">
            <?php foreach ($partners as $partner) : 
                $logo_raw = $partner['logo'] ?? '';
                $name = $partner['name'] ?? '';
                
                // Handle different image formats (array, ID, or URL string)
                $logo = '';
                if ($logo_raw) {
                    if (is_array($logo_raw)) {
                        $logo = $logo_raw['url'] ?? '';
                    } elseif (is_numeric($logo_raw)) {
                        $logo = wp_get_attachment_image_url($logo_raw, 'thumbnail');
                    } elseif (is_string($logo_raw)) {
                        $logo = $logo_raw;
                    }
                }
                
                if (!$logo || !is_string($logo)) continue;
            ?>
                <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($name ?: 'Event Partner'); ?>" />
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

