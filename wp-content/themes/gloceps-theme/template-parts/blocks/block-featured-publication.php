<?php
/**
 * Flexible Content Block: Featured Publication Section (Purchase Page)
 * Auto-selects premium publication if none selected
 *
 * @package GLOCEPS
 */

$selected_publication_id = get_sub_field('publication');

// If no publication selected, auto-select the most recent premium publication
if (!$selected_publication_id) {
    $premium_pubs = get_posts(array(
        'post_type' => 'publication',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'access_type',
                'value' => 'premium',
                'compare' => '=',
            ),
        ),
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    if (!empty($premium_pubs)) {
        $selected_publication_id = $premium_pubs[0]->ID;
    }
}

// If still no premium publication found, show a placeholder message for admins
if (!$selected_publication_id) {
    if (current_user_can('edit_posts')) {
        echo '<section class="section"><div class="container"><div class="pub-featured reveal" style="padding: var(--space-8); background: var(--color-gray-50); border-radius: var(--radius-xl); text-align: center;">';
        echo '<p style="color: var(--color-gray-600);">No premium publications found. Please add a premium publication to display here.</p>';
        echo '</div></div></section>';
    }
    return;
}

$post = get_post($selected_publication_id);
setup_postdata($post);

$access_type = get_field('access_type', $selected_publication_id);
$price = get_field('price', $selected_publication_id);
$publication_date = get_field('publication_date', $selected_publication_id);
$publication_format = get_field('publication_format', $selected_publication_id);
$page_count = get_field('page_count', $selected_publication_id);
$types = get_the_terms($selected_publication_id, 'publication_type');
$wc_product_id = get_field('wc_product', $selected_publication_id);

// Only show if premium
if ($access_type !== 'premium') {
    wp_reset_postdata();
    return;
}

// Get product for add to cart
$product = $wc_product_id ? wc_get_product($wc_product_id) : null;
?>

<section class="section pub-featured-section">
    <div class="container">
        <div class="pub-featured reveal">
            <div class="pub-featured__image">
                <?php if (has_post_thumbnail($selected_publication_id)) : ?>
                    <?php echo get_the_post_thumbnail($selected_publication_id, 'large'); ?>
                <?php else : ?>
                    <img src="<?php echo esc_url(gloceps_get_favicon_url(800)); ?>" alt="<?php the_title_attribute(); ?>" />
                <?php endif; ?>
            </div>
            <div class="pub-featured__content">
                <span class="pub-featured__label"><?php esc_html_e('Featured Publication', 'gloceps'); ?></span>
                <h2 class="pub-featured__title">
                    <a href="<?php echo esc_url(get_permalink($selected_publication_id)); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html(get_the_title($selected_publication_id)); ?></a>
                </h2>
                <input type="hidden" class="pub-featured__product-name" value="<?php echo esc_attr(get_the_title($selected_publication_id)); ?>" />
                <p class="pub-featured__excerpt"><?php echo esc_html(get_the_excerpt($selected_publication_id) ?: wp_trim_words(get_the_content(), 30)); ?></p>
                <div class="pub-featured__meta">
                    <span class="pub-featured__meta-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <?php 
                        $type_name = $types && !is_wp_error($types) ? $types[0]->name : '';
                        $format_name = $publication_format === 'pdf' ? 'PDF Document' : 'Article';
                        echo esc_html($type_name ? $type_name . ' • ' : '') . ($page_count ? $page_count . ' pages' : $format_name);
                        ?>
                    </span>
                    <?php if ($publication_date) : ?>
                    <span class="pub-featured__meta-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo esc_html(gloceps_format_date($publication_date, 'F Y')); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="pub-featured__price-cart">
                    <div class="pub-featured__price">
                        <span style="font-size: var(--text-3xl); font-weight: 700; color: var(--color-white);"><?php echo esc_html(gloceps_format_price($price)); ?></span>
                        <?php if ($price) : ?>
                        <span style="font-size: var(--text-sm); color: rgba(255,255,255,0.5); margin-left: var(--space-2);">≈ $<?php echo esc_html(number_format($price / 128, 0)); ?> USD</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product && class_exists('WooCommerce')) : ?>
                    <form class="pub-featured__add-to-cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                        <?php wp_nonce_field('woocommerce-add_to_cart', 'woocommerce-add-to-cart-nonce'); ?>
                        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />
                        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>" />
                        <button type="submit" class="btn btn--primary btn--block add_to_cart_button ajax_add_to_cart" aria-label="<?php esc_attr_e('Add to cart', 'gloceps'); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                            <?php esc_html_e('Add to Cart', 'gloceps'); ?>
                        </button>
                    </form>
                    <?php else : ?>
                    <a href="<?php echo esc_url(get_permalink($selected_publication_id)); ?>" class="btn btn--primary btn--lg">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <?php esc_html_e('Add to Cart', 'gloceps'); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
wp_reset_postdata();
?>
