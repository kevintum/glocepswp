<?php
/**
 * Template part for displaying a publication item in the feed
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$is_premium = gloceps_is_premium_publication();
$price = gloceps_get_publication_price();
$pillars = get_the_terms( get_the_ID(), 'research_pillar' );
$types = get_the_terms( get_the_ID(), 'publication_type' );
?>

<article class="pub-item">
    <div class="pub-item__image">
        <?php 
        if ( has_post_thumbnail() ) {
            the_post_thumbnail( 'thumbnail' );
        } else {
            echo '<img src="' . esc_url( GLOCEPS_URI . '/assets/images/glocep-logo.png' ) . '" alt="">';
        }
        ?>
    </div>
    <div class="pub-item__content">
        <div class="pub-item__meta">
            <span class="pub-item__type">
                <?php 
                if ( $types && ! is_wp_error( $types ) ) {
                    echo esc_html( $types[0]->name );
                }
                ?>
            </span>
            <span class="pub-item__date"><?php echo esc_html( get_the_date( 'F Y' ) ); ?></span>
        </div>
        <h3 class="pub-item__title"><?php the_title(); ?></h3>
        <p class="pub-item__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
    </div>
    <div class="pub-item__action">
        <?php if ( $is_premium ) : ?>
        <a href="<?php the_permalink(); ?>" class="btn btn--accent btn--sm">
            <?php esc_html_e( 'Buy', 'gloceps' ); ?> <?php echo esc_html( gloceps_format_price( $price ) ); ?>
        </a>
        <?php else : ?>
        <a href="<?php the_permalink(); ?>" class="btn btn--ghost btn--sm">
            <?php esc_html_e( 'Read →', 'gloceps' ); ?>
        </a>
        <?php endif; ?>
    </div>
</article>

