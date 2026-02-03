<?php
/**
 * Block: Hero Image Carousel
 * 
 * Full-width hero carousel with up to 3 slides
 * Each slide has image, headline, description, and 2 CTAs
 * 
 * @package GLOCEPS
 */

$slides = get_sub_field('slides');
$autoplay_speed = get_sub_field('autoplay_speed') ?: 5;

// Ensure we have at least one slide
if (!$slides || empty($slides)) {
    return;
}

// Get first slide as default
$default_slide = $slides[0];

// Process slides - use first slide data as defaults for empty slides
$processed_slides = array();
foreach ($slides as $index => $slide) {
    $processed_slides[] = array(
        'image' => $slide['image'] ?: $default_slide['image'],
        'headline' => $slide['headline'] ?: $default_slide['headline'],
        'description' => $slide['description'] ?: $default_slide['description'],
        'button1_text' => $slide['button1_text'] ?: $default_slide['button1_text'],
        'button1_link' => $slide['button1_link'] ?: $default_slide['button1_link'],
        'button2_text' => $slide['button2_text'] ?: $default_slide['button2_text'],
        'button2_link' => $slide['button2_link'] ?: $default_slide['button2_link'],
    );
}

$slide_count = count($processed_slides);
?>

<!-- Hero Carousel -->
<section class="hero hero--carousel" data-hero-carousel data-autoplay="<?php echo esc_attr($autoplay_speed); ?>">
    <div class="hero-carousel__container">
        <div class="hero-carousel__track" data-carousel-track>
            <?php foreach ($processed_slides as $index => $slide) : 
                $image = $slide['image'];
                $headline = $slide['headline'];
                $description = $slide['description'];
                $btn1_text = $slide['button1_text'];
                $btn1_link = $slide['button1_link'];
                $btn2_text = $slide['button2_text'];
                $btn2_link = $slide['button2_link'];
                
                if (!$image) continue;
            ?>
                <div class="hero-carousel__slide<?php echo $index === 0 ? ' hero-carousel__slide--active' : ''; ?>" data-slide-index="<?php echo esc_attr($index); ?>">
                    <div class="hero-carousel__image-wrapper">
                        <img 
                            src="<?php echo esc_url($image['url']); ?>" 
                            alt="<?php echo esc_attr($image['alt'] ?: $headline); ?>"
                            class="hero-carousel__image"
                            loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                        />
                        <div class="hero-carousel__overlay"></div>
                    </div>
                    
                    <div class="hero-carousel__content">
                        <div class="container">
                            <div class="hero-carousel__content-inner">
                                <?php if ($headline) : ?>
                                    <h1 class="hero-carousel__headline">
                                        <?php echo esc_html($headline); ?>
                                    </h1>
                                <?php endif; ?>
                                
                                <?php if ($description) : ?>
                                    <p class="hero-carousel__description">
                                        <?php echo esc_html($description); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="hero-carousel__actions">
                                    <?php if ($btn1_text && $btn1_link) : 
                                        $btn1_url = is_array($btn1_link) ? $btn1_link['url'] : $btn1_link;
                                        $btn1_target = is_array($btn1_link) && !empty($btn1_link['target']) ? $btn1_link['target'] : '';
                                    ?>
                                        <a href="<?php echo esc_url($btn1_url); ?>" 
                                           class="btn btn--primary btn--lg hero-carousel__btn hero-carousel__btn--primary"
                                           <?php if ($btn1_target) echo 'target="' . esc_attr($btn1_target) . '" rel="noopener noreferrer"'; ?>>
                                            <?php echo esc_html($btn1_text); ?>
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($btn2_text && $btn2_link) : 
                                        $btn2_url = is_array($btn2_link) ? $btn2_link['url'] : $btn2_link;
                                        $btn2_target = is_array($btn2_link) && !empty($btn2_link['target']) ? $btn2_link['target'] : '';
                                    ?>
                                        <a href="<?php echo esc_url($btn2_url); ?>" 
                                           class="btn btn--outline btn--lg hero-carousel__btn hero-carousel__btn--secondary"
                                           <?php if ($btn2_target) echo 'target="' . esc_attr($btn2_target) . '" rel="noopener noreferrer"'; ?>>
                                            <?php echo esc_html($btn2_text); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($slide_count > 1) : ?>
            <!-- Navigation Dots -->
            <div class="hero-carousel__dots" data-carousel-dots>
                <?php for ($i = 0; $i < $slide_count; $i++) : ?>
                    <button 
                        class="hero-carousel__dot<?php echo $i === 0 ? ' hero-carousel__dot--active' : ''; ?>" 
                        data-slide-index="<?php echo esc_attr($i); ?>"
                        aria-label="<?php printf(esc_attr__('Go to slide %d', 'gloceps'), $i + 1); ?>"
                    ></button>
                <?php endfor; ?>
            </div>
            
            <!-- Navigation Arrows -->
            <button class="hero-carousel__arrow hero-carousel__arrow--prev" data-carousel-prev aria-label="<?php esc_attr_e('Previous slide', 'gloceps'); ?>">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button class="hero-carousel__arrow hero-carousel__arrow--next" data-carousel-next aria-label="<?php esc_attr_e('Next slide', 'gloceps'); ?>">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        <?php endif; ?>
    </div>
    
    <div class="hero-carousel__scroll">
        <span><?php esc_html_e('Scroll', 'gloceps'); ?></span>
        <div class="hero-carousel__scroll-line"></div>
    </div>
</section>
