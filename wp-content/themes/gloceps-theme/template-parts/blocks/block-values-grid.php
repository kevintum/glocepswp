<?php
/**
 * Block: Values Grid / Our Approach
 * 
 * Grid of value/approach cards with icons
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'What We Do';
$title = get_sub_field('title') ?: 'Our Approach';
$description = get_sub_field('description');
$items = get_sub_field('items');
$anchor_id = get_sub_field('anchor_id') ?: 'our-approach';
?>

<?php if ($items && !empty($items)) : ?>
<section class="section" id="<?php echo esc_attr($anchor_id); ?>">
    <div class="container">
        <div class="section-header section-header--center reveal">
            <?php if ($eyebrow) : ?>
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($title) : ?>
                <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($description) : ?>
                <p class="section-header__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <div class="values-grid reveal stagger-children">
            <?php foreach ($items as $item) : 
                $icon = $item['icon'] ?? '';
                $item_title = $item['title'] ?? '';
                $text = $item['text'] ?? '';
                
                if (!$item_title) continue;
            ?>
                <div class="value-card">
                    <?php if ($icon) : ?>
                        <div class="value-card__icon">
                            <?php echo wp_kses($icon, array(
                                'svg' => array('width' => array(), 'height' => array(), 'fill' => array(), 'stroke' => array(), 'stroke-width' => array(), 'viewBox' => array(), 'class' => array()),
                                'path' => array('stroke-linecap' => array(), 'stroke-linejoin' => array(), 'd' => array()),
                            )); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($item_title) : ?>
                        <h4 class="value-card__title"><?php echo esc_html($item_title); ?></h4>
                    <?php endif; ?>
                    <?php if ($text) : ?>
                        <p class="value-card__text"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


