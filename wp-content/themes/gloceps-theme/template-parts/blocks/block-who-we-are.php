<?php
/**
 * Block: Who We Are
 * 
 * Content with image section for About page
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Who We Are';
$title = get_sub_field('title');
$lead_text = get_sub_field('lead_text');
$content = get_sub_field('content');
$image = get_sub_field('image');
$cta = get_sub_field('cta');
$anchor_id = get_sub_field('anchor_id') ?: 'who-we-are';
?>

<section class="section" id="<?php echo esc_attr($anchor_id); ?>">
    <div class="container">
        <div class="two-col">
            <div class="reveal">
                <div class="section-header">
                    <?php if ($eyebrow) : ?>
                        <div class="section-header__eyebrow">
                            <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($title) : ?>
                        <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                </div>
                
                <?php if ($lead_text) : ?>
                    <p class="lead" style="margin-bottom: var(--space-6)">
                        <?php echo esc_html($lead_text); ?>
                    </p>
                <?php endif; ?>
                
                <?php if ($content) : ?>
                    <div class="page-content" style="color: var(--color-gray-600); line-height: var(--leading-relaxed);">
                        <?php echo wp_kses_post($content); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($cta) : 
                    $cta_url = is_array($cta) ? ($cta['url'] ?? '#') : $cta;
                    $cta_title = is_array($cta) ? ($cta['title'] ?? 'Learn More') : 'Learn More';
                    $cta_target = is_array($cta) ? ($cta['target'] ?? '') : '';
                ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="btn btn--primary" <?php echo $cta_target ? 'target="' . esc_attr($cta_target) . '"' : ''; ?>>
                        <?php echo esc_html($cta_title); ?>
                        <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if ($image) : ?>
                <div class="reveal reveal--delay-2">
                    <div class="rounded-image" style="aspect-ratio: 4/3">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: $title); ?>" style="object-position: center center" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>


