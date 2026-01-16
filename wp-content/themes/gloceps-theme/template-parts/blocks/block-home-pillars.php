<?php
/**
 * Block: Grid Cards (Reusable Bento Layout)
 * 
 * Displays cards in a bento grid layout with images
 * Can use Research Pillars as default or custom cards
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Research Focus Areas';
$title = get_sub_field('title') ?: 'Our Five Pillars';
$description = get_sub_field('description') ?: 'GLOCEPS work cuts across five interconnected pillars addressing the most pressing challenges facing Eastern Africa and the broader region.';
$use_pillars = get_sub_field('use_pillars') !== false; // Default to true
$custom_cards = get_sub_field('cards');
$use_page_featured_image = get_sub_field('use_page_featured_image') !== false; // Default to true

// Get page featured image as fallback
$page_featured_image = null;
if ($use_page_featured_image) {
    global $post;
    if ($post && has_post_thumbnail($post->ID)) {
        $page_featured_image = get_post_thumbnail_id($post->ID);
    }
}

$cards = array();

// Use custom cards if provided and not using pillars
if (!$use_pillars && !empty($custom_cards)) {
    foreach ($custom_cards as $index => $card) {
        $card_image = $card['image'];
        
        // Check if card_image is actually empty
        $has_card_image = false;
        if ($card_image) {
            if (is_array($card_image) && !empty($card_image['ID'])) {
                $has_card_image = true;
            } elseif (is_numeric($card_image) && $card_image > 0) {
                $has_card_image = true;
            }
        }
        
        // Fallback to page featured image if card image is not set and fallback is enabled
        if (!$has_card_image && $use_page_featured_image && $page_featured_image) {
            $card_image = $page_featured_image;
        }
        
        $cards[] = array(
            'title' => $card['title'],
            'description' => $card['description'],
            'image' => $card_image,
            'link' => $card['link'],
            'is_large' => $card['is_large'] || ($index === 0), // First card is large by default
        );
    }
} else {
    // Use Research Pillars as default
    $pillars = get_terms(array(
        'taxonomy' => 'research_pillar',
        'hide_empty' => false,
        'orderby' => 'term_order',
        'order' => 'ASC',
    ));

    // If no order set, use default order
    if (empty($pillars) || is_wp_error($pillars)) {
        $pillar_slugs = array('foreign-policy', 'security-defence', 'governance-ethics', 'development', 'transnational-organised-crimes');
        $pillars = array();
        foreach ($pillar_slugs as $slug) {
            $term = get_term_by('slug', $slug, 'research_pillar');
            if ($term && !is_wp_error($term)) {
                $pillars[] = $term;
            }
        }
    }

    // Limit to 5 pillars and convert to card format
    $pillars = array_slice($pillars, 0, 5);
    foreach ($pillars as $index => $pillar) {
        $pillar_image = get_field('pillar_image', $pillar);
        
        // Check if pillar_image is actually empty (could be false, null, empty array, or 0)
        $has_pillar_image = false;
        if ($pillar_image) {
            if (is_array($pillar_image) && !empty($pillar_image['ID'])) {
                $has_pillar_image = true;
            } elseif (is_numeric($pillar_image) && $pillar_image > 0) {
                $has_pillar_image = true;
            }
        }
        
        // Fallback to page featured image if pillar image is not set and fallback is enabled
        if (!$has_pillar_image && $use_page_featured_image && $page_featured_image) {
            $pillar_image = $page_featured_image;
        }
        
        $pillar_description = get_field('pillar_description', $pillar) ?: term_description($pillar->term_id, 'research_pillar');
        $pillar_link = get_term_link($pillar);
        
        // Fallback description
        if (empty($pillar_description)) {
            $default_descriptions = array(
                'foreign-policy' => 'Analysing diplomatic relations, regional integration through the EAC and IGAD, and international partnerships shaping Eastern Africa\'s global engagement including BRICS membership and bilateral alliances.',
                'security-defence' => 'Examining national security frameworks, counter-terrorism, and regional defence cooperation.',
                'governance-ethics' => 'Promoting accountability, democratic processes, and ethical leadership in public governance.',
                'development' => 'Driving sustainable economic growth, innovation, and inclusive development strategies.',
                'transnational-organised-crimes' => 'Combating trafficking, money laundering, and cross-border criminal networks.',
            );
            $pillar_description = isset($default_descriptions[$pillar->slug]) ? $default_descriptions[$pillar->slug] : '';
        }
        
        $cards[] = array(
            'title' => $pillar->name,
            'description' => $pillar_description,
            'image' => $pillar_image,
            'link' => $pillar_link,
            'is_large' => ($index === 0), // First pillar is large
        );
    }
}
?>

<section class="section" id="research">
    <div class="container">
        <div class="section-header reveal">
            <?php if ($eyebrow) : ?>
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="section-header__title"><?php echo wp_kses_post($title); ?></h2>
            <?php endif; ?>

            <?php if ($description) : ?>
                <p class="section-header__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($cards)) : ?>
            <div class="pillars-grid reveal">
                <?php 
                foreach ($cards as $index => $card) : 
                    $is_large = $card['is_large'] || ($index === 0); // First card is large by default
                    
                    // Get image URL - handle both ACF array format and attachment ID
                    $image_url = '';
                    if ($card['image']) {
                        if (is_array($card['image']) && !empty($card['image']['url'])) {
                            $image_url = $card['image']['url'];
                        } elseif (is_array($card['image']) && !empty($card['image']['ID'])) {
                            $image_url = wp_get_attachment_image_url($card['image']['ID'], 'large');
                        } elseif (is_numeric($card['image']) && $card['image'] > 0) {
                            $image_url = wp_get_attachment_image_url($card['image'], 'large');
                        } elseif (is_string($card['image']) && !empty($card['image'])) {
                            // Handle URL string
                            $image_url = $card['image'];
                        }
                    }
                    
                    // Final fallback to page featured image if no image is set and fallback is enabled
                    if (empty($image_url) && $use_page_featured_image && $page_featured_image) {
                        $image_url = wp_get_attachment_image_url($page_featured_image, 'large');
                    }
                    
                    // Get link URL
                    $link_url = '#';
                    $link_text = $is_large ? esc_html__('Explore Research', 'gloceps') : esc_html__('Explore', 'gloceps');
                    if (is_array($card['link'])) {
                        $link_url = $card['link']['url'];
                        $link_text = $card['link']['title'] ?: $link_text;
                    } elseif (is_string($card['link'])) {
                        $link_url = $card['link'];
                    }
                ?>
                    <article class="pillar-card<?php echo $is_large ? ' pillar-card--large' : ''; ?>">
                        <?php if ($image_url) : ?>
                            <div class="pillar-card__bg">
                                <img
                                    src="<?php echo esc_url($image_url); ?>"
                                    alt="<?php echo esc_attr($card['title']); ?>"
                                    style="object-position: center center"
                                />
                            </div>
                        <?php endif; ?>
                        
                        <span class="pillar-card__number"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span>
                        
                        <div class="pillar-card__inner">
                            <div class="pillar-card__content">
                                <h3 class="pillar-card__title"><?php echo esc_html($card['title']); ?></h3>
                                
                                <?php if ($card['description']) : ?>
                                    <p class="pillar-card__description"><?php echo esc_html($card['description']); ?></p>
                                <?php endif; ?>
                                
                                <a href="<?php echo esc_url($link_url); ?>" class="pillar-card__link">
                                    <?php echo esc_html($link_text); ?>
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="no-results"><?php esc_html_e('No cards found. Please add cards or enable Research Pillars.', 'gloceps'); ?></p>
        <?php endif; ?>
    </div>
</section>
