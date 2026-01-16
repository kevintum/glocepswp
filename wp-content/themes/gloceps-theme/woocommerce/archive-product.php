<?php
/**
 * The Template for displaying product archives, including the main shop page
 * which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package GLOCEPS
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <h1 class="page-header__title"><?php woocommerce_page_title(); ?></h1>
            <p class="page-header__description">
                <?php esc_html_e( 'Access our research publications and policy papers. Browse free resources or purchase premium research materials.', 'gloceps' ); ?>
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        
        <!-- Publications Toolbar -->
        <div class="publications-toolbar reveal">
            <div class="publications-toolbar__left">
                <?php woocommerce_result_count(); ?>
            </div>
            <div class="publications-toolbar__right">
                <?php woocommerce_catalog_ordering(); ?>
            </div>
        </div>

        <div class="shop-layout">
            <!-- Filters Sidebar -->
            <aside class="shop-sidebar">
                <div class="filter-section">
                    <h3 class="filter-section__title"><?php esc_html_e( 'Filter by Category', 'gloceps' ); ?></h3>
                    <?php
                    $product_categories = get_terms( array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                    ) );
                    
                    if ( $product_categories && ! is_wp_error( $product_categories ) ) :
                    ?>
                    <ul class="filter-list">
                        <?php foreach ( $product_categories as $category ) : ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" 
                               class="filter-list__item<?php echo is_product_category( $category->slug ) ? ' filter-list__item--active' : ''; ?>">
                                <?php echo esc_html( $category->name ); ?>
                                <span class="filter-list__count">(<?php echo esc_html( $category->count ); ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <div class="filter-section">
                    <h3 class="filter-section__title"><?php esc_html_e( 'Price Filter', 'gloceps' ); ?></h3>
                    <?php the_widget( 'WC_Widget_Price_Filter' ); ?>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="shop-content">
                <?php
                if ( woocommerce_product_loop() ) {

                    woocommerce_product_loop_start();

                    if ( wc_get_loop_prop( 'total' ) ) {
                        while ( have_posts() ) {
                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action( 'woocommerce_shop_loop' );

                            wc_get_template_part( 'content', 'product' );
                        }
                    }

                    woocommerce_product_loop_end();

                    /**
                     * Hook: woocommerce_after_shop_loop.
                     *
                     * @hooked woocommerce_pagination - 10
                     */
                    do_action( 'woocommerce_after_shop_loop' );
                } else {
                    /**
                     * Hook: woocommerce_no_products_found.
                     *
                     * @hooked wc_no_products_found - 10
                     */
                    do_action( 'woocommerce_no_products_found' );
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );

