<?php
/**
 * Template Name: Cart Page
 * Template for Cart page - ensures WooCommerce shortcode renders correctly
 *
 * @package GLOCEPS
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        
        // Output the page content (which should contain [woocommerce_cart] shortcode)
        the_content();
        
    endwhile;
    ?>
</main>

<?php
get_footer();

