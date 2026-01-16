<?php
/**
 * Block: Timeline
 * 
 * Vertical timeline for milestones/journey
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Our Story';
$title = get_sub_field('title') ?: 'The GLOCEPS Journey';
$items = get_sub_field('items');
$anchor_id = get_sub_field('anchor_id') ?: 'journey';
?>

<?php if ($items && !empty($items)) : ?>
<section class="section section--gray" id="<?php echo esc_attr($anchor_id); ?>">
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
        </div>

        <div class="timeline reveal">
            <div class="timeline__line"></div>
            
            <?php foreach ($items as $index => $item) : 
                $year = $item['year'] ?? '';
                $item_title = $item['title'] ?? '';
                $description = $item['description'] ?? '';
                $is_future = $item['is_future'] ?? false;
                
                if (!$year || !$item_title) continue;
            ?>
                <div class="timeline__item <?php echo $is_future ? 'timeline__item--future' : ''; ?>">
                    <div class="timeline__marker"></div>
                    <div class="timeline__content">
                        <?php if ($year) : ?>
                            <span class="timeline__year"><?php echo esc_html($year); ?></span>
                        <?php endif; ?>
                        <?php if ($item_title) : ?>
                            <h3 class="timeline__title"><?php echo esc_html($item_title); ?></h3>
                        <?php endif; ?>
                        <?php if ($description) : ?>
                            <p class="timeline__description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


