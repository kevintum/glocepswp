<?php
/**
 * Block: Team Grid
 * 
 * Displays team members in a grid
 * 
 * @package GLOCEPS
 */

$eyebrow = get_sub_field('eyebrow') ?: 'Our Team';
$title = get_sub_field('title');
$description = get_sub_field('description');
$category = get_sub_field('category');
$pillar_slug = get_sub_field('pillar_slug'); // Filter by research pillar slug
$count = get_sub_field('count') ?: -1;
$view_all_link = get_sub_field('view_all_link'); // "View Full Team" link
$secondary_cta = get_sub_field('secondary_cta'); // Secondary CTA (e.g., "Council of Advisors")
$show_filter = get_sub_field('show_filter'); // Show category filter
$simple_layout = get_sub_field('simple_layout'); // Use simple card layout (no bio link)
$anchor_id = get_sub_field('anchor_id') ?: 'team';

// Build query args
$args = array(
    'post_type' => 'team_member',
    'posts_per_page' => $count,
    'orderby' => 'meta_value_num',
    'meta_key' => 'display_order',
    'order' => 'ASC',
);

// Filter by category if set
if ($category) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'team_category',
            'field' => 'term_id',
            'terms' => $category,
        ),
    );
}

// Filter by research pillar if set
if ($pillar_slug) {
    $pillar_term = get_term_by('slug', $pillar_slug, 'research_pillar');
    
    if ($pillar_term && !is_wp_error($pillar_term)) {
        // ACF taxonomy field stores term IDs but doesn't sync to WordPress taxonomy
        // Query ACF field directly - ACF stores as serialized array
        $term_id = $pillar_term->term_id;
        
        // Pattern 1: Single value in array: a:1:{i:0;i:25;}
        // Pattern 2: Multiple values: a:2:{i:0;i:28;i:1;i:25;}
        // Use LIKE to match either pattern
        $args['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key' => 'research_pillars',
                'value' => 'i:' . $term_id . ';', // Matches serialized integer in array
                'compare' => 'LIKE',
            ),
            array(
                'key' => 'research_pillars',
                'value' => 's:' . strlen(strval($term_id)) . ':"' . $term_id . '"', // Matches serialized string
                'compare' => 'LIKE',
            ),
        );
    }
}

$team = new WP_Query($args);
?>

<section class="section team-section" id="<?php echo esc_attr($anchor_id); ?>" style="<?php echo $anchor_id === 'team' ? 'background: var(--color-gray-50);' : ''; ?>">
    <div class="container">
        <div class="section-header <?php echo $view_all_link ? 'section-header--with-action' : ''; ?> reveal">
            <div>
                <?php if ($eyebrow) : ?>
                    <div class="section-header__eyebrow">
                        <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($title) : ?>
                    <h2 class="section-header__title"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>
                
                <?php if ($description) : ?>
                    <p class="section-header__description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if ($view_all_link) : 
                $view_all_url = is_array($view_all_link) ? ($view_all_link['url'] ?? home_url('/team/')) : home_url('/team/');
                $view_all_title = is_array($view_all_link) ? ($view_all_link['title'] ?? 'View Full Team') : 'View Full Team';
            ?>
                <a href="<?php echo esc_url($view_all_url); ?>" class="btn btn--secondary">
                    <?php echo esc_html($view_all_title); ?>
                    <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
        
        <?php 
        // Show category filter if enabled and no specific category is selected
        if ($show_filter && !$category) :
            $team_categories = get_terms(array(
                'taxonomy' => 'team_category',
                'hide_empty' => true,
            ));
            
            if (!empty($team_categories) && !is_wp_error($team_categories)) :
        ?>
            <div class="team-filter reveal" style="margin-bottom: var(--space-8);">
                <select class="team-filter__select" id="teamCategoryFilter">
                    <option value="">All Categories</option>
                    <?php foreach ($team_categories as $term) : ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php 
            endif;
        endif; 
        ?>

        <?php if ($team->have_posts()) : ?>
            <div class="team-grid <?php echo $anchor_id === 'team' ? 'team-grid--5' : ''; ?> reveal stagger-children">
                <?php while ($team->have_posts()) : $team->the_post(); ?>
                    <?php if ($simple_layout) : ?>
                        <?php get_template_part('template-parts/components/team-card-simple'); ?>
                    <?php else : ?>
                        <?php get_template_part('template-parts/components/team-card'); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="no-results">No team members found.</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
        
        <?php if ($view_all_link || $secondary_cta) : ?>
            <div class="text-center reveal" style="margin-top: var(--space-12);">
                <?php if ($view_all_link) : 
                    $view_all_url = is_array($view_all_link) ? ($view_all_link['url'] ?? home_url('/team/')) : home_url('/team/');
                    $view_all_title = is_array($view_all_link) ? ($view_all_link['title'] ?? 'View Full Team') : 'View Full Team';
                ?>
                    <a href="<?php echo esc_url($view_all_url); ?>" class="btn btn--secondary">
                        <?php echo esc_html($view_all_title); ?>
                        <svg class="btn__arrow" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                <?php endif; ?>
                
                <?php if ($secondary_cta) : 
                    $secondary_url = is_array($secondary_cta) ? ($secondary_cta['url'] ?? '#') : $secondary_cta;
                    $secondary_title = is_array($secondary_cta) ? ($secondary_cta['title'] ?? 'Learn More') : 'Learn More';
                    $secondary_target = is_array($secondary_cta) ? ($secondary_cta['target'] ?? '') : '';
                ?>
                    <a href="<?php echo esc_url($secondary_url); ?>" class="btn btn--ghost" style="margin-left: var(--space-4);" <?php echo $secondary_target ? 'target="' . esc_attr($secondary_target) . '"' : ''; ?>>
                        <?php echo esc_html($secondary_title); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

