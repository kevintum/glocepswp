<?php
/**
 * Single template for Publications
 * Matches publication-single.html structure exactly
 *
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while (have_posts()) : the_post();
    
    // Get ACF fields
    $access_type = get_field('access_type') ?: 'free';
    $price = gloceps_get_publication_price();
    $publication_format = get_field('publication_format') ?: 'pdf';
    $pdf_file = get_field('pdf_file');
    $page_count = get_field('page_count');
    $language = get_field('language') ?: 'en';
    $doi = get_field('doi');
    $isbn = get_field('isbn');
    $download_count = (int) get_field('download_count') ?: 0;
    $abstract = get_field('abstract') ?: get_the_excerpt();
    $executive_summary = get_field('executive_summary');
    $reading_time = get_field('reading_time') ?: gloceps_reading_time();
    
    // Get taxonomies
    $pillars = get_the_terms(get_the_ID(), 'research_pillar');
    $types = get_the_terms(get_the_ID(), 'publication_type');
    $tags = get_the_tags();
    
    // Get author info
    $author_info = gloceps_get_publication_author();
    
    // Generate TOC
    $toc = gloceps_generate_publication_toc();
    
    // Get featured image
    $featured_image_url = '';
    if (has_post_thumbnail()) {
        $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
    }
    if (!$featured_image_url) {
        $featured_image_url = gloceps_get_favicon_url(192);
    }
    
    // Format date
    $date_display = get_the_date('F j, Y');
    $date_display_short = get_the_date('M Y');
    
    // Language display
    $language_names = array(
        'en' => 'English',
        'sw' => 'Swahili',
        'fr' => 'French',
    );
    $language_display = $language_names[$language] ?? 'English';
    
    // Format display
    $format_display = ($publication_format === 'pdf') ? 'PDF Document' : 'Online Article';
    
    // Get related publications
    $related_args = array(
        'post_type' => 'publication',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    if ($pillars && !is_wp_error($pillars)) {
        $pillar_ids = wp_list_pluck($pillars, 'term_id');
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'research_pillar',
                'field' => 'term_id',
                'terms' => $pillar_ids,
            ),
        );
    }
    
    $related_publications = new WP_Query($related_args);
    
    // Get more publications for bottom section
    $more_exclude = array_merge(array(get_the_ID()), wp_list_pluck($related_publications->posts, 'ID'));
    $more_args = array(
        'post_type' => 'publication',
        'posts_per_page' => 3,
        'post__not_in' => $more_exclude,
        'orderby' => 'rand',
    );
    $more_publications = new WP_Query($more_args);
    
    // Get prev/next publications
    $prev_post = get_previous_post();
    $next_post = get_next_post();
    
    // Social share URLs
    $share_url = urlencode(get_permalink());
    $share_title = urlencode(get_the_title());
    
    // WooCommerce product link
    $wc_product_id = get_field('wc_product');
    $add_to_cart_url = '';
    if ($wc_product_id && class_exists('WooCommerce')) {
        $product = wc_get_product($wc_product_id);
        if ($product) {
            $add_to_cart_url = $product->add_to_cart_url();
        }
    }
    
    // PDF download URL
    $pdf_download_url = '';
    if ($pdf_file && is_array($pdf_file) && !empty($pdf_file['url'])) {
        $pdf_download_url = $pdf_file['url'];
    }
?>

<main>
    <article class="publication-single">
        <!-- Split Header -->
        <header class="publication-header--split">
            <div class="publication-header__content-block">
                <div class="publication-header__content-inner">
                    <nav class="publication-header__breadcrumb" aria-label="Breadcrumb">
                        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'gloceps'); ?></a>
                        <span>/</span>
                        <a href="<?php echo esc_url(get_post_type_archive_link('publication')); ?>"><?php esc_html_e('Publications', 'gloceps'); ?></a>
                        <?php if ($types && !is_wp_error($types)) : ?>
                        <span>/</span>
                        <a href="<?php echo esc_url(add_query_arg('type', $types[0]->slug, get_post_type_archive_link('publication'))); ?>"><?php echo esc_html($types[0]->name); ?></a>
                        <?php endif; ?>
                    </nav>

                    <div class="publication-header__badges">
                        <?php if ($types && !is_wp_error($types)) : ?>
                        <span class="publication-header__badge publication-header__badge--type-light"><?php echo esc_html($types[0]->name); ?></span>
                        <?php endif; ?>
                        <?php if ($pillars && !is_wp_error($pillars)) : ?>
                        <a href="<?php echo esc_url(get_term_link($pillars[0])); ?>" class="publication-header__badge publication-header__badge--pillar-light"><?php echo esc_html($pillars[0]->name); ?></a>
                        <?php endif; ?>
                        <?php if ($access_type === 'free') : ?>
                        <span class="publication-header__badge publication-header__badge--free"><?php esc_html_e('Free Access', 'gloceps'); ?></span>
                        <?php endif; ?>
                    </div>

                    <h1 class="publication-header__title"><?php the_title(); ?></h1>

                    <div class="publication-header__meta">
                        <div class="publication-header__meta-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html($date_display); ?></time>
                        </div>
                        <div class="publication-header__meta-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span><?php echo esc_html($author_info['name'] ?: get_the_author()); ?></span>
                        </div>
                        <?php if ($reading_time) : ?>
                        <div class="publication-header__meta-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span><?php printf(esc_html__('%d min read', 'gloceps'), $reading_time); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="publication-header__meta-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span><?php echo esc_html($format_display); ?></span>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="publication-header__actions">
                        <?php if ($access_type === 'free' && $pdf_download_url) : ?>
                        <a href="<?php echo esc_url($pdf_download_url); ?>" class="btn btn--white" download onclick="gloceps_track_publication_download(<?php echo get_the_ID(); ?>);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <?php esc_html_e('Download PDF', 'gloceps'); ?>
                        </a>
                        <?php elseif ($access_type === 'premium' && $wc_product_id) : 
                            $product = wc_get_product($wc_product_id);
                            if ($product && $product->is_purchasable()) :
                        ?>
                        <form class="publication-header__add-to-cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr( $wc_product_id ); ?>">
                            <?php wp_nonce_field( 'woocommerce-add_to_cart', 'woocommerce-add-to-cart-nonce' ); ?>
                            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $wc_product_id ); ?>" />
                            <input type="hidden" name="product_id" value="<?php echo esc_attr( $wc_product_id ); ?>" />
                            <button type="submit" class="btn btn--white add_to_cart_button ajax_add_to_cart" aria-label="<?php esc_attr_e( 'Add to cart', 'gloceps' ); ?>" data-product_id="<?php echo esc_attr( $wc_product_id ); ?>">
                                <?php esc_html_e('Purchase Publication', 'gloceps'); ?>
                            </button>
                        </form>
                        <?php endif; endif; ?>
                        <div class="publication-header__share">
                            <span><?php esc_html_e('Share:', 'gloceps'); ?></span>
                            <button onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>', '_blank', 'width=600,height=400');" aria-label="<?php esc_attr_e('Share on LinkedIn', 'gloceps'); ?>">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </button>
                            <button onclick="window.open('https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>', '_blank', 'width=600,height=400');" aria-label="<?php esc_attr_e('Share on X', 'gloceps'); ?>">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </button>
                            <button onclick="navigator.clipboard.writeText('<?php echo esc_js(get_permalink()); ?>'); alert('<?php esc_attr_e('Link copied to clipboard!', 'gloceps'); ?>');" aria-label="<?php esc_attr_e('Copy link', 'gloceps'); ?>">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="publication-header__image">
                <img src="<?php echo esc_url($featured_image_url); ?>" alt="<?php the_title_attribute(); ?>" />
            </div>
        </header>

        <!-- Publication Body -->
        <div class="publication-body">
            <div class="container">
                <div class="publication-layout">
                    <!-- Main Content -->
                    <div class="publication-content reveal">
                        <!-- Abstract -->
                        <?php if ($abstract) : ?>
                        <div class="publication-abstract">
                            <h2 class="publication-abstract__title"><?php esc_html_e('Abstract', 'gloceps'); ?></h2>
                            <p class="publication-abstract__text"><?php echo esc_html($abstract); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Executive Summary -->
                        <?php if ($executive_summary) : ?>
                        <div class="publication-prose">
                            <h2><?php esc_html_e('Executive Summary', 'gloceps'); ?></h2>
                            <?php echo wp_kses_post($executive_summary); ?>
                        </div>
                        <?php endif; ?>

                        <!-- PDF Embed (Auto-inserted if PDF exists and publication is FREE) -->
                        <?php if ($access_type === 'free' && $publication_format === 'pdf' && $pdf_file && is_array($pdf_file) && !empty($pdf_file['url'])) : ?>
                        <div class="publication-pdf-embed">
                            <iframe 
                                src="<?php echo esc_url($pdf_file['url']); ?>#toolbar=1&navpanes=1&scrollbar=1" 
                                width="100%" 
                                height="800" 
                                style="border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg);"
                                title="<?php echo esc_attr(get_the_title()); ?> PDF">
                                <p><?php esc_html_e('Your browser does not support PDFs.', 'gloceps'); ?> 
                                <a href="<?php echo esc_url($pdf_file['url']); ?>" target="_blank" rel="noopener">
                                    <?php esc_html_e('Download the PDF', 'gloceps'); ?>
                                </a></p>
                            </iframe>
                        </div>
                        <?php endif; ?>

                        <!-- Main Content -->
                        <div class="publication-prose" id="publicationContent">
                            <?php
                            // Add IDs to headings for TOC navigation
                            $content = get_the_content();
                            $content = preg_replace_callback(
                                '/<h([2-4])[^>]*>(.*?)<\/h[2-4]>/i',
                                function($matches) {
                                    $level = $matches[1];
                                    $title = strip_tags($matches[2]);
                                    $id = 'section-' . sanitize_title($title);
                                    return '<h' . $level . ' id="' . esc_attr($id) . '">' . $matches[2] . '</h' . $level . '>';
                                },
                                $content
                            );
                            echo apply_filters('the_content', $content);
                            ?>
                        </div>

                        <!-- Tags -->
                        <?php if ($tags && !is_wp_error($tags)) : ?>
                        <div class="publication-tags">
                            <h3 class="publication-tags__title"><?php esc_html_e('Tags:', 'gloceps'); ?></h3>
                            <div class="publication-tags__list">
                                <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="publication-tag"><?php echo esc_html($tag->name); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Author Bio -->
                        <div class="publication-author">
                            <h3 class="publication-author__heading"><?php esc_html_e('About the Author', 'gloceps'); ?></h3>
                            <div class="publication-author__card">
                                <div class="publication-author__image">
                                    <img src="<?php echo esc_url($author_info['image']); ?>" alt="<?php echo esc_attr($author_info['name']); ?>" />
                                </div>
                                <div class="publication-author__content">
                                    <h4 class="publication-author__name"><?php echo esc_html($author_info['name']); ?></h4>
                                    <?php if ($author_info['title']) : ?>
                                    <p class="publication-author__role"><?php echo esc_html($author_info['title']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($author_info['bio']) : ?>
                                    <p class="publication-author__bio"><?php echo esc_html($author_info['bio']); ?></p>
                                    <?php endif; ?>
                                    <div class="publication-author__links">
                                        <?php if ($author_info['type'] === 'team' && $author_info['link']) : ?>
                                        <a href="<?php echo esc_url($author_info['link']); ?>" class="publication-author__link"><?php esc_html_e('View Profile', 'gloceps'); ?></a>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url(add_query_arg('author', sanitize_title($author_info['name']), get_post_type_archive_link('publication'))); ?>" class="publication-author__link"><?php esc_html_e('More Publications', 'gloceps'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="publication-sidebar reveal reveal--delay-1">
                        <!-- Publication Details -->
                        <div class="publication-sidebar__card">
                            <h3 class="publication-sidebar__title"><?php esc_html_e('Publication Details', 'gloceps'); ?></h3>
                            <dl class="publication-details">
                                <?php if ($types && !is_wp_error($types)) : ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Type', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html($types[0]->name); ?></dd>
                                </div>
                                <?php endif; ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Published', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html($date_display); ?></dd>
                                </div>
                                <?php if ($pillars && !is_wp_error($pillars)) : ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Research Pillar', 'gloceps'); ?></dt>
                                    <dd><a href="<?php echo esc_url(get_term_link($pillars[0])); ?>"><?php echo esc_html($pillars[0]->name); ?></a></dd>
                                </div>
                                <?php endif; ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Format', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html($format_display); ?></dd>
                                </div>
                                <?php if ($page_count) : ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Pages', 'gloceps'); ?></dt>
                                    <dd><?php printf(esc_html__('%d pages', 'gloceps'), $page_count); ?></dd>
                                </div>
                                <?php endif; ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Language', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html($language_display); ?></dd>
                                </div>
                                <?php if ($doi) : ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('DOI', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html($doi); ?></dd>
                                </div>
                                <?php endif; ?>
                                <?php if ($isbn) : ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('ISBN', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html($isbn); ?></dd>
                                </div>
                                <?php endif; ?>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Access', 'gloceps'); ?></dt>
                                    <dd>
                                        <?php if ($access_type === 'free') : ?>
                                        <span class="badge badge--success"><?php esc_html_e('Free', 'gloceps'); ?></span>
                                        <?php else : ?>
                                        <span class="badge badge--warning"><?php esc_html_e('Premium', 'gloceps'); ?></span>
                                        <?php endif; ?>
                                    </dd>
                                </div>
                                <div class="publication-details__row">
                                    <dt><?php esc_html_e('Downloads', 'gloceps'); ?></dt>
                                    <dd><?php echo esc_html(number_format($download_count)); ?></dd>
                                </div>
                            </dl>

                            <!-- Download/Purchase CTA -->
                            <div class="publication-sidebar__cta">
                                <?php if ($access_type === 'free' && $pdf_download_url) : ?>
                                <a href="<?php echo esc_url($pdf_download_url); ?>" class="btn btn--primary btn--block" download onclick="gloceps_track_publication_download(<?php echo get_the_ID(); ?>);">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <?php esc_html_e('Download PDF', 'gloceps'); ?>
                                </a>
                                <?php elseif ($access_type === 'premium') : ?>
                                <?php if ($price) : ?>
                                <div class="publication-sidebar__price">
                                    <span class="publication-sidebar__price-label"><?php esc_html_e('Price', 'gloceps'); ?></span>
                                    <span class="publication-sidebar__price-value"><?php echo gloceps_format_price($price); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($wc_product_id) : 
                                    $product = wc_get_product($wc_product_id);
                                    if ($product && $product->is_purchasable()) :
                                ?>
                                <form class="publication-sidebar__add-to-cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr( $wc_product_id ); ?>">
                                    <?php wp_nonce_field( 'woocommerce-add_to_cart', 'woocommerce-add-to-cart-nonce' ); ?>
                                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $wc_product_id ); ?>" />
                                    <input type="hidden" name="product_id" value="<?php echo esc_attr( $wc_product_id ); ?>" />
                                    <button type="submit" class="btn btn--primary btn--block add_to_cart_button ajax_add_to_cart" aria-label="<?php esc_attr_e( 'Add to cart', 'gloceps' ); ?>" data-product_id="<?php echo esc_attr( $wc_product_id ); ?>">
                                        <?php esc_html_e('Add to Cart', 'gloceps'); ?>
                                    </button>
                                </form>
                                <?php else : ?>
                                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn--primary btn--block">
                                    <?php esc_html_e('Purchase Publication', 'gloceps'); ?>
                                </a>
                                <?php endif; endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Table of Contents -->
                        <?php if (!empty($toc)) : ?>
                        <div class="publication-sidebar__card publication-toc">
                            <h3 class="publication-sidebar__title"><?php esc_html_e('Table of Contents', 'gloceps'); ?></h3>
                            <nav class="publication-toc__nav">
                                <?php foreach ($toc as $item) : ?>
                                <a href="#<?php echo esc_attr($item['id']); ?>" 
                                   class="publication-toc__link <?php echo $item['level'] > 2 ? 'publication-toc__link--sub' : ''; ?>">
                                    <?php echo esc_html($item['title']); ?>
                                </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                        <?php endif; ?>

                        <!-- Related Publications -->
                        <?php if ($related_publications->have_posts()) : ?>
                        <div class="publication-sidebar__card">
                            <h3 class="publication-sidebar__title"><?php esc_html_e('Related Publications', 'gloceps'); ?></h3>
                            <div class="related-publications">
                                <?php while ($related_publications->have_posts()) : $related_publications->the_post(); ?>
                                <a href="<?php the_permalink(); ?>" class="related-publication">
                                    <div class="related-publication__image">
                                        <?php if (has_post_thumbnail()) : ?>
                                        <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>" />
                                        <?php else : ?>
                                        <img src="<?php echo esc_url(gloceps_get_favicon_url(80)); ?>" alt="<?php the_title_attribute(); ?>" />
                                        <?php endif; ?>
                                    </div>
                                    <div class="related-publication__content">
                                        <h4 class="related-publication__title"><?php the_title(); ?></h4>
                                        <span class="related-publication__date"><?php echo esc_html($date_display_short); ?></span>
                                    </div>
                                </a>
                                <?php endwhile; ?>
                            </div>
                            <?php if ($pillars && !is_wp_error($pillars)) : ?>
                            <a href="<?php echo esc_url(add_query_arg('pillar', $pillars[0]->slug, get_post_type_archive_link('publication'))); ?>" class="publication-sidebar__more">
                                <?php printf(esc_html__('View All %s Publications', 'gloceps'), $pillars[0]->name); ?>
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php wp_reset_postdata(); endif; ?>
                    </aside>
                </div>
            </div>
        </div>

        <!-- Prev/Next Navigation -->
        <?php if ($prev_post || $next_post) : ?>
        <nav class="publication-nav">
            <div class="container">
                <div class="publication-nav__inner">
                    <?php if ($prev_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="publication-nav__link publication-nav__link--prev">
                        <span class="publication-nav__label">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            <?php esc_html_e('Previous', 'gloceps'); ?>
                        </span>
                        <span class="publication-nav__title"><?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
                    </a>
                    <?php endif; ?>
                    <?php if ($next_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="publication-nav__link publication-nav__link--next">
                        <span class="publication-nav__label">
                            <?php esc_html_e('Next', 'gloceps'); ?>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                        <span class="publication-nav__title"><?php echo esc_html(get_the_title($next_post->ID)); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        <?php endif; ?>
    </article>

    <!-- More Publications -->
    <?php if ($more_publications->have_posts()) : ?>
    <section class="section more-publications" style="background: var(--color-gray-50);">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-header__eyebrow">
                    <span class="eyebrow"><?php esc_html_e('Explore More', 'gloceps'); ?></span>
                </div>
                <h2 class="section-header__title"><?php esc_html_e('More Publications', 'gloceps'); ?></h2>
            </div>
            <div class="more-publications__grid reveal stagger-children">
                <?php while ($more_publications->have_posts()) : $more_publications->the_post(); ?>
                    <?php get_template_part('template-parts/components/publication-card'); ?>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php wp_reset_postdata(); endif; ?>
</main>

<script>
// Track publication download
function gloceps_track_publication_download(postId) {
    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=gloceps_track_download&post_id=' + postId + '&nonce=<?php echo wp_create_nonce('gloceps_download_nonce'); ?>'
    });
}

// Smooth scroll for TOC links
document.addEventListener('DOMContentLoaded', function() {
    const tocLinks = document.querySelectorAll('.publication-toc__link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Update active TOC link
                tocLinks.forEach(l => l.classList.remove('publication-toc__link--active'));
                this.classList.add('publication-toc__link--active');
            }
        });
    });
    
    // Highlight active TOC item on scroll
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                tocLinks.forEach(link => {
                    link.classList.remove('publication-toc__link--active');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('publication-toc__link--active');
                    }
                });
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('#publicationContent h2, #publicationContent h3, #publicationContent h4').forEach(heading => {
        if (heading.id) {
            observer.observe(heading);
        }
    });
});
</script>

<?php
endwhile;

get_footer();
?>
