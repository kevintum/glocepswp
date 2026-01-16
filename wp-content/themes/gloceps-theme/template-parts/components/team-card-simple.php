<?php
/**
 * Template part for displaying a simple team member card (for pillar contributors)
 * No bio link, just name and title
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$job_title = get_field( 'job_title' );
$credentials = get_field( 'credentials' );
?>

<article class="team-card team-card--simple">
    <?php 
    $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'gloceps-team' );
    $is_placeholder = !$featured_image;
    if (!$featured_image) {
        $featured_image = gloceps_get_favicon_url(192);
    }
    ?>
    <div class="team-card__image-wrapper">
        <img 
            src="<?php echo esc_url( $featured_image ); ?>" 
            alt="<?php the_title_attribute(); ?>" 
            class="team-card__image <?php echo $is_placeholder ? 'team-card__image--placeholder-icon' : ''; ?>"
        />
    </div>
    
    <div class="team-card__content">
        <h3 class="team-card__name"><?php the_title(); ?></h3>
        
        <?php if ( $job_title ) : ?>
        <p class="team-card__job-title"><?php echo esc_html( $job_title ); ?></p>
        <?php endif; ?>
    </div>
</article>

