<?php
/**
 * Template part for displaying an event item
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

$event_date = get_field( 'event_date' );
$event_end_date = get_field( 'event_end_date' );
$event_location = get_field( 'event_location' );
$event_type = get_field( 'event_type' );
$event_format = get_field( 'event_format' ); // in-person, virtual, hybrid
$event_types = get_the_terms( get_the_ID(), 'event_type' );
$status = gloceps_get_event_status();

// Parse date components
$date_obj = $event_date ? DateTime::createFromFormat( 'Ymd', $event_date ) : null;
$day = $date_obj ? $date_obj->format( 'd' ) : '';
$month = $date_obj ? $date_obj->format( 'M' ) : '';
$year = $date_obj ? $date_obj->format( 'Y' ) : '';
?>

<article class="event-item event-item--<?php echo esc_attr( $status ); ?>">
    <a href="<?php the_permalink(); ?>" class="event-item__link">
        <div class="event-item__date-block">
            <span class="event-item__day"><?php echo esc_html( $day ); ?></span>
            <span class="event-item__month"><?php echo esc_html( $month ); ?></span>
            <span class="event-item__year"><?php echo esc_html( $year ); ?></span>
        </div>
        
        <div class="event-item__content">
            <div class="event-item__meta">
                <?php if ( $event_types && ! is_wp_error( $event_types ) ) : ?>
                <span class="event-item__type"><?php echo esc_html( $event_types[0]->name ); ?></span>
                <?php endif; ?>
                
                <?php gloceps_event_status_badge(); ?>
                
                <?php if ( $event_format ) : ?>
                <span class="event-item__format event-item__format--<?php echo esc_attr( $event_format ); ?>">
                    <?php
                    switch ( $event_format ) {
                        case 'virtual':
                            echo '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>';
                            esc_html_e( 'Virtual', 'gloceps' );
                            break;
                        case 'hybrid':
                            echo '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>';
                            esc_html_e( 'Hybrid', 'gloceps' );
                            break;
                        default:
                            echo '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                            esc_html_e( 'In Person', 'gloceps' );
                    }
                    ?>
                </span>
                <?php endif; ?>
            </div>
            
            <h3 class="event-item__title"><?php the_title(); ?></h3>
            
            <?php if ( has_excerpt() ) : ?>
            <p class="event-item__excerpt"><?php echo esc_html( gloceps_truncate( get_the_excerpt(), 120 ) ); ?></p>
            <?php endif; ?>
            
            <?php if ( $event_location ) : ?>
            <div class="event-item__location">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span><?php echo esc_html( $event_location ); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="event-item__action">
            <span class="event-item__cta">
                <?php 
                if ( $status === 'past' ) {
                    esc_html_e( 'View Recap', 'gloceps' );
                } else {
                    esc_html_e( 'Learn More', 'gloceps' );
                }
                ?>
            </span>
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </div>
    </a>
</article>
