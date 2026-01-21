<?php
/**
 * Single template for Events
 * 
 * Matches event-single.html structure exactly
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while ( have_posts() ) :
    the_post();
    
    // Get event fields
    $event_date = get_field('event_date');
    $event_end_date = get_field('event_end_date');
    $event_time = get_field('event_time');
    $is_virtual = get_field('is_virtual');
    $venue_name = get_field('venue_name');
    $venue_address = get_field('venue_address');
    $location_city = get_field('location_city');
    $location_country = get_field('location_country');
    $map_embed_url = get_field('map_embed_url');
    $registration_link = get_field('registration_link');
    $registration_fee = get_field('registration_fee');
    $registration_includes = get_field('registration_includes');
    $registration_deadline = get_field('registration_deadline');
    $early_bird_discount = get_field('early_bird_discount');
    $description_lead = get_field('description_lead');
    $key_themes = get_field('key_themes');
    $who_should_attend = get_field('who_should_attend');
    $speakers = get_field('speakers');
    $agenda = get_field('agenda');
    $related_publications = get_field('related_publications');
    $organizer_name = get_field('organizer_name') ?: 'GLOCEPS';
    $organizer_description = get_field('organizer_description') ?: 'Global Centre for Policy and Strategy';
    $partners = get_field('partners');
    
    // Get event type from taxonomy
    $event_types = get_the_terms(get_the_ID(), 'event_type');
    $event_type = $event_types && !is_wp_error($event_types) ? $event_types[0] : null;
    
    // Determine event status (upcoming/past)
    $today = date('Y-m-d');
    $event_status = ($event_date && $event_date >= $today) ? 'upcoming' : 'past';
    
    // Format date range
    $date_display = '';
    if ($event_date) {
        $start_date = date('F j, Y', strtotime($event_date));
        if ($event_end_date && $event_end_date != $event_date) {
            $end_date = date('F j, Y', strtotime($event_end_date));
            $date_display = $start_date . ' - ' . $end_date;
            // Calculate duration
            $start = new DateTime($event_date);
            $end = new DateTime($event_end_date);
            $duration = $start->diff($end)->days + 1;
            $duration_text = $duration . '-day conference';
        } else {
            $date_display = $start_date;
            $duration_text = '';
        }
    }
?>

<article class="event-single">
    <?php
    // Include event header (split layout with image)
    include get_template_directory() . '/template-parts/events/event-header.php';
    ?>
    
    <!-- Event Body: Main Content + Sidebar -->
    <div class="event-body">
        <div class="container">
            <div class="event-layout">
                <!-- Main Content -->
                <div class="event-content reveal">
                    <?php
                    // Include event description section
                    include get_template_directory() . '/template-parts/events/event-description.php';
                    
                    // Include speakers section
                    if ($speakers) {
                        include get_template_directory() . '/template-parts/events/event-speakers.php';
                    }
                    
                    // Include gallery section
                    $gallery = get_field('gallery');
                    if ($gallery && !empty($gallery)) {
                        include get_template_directory() . '/template-parts/events/event-gallery.php';
                    }
                    
                    // Include agenda section
                    if ($agenda) {
                        include get_template_directory() . '/template-parts/events/event-agenda.php';
                    }
                    
                    // Include related publications section
                    if ($related_publications) {
                        include get_template_directory() . '/template-parts/events/event-publications.php';
                    }
                    ?>
                </div>
                
                <!-- Sidebar -->
                <aside class="event-sidebar">
                    <?php
                    // Include event details card
                    include get_template_directory() . '/template-parts/events/event-details-card.php';
                    
                    // Include registration card
                    if ($registration_link) {
                        include get_template_directory() . '/template-parts/events/event-registration-card.php';
                    }
                    
                    // Include organizer card
                    include get_template_directory() . '/template-parts/events/event-organizer-card.php';
                    
                    // Include partners card
                    if ($partners) {
                        include get_template_directory() . '/template-parts/events/event-partners-card.php';
                    }
                    ?>
                </aside>
            </div>
        </div>
    </div>
    
    <?php
    // Include more events section
    include get_template_directory() . '/template-parts/events/more-events.php';
    ?>
</article>

<?php
endwhile;

get_footer();
?>

