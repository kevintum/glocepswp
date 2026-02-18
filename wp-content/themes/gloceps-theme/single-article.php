<?php
/**
 * Single Article Template
 * Matches article-single.html structure
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while (have_posts()) :
    the_post();
    
    // Get ACF fields
    $read_time = get_field('read_time') ?: 5;
    
    // Get authors (supports multiple)
    $authors = gloceps_get_article_authors();
    
    // Get category
    $categories = get_the_terms(get_the_ID(), 'article_category');
    $category_name = $categories && !is_wp_error($categories) ? $categories[0]->name : '';
    
    // Format date
    $date_display = date('F j, Y', strtotime(get_the_date('c')));
    
    // Get first author for header display (backward compatibility)
    $first_author = !empty($authors) ? $authors[0] : array(
        'name' => get_the_author(),
        'title' => '',
        'image' => gloceps_get_favicon_url(80),
        'type' => 'team'
    );
    
    // Get tags
    $tags = get_the_tags();
    
    // Get related articles (same category, excluding current)
    $related_args = array(
        'post_type' => 'article',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    if ($category_name && $categories && !is_wp_error($categories)) {
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'article_category',
                'field' => 'term_id',
                'terms' => $categories[0]->term_id,
            ),
        );
    }
    
    $related_articles = new WP_Query($related_args);
    
    // Get more articles for bottom section
    $more_args = array(
        'post_type' => 'article',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $more_articles = new WP_Query($more_args);
    ?>

    <main>
        <!-- Breadcrumb -->
        <?php
        $header_attrs = gloceps_get_page_header_attrs();
        ?>
        <section class="<?php echo esc_attr($header_attrs['classes']); ?>"<?php echo $header_attrs['style']; ?>>
            <div class="container">
                <?php gloceps_breadcrumbs(); ?>
            </div>
        </section>

        <!-- Article Header -->
        <article class="article-single">
            <header class="article-single__header">
                <div class="container">
                    <div class="article-single__meta">
                        <?php if ($category_name) : ?>
                        <span class="article-single__category"><?php echo esc_html($category_name); ?></span>
                        <?php endif; ?>
                        <span class="article-single__date"><?php echo esc_html($date_display); ?></span>
                        <span class="article-single__read-time"><?php echo esc_html($read_time); ?> <?php esc_html_e('min read', 'gloceps'); ?></span>
                    </div>
                    <h1 class="article-single__title"><?php the_title(); ?></h1>
                    <?php if (has_excerpt()) : ?>
                    <p class="article-single__lead"><?php the_excerpt(); ?></p>
                    <?php endif; ?>
                    <div class="article-single__author">
                        <img src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($author_name); ?>" class="article-single__author-image" />
                        <div class="article-single__author-info">
                            <strong><?php echo esc_html($author_name); ?></strong>
                            <?php if ($author_title) : ?>
                            <span><?php echo esc_html($author_title); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if (has_post_thumbnail()) : ?>
            <div class="article-single__featured">
                <div class="container">
                    <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Article Body -->
            <div class="article-single__body">
                <div class="container">
                    <div class="article-single__content">
                        <?php the_content(); ?>

                        <!-- Tags -->
                        <?php if ($tags && !is_wp_error($tags)) : ?>
                        <div class="article-single__tags">
                            <span><?php esc_html_e('Tags:', 'gloceps'); ?></span>
                            <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>"><?php echo esc_html($tag->name); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Social Share -->
                        <div class="article-single__share">
                            <span><?php esc_html_e('Share this article:', 'gloceps'); ?></span>
                            <?php
                            $share_url = urlencode(get_permalink());
                            $share_title = urlencode(get_the_title());
                            ?>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener"><?php esc_html_e('LinkedIn', 'gloceps'); ?></a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener"><?php esc_html_e('X', 'gloceps'); ?></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener"><?php esc_html_e('Facebook', 'gloceps'); ?></a>
                            <a href="mailto:?subject=<?php echo $share_title; ?>&body=<?php echo $share_url; ?>"><?php esc_html_e('Email', 'gloceps'); ?></a>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="article-single__sidebar">
                        <!-- Author Card -->
                        <?php if (!empty($authors)) : ?>
                        <div class="article-single__authors">
                            <?php foreach ($authors as $author) : ?>
                            <div class="article-single__author-card">
                                <img src="<?php echo esc_url($author['image']); ?>" alt="<?php echo esc_attr($author['name']); ?>" />
                                <h3><?php echo esc_html($author['name']); ?></h3>
                                <?php if ($author['title']) : ?>
                                <p><?php echo esc_html($author['title']); ?></p>
                                <?php endif; ?>
                                <?php if ($author['type'] === 'team' && $author['link']) : ?>
                                <a href="<?php echo esc_url($author['link']); ?>" class="btn btn--secondary btn--sm"><?php esc_html_e('View Profile', 'gloceps'); ?></a>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Related Articles -->
                        <?php if ($related_articles->have_posts()) : ?>
                        <div class="article-single__related">
                            <h3><?php esc_html_e('Related Articles', 'gloceps'); ?></h3>
                            <ul>
                                <?php while ($related_articles->have_posts()) : $related_articles->the_post(); ?>
                                <li>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        <?php wp_reset_postdata(); ?>
                    </aside>
                </div>
            </div>
        </article>

        <!-- More Articles -->
        <?php if ($more_articles->have_posts()) : ?>
        <section class="section section--gray">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-header__title"><?php esc_html_e('More Articles', 'gloceps'); ?></h2>
                </div>
                <div class="articles-grid">
                    <?php while ($more_articles->have_posts()) : $more_articles->the_post(); ?>
                        <?php get_template_part('template-parts/components/article-card'); ?>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </main>

<?php
endwhile;
get_footer();
?>

