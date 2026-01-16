<?php
/**
 * Flexible Content Block: Store Hero Section
 *
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Purchase Publications';
$title = get_sub_field('title') ?: 'Research That Shapes Policy';
$description = get_sub_field('description') ?: 'Access in-depth analysis, strategic insights, and policy recommendations from GLOCEPS experts. Your purchase directly supports independent research advancing peace, security, and development in Eastern Africa.';
?>

<section class="store-hero">
    <div class="container">
        <div class="store-hero__content reveal">
            <div class="section-header__eyebrow" style="margin-bottom: var(--space-4);">
                <span class="eyebrow" style="color: var(--color-primary-light);"><?php echo esc_html($eyebrow); ?></span>
            </div>
            <h1 class="store-hero__title"><?php echo esc_html($title); ?></h1>
            <p class="store-hero__description"><?php echo esc_html($description); ?></p>
        </div>
    </div>
</section>

