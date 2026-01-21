<?php
/**
 * Template part for displaying a publication card
 * Matches publications.html card design
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$access_type = get_field( 'access_type' ) ?: 'free';
$price = gloceps_get_publication_price();
$publication_format = get_field( 'publication_format' ) ?: 'pdf';
$pillars = get_the_terms( get_the_ID(), 'research_pillar' );
$types = get_the_terms( get_the_ID(), 'publication_type' );
$author_info = gloceps_get_publication_author();

// Get publication type slug for data attribute
$type_slug = '';
if ( $types && ! is_wp_error( $types ) ) {
    $type_slug = $types[0]->slug;
}

// Get pillar slug for data attribute
$pillar_slug = '';
if ( $pillars && ! is_wp_error( $pillars ) ) {
    $pillar_slug = $pillars[0]->slug;
}

// Get year for data attribute
$year = get_the_date( 'Y' );

// Get excerpt
$excerpt = get_the_excerpt();
if ( empty( $excerpt ) ) {
    $excerpt = wp_trim_words( get_the_content(), 20, '...' );
}

// Get thumbnail
$thumbnail_url = '';
if ( has_post_thumbnail() ) {
    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
}
if ( ! $thumbnail_url ) {
    $thumbnail_url = gloceps_get_favicon_url( 192 );
}

// Format date
$date_display = get_the_date( 'M j, Y' );

// CTA text based on format
$cta_text = ( $publication_format === 'article' ) ? __( 'Read Article', 'gloceps' ) : __( 'Read More', 'gloceps' );
if ( $access_type === 'premium' ) {
    $cta_text = __( 'View Details', 'gloceps' );
}
?>

<article class="publication-card" 
    data-type="<?php echo esc_attr( $type_slug ); ?>" 
    data-pillar="<?php echo esc_attr( $pillar_slug ); ?>" 
    data-access="<?php echo esc_attr( $access_type ); ?>"
    data-format="<?php echo esc_attr( $publication_format ); ?>"
    data-year="<?php echo esc_attr( $year ); ?>">
    <a href="<?php the_permalink(); ?>" class="publication-card__link">
        <div class="publication-card__image">
            <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
            <div class="publication-card__badges">
                <?php if ( $types && ! is_wp_error( $types ) ) : ?>
                <span class="publication-card__badge publication-card__badge--type"><?php echo esc_html( $types[0]->name ); ?></span>
                <?php endif; ?>
                <?php if ( $access_type === 'free' ) : ?>
                <span class="publication-card__badge publication-card__badge--free"><?php esc_html_e( 'Free', 'gloceps' ); ?></span>
                <?php else : ?>
                <span class="publication-card__badge publication-card__badge--premium">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                    </svg>
                    <?php echo $price ? gloceps_format_price( $price ) : esc_html__( 'Premium', 'gloceps' ); ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="publication-card__format">
                <?php if ( $publication_format === 'pdf' ) : ?>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <?php esc_html_e( 'PDF', 'gloceps' ); ?>
                <?php else : ?>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <?php esc_html_e( 'Article', 'gloceps' ); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="publication-card__content">
            <div class="publication-card__meta">
                <time class="publication-card__date"><?php echo esc_html( $date_display ); ?></time>
                <?php if ( $pillars && ! is_wp_error( $pillars ) ) : ?>
                <span class="publication-card__pillar"><?php echo esc_html( $pillars[0]->name ); ?></span>
                <?php endif; ?>
            </div>
            <h4 class="publication-card__title"><?php the_title(); ?></h4>
            <?php if ( $excerpt ) : ?>
            <p class="publication-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>
            <div class="publication-card__footer">
                <span class="publication-card__author">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <?php echo esc_html( $author_info['name'] ?: get_the_author() ); ?>
                </span>
                <div class="publication-card__actions">
                    <?php if ( $access_type === 'premium' && class_exists( 'WooCommerce' ) ) : 
                        $wc_product_id = get_field( 'wc_product' );
                        if ( $wc_product_id ) :
                            $product = wc_get_product( $wc_product_id );
                            if ( $product && $product->is_purchasable() ) :
                    ?>
                        <form class="publication-card__add-to-cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr( $wc_product_id ); ?>">
                            <?php wp_nonce_field( 'woocommerce-add_to_cart', 'woocommerce-add-to-cart-nonce' ); ?>
                            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $wc_product_id ); ?>" />
                            <input type="hidden" name="product_id" value="<?php echo esc_attr( $wc_product_id ); ?>" />
                            <button type="submit" class="publication-card__cart-btn add_to_cart_button ajax_add_to_cart" aria-label="<?php esc_attr_e( 'Add to cart', 'gloceps' ); ?>" data-product_id="<?php echo esc_attr( $wc_product_id ); ?>">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </button>
                        </form>
                    <?php endif; endif; endif; ?>
                    <a href="<?php the_permalink(); ?>" class="publication-card__cta">
                        <?php echo esc_html( $cta_text ); ?>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </a>
</article>
