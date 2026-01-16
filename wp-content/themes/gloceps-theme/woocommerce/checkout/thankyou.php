<?php
/**
 * Thankyou page - Custom GLOCEPS Design
 * Matches the static order-complete.html design
 *
 * @package GLOCEPS
 */

defined('ABSPATH') || exit;

// Ensure $order variable is available (WooCommerce passes it via wc_get_template)
if ( ! isset( $order ) ) {
    $order = false;
    $order_id = 0;
    $order_key = '';
    
    // Get order from query vars
    if ( isset( $_GET['order'] ) ) {
        $order_id = absint( $_GET['order'] );
    }
    if ( isset( $_GET['key'] ) ) {
        $order_key = wc_clean( wp_unslash( $_GET['key'] ) );
    }
    
    if ( $order_id > 0 ) {
        $order = wc_get_order( $order_id );
        if ( $order && ! hash_equals( $order->get_order_key(), $order_key ) ) {
            $order = false;
        }
    }
}
?>

<section class="order-complete">
    <div class="container">
        <div class="order-complete__wrapper">
            <?php if ($order) :
                do_action('woocommerce_before_thankyou', $order->get_id());
            ?>
            
            <?php if ($order->has_status('failed')) : ?>
                <div class="order-complete__icon order-complete__icon--error">
                    <svg viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="2"/>
                        <path fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" d="M16 16l20 20M36 16l-20 20"/>
                    </svg>
                </div>
                
                <h1 class="order-complete__title"><?php esc_html_e('Payment Failed', 'gloceps'); ?></h1>
                <p class="order-complete__message">
                    <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'gloceps'); ?>
                </p>
                
                <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="btn btn--primary btn--lg">
                    <?php esc_html_e('Pay Now', 'gloceps'); ?>
                </a>
                
            <?php elseif ($order->has_status('pending') || $order->has_status('on-hold')) : ?>
                <!-- Payment Processing -->
                <div class="order-complete__icon">
                    <svg class="order-complete__spinner" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="157" stroke-dashoffset="78.5">
                            <animate attributeName="stroke-dashoffset" values="157;0;157" dur="2s" repeatCount="indefinite"/>
                        </circle>
                    </svg>
                </div>
                
                <h1 class="order-complete__title"><?php esc_html_e('Payment Processing', 'gloceps'); ?></h1>
                <p class="order-complete__message">
                    <?php esc_html_e('Your payment is being processed. You will receive an email confirmation once your payment is confirmed. This usually takes a few minutes.', 'gloceps'); ?>
                </p>
                
                <!-- Order Details Card -->
                <div class="order-details">
                    <div class="order-details__header">
                        <div class="order-details__info">
                            <span class="order-details__label"><?php esc_html_e('Order Number', 'gloceps'); ?></span>
                            <span class="order-details__value">#<?php echo esc_html($order->get_order_number()); ?></span>
                        </div>
                        <div class="order-details__info">
                            <span class="order-details__label"><?php esc_html_e('Date', 'gloceps'); ?></span>
                            <span class="order-details__value"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></span>
                        </div>
                        <div class="order-details__info">
                            <span class="order-details__label"><?php esc_html_e('Total', 'gloceps'); ?></span>
                            <span class="order-details__value order-details__value--highlight"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="order-complete__actions">
                    <a href="<?php echo esc_url(home_url('/store/')); ?>" class="btn btn--primary btn--lg">
                        <?php esc_html_e('Continue Shopping', 'gloceps'); ?>
                    </a>
                    <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="btn btn--secondary btn--lg">
                        <?php esc_html_e('View Order', 'gloceps'); ?>
                    </a>
                </div>
                
            <?php else : ?>
            
                <!-- Success Animation -->
                <div class="order-complete__icon">
                    <svg class="order-complete__checkmark" viewBox="0 0 52 52">
                        <circle class="order-complete__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="order-complete__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                
                <h1 class="order-complete__title"><?php esc_html_e('Thank You for Your Purchase!', 'gloceps'); ?></h1>
                <p class="order-complete__message">
                    <?php esc_html_e('Your order has been successfully processed. Your publications are on their way to your inbox.', 'gloceps'); ?>
                </p>
                
                <!-- Order Details Card -->
                <div class="order-details">
                    <div class="order-details__header">
                        <div class="order-details__info">
                            <span class="order-details__label"><?php esc_html_e('ORDER NUMBER', 'gloceps'); ?></span>
                            <span class="order-details__value">#<?php echo esc_html($order->get_order_number()); ?></span>
                        </div>
                        <div class="order-details__info">
                            <span class="order-details__label"><?php esc_html_e('DATE', 'gloceps'); ?></span>
                            <span class="order-details__value"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></span>
                        </div>
                        <div class="order-details__info">
                            <span class="order-details__label"><?php esc_html_e('TOTAL PAID', 'gloceps'); ?></span>
                            <span class="order-details__value order-details__value--highlight"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
                        </div>
                    </div>
                    
                    <div class="order-details__items">
                        <h3><?php esc_html_e('YOUR PUBLICATIONS', 'gloceps'); ?></h3>
                        
                        <?php 
                        $publication_ids = array();
                        foreach ($order->get_items() as $item_id => $item) :
                            $product = $item->get_product();
                            $is_downloadable = $product && $product->is_downloadable();
                            $downloads = $order->get_item_downloads($item);
                            
                            // Get publication ID from product meta
                            $publication_id = $item->get_meta('_publication_id');
                            if ($publication_id) {
                                $publication_ids[] = $publication_id;
                            }
                            
                            // Get publication type and page count
                            $publication_type = '';
                            $page_count = '';
                            if ($publication_id) {
                                $publication_type_obj = get_the_terms($publication_id, 'publication_type');
                                if ($publication_type_obj && !is_wp_error($publication_type_obj)) {
                                    $publication_type = $publication_type_obj[0]->name;
                                }
                                $page_count = get_field('page_count', $publication_id);
                            }
                        ?>
                        <div class="order-item">
                            <div class="order-item__icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="order-item__details">
                                <strong><?php echo wp_kses_post($item->get_name()); ?></strong>
                                <span>
                                    <?php 
                                    if ($publication_type) {
                                        echo esc_html($publication_type);
                                    } else {
                                        $terms = get_the_terms($product->get_id(), 'product_cat');
                                        if ($terms && !is_wp_error($terms)) {
                                            echo esc_html($terms[0]->name);
                                        }
                                    }
                                    if ($page_count) {
                                        echo ' • ' . esc_html($page_count) . ' pages';
                                    }
                                    echo ' • PDF';
                                    ?>
                                </span>
                            </div>
                            <?php if ($is_downloadable && !empty($downloads)) : ?>
                                <?php foreach ($downloads as $download) : ?>
                                <a href="<?php echo esc_url($download['download_url']); ?>" class="btn btn--primary btn--sm">
                                    <?php esc_html_e('Download', 'gloceps'); ?>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Email Confirmation Box -->
                <div class="order-complete__email-notice">
                    <div class="order-complete__email-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="order-complete__email-content">
                        <?php 
                        printf(
                            esc_html__('Check your email. We\'ve sent a confirmation with your publications attached to %s.', 'gloceps'),
                            '<strong>' . esc_html($order->get_billing_email()) . '</strong>'
                        ); 
                        ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="order-complete__actions">
                    <a href="<?php echo esc_url(home_url('/store/')); ?>" class="btn btn--primary btn--lg">
                        <?php esc_html_e('Continue Shopping', 'gloceps'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/resend-publications/')); ?>" class="order-complete__resend-link">
                        <?php esc_html_e('Didn\'t receive email? Resend', 'gloceps'); ?>
                    </a>
                </div>
                
                <!-- Receipt Link -->
                <div class="order-complete__receipt">
                    <p>
                        <?php esc_html_e('A receipt has been sent to your email for your records.', 'gloceps'); ?>
                        <a href="<?php echo esc_url(add_query_arg(array('order_id' => $order->get_id(), 'key' => $order->get_order_key()), home_url('/order-receipt/'))); ?>">
                            <?php esc_html_e('Download PDF Receipt', 'gloceps'); ?>
                        </a>
                    </p>
                </div>
                
                <!-- You May Also Be Interested In -->
                <?php
                if (!empty($publication_ids)) {
                    // Get related publications (exclude purchased ones)
                    $related_args = array(
                        'post_type' => 'publication',
                        'posts_per_page' => 3, // Show maximum 3 related publications
                        'post__not_in' => $publication_ids,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'meta_query' => array(
                            array(
                                'key' => 'access_type',
                                'value' => 'premium',
                                'compare' => '='
                            )
                        )
                    );
                    
                    $related_publications = new WP_Query($related_args);
                    
                    if ($related_publications->have_posts()) :
                ?>
                <div class="order-complete__related">
                    <h2 class="order-complete__related-title"><?php esc_html_e('You May Also Be Interested In', 'gloceps'); ?></h2>
                    <p class="order-complete__related-subtitle"><?php esc_html_e('Explore more research on related topics.', 'gloceps'); ?></p>
                    <div class="order-complete__related-grid">
                        <?php while ($related_publications->have_posts()) : $related_publications->the_post();
                            $publication_id = get_the_ID();
                            $featured_image = get_field('featured_image', $publication_id);
                            $publication_type = get_the_terms($publication_id, 'publication_type');
                            $page_count = get_field('page_count', $publication_id);
                            $wc_product_id = get_field('wc_product', $publication_id);
                            $product = $wc_product_id ? wc_get_product($wc_product_id) : null;
                            $price = $product ? $product->get_price_html() : '';
                        ?>
                        <div class="publication-card">
                            <?php if ($featured_image && is_array($featured_image)) : ?>
                                <a href="<?php echo esc_url(get_permalink($publication_id)); ?>" class="publication-card__image">
                                    <img src="<?php echo esc_url($featured_image['sizes']['medium'] ?? $featured_image['url']); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                </a>
                            <?php else : 
                                // Use favicon as placeholder
                                $favicon_url = function_exists('gloceps_get_favicon_url') ? gloceps_get_favicon_url(192) : '';
                            ?>
                                <a href="<?php echo esc_url(get_permalink($publication_id)); ?>" class="publication-card__image publication-card__image--placeholder">
                                    <?php if ($favicon_url) : ?>
                                        <img src="<?php echo esc_url($favicon_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 60px; height: 60px; object-fit: contain; opacity: 0.8;">
                                    <?php else : ?>
                                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                            <div class="publication-card__content">
                                <?php if ($publication_type && !is_wp_error($publication_type)) : ?>
                                    <span class="publication-card__type"><?php echo esc_html(strtoupper($publication_type[0]->name)); ?></span>
                                <?php endif; ?>
                                <h3 class="publication-card__title">
                                    <a href="<?php echo esc_url(get_permalink($publication_id)); ?>"><?php echo esc_html(get_the_title()); ?></a>
                                </h3>
                                <div class="publication-card__meta">
                                    <?php 
                                    $date = get_the_date('M Y');
                                    echo esc_html($date);
                                    if ($page_count) {
                                        echo ' • ' . esc_html($page_count) . ' pages';
                                    }
                                    ?>
                                </div>
                                <?php if ($price) : ?>
                                    <div class="publication-card__price"><?php echo wp_kses_post($price); ?></div>
                                <?php endif; ?>
                                <a href="<?php echo esc_url(get_permalink($publication_id)); ?>" class="btn btn--primary btn--sm">
                                    <?php esc_html_e('View', 'gloceps'); ?>
                                </a>
                            </div>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
                <?php endif; } ?>
                
            <?php endif; ?>
            
            <?php do_action('woocommerce_thankyou', $order->get_id()); ?>
            
        <?php else : ?>
            
            <div class="order-complete__icon">
                <svg viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" d="M26 15v12M26 31v4"/>
                </svg>
            </div>
            
            <h1 class="order-complete__title"><?php esc_html_e('Order Not Found', 'gloceps'); ?></h1>
            <p class="order-complete__message woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                <?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'gloceps'), null); ?>
            </p>
            
        <?php endif; ?>
        </div>
    </div>
</section>
