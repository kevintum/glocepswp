<?php
/**
 * Block: Latest Research Publications (Editorial Style Listing)
 * 
 * Displays publications in an editorial-style list format
 * Used on homepage for "Latest Research Publications" section
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Latest Research';
$title = get_sub_field('title') ?: 'Publications';
$description = get_sub_field('description') ?: 'Access our latest policy briefs, research papers, and strategic analyses shaping discourse on regional and global issues.';
$count = get_sub_field('count') ?: 5;
$view_all_link = get_sub_field('view_all_link');

// Query publications
$args = array(
    'post_type' => 'publication',
    'posts_per_page' => $count,
    'orderby' => 'date',
    'order' => 'DESC',
);

$publications = new WP_Query($args);
?>

<section class="section">
    <div class="container">
        <div class="two-col publications-header">
            <div>
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
            </div>
            
            <?php if ($view_all_link) : ?>
                <div class="publications-header__action reveal">
                    <a href="<?php echo esc_url(is_array($view_all_link) ? $view_all_link['url'] : $view_all_link); ?>" class="btn btn--secondary">
                        <?php echo esc_html(is_array($view_all_link) ? ($view_all_link['title'] ?: 'View All Publications') : 'View All Publications'); ?>
                        <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($publications->have_posts()) : ?>
            <div class="publications-feed reveal">
                <?php while ($publications->have_posts()) : $publications->the_post(); 
                    $publication_id = get_the_ID();
                    $access_type = get_field('access_type', $publication_id);
                    $price = get_field('price', $publication_id);
                    $publication_date = get_field('publication_date', $publication_id);
                    $types = get_the_terms($publication_id, 'publication_type');
                    $type_name = $types && !is_wp_error($types) && !empty($types) ? $types[0]->name : '';
                    $wc_product_id = get_field('wc_product', $publication_id);
                    $product = $wc_product_id ? wc_get_product($wc_product_id) : null;
                    
                    // Determine action button
                    $is_premium = ($access_type === 'premium');
                    $action_text = $is_premium && $product ? gloceps_format_price($price) : __('Read →', 'gloceps');
                    $action_url = $is_premium && $product ? wc_get_cart_url() : get_permalink($publication_id);
                    $action_class = $is_premium && $product ? 'btn btn--accent btn--sm' : 'btn btn--ghost btn--sm';
                ?>
                    <article class="pub-item">
                        <div class="pub-item__image">
                            <?php if (has_post_thumbnail($publication_id)) : ?>
                                <?php echo get_the_post_thumbnail($publication_id, 'thumbnail', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                            <?php else : ?>
                                <div style="width: 100%; height: 100%; background: var(--color-gray-100); display: flex; align-items: center; justify-content: center;">
                                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--color-gray-400);">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pub-item__content">
                            <div class="pub-item__meta">
                                <?php if ($type_name) : ?>
                                    <span class="pub-item__type"><?php echo esc_html($type_name); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($publication_date) : ?>
                                    <span class="pub-item__date"><?php echo esc_html(gloceps_format_date($publication_date, 'F Y')); ?></span>
                                <?php else : ?>
                                    <span class="pub-item__date"><?php echo esc_html(get_the_date('F Y')); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="pub-item__title">
                                <a href="<?php echo esc_url(get_permalink($publication_id)); ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo esc_html(get_the_title()); ?>
                                </a>
                            </h3>
                            
                            <?php if (get_the_excerpt() || get_the_content()) : ?>
                                <p class="pub-item__excerpt">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 20)); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pub-item__action">
                            <?php if ($is_premium && $product) : ?>
                                <form action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data" style="display: inline;">
                                    <?php wp_nonce_field('woocommerce-add_to_cart', 'woocommerce-add-to-cart-nonce'); ?>
                                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />
                                    <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>" />
                                    <button type="submit" class="<?php echo esc_attr($action_class); ?>" aria-label="<?php esc_attr_e('Add to cart', 'gloceps'); ?>">
                                        <?php echo esc_html($action_text); ?>
                                    </button>
                                </form>
                            <?php else : ?>
                                <a href="<?php echo esc_url($action_url); ?>" class="<?php echo esc_attr($action_class); ?>">
                                    <?php echo esc_html($action_text); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="no-results"><?php esc_html_e('No publications found.', 'gloceps'); ?></p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>
