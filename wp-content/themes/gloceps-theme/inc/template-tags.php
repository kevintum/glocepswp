<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package GLOCEPS
 */

if ( ! function_exists( 'gloceps_posted_on' ) ) :
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function gloceps_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x( 'Posted on %s', 'post date', 'gloceps' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>';
    }
endif;

if ( ! function_exists( 'gloceps_posted_by' ) ) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function gloceps_posted_by() {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x( 'by %s', 'post author', 'gloceps' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>';
    }
endif;

if ( ! function_exists( 'gloceps_entry_footer' ) ) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function gloceps_entry_footer() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() ) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list( esc_html__( ', ', 'gloceps' ) );
            if ( $categories_list ) {
                /* translators: 1: list of categories. */
                printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'gloceps' ) . '</span>', $categories_list );
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'gloceps' ) );
            if ( $tags_list ) {
                /* translators: 1: list of tags. */
                printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'gloceps' ) . '</span>', $tags_list );
            }
        }

        if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'gloceps' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post( get_the_title() )
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __( 'Edit <span class="screen-reader-text">%s</span>', 'gloceps' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post( get_the_title() )
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;

if ( ! function_exists( 'gloceps_post_thumbnail' ) ) :
    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function gloceps_post_thumbnail() {
        if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
            return;
        }

        if ( is_singular() ) :
            ?>

            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div><!-- .post-thumbnail -->

        <?php else : ?>

            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                    the_post_thumbnail(
                        'post-thumbnail',
                        array(
                            'alt' => the_title_attribute(
                                array(
                                    'echo' => false,
                                )
                            ),
                        )
                    );
                ?>
            </a>

            <?php
        endif;
    }
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
    /**
     * Shim for sites older than 5.2.
     *
     * @link https://core.trac.wordpress.org/ticket/12563
     */
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
endif;

/**
 * Display publication type badge
 *
 * @param int $post_id Post ID
 */
function gloceps_publication_type_badge( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $types = get_the_terms( $post_id, 'publication_type' );
    
    if ( $types && ! is_wp_error( $types ) ) {
        $type = $types[0];
        echo '<span class="badge badge--type">' . esc_html( $type->name ) . '</span>';
    }
}

/**
 * Display research pillar tags
 *
 * @param int $post_id Post ID
 */
function gloceps_research_pillar_tags( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $pillars = get_the_terms( $post_id, 'research_pillar' );
    
    if ( $pillars && ! is_wp_error( $pillars ) ) {
        echo '<div class="pillar-tags">';
        foreach ( $pillars as $pillar ) {
            $pillar_page = get_page_by_path( $pillar->slug );
            $pillar_url = $pillar_page ? get_permalink( $pillar_page ) : get_term_link( $pillar );
            echo '<a href="' . esc_url( $pillar_url ) . '" class="tag">' . esc_html( $pillar->name ) . '</a>';
        }
        echo '</div>';
    }
}

/**
 * Get event status
 *
 * @param int $post_id Post ID
 * @return string
 */
function gloceps_get_event_status( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $event_date = get_field( 'event_date', $post_id );
    $event_end_date = get_field( 'event_end_date', $post_id );
    
    if ( ! $event_date ) {
        return 'upcoming';
    }
    
    $now = current_time( 'timestamp' );
    $start = strtotime( $event_date );
    $end = $event_end_date ? strtotime( $event_end_date ) : $start + ( 24 * 60 * 60 );
    
    if ( $now < $start ) {
        return 'upcoming';
    } elseif ( $now >= $start && $now <= $end ) {
        return 'ongoing';
    } else {
        return 'past';
    }
}

/**
 * Display event status badge
 *
 * @param int $post_id Post ID
 */
function gloceps_event_status_badge( $post_id = null ) {
    $status = gloceps_get_event_status( $post_id );
    
    $badge_class = 'badge';
    $badge_text = '';
    
    switch ( $status ) {
        case 'upcoming':
            $badge_class .= ' badge--info';
            $badge_text = __( 'Upcoming', 'gloceps' );
            break;
        case 'ongoing':
            $badge_class .= ' badge--success';
            $badge_text = __( 'Happening Now', 'gloceps' );
            break;
        case 'past':
            $badge_class .= ' badge--secondary';
            $badge_text = __( 'Past Event', 'gloceps' );
            break;
    }
    
    if ( $badge_text ) {
        echo '<span class="' . esc_attr( $badge_class ) . '">' . esc_html( $badge_text ) . '</span>';
    }
}

/**
 * Display share buttons
 *
 * @param int $post_id Post ID
 */
function gloceps_share_buttons( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $url = urlencode( get_permalink( $post_id ) );
    $title = urlencode( get_the_title( $post_id ) );
    
    ?>
    <div class="share-buttons">
        <span class="share-buttons__label"><?php esc_html_e( 'Share:', 'gloceps' ); ?></span>
        <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-buttons__item share-buttons__item--twitter"
           aria-label="<?php esc_attr_e( 'Share on Twitter', 'gloceps' ); ?>">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </a>
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-buttons__item share-buttons__item--linkedin"
           aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'gloceps' ); ?>">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-buttons__item share-buttons__item--facebook"
           aria-label="<?php esc_attr_e( 'Share on Facebook', 'gloceps' ); ?>">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </a>
    </div>
    <?php
}
