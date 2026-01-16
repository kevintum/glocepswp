<?php
/**
 * Block: Hero Video / Split Hero
 * 
 * Full-width hero section with split layout (content left, media right)
 * Matches the static homepage design
 * 
 * @package GLOCEPS
 */

// Get fields (support both naming conventions)
$title = get_sub_field('title');
$description = get_sub_field('description') ?: get_sub_field('subtitle');
$button1_text = get_sub_field('button1_text');
$button1_link = get_sub_field('button1_link');
$button2_text = get_sub_field('button2_text');
$button2_link = get_sub_field('button2_link');
$cta_text = get_sub_field('cta_text') ?: $button1_text;
$cta_link = get_sub_field('cta_link') ?: $button1_link;
$video_file = get_sub_field('video_file');
$video_url = get_sub_field('video_url'); // YouTube/Vimeo URL
$poster_image = get_sub_field('poster_image');
$hero_image = get_sub_field('hero_image');

// Fallback values
if (!$title) {
    $title = 'Research. <em>Knowledge.</em> Influence.';
}
if (!$description) {
    $description = 'The Global Centre for Policy and Strategy (GLOCEPS) provides strategic linkage between experience and research, bringing together outstanding professionals, thought leaders, and academia to advance key issues on peace and security.';
}
if (!$cta_text) {
    $cta_text = 'Explore Our Work';
}
?>

<!-- Hero - Immersive Split Layout -->
<section class="hero hero--split">
    <!-- Content Side (Left) -->
    <div class="hero__content-block">
        <div class="hero__content-inner">
            <h1 class="hero__title">
                <?php echo wp_kses_post($title); ?>
            </h1>

            <p class="hero__description">
                <?php echo esc_html($description); ?>
            </p>

            <div class="hero__actions">
                <?php if ($button1_text || $cta_text) : 
                    $btn1_text = $button1_text ?: $cta_text;
                    $btn1_link = $button1_link ?: $cta_link;
                ?>
                    <?php if ($btn1_link && is_array($btn1_link)) : ?>
                        <a href="<?php echo esc_url($btn1_link['url']); ?>" class="hero__link">
                            <span class="hero__link-text"><?php echo esc_html($btn1_text ?: $btn1_link['title']); ?></span>
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url($btn1_link ?: '#research'); ?>" class="hero__link">
                            <span class="hero__link-text"><?php echo esc_html($btn1_text); ?></span>
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($button2_text && $button2_link) : ?>
                    <?php if (is_array($button2_link)) : ?>
                        <a href="<?php echo esc_url($button2_link['url']); ?>" class="hero__link hero__link--secondary">
                            <?php echo esc_html($button2_text ?: $button2_link['title']); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url($button2_link); ?>" class="hero__link hero__link--secondary">
                            <?php echo esc_html($button2_text); ?>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Image/Video Side (Right) -->
    <div class="hero__image<?php echo ($video_file || $video_url) ? ' hero__image--has-video' : ''; ?>">
        <?php 
        // Determine fallback image (always show as placeholder/fallback)
        $fallback_image = $hero_image ?: $poster_image;
        $fallback_url = $fallback_image ? $fallback_image['url'] : (GLOCEPS_URI . '/assets/images/home-hero.png');
        $fallback_alt = $fallback_image ? ($fallback_image['alt'] ?: 'GLOCEPS') : __('GLOCEPS Panel Discussion', 'gloceps');
        
        // Check if video_url is YouTube or Vimeo
        $is_youtube = $video_url && (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false);
        $is_vimeo = $video_url && strpos($video_url, 'vimeo.com') !== false;
        ?>
        
        <?php if ($video_file) : ?>
            <video 
                autoplay 
                muted 
                loop 
                playsinline 
                class="hero__video" 
                poster="<?php echo esc_url($fallback_url); ?>"
            >
                <source src="<?php echo esc_url($video_file['url']); ?>" type="video/mp4">
                <!-- Fallback if video fails to load -->
            </video>
        <?php elseif ($video_url && ($is_youtube || $is_vimeo)) : ?>
            <?php
            // Extract video ID from YouTube or Vimeo URL
            $video_id = '';
            if ($is_youtube) {
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches);
                $video_id = isset($matches[1]) ? $matches[1] : '';
                if ($video_id) {
                    $embed_url = 'https://www.youtube.com/embed/' . $video_id . '?autoplay=1&mute=1&loop=1&playlist=' . $video_id . '&controls=0&showinfo=0&rel=0&modestbranding=1';
                }
            } elseif ($is_vimeo) {
                preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $video_url, $matches);
                $video_id = isset($matches[1]) ? $matches[1] : '';
                if ($video_id) {
                    $embed_url = 'https://player.vimeo.com/video/' . $video_id . '?autoplay=1&muted=1&loop=1&background=1&controls=0';
                }
            }
            ?>
            <?php if (!empty($embed_url)) : ?>
                <iframe 
                    class="hero__video hero__video--embed"
                    src="<?php echo esc_url($embed_url); ?>"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                ></iframe>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Fallback image - always present, shows when video unavailable or loading -->
        <img
            src="<?php echo esc_url($fallback_url); ?>"
            alt="<?php echo esc_attr($fallback_alt); ?>"
            class="hero__image-fallback"
        />
    </div>

    <div class="hero__scroll">
        <span><?php esc_html_e('Scroll', 'gloceps'); ?></span>
        <div class="hero__scroll-line"></div>
    </div>
</section>
