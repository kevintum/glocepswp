<?php
/**
 * Block: Key Areas of Focus Grid
 * 
 * Grid of key focus areas with icons
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'RESEARCH THEMES';
$title = get_sub_field('title') ?: 'Key Areas of Focus';
$items = get_sub_field('items');
?>

<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <?php if ($eyebrow) : ?>
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($title) : ?>
                <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
        </div>
        
        <?php if ($items) : ?>
            <div class="themes-grid reveal stagger-children">
                <?php foreach ($items as $item) : 
                    $icon = $item['icon'] ?? '';
                    $item_title = $item['title'] ?? '';
                    $item_description = $item['description'] ?? '';
                ?>
                    <div class="theme-card">
                        <?php if ($icon) : ?>
                            <div class="theme-card__icon">
                                <?php echo $icon; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($item_title) : ?>
                            <h3 class="theme-card__title"><?php echo esc_html($item_title); ?></h3>
                        <?php endif; ?>
                        
                        <?php if ($item_description) : ?>
                            <p class="theme-card__text"><?php echo esc_html($item_description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

