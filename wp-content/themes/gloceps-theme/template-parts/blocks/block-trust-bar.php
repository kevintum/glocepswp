<?php
/**
 * Flexible Content Block: Trust Bar Section
 *
 * @package GLOCEPS
 */

$trust_items = get_sub_field('trust_items');
?>

<?php if ($trust_items && !empty($trust_items)) : ?>
<div class="trust-bar">
    <div class="container">
        <div class="trust-bar__inner">
            <?php foreach ($trust_items as $item) : 
                $text = $item['text'] ?? '';
                if (empty($text)) continue;
            ?>
            <div class="trust-item">
                <?php
                // Match static HTML exactly - icons based on text content
                // Use GLOCEPS green color (#70b544) directly in SVG
                $green_color = '#70b544';
                $icon = '';
                if (stripos($text, 'secure') !== false || stripos($text, 'payment') !== false || stripos($text, 'm-pesa') !== false) {
                    $icon = '<svg width="18" height="18" fill="none" stroke="' . esc_attr($green_color) . '" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
                } elseif (stripos($text, 'download') !== false || stripos($text, 'pdf') !== false || stripos($text, 'instant') !== false) {
                    $icon = '<svg width="18" height="18" fill="none" stroke="' . esc_attr($green_color) . '" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                } elseif (stripos($text, 'peer') !== false || stripos($text, 'review') !== false || stripos($text, 'research') !== false) {
                    $icon = '<svg width="18" height="18" fill="none" stroke="' . esc_attr($green_color) . '" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                } elseif (stripos($text, 'invoice') !== false) {
                    $icon = '<svg width="18" height="18" fill="none" stroke="' . esc_attr($green_color) . '" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                } else {
                    // Default checkmark icon
                    $icon = '<svg width="18" height="18" fill="none" stroke="' . esc_attr($green_color) . '" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
                }
                ?>
                <?php echo $icon; ?>
                <span><?php echo esc_html($text); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
