<?php
/**
 * Template for displaying single gallery posts
 * Matches the gallery-single.html static design
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while ( have_posts() ) :
    the_post();
    
    // Get gallery images from ACF
    $gallery_images = get_field('gallery_images');
    $event_date = get_field('event_date');
    $event_end_date = get_field('event_end_date');
    $venue = get_field('venue');
    $participant_count = get_field('participant_count');
    $photographer = get_field('photographer');
    $about_event = get_field('about_event');
    $image_count = $gallery_images ? count($gallery_images) : 0;
    
    // Format date range
    $date_display = '';
    if ($event_date) {
        $start_date = date('F j', strtotime($event_date));
        if ($event_end_date && $event_end_date != $event_date) {
            $end_date = date('j, Y', strtotime($event_end_date));
            $date_display = $start_date . '-' . $end_date;
        } else {
            $date_display = date('F j, Y', strtotime($event_date));
        }
    }
    
    // Format description
    $description_parts = array();
    if ($image_count > 0) {
        $description_parts[] = $image_count . ' photos';
    }
    if ($date_display) {
        $description_parts[] = 'from the event held on ' . $date_display;
    }
    if ($venue) {
        $description_parts[] = 'at ' . $venue;
    }
    $description = !empty($description_parts) ? implode(' ', $description_parts) . '.' : '';
?>

<main>
    <!-- Page Header -->
    <?php
    $header_attrs = gloceps_get_page_header_attrs(false);
    ?>
    <section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
        <div class="container">
            <div class="page-header__content">
                <?php gloceps_breadcrumbs(); ?>
                <h1 class="page-header__title"><?php the_title(); ?></h1>
                <?php if ($description) : ?>
                <p class="page-header__description">
                    <?php echo esc_html($description); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="section" style="padding-top: var(--space-8);">
        <div class="container">
            <?php if ($gallery_images) : ?>
            <div class="gallery-single-grid">
                <?php foreach ($gallery_images as $index => $image) : 
                    $image_url = $image['url'] ?? ($image['sizes']['large'] ?? '');
                    $image_thumb = $image['sizes']['medium_large'] ?? ($image['sizes']['medium'] ?? $image_url);
                    $image_alt = $image['alt'] ?? get_the_title();
                    $image_caption = $image['caption'] ?? '';
                ?>
                <a href="<?php echo esc_url($image_url); ?>" 
                   class="gallery-single-item" 
                   data-index="<?php echo esc_attr($index); ?>"
                   data-lightbox="gallery-<?php echo esc_attr(get_the_ID()); ?>"
                   data-title="<?php echo esc_attr($image_caption ?: $image_alt); ?>">
                    <img
                        src="<?php echo esc_url($image_thumb); ?>"
                        alt="<?php echo esc_attr($image_alt); ?>"
                        loading="lazy"
                    />
                </a>
                <?php endforeach; ?>
            </div>
            
            <?php if ($image_count > 12) : ?>
            <div class="gallery-load-more-wrapper">
                <button class="btn btn--secondary btn--lg gallery-load-more" data-page="1" data-per-page="12">
                    <?php esc_html_e('Load More Photos', 'gloceps'); ?>
                </button>
            </div>
            <?php endif; ?>
            
            <?php else : ?>
            <p class="text-center" style="color: var(--color-gray-500); padding: var(--space-8);">
                <?php esc_html_e('No photos in this gallery yet.', 'gloceps'); ?>
            </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Gallery Info Section -->
    <section class="section section--gray">
        <div class="container">
            <div class="gallery-info">
                <!-- About This Event -->
                <div class="gallery-info__content">
                    <h2><?php esc_html_e('About This Event', 'gloceps'); ?></h2>
                    <?php if ($about_event) : ?>
                        <?php echo wp_kses_post($about_event); ?>
                    <?php elseif (get_the_content()) : ?>
                        <?php the_content(); ?>
                    <?php endif; ?>
                    
                    <div class="gallery-info__details">
                        <?php if ($date_display) : ?>
                        <div>
                            <strong><?php esc_html_e('Date:', 'gloceps'); ?></strong> 
                            <?php echo esc_html($date_display); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($venue) : ?>
                        <div>
                            <strong><?php esc_html_e('Venue:', 'gloceps'); ?></strong> 
                            <?php echo esc_html($venue); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($participant_count) : ?>
                        <div>
                            <strong><?php esc_html_e('Participants:', 'gloceps'); ?></strong> 
                            <?php echo esc_html($participant_count); ?>+
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($photographer) : ?>
                        <div>
                            <strong><?php esc_html_e('Photographer:', 'gloceps'); ?></strong> 
                            <?php echo esc_html($photographer); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Share Gallery -->
                <div class="gallery-info__share">
                    <h3><?php esc_html_e('Share Gallery', 'gloceps'); ?></h3>
                    <div class="gallery-info__share-buttons">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" 
                           class="btn btn--secondary btn--sm" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            <?php esc_html_e('LinkedIn', 'gloceps'); ?>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                           class="btn btn--secondary btn--sm" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            <?php esc_html_e('X', 'gloceps'); ?>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                           class="btn btn--secondary btn--sm" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            <?php esc_html_e('Facebook', 'gloceps'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- More Galleries -->
    <?php
    $related_galleries = new WP_Query(array(
        'post_type' => 'gallery',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    if ($related_galleries->have_posts()) :
    ?>
    <section class="section">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-header__title"><?php esc_html_e('More Galleries', 'gloceps'); ?></h2>
            </div>
            
            <div class="gallery-grid">
                <?php while ($related_galleries->have_posts()) : $related_galleries->the_post(); 
                    $related_gallery_images = get_field('gallery_images');
                    $related_image_count = $related_gallery_images ? count($related_gallery_images) : 0;
                    $related_featured = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$related_featured && $related_gallery_images && !empty($related_gallery_images[0])) {
                        $related_featured = $related_gallery_images[0]['sizes']['large'] ?? '';
                    }
                ?>
                <a href="<?php the_permalink(); ?>" class="gallery-card">
                    <?php if ($related_featured) : ?>
                    <img src="<?php echo esc_url($related_featured); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
                    <?php else : ?>
                    <div class="gallery-card__placeholder">
                        <?php echo gloceps_get_favicon_url(64); ?>
                    </div>
                    <?php endif; ?>
                    <div class="gallery-card__overlay">
                        <span class="gallery-card__count"><?php echo esc_html($related_image_count); ?> <?php esc_html_e('PHOTOS', 'gloceps'); ?></span>
                        <h3 class="gallery-card__title"><?php the_title(); ?></h3>
                    </div>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<!-- Lightbox for Gallery -->
<div class="lightbox" id="gallery-lightbox">
    <button class="lightbox__close" aria-label="<?php esc_attr_e('Close', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <button class="lightbox__nav lightbox__nav--prev" aria-label="<?php esc_attr_e('Previous', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button class="lightbox__nav lightbox__nav--next" aria-label="<?php esc_attr_e('Next', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <div class="lightbox__content">
        <img src="" alt="" class="lightbox__image" />
        <div class="lightbox__caption"></div>
    </div>
    <div class="lightbox__counter"></div>
</div>

<script>
// Lightbox Functionality
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Lightbox script loaded');
        const lightbox = document.getElementById('gallery-lightbox');
        if (!lightbox) {
            console.error('Gallery lightbox element not found. Looking for #gallery-lightbox');
            // Try to find any lightbox element
            const anyLightbox = document.querySelector('.lightbox');
            if (anyLightbox) {
                console.log('Found lightbox element but with different ID:', anyLightbox.id);
            }
            return;
        }
        console.log('Lightbox element found:', lightbox);
        
        const lightboxImage = lightbox.querySelector('.lightbox__image');
        const lightboxCaption = lightbox.querySelector('.lightbox__caption');
        const lightboxCounter = lightbox.querySelector('.lightbox__counter');
        const closeBtn = lightbox.querySelector('.lightbox__close');
        const prevBtn = lightbox.querySelector('.lightbox__nav--prev');
        const nextBtn = lightbox.querySelector('.lightbox__nav--next');
        const galleryItems = document.querySelectorAll('.gallery-single-item');
        
        console.log('Gallery items found:', galleryItems.length);
        console.log('Lightbox image element:', lightboxImage);
        
        if (!galleryItems.length) {
            console.warn('No gallery items found');
            return;
        }
        
        if (!lightboxImage) {
            console.error('Lightbox image element not found');
            return;
        }
        
        let currentIndex = 0;
        let images = [];
        
        // Build images array from gallery items
        galleryItems.forEach((item, index) => {
            images.push({
                src: item.getAttribute('href'),
                title: item.getAttribute('data-title') || '',
                index: index
            });
        });
        
        function openLightbox(index) {
            if (index < 0 || index >= images.length) {
                console.error('Invalid index:', index, 'Total images:', images.length);
                return;
            }
            if (!images[index] || !images[index].src) {
                console.error('Invalid image data at index:', index);
                return;
            }
            
            console.log('Opening lightbox at index:', index, 'Image src:', images[index].src);
            currentIndex = index;
            
            // Set image source immediately (don't wait for load)
            if (!lightboxImage) {
                console.error('lightboxImage element not found');
                return;
            }
            
            // Update lightbox content immediately
            updateLightbox();
            
            // Show lightbox immediately (image will load in background)
            lightbox.classList.add('lightbox--open');
            document.body.style.overflow = 'hidden';
            console.log('Lightbox opened, class added');
            
            // Preload image in background to ensure it's ready
            const img = new Image();
            img.onload = function() {
                console.log('Image preloaded successfully');
                // Image is already set, just ensure it's displayed
                if (lightboxImage.src !== images[index].src) {
                    lightboxImage.src = images[index].src;
                }
            };
            img.onerror = function() {
                console.error('Failed to preload image:', images[index].src);
            };
            img.src = images[index].src;
        }
        
        function closeLightbox() {
            lightbox.classList.remove('lightbox--open');
            document.body.style.overflow = '';
        }
        
        function updateLightbox() {
            if (!images[currentIndex]) {
                console.error('No image at index:', currentIndex);
                return false;
            }
            if (!lightboxImage) {
                console.error('lightboxImage element not found');
                return false;
            }
            
            try {
                lightboxImage.src = images[currentIndex].src;
                lightboxImage.alt = images[currentIndex].title || '';
                
                if (lightboxCaption) {
                    lightboxCaption.textContent = images[currentIndex].title || '';
                }
                if (lightboxCounter) {
                    lightboxCounter.textContent = (currentIndex + 1) + ' / ' + images.length;
                }
                console.log('Lightbox updated successfully');
                return true;
            } catch (error) {
                console.error('Error updating lightbox:', error);
                return false;
            }
        }
        
        function nextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            updateLightbox();
        }
        
        function prevImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateLightbox();
        }
        
        // Open lightbox on gallery item click
        galleryItems.forEach((item, index) => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Opening lightbox for image', index);
                openLightbox(index);
            });
        });
        
        // Close lightbox
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeLightbox();
            });
        }
        
        // Close on background click (but not on content)
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
        
        // Prevent content clicks from closing
        const lightboxContent = lightbox.querySelector('.lightbox__content');
        if (lightboxContent) {
            lightboxContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Navigation
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                nextImage();
            });
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                prevImage();
            });
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (!lightbox.classList.contains('lightbox--open')) return;
            
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'ArrowLeft') {
                prevImage();
            }
        });
    });
})();
</script>

<?php
endwhile;

get_footer();
