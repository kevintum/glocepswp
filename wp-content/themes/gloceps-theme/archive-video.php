<?php
/**
 * Archive template for Videos
 * Matches media-videos.html structure
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for video archive
$video_title = get_field('video_intro_title', 'option') ?: 'Videos';
$video_description = get_field('video_intro_description', 'option') ?: 'Watch expert interviews, panel discussions, webinar recordings, and event highlights from GLOCEPS.';

// Get current category filter from URL
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get all video categories (if taxonomy exists)
$video_categories = get_terms(array(
    'taxonomy' => 'video_category',
    'hide_empty' => true,
    'orderby' => 'term_id',
));
?>

<main>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header__content">
                <?php gloceps_breadcrumbs(); ?>
                <h1 class="page-header__title"><?php echo esc_html($video_title); ?></h1>
                <?php if ($video_description) : ?>
                    <p class="page-header__description">
                        <?php echo esc_html($video_description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Video Category Filters -->
    <?php if ($video_categories && !is_wp_error($video_categories)) : ?>
        <section class="section section--filters">
            <div class="container">
                <div class="events-tabs">
                    <div class="events-tabs__wrapper">
                        <a href="<?php echo esc_url(remove_query_arg('category')); ?>" 
                           class="events-tab <?php echo $current_category === 'all' ? 'events-tab--active' : ''; ?>">
                            <?php esc_html_e('All Videos', 'gloceps'); ?>
                        </a>
                        <?php foreach ($video_categories as $category) : ?>
                            <a href="<?php echo esc_url(add_query_arg('category', $category->slug)); ?>" 
                               class="events-tab <?php echo $current_category === $category->slug ? 'events-tab--active' : ''; ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Video Grid -->
    <section class="section">
        <div class="container">
            <?php
            // Build query args based on category filter
            $query_args = array(
                'post_type' => 'video',
                'posts_per_page' => 9,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            // Add category filter if not "all"
            if ($current_category !== 'all' && $video_categories && !is_wp_error($video_categories)) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'video_category',
                        'field' => 'slug',
                        'terms' => $current_category,
                    ),
                );
            }

            $videos_query = new WP_Query($query_args);

            if ($videos_query->have_posts()) :
            ?>
                <div class="video-grid">
                    <?php while ($videos_query->have_posts()) : $videos_query->the_post(); ?>
                        <?php get_template_part('template-parts/components/video-card'); ?>
                    <?php endwhile; ?>
                </div>

                <?php
                // Custom pagination matching static HTML
                $total_pages = $videos_query->max_num_pages;
                if ($total_pages > 1) :
                    $current_page = max(1, $paged);
                    $base_url = remove_query_arg('paged');
                    if ($current_category !== 'all') {
                        $base_url = add_query_arg('category', $current_category, $base_url);
                    }
                ?>
                <div class="pagination">
                    <a href="<?php echo esc_url($current_page > 1 ? add_query_arg('paged', $current_page - 1, $base_url) : '#'); ?>" 
                       class="pagination__btn pagination__btn--prev" 
                       <?php if ($current_page <= 1) : ?>disabled style="pointer-events: none; opacity: 0.5;"<?php endif; ?>>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                        <?php esc_html_e('Previous', 'gloceps'); ?>
                    </a>
                    <div class="pagination__pages">
                        <?php
                        $prev_ellipsis = false;
                        for ($i = 1; $i <= $total_pages; $i++) :
                            if ($i == 1 || $i == $total_pages || ($i >= $current_page - 1 && $i <= $current_page + 1)) :
                                $prev_ellipsis = false;
                        ?>
                            <a href="<?php echo esc_url(add_query_arg('paged', $i, $base_url)); ?>" 
                               class="pagination__page <?php echo $i == $current_page ? 'pagination__page--active' : ''; ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                        <?php
                            elseif (!$prev_ellipsis && ($i < $current_page - 1 || $i > $current_page + 1)) :
                                $prev_ellipsis = true;
                        ?>
                            <span class="pagination__ellipsis">...</span>
                        <?php
                            endif;
                        endfor;
                        ?>
                    </div>
                    <a href="<?php echo esc_url($current_page < $total_pages ? add_query_arg('paged', $current_page + 1, $base_url) : '#'); ?>" 
                       class="pagination__btn pagination__btn--next"
                       <?php if ($current_page >= $total_pages) : ?>disabled style="pointer-events: none; opacity: 0.5;"<?php endif; ?>>
                        <?php esc_html_e('Next', 'gloceps'); ?>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="no-results">
                    <p><?php esc_html_e('No videos found. Try adjusting your filters.', 'gloceps'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Video Modal -->
<div class="video-modal" id="video-modal">
    <button class="video-modal__close" aria-label="<?php esc_attr_e('Close', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="video-modal__content">
        <div class="video-modal__player" id="video-modal-player"></div>
        <h3 class="video-modal__title" id="video-modal-title"></h3>
    </div>
</div>

<script>
// Video Modal Functionality
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('video-modal');
        const modalPlayer = document.getElementById('video-modal-player');
        const modalTitle = document.getElementById('video-modal-title');
        const closeBtn = modal.querySelector('.video-modal__close');
        const videoCards = document.querySelectorAll('.video-card');
        
        if (!modal || !videoCards.length) return;
        
        function openModal(card) {
            const videoType = card.dataset.videoType;
            const videoUrl = card.dataset.videoUrl || '';
            const videoFile = card.dataset.videoFile || '';
            const videoTitle = card.dataset.videoTitle || '';
            
            modalTitle.textContent = videoTitle;
            modalPlayer.innerHTML = '';
            
            if (videoType === 'embed' && videoUrl) {
                // Extract video ID from YouTube or Vimeo URL
                let embedUrl = '';
                if (videoUrl.includes('youtube.com/watch') || videoUrl.includes('youtu.be/')) {
                    const youtubeId = videoUrl.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
                    if (youtubeId) {
                        embedUrl = 'https://www.youtube.com/embed/' + youtubeId[1];
                    }
                } else if (videoUrl.includes('vimeo.com/')) {
                    const vimeoId = videoUrl.match(/vimeo\.com\/(\d+)/);
                    if (vimeoId) {
                        embedUrl = 'https://player.vimeo.com/video/' + vimeoId[1];
                    }
                }
                
                if (embedUrl) {
                    modalPlayer.innerHTML = '<iframe src="' + embedUrl + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
            } else if (videoType === 'upload' && videoFile) {
                modalPlayer.innerHTML = '<video controls><source src="' + videoFile + '" type="video/mp4">Your browser does not support the video tag.</video>';
            }
            
            modal.classList.add('video-modal--open');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            modal.classList.remove('video-modal--open');
            document.body.style.overflow = '';
            // Clear video to stop playback
            modalPlayer.innerHTML = '';
        }
        
        videoCards.forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(card);
            });
        });
        
        closeBtn.addEventListener('click', closeModal);
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('video-modal--open')) {
                closeModal();
            }
        });
    });
})();
</script>

<?php
get_footer();

