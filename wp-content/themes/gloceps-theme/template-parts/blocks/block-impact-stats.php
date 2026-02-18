<?php
/**
 * Block: Impact Statistics
 * 
 * Displays impact statistics with animated counters
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Our Impact';
$title = get_sub_field('title') ?: 'Impact in Numbers';
$show_eyebrow = get_sub_field('show_eyebrow') !== false; // Default to true if not set
$show_title = get_sub_field('show_title') !== false; // Default to true if not set
$title_color = get_sub_field('title_color') ?: 'default'; // 'default' or 'white'
$stats = get_sub_field('stats');
$anchor_id = get_sub_field('anchor_id');
$background_image = get_sub_field('background_image');

// Get background image URL
$bg_image_url = '';
$has_bg_image = false;
if ($background_image && is_array($background_image)) {
    $bg_image_url = esc_url($background_image['url'] ?? '');
    $has_bg_image = !empty($bg_image_url);
} elseif ($background_image) {
    $bg_image_url = esc_url(wp_get_attachment_image_url($background_image, 'full'));
    $has_bg_image = !empty($bg_image_url);
}

// Default stats if none provided
if (empty($stats)) {
    $stats = array(
        array(
            'value' => '200',
            'suffix' => '+',
            'label' => 'Publications Released',
            'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
        ),
        array(
            'value' => '50',
            'suffix' => '+',
            'label' => 'Events Hosted',
            'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
        ),
        array(
            'value' => '15',
            'suffix' => '',
            'label' => 'Partner Organisations',
            'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>',
        ),
        array(
            'value' => '8',
            'suffix' => '',
            'label' => 'Countries Engaged',
            'icon' => '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3"/></svg>',
        ),
    );
}
?>

<section class="section stats-section<?php echo $title_color === 'white' ? ' stats-section--white-title' : ''; ?><?php echo $has_bg_image ? ' stats-section--has-bg' : ''; ?>" <?php echo $anchor_id ? 'id="' . esc_attr($anchor_id) . '"' : ''; ?><?php if ($has_bg_image) : ?> style="background-image: url('<?php echo $bg_image_url; ?>');"<?php endif; ?>>
    <div class="container">
        <?php if ($show_eyebrow || $show_title) : ?>
            <div class="section-header section-header--center reveal">
                <?php if ($show_eyebrow && $eyebrow) : ?>
                    <div class="section-header__eyebrow">
                        <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($show_title && $title) : ?>
                    <h2 class="section-header__title<?php echo $title_color === 'white' ? ' section-header__title--white' : ''; ?>"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid reveal stagger-children">
            <?php foreach ($stats as $stat) : ?>
                <div class="stat-item">
                    <div class="stat-item__value">
                        <?php echo esc_html($stat['value']); ?>
                        <?php if (!empty($stat['suffix'])) : ?>
                            <span><?php echo esc_html($stat['suffix']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-item__label"><?php echo esc_html($stat['label']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

