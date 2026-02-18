<?php
/**
 * Flexible Content Block: Page Header
 *
 * @package GLOCEPS
 */

$title = get_sub_field('title');
$description = get_sub_field('description');

// Use page title if no title provided
if ( empty( $title ) ) {
    $title = get_the_title();
}

// Get page header attributes (includes background image support)
$header_attrs = gloceps_get_page_header_attrs();
?>

<section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
    <div class="container">
        <div class="page-header__content reveal">
            <?php gloceps_breadcrumbs(); ?>
            <?php if ( $title ) : ?>
                <h1 class="page-header__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if ( $description ) : ?>
                <p class="page-header__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

