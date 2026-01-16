<?php
/**
 * The Template for displaying single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     GLOCEPS
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

// Get linked publication if exists
$publication_id = get_post_meta( get_the_ID(), '_gloceps_publication_id', true );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
    the_post();

    global $product;
?>

<!-- Page Header -->
<section class="page-header page-header--compact">
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
        </div>
    </div>
</section>

<section class="section section--product">
    <div class="container">
        <div class="product-layout">
            <!-- Product Image -->
            <div class="product-gallery reveal">
                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 *
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>
            </div>

            <!-- Product Details -->
            <div class="product-summary reveal">
                <div class="product-summary__inner">
                    <?php if ( $publication_id ) : ?>
                    <span class="badge badge--info"><?php esc_html_e( 'Publication', 'gloceps' ); ?></span>
                    <?php endif; ?>
                    
                    <h1 class="product-summary__title"><?php the_title(); ?></h1>
                    
                    <?php if ( $publication_id ) : 
                        $pub_type = get_the_terms( $publication_id, 'publication_type' );
                        $pub_pillar = get_the_terms( $publication_id, 'research_pillar' );
                        $pub_date = get_field( 'publication_date', $publication_id );
                    ?>
                    <div class="product-summary__meta">
                        <?php if ( $pub_type && ! is_wp_error( $pub_type ) ) : ?>
                        <span class="product-summary__type"><?php echo esc_html( $pub_type[0]->name ); ?></span>
                        <?php endif; ?>
                        
                        <?php if ( $pub_date ) : ?>
                        <span class="product-summary__date"><?php echo esc_html( gloceps_format_date( $pub_date ) ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-summary__short-description">
                        <?php the_excerpt(); ?>
                    </div>
                    
                    <?php if ( $pub_pillar && ! is_wp_error( $pub_pillar ) ) : ?>
                    <div class="product-summary__pillars">
                        <span class="product-summary__pillars-label"><?php esc_html_e( 'Research Pillar:', 'gloceps' ); ?></span>
                        <?php foreach ( $pub_pillar as $pillar ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $pillar ) ); ?>" class="tag"><?php echo esc_html( $pillar->name ); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-summary__price">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    
                    <div class="product-summary__add-to-cart">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                    
                    <div class="product-summary__features">
                        <div class="product-summary__feature">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span><?php esc_html_e( 'Instant PDF Download', 'gloceps' ); ?></span>
                        </div>
                        <div class="product-summary__feature">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span><?php esc_html_e( 'Secure Payment', 'gloceps' ); ?></span>
                        </div>
                        <div class="product-summary__feature">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span><?php esc_html_e( 'M-Pesa & PayPal', 'gloceps' ); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description Tabs -->
        <div class="product-tabs reveal">
            <?php
            /**
             * Hook: woocommerce_after_single_product_summary.
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action( 'woocommerce_after_single_product_summary' );
            ?>
        </div>
    </div>
</section>

<?php
endwhile; // end of the loop.

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );

