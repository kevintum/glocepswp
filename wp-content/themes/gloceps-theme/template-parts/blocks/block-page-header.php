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
?>

<section class="page-header page-header--minimal">
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

