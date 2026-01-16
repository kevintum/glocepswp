<?php
/**
 * Block: CTA Section
 * 
 * Call to action section with optional background image
 * 
 * @package GLOCEPS
 */

// Always render this block - no early returns
$style = get_sub_field('style') ?: 'default';
$title = get_sub_field('title') ?: "Can't Find What You're Looking For?";
$description = get_sub_field('description') ?: 'Browse our free publications or contact us for custom research commissions tailored to your specific policy needs.';
$background_image = get_sub_field('background_image');
$primary_button = get_sub_field('primary_button');
$secondary_button = get_sub_field('secondary_button');
$anchor_id = get_sub_field('anchor_id');

$section_classes = 'section cta-section';
if ($style === 'dark') {
    $section_classes .= ' cta-section--dark';
} elseif ($style === 'primary') {
    $section_classes .= ' cta-section--primary';
}
?>

<section class="<?php echo esc_attr($section_classes); ?>" <?php echo $anchor_id ? 'id="' . esc_attr($anchor_id) . '"' : 'id="cta-section"'; ?>>
    <?php if ($background_image) : ?>
        <div class="cta-section__bg">
            <img src="<?php echo esc_url($background_image['url']); ?>" alt="<?php echo esc_attr($background_image['alt']); ?>">
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="cta-section__content reveal">
            <?php if ($title) : ?>
                <h2 class="cta-section__title"><?php echo wp_kses_post($title); ?></h2>
            <?php endif; ?>

            <?php if ($description) : ?>
                <p class="cta-section__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php 
            // Use default buttons if none provided
            if (empty($primary_button) && empty($secondary_button)) {
                $primary_button = array('title' => 'Free Publications', 'url' => '/publications/', 'target' => '');
                $secondary_button = array('title' => 'Commission Research', 'url' => '/contact/', 'target' => '');
            }
            if ($primary_button || $secondary_button) : ?>
                <div class="cta-section__actions">
                    <?php if ($primary_button) : 
                        $primary_url = is_array($primary_button) ? ($primary_button['url'] ?? '#') : '#';
                        $primary_title = is_array($primary_button) ? ($primary_button['title'] ?? 'Free Publications') : $primary_button;
                        $primary_target = is_array($primary_button) ? ($primary_button['target'] ?? '') : '';
                    ?>
                        <a href="<?php echo esc_url($primary_url); ?>" class="btn btn--primary btn--lg" <?php echo $primary_target ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <?php echo esc_html($primary_title); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_button) : 
                        $secondary_url = is_array($secondary_button) ? ($secondary_button['url'] ?? '#') : '#';
                        $secondary_title = is_array($secondary_button) ? ($secondary_button['title'] ?? 'Commission Research') : $secondary_button;
                        $secondary_target = is_array($secondary_button) ? ($secondary_button['target'] ?? '') : '';
                    ?>
                        <a href="<?php echo esc_url($secondary_url); ?>" class="btn btn--secondary btn--lg" <?php echo $secondary_target ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <?php echo esc_html($secondary_title); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

