<?php
/**
 * Flexible Content Block: Products Section
 *
 * @package GLOCEPS
 */

$section_title = get_sub_field('section_title');
$number_of_products = get_sub_field('number_of_products');
$filter_by_type = get_sub_field('filter_by_type');

$args = array(
    'post_type'      => 'publication',
    'posts_per_page' => $number_of_products ?: 12,
    'post_status'    => 'publish',
    'meta_query'     => array(
        array(
            'key'     => 'access_type',
            'value'   => 'premium',
            'compare' => '=',
        ),
    ),
);

if ( ! empty($filter_by_type) ) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'publication_type',
            'field'    => 'term_id',
            'terms'    => $filter_by_type,
        ),
    );
}

$products_query = new WP_Query($args);

// Get all publication types for filter
$publication_types = get_terms(array(
    'taxonomy'   => 'publication_type',
    'hide_empty' => true,
));
?>

<section class="section products-section">
    <div class="container">
        <?php if ( $section_title ) : ?>
            <div class="section-header reveal">
                <h2 class="section-header__title"><?php echo esc_html($section_title); ?></h2>
            </div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <?php if ( $publication_types && ! is_wp_error($publication_types) ) : ?>
            <div class="products-filter reveal">
                <button class="products-filter__btn products-filter__btn--active" data-filter="all">
                    <?php esc_html_e('All Publications', 'gloceps'); ?>
                </button>
                <?php foreach ( $publication_types as $type ) : ?>
                    <button class="products-filter__btn" data-filter="<?php echo esc_attr($type->slug); ?>">
                        <?php echo esc_html($type->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( $products_query->have_posts() ) : ?>
            <div class="products-grid reveal stagger-children">
                <?php while ( $products_query->have_posts() ) : $products_query->the_post(); 
                    $types = get_the_terms(get_the_ID(), 'publication_type');
                    $type_slug = $types && ! is_wp_error($types) ? $types[0]->slug : '';
                ?>
                    <div class="product-card" data-category="<?php echo esc_attr($type_slug); ?>">
                        <?php get_template_part('template-parts/components/publication-card'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p class="text-center"><?php esc_html_e('No premium publications available at this time.', 'gloceps'); ?></p>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.products-filter__btn');
    const productCards = document.querySelectorAll('.product-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('products-filter__btn--active'));
            btn.classList.add('products-filter__btn--active');
            
            // Filter products
            productCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

