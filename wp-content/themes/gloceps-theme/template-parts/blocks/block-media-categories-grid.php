<?php
/**
 * Flexible Content Block: Media Categories Grid
 *
 * @package GLOCEPS
 */

$categories = get_sub_field('categories');

$icons = array(
    'videos' => '<svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z"/></svg>',
    'podcasts' => '<svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/></svg>',
    'gallery' => '<svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>',
    'articles' => '<svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>',
);
?>

<?php if ( $categories ) : ?>
<section class="section media-categories">
    <div class="container">
        <div class="media-categories__grid reveal stagger-children">
            <?php foreach ( $categories as $category ) : 
                $type = $category['type'];
                $cat_title = $category['title'];
                $cat_description = $category['description'];
                $cat_link = $category['link'];
                $cat_image = $category['image'];
            ?>
                <a href="<?php echo esc_url($cat_link['url']); ?>" class="media-category-card" target="<?php echo esc_attr($cat_link['target']); ?>">
                    <?php if ( $cat_image ) : ?>
                        <div class="media-category-card__bg">
                            <img src="<?php echo esc_url($cat_image); ?>" alt="" loading="lazy">
                            <div class="media-category-card__overlay"></div>
                        </div>
                    <?php endif; ?>
                    <div class="media-category-card__content">
                        <div class="media-category-card__icon">
                            <?php echo $icons[$type] ?? ''; ?>
                        </div>
                        <h3 class="media-category-card__title"><?php echo esc_html($cat_title); ?></h3>
                        <p class="media-category-card__description"><?php echo esc_html($cat_description); ?></p>
                        <span class="media-category-card__link">
                            <?php echo esc_html($cat_link['title']); ?>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

