<?php
/**
 * The front page template
 * 
 * This template uses ACF Flexible Content blocks when a page is set as
 * the static front page. The blocks are defined in inc/acf-fields.php
 * and rendered via template-parts/blocks/
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

// Check if we have ACF flexible content blocks
if ( have_rows('content_blocks') ) :
    // Render all content blocks from ACF
    while ( have_rows('content_blocks') ) : the_row();
        $layout = get_row_layout();
        
        // Convert layout name to template file name (e.g., hero_video -> block-hero-video.php)
        $template_name = 'template-parts/blocks/block-' . str_replace('_', '-', $layout);
        
        // Include the block template
        get_template_part($template_name);
        
    endwhile;
else :
    // Fallback: Show default hardcoded content when no blocks are set
    // This allows the site to work before pages are configured
    ?>

<!-- Hero - Immersive Full-Screen -->
<section class="hero hero--split">
    <!-- Content Side (Left) -->
    <div class="hero__content-block">
        <div class="hero__content-inner">
            <h1 class="hero__title">
                Research. <em>Knowledge.</em> Influence.
            </h1>

            <p class="hero__description">
                <?php esc_html_e( 'The Global Centre for Policy and Strategy (GLOCEPS) provides strategic linkage between experience and research, bringing together outstanding professionals, thought leaders, and academia to advance key issues on peace and security.', 'gloceps' ); ?>
            </p>

            <a href="#research" class="hero__link">
                <?php esc_html_e( 'Explore Our Work', 'gloceps' ); ?>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Image/Video Side (Right) -->
    <div class="hero__image hero__image--has-video">
        <img
            src="<?php echo esc_url( GLOCEPS_URI . '/assets/images/home-hero.png' ); ?>"
            alt="<?php esc_attr_e( 'GLOCEPS Panel Discussion', 'gloceps' ); ?>"
            class="hero__image-fallback"
        />
    </div>

    <div class="hero__scroll">
        <span><?php esc_html_e( 'Scroll', 'gloceps' ); ?></span>
        <div class="hero__scroll-line"></div>
    </div>
</section>

<main>
    <!-- Research Pillars - Bento Grid -->
    <section class="section" id="research">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e( 'Research Focus Areas', 'gloceps' ); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e( 'Our Five Pillars', 'gloceps' ); ?></h2>
                <p class="section-header__description">
                    <?php esc_html_e( 'GLOCEPS work cuts across five interconnected pillars addressing the most pressing challenges facing Eastern Africa and the broader region.', 'gloceps' ); ?>
                </p>
            </div>

            <div class="pillars-grid reveal">
                <?php
                $pillars = get_terms(
                    array(
                        'taxonomy'   => 'research_pillar',
                        'hide_empty' => false,
                        'orderby'    => 'term_id',
                    )
                );

                if ( $pillars && ! is_wp_error( $pillars ) ) :
                    $pillar_images = array(
                        'foreign-policy'                => 'foreign.jpg',
                        'security-defence'              => 'security.jpg',
                        'governance-ethics'             => 'governance.jpg',
                        'development'                   => 'development.jpg',
                        'transnational-organised-crimes' => 'tog.jpg',
                    );
                    
                    $pillar_descriptions = array(
                        'foreign-policy' => 'Analysing diplomatic relations, regional integration through the EAC and IGAD, and international partnerships shaping Eastern Africa\'s global engagement.',
                        'security-defence' => 'Examining national security frameworks, counter-terrorism, and regional defence cooperation.',
                        'governance-ethics' => 'Promoting accountability, democratic processes, and ethical leadership in public governance.',
                        'development' => 'Driving sustainable economic growth, innovation, and inclusive development strategies.',
                        'transnational-organised-crimes' => 'Combating trafficking, money laundering, and cross-border criminal networks.',
                    );

                    $counter = 1;
                    foreach ( $pillars as $pillar ) :
                        $pillar_slug = $pillar->slug;
                        $image_file = isset( $pillar_images[ $pillar_slug ] ) ? $pillar_images[ $pillar_slug ] : '';
                        $description = isset( $pillar_descriptions[ $pillar_slug ] ) ? $pillar_descriptions[ $pillar_slug ] : $pillar->description;
                        $pillar_page_url = gloceps_get_pillar_page( $pillar_slug );
                        $is_large = $counter === 1;
                ?>
                <article class="pillar-card<?php echo $is_large ? ' pillar-card--large' : ''; ?>">
                    <div class="pillar-card__bg">
                        <img
                            src="<?php echo esc_url( GLOCEPS_URI . '/assets/images/' . $image_file ); ?>"
                            alt="<?php echo esc_attr( $pillar->name ); ?>"
                            style="object-position: center center"
                        />
                    </div>
                    <span class="pillar-card__number"><?php echo sprintf( '%02d', $counter ); ?></span>
                    <div class="pillar-card__inner">
                        <div class="pillar-card__content">
                            <h3 class="pillar-card__title"><?php echo esc_html( $pillar->name ); ?></h3>
                            <p class="pillar-card__description"><?php echo esc_html( $description ); ?></p>
                            <a href="<?php echo esc_url( $pillar_page_url ); ?>" class="pillar-card__link">
                                <?php esc_html_e( 'Explore Research', 'gloceps' ); ?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
                <?php
                        $counter++;
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- Featured Publication -->
    <?php
    $featured_pub = gloceps_get_featured_publications( 1 );
    if ( $featured_pub->have_posts() ) :
        $featured_pub->the_post();
    ?>
    <section class="section section--compact" style="background: var(--color-gray-50)">
        <div class="container">
            <div class="pub-featured reveal">
                <div class="pub-featured__image">
                    <?php 
                    if ( has_post_thumbnail() ) {
                        the_post_thumbnail( 'gloceps-card' );
                    }
                    ?>
                </div>
                <div class="pub-featured__content">
                    <span class="pub-featured__label"><?php esc_html_e( 'Featured Insight', 'gloceps' ); ?></span>
                    <h2 class="pub-featured__title"><?php the_title(); ?></h2>
                    <p class="pub-featured__excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--lg">
                        <?php esc_html_e( 'Read Analysis', 'gloceps' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php 
        wp_reset_postdata();
    endif; 
    ?>

    <!-- Publications Feed -->
    <section class="section">
        <div class="container">
            <div class="two-col" style="gap: var(--space-20)">
                <div>
                    <div class="section-header reveal">
                        <div class="section-header__eyebrow">
                            <span class="eyebrow"><?php esc_html_e( 'Latest Research', 'gloceps' ); ?></span>
                        </div>
                        <h2 class="section-header__title"><?php esc_html_e( 'Publications', 'gloceps' ); ?></h2>
                        <p class="section-header__description">
                            <?php esc_html_e( 'Access our latest policy briefs, research papers, and strategic analyses shaping discourse on regional and global issues.', 'gloceps' ); ?>
                        </p>
                    </div>
                </div>
                <div class="reveal" style="display: flex; align-items: flex-end; justify-content: flex-end;">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--secondary">
                        <?php esc_html_e( 'View All Publications', 'gloceps' ); ?>
                    </a>
                </div>
            </div>

            <div class="publications-feed reveal">
                <?php
                $publications = gloceps_get_latest_publications( 5 );
                if ( $publications->have_posts() ) :
                    while ( $publications->have_posts() ) :
                        $publications->the_post();
                        get_template_part( 'template-parts/components/publication-item' );
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <p class="text-center" style="color: var(--color-gray-500); padding: var(--space-8);">
                    <?php esc_html_e( 'No publications yet. Add publications from the admin dashboard.', 'gloceps' ); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Impact Stats -->
    <section class="section stats-section">
        <div class="container">
            <div class="stats-grid reveal stagger-children">
                <?php
                $stats = array(
                    array( 'value' => '50+', 'label' => __( 'Research Publications', 'gloceps' ) ),
                    array( 'value' => '12', 'label' => __( 'Countries Engaged', 'gloceps' ) ),
                    array( 'value' => '35+', 'label' => __( 'Policy Dialogues', 'gloceps' ) ),
                    array( 'value' => '20+', 'label' => __( 'Expert Fellows', 'gloceps' ) ),
                );
                
                foreach ( $stats as $stat ) :
                ?>
                <div class="stat-item">
                    <div class="stat-item__value"><?php echo esc_html( $stat['value'] ); ?></div>
                    <div class="stat-item__label"><?php echo esc_html( $stat['label'] ); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Events -->
    <section class="section">
        <div class="container">
            <div class="section-header section-header--center reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e( 'Upcoming', 'gloceps' ); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e( 'Events & Dialogues', 'gloceps' ); ?></h2>
                <p class="section-header__description">
                    <?php esc_html_e( 'Join our policy dialogues, roundtables, and expert discussions shaping regional discourse on critical issues.', 'gloceps' ); ?>
                </p>
            </div>

            <div class="events-list reveal">
                <?php
                $events = gloceps_get_upcoming_events( 3 );
                if ( $events->have_posts() ) :
                    while ( $events->have_posts() ) :
                        $events->the_post();
                        get_template_part( 'template-parts/components/event-item' );
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <p class="text-center" style="color: var(--color-gray-500);">
                    <?php esc_html_e( 'No upcoming events at this time. Check back soon!', 'gloceps' ); ?>
                </p>
                <?php endif; ?>
            </div>

            <div class="text-center" style="margin-top: var(--space-12)">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" class="btn btn--ghost">
                    <?php esc_html_e( 'View All Events →', 'gloceps' ); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="cta-section__bg">
            <img
                src="<?php echo esc_url( GLOCEPS_URI . '/assets/images/cta-bg.jpg' ); ?>"
                alt="<?php esc_attr_e( 'Mount Kenya', 'gloceps' ); ?>"
            />
        </div>
        <div class="container">
            <div class="cta-section__content">
                <h2 class="cta-section__title"><?php esc_html_e( 'Access Our Research', 'gloceps' ); ?></h2>
                <p class="cta-section__description">
                    <?php esc_html_e( 'Explore in-depth analysis and strategic insights from GLOCEPS. Download free briefs or purchase comprehensive research papers to support evidence-based policy making in Eastern Africa.', 'gloceps' ); ?>
                </p>
                <div class="cta-section__actions">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'publication' ) ); ?>" class="btn btn--primary btn--lg">
                        <?php esc_html_e( 'Browse Publications', 'gloceps' ); ?>
                    </a>
                    <a href="#newsletter" class="btn btn--secondary btn--lg" style="border-color: rgba(255, 255, 255, 0.3); color: #fff">
                        <?php esc_html_e( 'Subscribe to Updates', 'gloceps' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
endif; // End have_rows check

get_footer();
