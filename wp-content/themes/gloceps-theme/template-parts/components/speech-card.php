<?php
/**
 * Template part for displaying a speech card
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$speech_date = get_field('speech_date');
$speech_file = get_field('speech_file');

// Format speech date
$formatted_date = '';
if ($speech_date) {
    $date_obj = DateTime::createFromFormat('Y-m-d', $speech_date);
    if ($date_obj) {
        $formatted_date = $date_obj->format('j M Y');
    }
}

// Get file URL and name
$file_url = '';
$file_name = '';
if ($speech_file && is_array($speech_file)) {
    $file_url = $speech_file['url'] ?? '';
    $file_name = $speech_file['filename'] ?? '';
}

// Get thumbnail - priority: featured image > placeholder
$thumbnail_url = '';
$is_placeholder = false;
if (has_post_thumbnail()) {
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
} else {
    $thumbnail_url = gloceps_get_favicon_url(192);
    $is_placeholder = true;
}

// Get excerpt/description
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(get_the_content(), 20, '...');
}
?>

<article class="speech-card">
    <div class="speech-card__image">
        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" <?php echo $is_placeholder ? 'class="is-placeholder"' : ''; ?> />
        <?php if ($formatted_date) : ?>
        <span class="speech-card__date"><?php echo esc_html($formatted_date); ?></span>
        <?php endif; ?>
    </div>
    <div class="speech-card__content">
        <h4 class="speech-card__title"><?php the_title(); ?></h4>
        <?php if ($excerpt) : ?>
        <p class="speech-card__excerpt"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>
        <?php if ($file_url) : ?>
        <a href="<?php echo esc_url($file_url); ?>" 
           class="speech-card__link" 
           download="<?php echo esc_attr($file_name); ?>"
           target="_blank"
           rel="noopener">
            <?php esc_html_e('Download Speech', 'gloceps'); ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
        </a>
        <?php endif; ?>
    </div>
</article>
