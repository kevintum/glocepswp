<?php
/**
 * Single template for Jobs/Vacancies
 * 
 * @package GLOCEPS
 * @since 1.0.0
 */

get_header();

while (have_posts()) :
    the_post();
    
    // Get ACF fields
    $location = get_field('vacancy_location');
    $engagement_type = get_field('vacancy_engagement_type');
    $deadline = get_field('vacancy_deadline');
    $salary_range = get_field('vacancy_salary_range');
    $writeup = get_field('vacancy_writeup');
    $application_url = get_field('vacancy_application_url');
    $documents = get_field('vacancy_documents');
    
    // Format deadline
    $deadline_display = '';
    $deadline_status = '';
    if ($deadline) {
        $deadline_timestamp = strtotime($deadline);
        $today = strtotime('today');
        $deadline_display = date('F j, Y', $deadline_timestamp);
        if ($deadline_timestamp < $today) {
            $deadline_status = 'closed';
        } elseif ($deadline_timestamp <= strtotime('+7 days')) {
            $deadline_status = 'urgent';
        }
    }
    
    // Engagement type labels
    $engagement_labels = array(
        'full-time' => 'Full Time',
        'part-time' => 'Part Time',
        'contract' => 'Contract',
        'internship' => 'Internship',
        'consultancy' => 'Consultancy',
        'volunteer' => 'Volunteer',
    );
    $engagement_display = isset($engagement_labels[$engagement_type]) ? $engagement_labels[$engagement_type] : $engagement_type;
    
    // Get featured image
    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
    $is_placeholder = false;
    if (!$featured_image) {
        $featured_image = gloceps_get_favicon_url(192);
        $is_placeholder = true;
    }
    
    // Get related jobs
    $related_args = array(
        'post_type' => 'vacancy',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $related_jobs = new WP_Query($related_args);
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

        <!-- Job Header -->
        <article class="job-single">
            <div class="container">
                <div class="job-single__header">
                    <div class="job-single__header-content">
                        <div class="job-single__meta">
                            <?php if ($engagement_type) : ?>
                                <span class="job-single__badge job-single__badge--type">
                                    <?php echo esc_html($engagement_display); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($deadline_status === 'closed') : ?>
                                <span class="job-single__badge job-single__badge--closed">
                                    <?php esc_html_e('Closed', 'gloceps'); ?>
                                </span>
                            <?php elseif ($deadline_status === 'urgent') : ?>
                                <span class="job-single__badge job-single__badge--urgent">
                                    <?php esc_html_e('Urgently Hiring', 'gloceps'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h1 class="job-single__title"><?php the_title(); ?></h1>
                        <div class="job-single__details">
                            <?php if ($location) : ?>
                                <div class="job-single__detail">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <span><?php echo esc_html($location); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($deadline_display) : ?>
                                <div class="job-single__detail">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <span><?php esc_html_e('Deadline:', 'gloceps'); ?> <?php echo esc_html($deadline_display); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($salary_range) : ?>
                                <div class="job-single__detail">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="2" y="6" width="20" height="12" rx="2" />
                                        <path d="M6 10h12M6 14h8" />
                                    </svg>
                                    <span>KES <?php echo esc_html($salary_range); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($application_url && $deadline_status !== 'closed') : ?>
                            <div class="job-single__actions">
                                <a href="<?php echo esc_url($application_url); ?>" class="btn btn--primary btn--lg" target="_blank" rel="noopener">
                                    <?php esc_html_e('Apply Now', 'gloceps'); ?> →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($featured_image) : ?>
                        <div class="job-single__header-image">
                            <img src="<?php echo esc_url($featured_image); ?>" 
                                 alt="<?php echo esc_attr(get_the_title()); ?>"
                                 <?php echo $is_placeholder ? 'class="is-placeholder"' : ''; ?> />
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Job Body: Main Content + Sidebar -->
            <div class="job-single__body">
                <div class="container">
                    <div class="job-single__layout">
                        <!-- Main Content -->
                        <div class="job-single__content reveal">
                            <?php if ($writeup) : ?>
                                <div class="job-single__section">
                                    <h2><?php esc_html_e('Job Description', 'gloceps'); ?></h2>
                                    <div class="job-single__writeup">
                                        <?php echo wp_kses_post($writeup); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (get_the_content()) : ?>
                                <div class="job-single__section">
                                    <?php if (!$writeup) : ?>
                                        <h2><?php esc_html_e('Job Description', 'gloceps'); ?></h2>
                                    <?php endif; ?>
                                    <div class="job-single__content-text">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($documents && !empty($documents)) : ?>
                                <div class="job-single__section">
                                    <h2><?php esc_html_e('Job Documents', 'gloceps'); ?></h2>
                                    <div class="job-single__documents">
                                        <?php foreach ($documents as $doc) : 
                                            $file = $doc['file'];
                                            $label = $doc['label'];
                                            if ($file && $label) :
                                        ?>
                                            <a href="<?php echo esc_url($file['url']); ?>" 
                                               class="job-single__document" 
                                               target="_blank" 
                                               rel="noopener"
                                               download>
                                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <span><?php echo esc_html($label); ?></span>
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        <?php 
                                            endif;
                                        endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Sidebar -->
                        <aside class="job-single__sidebar">
                            <div class="job-single__sidebar-card">
                                <h3><?php esc_html_e('Job Details', 'gloceps'); ?></h3>
                                <dl class="job-single__details-list">
                                    <?php if ($engagement_type) : ?>
                                        <div class="job-single__detail-item">
                                            <dt>
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                    <line x1="16" y1="2" x2="16" y2="6" />
                                                    <line x1="8" y1="2" x2="8" y2="6" />
                                                    <line x1="3" y1="10" x2="21" y2="10" />
                                                </svg>
                                                <?php esc_html_e('Engagement Type', 'gloceps'); ?>
                                            </dt>
                                            <dd><?php echo esc_html($engagement_display); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($location) : ?>
                                        <div class="job-single__detail-item">
                                            <dt>
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                                <?php esc_html_e('Location', 'gloceps'); ?>
                                            </dt>
                                            <dd><?php echo esc_html($location); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($deadline_display) : ?>
                                        <div class="job-single__detail-item">
                                            <dt>
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                    <line x1="16" y1="2" x2="16" y2="6" />
                                                    <line x1="8" y1="2" x2="8" y2="6" />
                                                    <line x1="3" y1="10" x2="21" y2="10" />
                                                </svg>
                                                <?php esc_html_e('Application Deadline', 'gloceps'); ?>
                                            </dt>
                                            <dd class="<?php echo $deadline_status === 'closed' ? 'job-single__deadline--closed' : ''; ?>">
                                                <?php echo esc_html($deadline_display); ?>
                                            </dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($salary_range) : ?>
                                        <div class="job-single__detail-item">
                                            <dt>
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <rect x="2" y="6" width="20" height="12" rx="2" />
                                                    <path d="M6 10h12M6 14h8" />
                                                </svg>
                                                <?php esc_html_e('Salary Range', 'gloceps'); ?>
                                            </dt>
                                            <dd>KES <?php echo esc_html($salary_range); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <div class="job-single__detail-item">
                                        <dt>
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                <line x1="16" y1="2" x2="16" y2="6" />
                                                <line x1="8" y1="2" x2="8" y2="6" />
                                                <line x1="3" y1="10" x2="21" y2="10" />
                                            </svg>
                                            <?php esc_html_e('Posted', 'gloceps'); ?>
                                        </dt>
                                        <dd><?php echo esc_html(get_the_date('F j, Y')); ?></dd>
                                    </div>
                                </dl>
                                <?php if ($application_url && $deadline_status !== 'closed') : ?>
                                    <div class="job-single__sidebar-cta">
                                        <a href="<?php echo esc_url($application_url); ?>" class="btn btn--primary btn--block" target="_blank" rel="noopener">
                                            <?php esc_html_e('Apply Now', 'gloceps'); ?>
                                        </a>
                                    </div>
                                <?php elseif ($deadline_status === 'closed') : ?>
                                    <div class="job-single__sidebar-cta">
                                        <p class="job-single__closed-message">
                                            <?php esc_html_e('This position is no longer accepting applications.', 'gloceps'); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($related_jobs->have_posts()) : ?>
                                <div class="job-single__sidebar-card">
                                    <h3><?php esc_html_e('Other Opportunities', 'gloceps'); ?></h3>
                                    <ul class="job-single__related">
                                        <?php while ($related_jobs->have_posts()) : $related_jobs->the_post(); ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_permalink()); ?>">
                                                    <?php echo esc_html(get_the_title()); ?>
                                                </a>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php wp_reset_postdata(); ?>
                        </aside>
                    </div>
                </div>
            </div>
        </article>
    </main>

<?php
endwhile;
get_footer();
