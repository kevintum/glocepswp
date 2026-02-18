<?php
/**
 * The template for displaying all pages
 *
 * This template checks for ACF Flexible Content blocks.
 * If blocks are found, it renders them using the block renderer.
 * Otherwise, it falls back to the default page content.
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while ( have_posts() ) :
    the_post();
    
    // Check if this is the cart page - if so, just output content (WooCommerce shortcode will handle it)
    $is_cart_page = false;
    if ( function_exists( 'wc_get_page_id' ) ) {
        $cart_page_id = wc_get_page_id( 'cart' );
        $is_cart_page = ( is_cart() || get_the_ID() === $cart_page_id );
    }
    
    if ( $is_cart_page ) {
        // For cart page, just output the content - WooCommerce shortcode will use our custom template
        // Don't add any page header - the cart template has its own hero section
        ?>
        <main id="main" class="site-main">
            <?php the_content(); ?>
        </main>
        <?php
        get_footer();
        return;
    }
    
    // Check if this is the checkout page - if so, just output content (WooCommerce shortcode will handle it)
    $is_checkout_page = false;
    if ( function_exists( 'wc_get_page_id' ) ) {
        $checkout_page_id = wc_get_page_id( 'checkout' );
        $is_checkout_page = ( is_checkout() || get_the_ID() === $checkout_page_id );
    }
    
    if ( $is_checkout_page ) {
        // For checkout page, just output the content - WooCommerce shortcode will use our custom template
        // Don't add any page header - the checkout template has its own header section
        ?>
        <main id="main" class="site-main">
            <?php the_content(); ?>
        </main>
        <?php
        get_footer();
        return;
    }
    
    // Check if this is the order-pay page - if so, just output content (WooCommerce shortcode will handle it)
    $is_order_pay = false;
    if ( function_exists( 'is_wc_endpoint_url' ) ) {
        $is_order_pay = is_wc_endpoint_url( 'order-pay' );
    }
    
    if ( $is_order_pay ) {
        // For order-pay page, just output the content - WooCommerce shortcode will use our custom template
        // Don't add any page header - the order-pay template has its own styling
        ?>
        <main id="main" class="site-main">
            <?php the_content(); ?>
        </main>
        <?php
        get_footer();
        return;
    }
    
    // Check if the page has flexible content blocks
    $has_content_blocks = function_exists( 'have_rows' ) && have_rows( 'content_blocks' );
?>

<?php if ( $has_content_blocks ) : ?>

    <!-- Render Flexible Content Blocks -->
    <main id="main" class="site-main">
        <?php include get_template_directory() . '/template-parts/blocks/block-renderer.php'; ?>
    </main>

<?php else : ?>

    <!-- Default Page Layout -->
    <?php
    $header_attrs = gloceps_get_page_header_attrs();
    ?>
    <section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
        <div class="container">
            <div class="page-header__content">
                <?php gloceps_breadcrumbs(); ?>
                <h1 class="page-header__title"><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                <p class="page-header__description"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <main>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'section' ); ?>>
            <div class="container container--narrow">
                <div class="page-content reveal">
                    <?php the_content(); ?>
                </div>
            </div>
        </article>
    </main>

<?php endif; ?>

<?php
endwhile;

get_footer();
