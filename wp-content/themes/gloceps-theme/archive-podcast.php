<?php
/**
 * Archive template for Podcasts
 * Matches media-podcasts.html structure
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Get ACF settings for podcast archive
$podcast_title = get_field('podcast_intro_title', 'option') ?: 'Podcasts';
$podcast_description = get_field('podcast_intro_description', 'option') ?: 'Listen to in-depth policy discussions, expert analysis, and strategic insights on the go.';
$spotify_url = get_field('podcast_spotify_url', 'option');
$apple_url = get_field('podcast_apple_url', 'option');
$google_url = get_field('podcast_google_url', 'option');
$rss_url = get_field('podcast_rss_url', 'option');

// Get current category filter from URL
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get all podcast categories (if taxonomy exists)
$podcast_categories = get_terms(array(
    'taxonomy' => 'podcast_category',
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
                <h1 class="page-header__title"><?php echo esc_html($podcast_title); ?></h1>
                <?php if ($podcast_description) : ?>
                    <p class="page-header__description">
                        <?php echo esc_html($podcast_description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Subscribe Banner -->
    <section class="section section--filters">
        <div class="container">
            <div class="podcast-subscribe-banner">
                <div class="podcast-subscribe-banner__bg">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M0,60 C150,100 350,0 600,60 C850,120 1050,20 1200,60 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.03)"/>
                        <path d="M0,80 C200,40 400,120 600,80 C800,40 1000,120 1200,80 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.02)"/>
                    </svg>
                </div>
                <div class="podcast-subscribe-banner__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                        <line x1="12" y1="19" x2="12" y2="23"/>
                        <line x1="8" y1="23" x2="16" y2="23"/>
                    </svg>
                </div>
                <div class="podcast-subscribe-banner__content">
                    <span class="podcast-subscribe-banner__label">Never Miss an Episode</span>
                    <h3>Subscribe to Our Podcast</h3>
                    <p>Get in-depth policy discussions, expert analysis, and strategic insights delivered to your favorite podcast app.</p>
                </div>
                <div class="podcast-subscribe-banner__platforms">
                    <?php if ($spotify_url) : ?>
                    <a href="<?php echo esc_url($spotify_url); ?>" target="_blank" rel="noopener" class="podcast-platform-link">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                        </svg>
                        <span>Spotify</span>
                    </a>
                    <?php endif; ?>
                    <?php if ($apple_url) : ?>
                    <a href="<?php echo esc_url($apple_url); ?>" target="_blank" rel="noopener" class="podcast-platform-link">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.34 0A5.328 5.328 0 0 0 0 5.34v13.32A5.328 5.328 0 0 0 5.34 24h13.32A5.328 5.328 0 0 0 24 18.66V5.34A5.328 5.328 0 0 0 18.66 0zm6.525 3.24c4.34 0 6.25 2.88 6.25 5.45 0 .36-.04.7-.12 1.03h-2.15a3.24 3.24 0 0 0 .07-.68c0-1.82-1.17-3.14-3.37-3.14-1.6 0-2.89.82-2.89 2.14 0 1.05.69 1.72 2.2 2.1l1.7.42c2.8.68 4.18 1.87 4.18 4.15 0 2.82-2.19 4.75-5.5 4.75-4.05 0-6.3-2.34-6.3-5.34 0-.46.05-.9.14-1.33h2.17c-.06.28-.09.57-.09.87 0 1.86 1.3 3.24 3.77 3.24 1.83 0 3.18-.86 3.18-2.28 0-1.09-.76-1.8-2.42-2.22l-1.76-.44c-2.5-.62-3.9-1.85-3.9-4.08 0-2.68 2.15-4.64 5.47-4.64z"/>
                        </svg>
                        <span>Apple Podcasts</span>
                    </a>
                    <?php endif; ?>
                    <?php if ($google_url) : ?>
                    <a href="<?php echo esc_url($google_url); ?>" target="_blank" rel="noopener" class="podcast-platform-link">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.376 0 0 5.376 0 12s5.376 12 12 12 12-5.376 12-12S18.624 0 12 0zm-.024 3.6c4.08 0 7.392 2.904 8.136 6.744h-1.656c-.696-2.88-3.312-5.04-6.48-5.04-3.168 0-5.784 2.16-6.48 5.04H3.84c.744-3.84 4.056-6.744 8.136-6.744zM12 8.4c2.088 0 3.792 1.536 4.104 3.528h-1.608c-.264-1.176-1.32-2.064-2.496-2.064s-2.232.888-2.496 2.064H7.896c.312-1.992 2.016-3.528 4.104-3.528zm0 3.6a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4zm-8.4 1.2c0 .552.072 1.08.192 1.584h1.68A6.737 6.737 0 0 1 5.28 13.2h-.48a.72.72 0 1 0 0 1.44h.648c.144.672.36 1.32.648 1.92H4.032A8.34 8.34 0 0 1 3.6 13.2zm4.8 0a.72.72 0 1 0 0 1.44h7.2a.72.72 0 1 0 0-1.44zm2.4 3.6a.72.72 0 1 0 0 1.44h2.4a.72.72 0 1 0 0-1.44zm-4.464 1.44h2.088c.792 1.176 1.848 2.16 3.072 2.832A8.377 8.377 0 0 1 4.536 16.8zm11.928 0h2.088a8.37 8.37 0 0 1-4.856 3.432c1.224-.672 2.28-1.656 3.072-2.832z"/>
                        </svg>
                        <span>Google Podcasts</span>
                    </a>
                    <?php endif; ?>
                    <?php if ($rss_url) : ?>
                    <a href="<?php echo esc_url($rss_url); ?>" target="_blank" rel="noopener" class="podcast-platform-link">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.5 4.5C6.5 2.01 8.51 0 11 0h2c2.49 0 4.5 2.01 4.5 4.5v7c0 2.49-2.01 4.5-4.5 4.5h-2c-2.49 0-4.5-2.01-4.5-4.5v-7zm1.5 0v7c0 1.66 1.34 3 3 3h2c1.66 0 3-1.34 3-3v-7c0-1.66-1.34-3-3-3h-2c-1.66 0-3 1.34-3 3zM1.5 9.5A.75.75 0 0 1 2.25 10v1.5c0 5.38 4.37 9.75 9.75 9.75s9.75-4.37 9.75-9.75V10a.75.75 0 0 1 1.5 0v1.5c0 5.73-4.36 10.44-9.94 10.98V24h3.19a.75.75 0 0 1 0 1.5h-8a.75.75 0 0 1 0-1.5h3.31v-1.52C6.36 21.94 2 17.23 2 11.5V10a.75.75 0 0 1 .75-.75z"/>
                        </svg>
                        <span>RSS Feed</span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="podcast-subscribe-banner__waveform">
                    <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Podcast Category Filters -->
    <?php if ($podcast_categories && !is_wp_error($podcast_categories)) : ?>
        <section class="section section--filters">
            <div class="container">
                <div class="events-tabs">
                    <div class="events-tabs__wrapper">
                        <a href="<?php echo esc_url(remove_query_arg('category')); ?>" 
                           class="events-tab <?php echo $current_category === 'all' ? 'events-tab--active' : ''; ?>">
                            <?php esc_html_e('All Podcasts', 'gloceps'); ?>
                        </a>
                        <?php foreach ($podcast_categories as $category) : ?>
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

    <!-- Podcast Grid -->
    <section class="section">
        <div class="container">
            <?php
            // Build query args based on category filter
            $query_args = array(
                'post_type' => 'podcast',
                'posts_per_page' => 9,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            // Add category filter if not "all"
            if ($current_category !== 'all' && $podcast_categories && !is_wp_error($podcast_categories)) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'podcast_category',
                        'field' => 'slug',
                        'terms' => $current_category,
                    ),
                );
            }

            $podcasts_query = new WP_Query($query_args);

            if ($podcasts_query->have_posts()) :
            ?>
                <div class="podcast-grid">
                    <?php while ($podcasts_query->have_posts()) : $podcasts_query->the_post(); ?>
                        <?php get_template_part('template-parts/components/podcast-card'); ?>
                    <?php endwhile; ?>
                </div>

                <?php
                // Custom pagination matching static HTML
                $total_pages = $podcasts_query->max_num_pages;
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
                    <p><?php esc_html_e('No podcasts found. Try adjusting your filters.', 'gloceps'); ?></p>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>
</main>

<!-- Podcast Modal -->
<div class="podcast-modal" id="podcast-modal">
    <button class="podcast-modal__close" aria-label="<?php esc_attr_e('Close', 'gloceps'); ?>">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="podcast-modal__content">
        <div class="podcast-modal__player" id="podcast-modal-player"></div>
        <h3 class="podcast-modal__title" id="podcast-modal-title"></h3>
    </div>
</div>

<script>
// Podcast Modal Functionality
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('podcast-modal');
        const modalPlayer = document.getElementById('podcast-modal-player');
        const modalTitle = document.getElementById('podcast-modal-title');
        const closeBtn = modal.querySelector('.podcast-modal__close');
        const podcastCards = document.querySelectorAll('.podcast-card');
        
        if (!modal || !podcastCards.length) return;
        
        function openModal(card) {
            const podcastType = card.dataset.podcastType;
            const podcastUrl = card.dataset.podcastUrl || '';
            const podcastFile = card.dataset.podcastFile || '';
            const podcastTitle = card.dataset.podcastTitle || '';
            
            modalTitle.textContent = podcastTitle;
            modalPlayer.innerHTML = ''; // Clear previous content
            
            if (podcastType === 'embed' && podcastUrl) {
                // For Spotify, Apple Podcasts, etc., we'll embed the URL
                // Spotify embed
                if (podcastUrl.includes('spotify.com')) {
                    const spotifyId = podcastUrl.match(/spotify\.com\/episode\/([^?\/]+)/);
                    if (spotifyId && spotifyId[1]) {
                        modalPlayer.innerHTML = `<iframe src="https://open.spotify.com/embed/episode/${spotifyId[1]}" width="100%" height="232" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>`;
                    } else {
                        modalPlayer.innerHTML = `<a href="${podcastUrl}" target="_blank" rel="noopener" style="display: block; padding: var(--space-8); text-align: center; color: var(--color-white);">Open in Spotify</a>`;
                    }
                } 
                // Apple Podcasts embed
                else if (podcastUrl.includes('podcasts.apple.com')) {
                    modalPlayer.innerHTML = `<a href="${podcastUrl}" target="_blank" rel="noopener" style="display: block; padding: var(--space-8); text-align: center; color: var(--color-white);">Open in Apple Podcasts</a>`;
                }
                // Google Podcasts
                else if (podcastUrl.includes('podcasts.google.com')) {
                    modalPlayer.innerHTML = `<a href="${podcastUrl}" target="_blank" rel="noopener" style="display: block; padding: var(--space-8); text-align: center; color: var(--color-white);">Open in Google Podcasts</a>`;
                }
                // Generic embed URL
                else {
                    modalPlayer.innerHTML = `<iframe src="${podcastUrl}" frameborder="0" allow="autoplay" style="width: 100%; height: 232px;"></iframe>`;
                }
            } else if (podcastType === 'upload' && podcastFile) {
                modalPlayer.innerHTML = `<audio controls autoplay style="width: 100%;"><source src="${podcastFile}" type="audio/mpeg">Your browser does not support the audio element.</audio>`;
            } else {
                modalPlayer.innerHTML = `<p style="color: var(--color-gray-400); text-align: center; padding: var(--space-8);">No podcast source available.</p>`;
            }
            
            modal.classList.add('podcast-modal--open');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            modal.classList.remove('podcast-modal--open');
            document.body.style.overflow = '';
            modalPlayer.innerHTML = ''; // Stop audio playback
        }
        
        podcastCards.forEach(card => {
            card.addEventListener('click', function(e) {
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
            if (e.key === 'Escape' && modal.classList.contains('podcast-modal--open')) {
                closeModal();
            }
        });
    });
})();
</script>

<?php
get_footer();
?>

