<?php
/**
 * Template for displaying Research Pillar taxonomy archive
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

$term = get_queried_object();
$term_description = term_description($term->term_id, 'research_pillar');
$term_icon = get_field('pillar_icon', $term);
$term_image = get_field('pillar_image', $term);
$lead_text = get_field('lead_text', $term);
$stats = get_field('pillar_stats', $term);
$focus_areas = get_field('focus_areas', $term);

// Default icon based on pillar slug
$default_icons = array(
    'foreign-policy' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />',
    'security-defence' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />',
    'governance-ethics' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z" />',
    'development' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />',
    'transnational-organised-crimes' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
);

$icon_path = isset($default_icons[$term->slug]) ? $default_icons[$term->slug] : $default_icons['foreign-policy'];

// Default images based on pillar
$default_images = array(
    'foreign-policy' => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=1600&q=80',
    'security-defence' => 'https://images.unsplash.com/photo-1569974507005-6dc61f97fb5c?w=1600&q=80',
    'governance-ethics' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=1600&q=80',
    'development' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=1600&q=80',
    'transnational-organised-crimes' => 'https://images.unsplash.com/photo-1589994160839-163cd867cfe8?w=1600&q=80',
);

$hero_image = $term_image ? $term_image['url'] : (isset($default_images[$term->slug]) ? $default_images[$term->slug] : $default_images['foreign-policy']);
?>

<!-- ============================================
   PILLAR HERO
   ============================================ -->
<section class="pillar-hero--split">
    <div class="pillar-hero__content-block">
        <div class="pillar-hero__content-inner">
            <?php gloceps_breadcrumbs(); ?>
            <div class="pillar-hero__badge">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <?php echo $icon_path; ?>
                </svg>
                <?php esc_html_e('Research Pillar', 'gloceps'); ?>
            </div>
            <h1 class="pillar-hero__title"><?php echo esc_html($term->name); ?></h1>
            <?php if ($term_description) : ?>
            <p class="pillar-hero__description"><?php echo wp_kses_post($term_description); ?></p>
            <?php endif; ?>
            <a href="#focus-areas" class="pillar-hero__cta">
                <?php esc_html_e('Explore Our Work', 'gloceps'); ?>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </a>
        </div>
    </div>
    <div class="pillar-hero__image">
        <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr($term->name); ?>" />
    </div>
</section>

<main>
    <!-- ============================================
       PILLAR INTRO
       ============================================ -->
    <section class="section pillar-intro">
        <div class="container">
            <div class="pillar-intro__grid">
                <div class="pillar-intro__content reveal">
                    <?php if ($lead_text) : ?>
                    <p class="pillar-intro__lead"><?php echo wp_kses_post($lead_text); ?></p>
                    <?php elseif ($term_description) : ?>
                    <p class="pillar-intro__lead"><?php echo wp_kses_post($term_description); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ($stats && is_array($stats)) : ?>
                <div class="pillar-intro__stats reveal reveal--delay-1">
                    <?php foreach ($stats as $stat) : ?>
                    <div class="pillar-stat">
                        <span class="pillar-stat__value"><?php echo esc_html($stat['value']); ?></span>
                        <span class="pillar-stat__label"><?php echo esc_html($stat['label']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <div class="pillar-intro__stats reveal reveal--delay-1">
                    <div class="pillar-stat">
                        <span class="pillar-stat__value">15<span>+</span></span>
                        <span class="pillar-stat__label"><?php esc_html_e('Policy Papers Published', 'gloceps'); ?></span>
                    </div>
                    <div class="pillar-stat">
                        <span class="pillar-stat__value">8</span>
                        <span class="pillar-stat__label"><?php esc_html_e('Countries Engaged', 'gloceps'); ?></span>
                    </div>
                    <div class="pillar-stat">
                        <span class="pillar-stat__value">20<span>+</span></span>
                        <span class="pillar-stat__label"><?php esc_html_e('Expert Contributors', 'gloceps'); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ============================================
       FOCUS AREAS
       ============================================ -->
    <?php if ($focus_areas && is_array($focus_areas)) : ?>
    <section class="section focus-areas" id="focus-areas" style="background: var(--color-gray-50)">
        <div class="container">
            <div class="section-header section-header--center reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e('Our Prospects', 'gloceps'); ?></span>
                </div>
                <h2 class="section-header__title"><?php printf(esc_html__('How We Support %s', 'gloceps'), esc_html($term->name)); ?></h2>
            </div>

            <?php 
            $i = 0;
            foreach ($focus_areas as $area) : 
                $i++;
                $reverse_class = ($i % 2 === 0) ? ' focus-block--reverse' : '';
            ?>
            <div class="focus-block<?php echo esc_attr($reverse_class); ?> reveal">
                <?php if (!empty($area['image'])) : ?>
                <div class="focus-block__image">
                    <img src="<?php echo esc_url($area['image']['url']); ?>" alt="<?php echo esc_attr($area['title']); ?>" />
                </div>
                <?php endif; ?>
                <div class="focus-block__content">
                    <span class="focus-block__number"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></span>
                    <h3 class="focus-block__title"><?php echo esc_html($area['title']); ?></h3>
                    <?php if (!empty($area['description'])) : ?>
                    <p class="focus-block__text"><?php echo wp_kses_post($area['description']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($area['features'])) : ?>
                    <ul class="focus-block__list">
                        <?php foreach ($area['features'] as $feature) : ?>
                        <li><?php echo esc_html($feature['text']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============================================
       RELATED PUBLICATIONS
       ============================================ -->
    <section class="section publications-section">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e('Research Output', 'gloceps'); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e('Latest Publications', 'gloceps'); ?></h2>
            </div>

            <div class="publications-grid reveal stagger-children">
                <?php
                $publications = new WP_Query(array(
                    'post_type' => 'publication',
                    'posts_per_page' => 6,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'research_pillar',
                            'field' => 'term_id',
                            'terms' => $term->term_id,
                        ),
                    ),
                ));

                if ($publications->have_posts()) :
                    while ($publications->have_posts()) :
                        $publications->the_post();
                        get_template_part('template-parts/components/publication-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="no-results"><?php esc_html_e('No publications found in this research pillar.', 'gloceps'); ?></p>
                    <?php
                endif;
                ?>
            </div>

            <?php if ($publications->have_posts()) : ?>
            <div class="section-footer reveal">
                <a href="<?php echo esc_url(add_query_arg('research_pillar', $term->slug, get_post_type_archive_link('publication'))); ?>" class="btn btn--outline">
                    <?php printf(esc_html__('View All %s Publications', 'gloceps'), esc_html($term->name)); ?>
                    <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============================================
       RELATED EVENTS
       ============================================ -->
    <?php
    $events = new WP_Query(array(
        'post_type' => 'event',
        'posts_per_page' => 3,
        'tax_query' => array(
            array(
                'taxonomy' => 'research_pillar',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ),
        ),
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
    ));

    if ($events->have_posts()) :
    ?>
    <section class="section section--gray">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e('Events', 'gloceps'); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e('Related Events', 'gloceps'); ?></h2>
            </div>

            <div class="events-grid reveal stagger-children">
                <?php
                while ($events->have_posts()) :
                    $events->the_post();
                    get_template_part('template-parts/components/event-card');
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============================================
       CTA SECTION
       ============================================ -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-section__inner reveal">
                <div class="cta-section__content">
                    <h2 class="cta-section__title"><?php printf(esc_html__('Explore %s Research', 'gloceps'), esc_html($term->name)); ?></h2>
                    <p class="cta-section__description">
                        <?php esc_html_e('Access our comprehensive research output and contribute to evidence-based policy making in Eastern Africa.', 'gloceps'); ?>
                    </p>
                </div>
                <div class="cta-section__actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('publication')); ?>" class="btn btn--white btn--lg">
                        <?php esc_html_e('Browse All Publications', 'gloceps'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn--outline-light btn--lg">
                        <?php esc_html_e('Partner With Us', 'gloceps'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

