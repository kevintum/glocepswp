<?php
/**
 * Template Name: Research Pillar
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while ( have_posts() ) :
    the_post();
    
    $pillar_color = get_field( 'pillar_color' ) ?: '#3f93c1';
    $hero_image = get_field( 'pillar_hero_image' );
    $key_areas = get_field( 'key_areas' );
    
    // Try to match this page to a research pillar taxonomy
    $page_slug = get_post_field( 'post_name', get_the_ID() );
    $pillar_term = get_term_by( 'slug', $page_slug, 'research_pillar' );
?>

<style>
    :root {
        --pillar-color: <?php echo esc_attr( $pillar_color ); ?>;
    }
</style>

<!-- Hero Section -->
<section class="page-header page-header--pillar" style="--pillar-color: <?php echo esc_attr( $pillar_color ); ?>">
    <div class="page-header__bg">
        <?php 
        if ( $hero_image ) {
            echo '<img src="' . esc_url( $hero_image['url'] ) . '" alt="' . esc_attr( $hero_image['alt'] ) . '">';
        } elseif ( has_post_thumbnail() ) {
            the_post_thumbnail( 'gloceps-hero' );
        }
        ?>
    </div>
    <div class="container">
        <div class="page-header__content">
            <?php gloceps_breadcrumbs(); ?>
            <div class="page-header__eyebrow">
                <span class="eyebrow" style="color: <?php echo esc_attr( $pillar_color ); ?>">
                    <?php esc_html_e( 'Research Pillar', 'gloceps' ); ?>
                </span>
            </div>
            <h1 class="page-header__title"><?php the_title(); ?></h1>
            <p class="page-header__description"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
        </div>
    </div>
</section>

<main>
    <!-- Overview -->
    <section class="section">
        <div class="container">
            <div class="two-col reveal">
                <div class="page-content">
                    <?php the_content(); ?>
                </div>
                
                <?php if ( $key_areas ) : ?>
                <div class="pillar-areas">
                    <h3><?php esc_html_e( 'Key Focus Areas', 'gloceps' ); ?></h3>
                    <ul class="pillar-areas__list">
                        <?php foreach ( $key_areas as $area ) : ?>
                        <li class="pillar-areas__item" style="--pillar-color: <?php echo esc_attr( $pillar_color ); ?>">
                            <strong><?php echo esc_html( $area['title'] ); ?></strong>
                            <p><?php echo esc_html( $area['description'] ); ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Publications in this Pillar -->
    <?php
    $publications_args = array(
        'post_type'      => 'publication',
        'posts_per_page' => 6,
    );
    
    if ( $pillar_term ) {
        $publications_args['tax_query'] = array(
            array(
                'taxonomy' => 'research_pillar',
                'field'    => 'term_id',
                'terms'    => $pillar_term->term_id,
            ),
        );
    }
    
    $publications = new WP_Query( $publications_args );
    
    if ( $publications->have_posts() ) :
    ?>
    <section class="section" style="background: var(--color-gray-50)">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e( 'Related Research', 'gloceps' ); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e( 'Publications', 'gloceps' ); ?></h2>
                <p class="section-header__description">
                    <?php printf( esc_html__( 'Research and analysis on %s issues.', 'gloceps' ), strtolower( get_the_title() ) ); ?>
                </p>
            </div>

            <div class="publications-grid reveal stagger-children">
                <?php
                while ( $publications->have_posts() ) :
                    $publications->the_post();
                    get_template_part( 'template-parts/components/publication-card' );
                endwhile;
                wp_reset_postdata();
                ?>
            </div>

            <div class="text-center" style="margin-top: var(--space-12)">
                <?php 
                $archive_url = get_post_type_archive_link( 'publication' );
                if ( $pillar_term ) {
                    $archive_url = get_term_link( $pillar_term );
                }
                ?>
                <a href="<?php echo esc_url( $archive_url ); ?>" class="btn btn--secondary">
                    <?php esc_html_e( 'View All Publications', 'gloceps' ); ?>
                    <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Events -->
    <?php
    $events_args = array(
        'post_type'      => 'event',
        'posts_per_page' => 3,
        'meta_key'       => 'event_date',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
    );
    
    if ( $pillar_term ) {
        $events_args['tax_query'] = array(
            array(
                'taxonomy' => 'research_pillar',
                'field'    => 'term_id',
                'terms'    => $pillar_term->term_id,
            ),
        );
    }
    
    $events = new WP_Query( $events_args );
    
    if ( $events->have_posts() ) :
    ?>
    <section class="section">
        <div class="container">
            <div class="section-header section-header--center reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e( 'Events', 'gloceps' ); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e( 'Related Dialogues', 'gloceps' ); ?></h2>
            </div>

            <div class="events-list reveal">
                <?php
                while ( $events->have_posts() ) :
                    $events->the_post();
                    get_template_part( 'template-parts/components/event-item' );
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="section cta-section" style="--pillar-color: <?php echo esc_attr( $pillar_color ); ?>">
        <div class="cta-section__bg" style="background: <?php echo esc_attr( $pillar_color ); ?>"></div>
        <div class="container">
            <div class="cta-section__content">
                <h2 class="cta-section__title"><?php esc_html_e( 'Explore Our Research', 'gloceps' ); ?></h2>
                <p class="cta-section__description">
                    <?php esc_html_e( 'Access our publications and contribute to the dialogue on critical policy issues.', 'gloceps' ); ?>
                </p>
                <div class="cta-section__actions">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--primary btn--lg" style="background: #fff; color: <?php echo esc_attr( $pillar_color ); ?>">
                        <?php esc_html_e( 'Browse Publications', 'gloceps' ); ?>
                    </a>
                    <?php 
                    $contact_page = get_page_by_path( 'contact' );
                    $contact_url = $contact_page ? get_permalink( $contact_page ) : '#contact';
                    ?>
                    <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--secondary btn--lg" style="border-color: rgba(255, 255, 255, 0.3); color: #fff">
                        <?php esc_html_e( 'Contact Us', 'gloceps' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
endwhile;

get_footer();

