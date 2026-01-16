<?php
/**
 * Template part for displaying a generic post card
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
    <div class="post-card__image">
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail( 'gloceps-card' ); ?>
        </a>
    </div>
    <?php endif; ?>
    
    <div class="post-card__content">
        <div class="post-card__meta">
            <?php 
            $categories = get_the_category();
            if ( ! empty( $categories ) ) :
            ?>
            <span class="post-card__category"><?php echo esc_html( $categories[0]->name ); ?></span>
            <?php endif; ?>
            <time class="post-card__date"><?php echo esc_html( get_the_date() ); ?></time>
        </div>
        
        <h3 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <p class="post-card__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
        
        <div class="post-card__footer">
            <span class="post-card__author">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <?php the_author(); ?>
            </span>
            <a href="<?php the_permalink(); ?>" class="post-card__link">
                <?php esc_html_e( 'Read More', 'gloceps' ); ?>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</article>

