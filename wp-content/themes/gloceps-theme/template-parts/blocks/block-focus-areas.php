<?php
/**
 * Block: Focus Areas / How We Support
 * 
 * Displays alternating image/text blocks showing how the organization supports the pillar
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'OUR PROSPECTS';
$title = get_sub_field('title') ?: 'How We Support Foreign Policy';
$description = get_sub_field('description');
$items = get_sub_field('items');
?>

<section class="section focus-areas" style="background: #fafafa;">
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
        
        <?php if ($items) : 
            $index = 0;
            foreach ($items as $item) : 
                $number = $item['number'] ?? '';
                $image = $item['image'] ?? null;
                $item_title = $item['title'] ?? '';
                $item_description = $item['description'] ?? '';
                $bullets = $item['bullets'] ?? array();
                $is_reverse = ($index % 2 === 1);
                $index++;
        ?>
            <div class="focus-block <?php echo $is_reverse ? 'focus-block--reverse' : ''; ?> reveal">
                <?php if ($image) : ?>
                    <div class="focus-block__image">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="focus-block__content">
                    <?php if ($number) : ?>
                        <div class="focus-block__number"><?php echo esc_html($number); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($item_title) : ?>
                        <h3 class="focus-block__title"><?php echo esc_html($item_title); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ($item_description) : ?>
                        <p class="focus-block__text"><?php echo esc_html($item_description); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($bullets) : ?>
                        <ul class="focus-block__list">
                            <?php foreach ($bullets as $bullet) : 
                                $bullet_text = $bullet['text'] ?? '';
                            ?>
                                <?php if ($bullet_text) : ?>
                                    <li><?php echo esc_html($bullet_text); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>

