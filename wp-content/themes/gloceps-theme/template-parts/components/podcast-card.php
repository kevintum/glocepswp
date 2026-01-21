<?php
/**
 * Template part for displaying a podcast card
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$podcast_source_type = get_field('podcast_source_type') ?: 'embed';
$podcast_url = get_field('podcast_url');
$podcast_external_url = get_field('podcast_external_url');
$podcast_audio_file = get_field('podcast_audio_file');
$duration = get_field('duration');
$episode_number = get_field('episode_number');
$podcast_thumbnail = get_field('podcast_thumbnail');

// Get thumbnail - priority: custom thumbnail > featured image > placeholder
$thumbnail_url = '';
$is_placeholder = false;
if ($podcast_thumbnail && is_array($podcast_thumbnail)) {
    $thumbnail_url = $podcast_thumbnail['url'] ?? ($podcast_thumbnail['sizes']['large'] ?? '');
} elseif (has_post_thumbnail()) {
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
} else {
    $thumbnail_url = gloceps_get_favicon_url(192);
    $is_placeholder = true;
}

// Get excerpt
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(get_the_content(), 20, '...');
}
?>

<article class="podcast-card" 
         data-podcast-type="<?php echo esc_attr($podcast_source_type); ?>"
         data-podcast-url="<?php echo esc_attr($podcast_url ?: ''); ?>"
         data-podcast-external-url="<?php echo esc_attr($podcast_external_url ?: ''); ?>"
         data-podcast-file="<?php echo esc_attr($podcast_audio_file && is_array($podcast_audio_file) ? $podcast_audio_file['url'] : ''); ?>"
         data-podcast-title="<?php echo esc_attr(get_the_title()); ?>">
    <div class="podcast-card__image">
        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" <?php echo $is_placeholder ? 'class="is-placeholder"' : ''; ?> />
        <?php if ($duration) : ?>
        <span class="podcast-card__duration"><?php echo esc_html($duration); ?></span>
        <?php endif; ?>
    </div>
    <div class="podcast-card__content">
        <?php if ($episode_number) : ?>
        <span class="podcast-card__episode"><?php printf(esc_html__('Episode %d', 'gloceps'), $episode_number); ?></span>
        <?php endif; ?>
        <h4 class="podcast-card__title"><?php the_title(); ?></h4>
        <?php if ($excerpt) : ?>
        <p class="podcast-card__excerpt"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>
        <?php if ($podcast_source_type === 'external' && $podcast_external_url) : ?>
            <a href="<?php echo esc_url($podcast_external_url); ?>" target="_blank" rel="noopener noreferrer" class="podcast-card__link">
                <?php esc_html_e('Listen Here', 'gloceps'); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>
        <?php else : ?>
            <a href="#" class="podcast-card__link">
                <?php esc_html_e('Listen Now', 'gloceps'); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
            </a>
        <?php endif; ?>
    </div>
</article>

