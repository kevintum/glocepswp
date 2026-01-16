<?php
/**
 * Block: Pillar Introduction
 * 
 * Two-column layout with lead text and statistics card
 * 
 * @package GLOCEPS
 */

$lead_text = get_sub_field('lead_text');
$text = get_sub_field('text');
$stats = get_sub_field('stats');
?>

<section class="section pillar-intro" id="pillar-intro">
    <div class="container">
        <div class="pillar-intro__grid reveal">
            <div class="pillar-intro__content">
                <?php if ($lead_text) : ?>
                    <p class="pillar-intro__lead"><?php echo esc_html($lead_text); ?></p>
                <?php endif; ?>
                
                <?php if ($text) : ?>
                    <div class="pillar-intro__text">
                        <?php echo wp_kses_post($text); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($stats) : ?>
                <div class="pillar-intro__stats">
                    <?php foreach ($stats as $stat) : 
                        $value = $stat['value'] ?? '';
                        $label = $stat['label'] ?? '';
                    ?>
                        <div class="pillar-stat">
                            <div class="pillar-stat__value"><?php echo esc_html($value); ?></div>
                            <div class="pillar-stat__label"><?php echo esc_html($label); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

