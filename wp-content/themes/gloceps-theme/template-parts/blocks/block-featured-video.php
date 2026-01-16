<?php
/**
 * Featured Video Block
 * 
 * @package GLOCEPS
 */

$section_title = get_sub_field('section_title') ?: 'Featured Video';
$section_description = get_sub_field('section_description') ?: 'Watch our latest expert discussion';
$selected_video = get_sub_field('video');

// If no video selected, get the latest video
if (!$selected_video) {
    $latest_video = new WP_Query(array(
        'post_type' => 'video',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    if ($latest_video->have_posts()) {
        $latest_video->the_post();
        $selected_video = get_post();
        wp_reset_postdata();
    }
}

if ($selected_video) {
    $video_id = is_object($selected_video) ? $selected_video->ID : $selected_video;
    $video_title = get_the_title($video_id);
    $video_description = get_the_excerpt($video_id) ?: wp_trim_words(get_the_content($video_id), 25, '...');
    $video_category = '';
    $categories = get_the_terms($video_id, 'video_category');
    if ($categories && !is_wp_error($categories)) {
        $video_category = $categories[0]->name;
    }
    $video_duration = get_field('duration', $video_id);
    $video_date = get_the_date('F Y', $video_id);
    
    // Get thumbnail
    $thumbnail_url = '';
    $video_thumbnail = get_field('video_thumbnail', $video_id);
    if ($video_thumbnail && is_array($video_thumbnail)) {
        $thumbnail_url = $video_thumbnail['url'] ?? ($video_thumbnail['sizes']['large'] ?? '');
    } elseif (has_post_thumbnail($video_id)) {
        $thumbnail_url = get_the_post_thumbnail_url($video_id, 'large');
    }
    
    if (!$thumbnail_url) {
        $thumbnail_url = gloceps_get_favicon_url(192);
    }
}
?>

<?php if ($selected_video) : ?>
<section class="section section--gray" style="padding-bottom: var(--space-12);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-header__title"><?php echo esc_html($section_title); ?></h2>
            <p class="section-header__description"><?php echo esc_html($section_description); ?></p>
        </div>
        <div class="featured-video">
            <div class="featured-video__wrapper">
                <img 
                    src="<?php echo esc_url($thumbnail_url); ?>" 
                    alt="<?php echo esc_attr($video_title); ?>"
                    class="featured-video__thumbnail"
                />
                <a href="#" class="featured-video__play" data-video-type="<?php echo esc_attr(get_field('video_source_type', $video_id) ?: 'embed'); ?>" data-video-url="<?php echo esc_attr(get_field('video_url', $video_id) ?: ''); ?>" data-video-file="<?php echo esc_attr(get_field('video_file', $video_id) && is_array(get_field('video_file', $video_id)) ? get_field('video_file', $video_id)['url'] : ''); ?>" data-video-title="<?php echo esc_attr($video_title); ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                </a>
            </div>
            <div class="featured-video__content">
                <?php if ($video_category) : ?>
                <span class="featured-video__category"><?php echo esc_html($video_category); ?></span>
                <?php endif; ?>
                <h3 class="featured-video__title"><?php echo esc_html($video_title); ?></h3>
                <?php if ($video_description) : ?>
                <p class="featured-video__description">
                    <?php echo esc_html($video_description); ?>
                </p>
                <?php endif; ?>
                <div class="featured-video__meta">
                    <?php if ($video_duration) : ?>
                    <span><?php echo esc_html($video_duration); ?></span>
                    <?php endif; ?>
                    <?php if ($video_duration && $video_date) : ?>
                    <span>•</span>
                    <?php endif; ?>
                    <?php if ($video_date) : ?>
                    <span><?php echo esc_html($video_date); ?></span>
                    <?php endif; ?>
                </div>
                <a href="#" class="btn btn--primary featured-video__watch" data-video-type="<?php echo esc_attr(get_field('video_source_type', $video_id) ?: 'embed'); ?>" data-video-url="<?php echo esc_attr(get_field('video_url', $video_id) ?: ''); ?>" data-video-file="<?php echo esc_attr(get_field('video_file', $video_id) && is_array(get_field('video_file', $video_id)) ? get_field('video_file', $video_id)['url'] : ''); ?>" data-video-title="<?php echo esc_attr($video_title); ?>"><?php esc_html_e('Watch Now', 'gloceps'); ?></a>
            </div>
        </div>
    </div>
</section>

<!-- Video Modal (reuse from archive-video.php) -->
<div class="video-modal" id="featured-video-modal">
    <button class="video-modal__close" aria-label="<?php esc_attr_e('Close', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="video-modal__content">
        <div class="video-modal__player" id="featured-video-modal-player"></div>
        <h3 class="video-modal__title" id="featured-video-modal-title"></h3>
    </div>
</div>

<script>
// Featured Video Modal Functionality
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('featured-video-modal');
        const modalPlayer = document.getElementById('featured-video-modal-player');
        const modalTitle = document.getElementById('featured-video-modal-title');
        const closeBtn = modal.querySelector('.video-modal__close');
        const playButtons = document.querySelectorAll('.featured-video__play, .featured-video__watch');
        
        if (!modal || !playButtons.length) return;
        
        function openModal(button) {
            const videoType = button.dataset.videoType;
            const videoUrl = button.dataset.videoUrl || '';
            const videoFile = button.dataset.videoFile || '';
            const videoTitle = button.dataset.videoTitle || '';
            
            modalTitle.textContent = videoTitle;
            modalPlayer.innerHTML = '';
            
            if (videoType === 'embed' && videoUrl) {
                let embedUrl = '';
                if (videoUrl.includes('youtube.com/watch') || videoUrl.includes('youtu.be/')) {
                    const youtubeId = videoUrl.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
                    if (youtubeId && youtubeId[1]) {
                        embedUrl = 'https://www.youtube.com/embed/' + youtubeId[1] + '?autoplay=1';
                    }
                } else if (videoUrl.includes('vimeo.com/')) {
                    const vimeoId = videoUrl.match(/vimeo\.com\/(\d+)/);
                    if (vimeoId && vimeoId[1]) {
                        embedUrl = 'https://player.vimeo.com/video/' + vimeoId[1] + '?autoplay=1';
                    }
                }
                
                if (embedUrl) {
                    modalPlayer.innerHTML = '<iframe src="' + embedUrl + '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                }
            } else if (videoType === 'upload' && videoFile) {
                modalPlayer.innerHTML = '<video controls autoplay src="' + videoFile + '" style="width: 100%; height: 100%;"></video>';
            }
            
            modal.classList.add('video-modal--open');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            modal.classList.remove('video-modal--open');
            document.body.style.overflow = '';
            modalPlayer.innerHTML = '';
        }
        
        playButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(this);
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
<?php endif; ?>

