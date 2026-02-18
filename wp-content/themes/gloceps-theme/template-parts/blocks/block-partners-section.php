<?php
/**
 * Block: Partners Section
 * 
 * Grid of partner logos
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Collaborations';
$title = get_sub_field('title') ?: 'Our Partners';
$description = get_sub_field('description');
$background_style = get_sub_field('background_style') ?: 'default';
$layout = get_sub_field('layout') ?: 'grid';
$logos = get_sub_field('logos');
$anchor_id = get_sub_field('anchor_id') ?: 'partners';
?>

<section class="section partners-section section--bg-<?php echo esc_attr($background_style); ?>" id="<?php echo esc_attr($anchor_id); ?>">
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

        <?php if ($logos && !empty($logos)) : ?>
            <?php if ($layout === 'carousel') : ?>
                <div class="partners-carousel-wrapper reveal">
                    <button class="partners-carousel__btn partners-carousel__btn--prev" aria-label="Previous logos">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                    <div class="partners-carousel" data-partners-carousel>
                        <div class="partners-carousel__track">
                            <?php foreach ($logos as $logo) : 
                                $name = $logo['name'] ?? '';
                                $logo_image = $logo['logo'] ?? null;
                                $link = $logo['link'] ?? '';
                            ?>
                                <div class="partners-carousel__slide">
                                    <div class="partner-logo">
                                        <?php if ($link) : ?>
                                            <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener">
                                        <?php endif; ?>
                                        
                                        <?php if ($logo_image) : ?>
                                            <img src="<?php echo esc_url($logo_image['url']); ?>" alt="<?php echo esc_attr($logo_image['alt'] ?: $name); ?>" />
                                        <?php else : ?>
                                            <div class="partner-logo__placeholder"><?php echo esc_html($name ?: 'Partner Logo'); ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if ($link) : ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="partners-carousel__btn partners-carousel__btn--next" aria-label="Next logos">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                </div>
            <?php else : ?>
                <div class="partners-grid reveal">
                    <?php foreach ($logos as $logo) : 
                        $name = $logo['name'] ?? '';
                        $logo_image = $logo['logo'] ?? null;
                        $link = $logo['link'] ?? '';
                    ?>
                        <div class="partner-logo">
                            <?php if ($link) : ?>
                                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener">
                            <?php endif; ?>
                            
                            <?php if ($logo_image) : ?>
                                <img src="<?php echo esc_url($logo_image['url']); ?>" alt="<?php echo esc_attr($logo_image['alt'] ?: $name); ?>" />
                            <?php else : ?>
                                <div class="partner-logo__placeholder"><?php echo esc_html($name ?: 'Partner Logo'); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($link) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="partners-grid reveal">
                <?php for ($i = 0; $i < 10; $i++) : ?>
                    <div class="partner-logo">
                        <div class="partner-logo__placeholder">Partner Logo</div>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>


