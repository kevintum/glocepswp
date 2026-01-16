<?php
/**
 * Template part for displaying a team member card
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$job_title = get_field( 'job_title' );
$credentials = get_field( 'credentials' );
$biography = get_field( 'biography' );
// Use ACF biography field, fallback to post content
$full_bio = $biography ? $biography : get_the_content();
// Strip HTML but preserve line breaks for paragraph formatting
$full_bio_text = wp_strip_all_tags( $full_bio );
$categories = get_the_terms( get_the_ID(), 'team_category' );
$category_name = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : '';
// Format category name for badge (uppercase)
$category_badge = $category_name ? strtoupper( str_replace( '-', ' ', $category_name ) ) : '';
?>

<article class="team-card"
    data-name="<?php echo esc_attr( get_the_title() ); ?>"
    data-title="<?php echo esc_attr( get_the_title() . ( $credentials ? ', ' . $credentials : '' ) ); ?>"
    data-role="<?php echo esc_attr( $category_badge ); ?>"
    data-job-title="<?php echo esc_attr( $job_title ); ?>"
    data-bio="<?php echo esc_attr( $full_bio_text ); ?>"
    data-bio-html="<?php echo esc_attr( htmlspecialchars( $full_bio, ENT_QUOTES, 'UTF-8' ) ); ?>">
    
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
        <?php if ( $category_badge ) : ?>
        <span class="team-card__badge"><?php echo esc_html( $category_badge ); ?></span>
        <?php endif; ?>
        
        <h3 class="team-card__name"><?php the_title(); ?></h3>
        
        <?php if ( $job_title ) : ?>
        <p class="team-card__job-title"><?php echo esc_html( $job_title ); ?></p>
        <?php endif; ?>
        
        <?php if ( $credentials ) : ?>
        <p class="team-card__credentials"><?php echo esc_html( $credentials ); ?></p>
        <?php endif; ?>
        
        <button class="team-card__bio-link" aria-label="<?php esc_attr_e( 'View full bio', 'gloceps' ); ?>">
            <?php esc_html_e( 'View Bio', 'gloceps' ); ?>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </button>
    </div>
</article>

