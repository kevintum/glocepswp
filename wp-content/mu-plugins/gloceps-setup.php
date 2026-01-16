<?php
/**
 * GLOCEPS Auto Setup
 * 
 * This mu-plugin automatically creates essential pages with ACF blocks.
 * Delete this file after setup is complete.
 * 
 * @package GLOCEPS
 */

// Initialize theme options with defaults if not already set
add_action('admin_init', 'gloceps_init_theme_options', 5);
function gloceps_init_theme_options() {
    if (!current_user_can('manage_options')) return;
    if (!function_exists('update_field')) return;
    if (get_option('gloceps_theme_options_initialized')) return;
    
    // Set Contact Info defaults
    if (!get_field('contact_phone', 'option')) {
        update_field('contact_phone', '+254 112 401 331', 'option');
    }
    if (!get_field('contact_email', 'option')) {
        update_field('contact_email', 'info@gloceps.org', 'option');
    }
    if (!get_field('contact_address', 'option')) {
        update_field('contact_address', "P.O Box 27023-00100\nRunda Drive, Nairobi, Kenya", 'option');
    }
    if (!get_field('office_hours', 'option')) {
        update_field('office_hours', 'Mon - Fri: 8:00am - 5:00pm', 'option');
    }
    
    // Set Social Media defaults
    if (!get_field('social_linkedin', 'option')) {
        update_field('social_linkedin', 'https://www.linkedin.com/company/gloceps', 'option');
    }
    if (!get_field('social_twitter', 'option')) {
        update_field('social_twitter', 'https://twitter.com/glolobal_ceps', 'option');
    }
    if (!get_field('social_youtube', 'option')) {
        update_field('social_youtube', 'https://www.youtube.com/@GLOCEPS', 'option');
    }
    if (!get_field('social_facebook', 'option')) {
        update_field('social_facebook', 'https://www.facebook.com/glolobal.ceps', 'option');
    }
    
    update_option('gloceps_theme_options_initialized', true);
}

/**
 * Update Contact page with all blocks (v5) - ALWAYS REGISTERED
 * This runs regardless of other setup completion flags
 */
add_action('admin_init', function() {
    // Only run once per version
    if (get_option('gloceps_contact_page_blocks_v5')) {
        return;
    }
    
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (!function_exists('update_field')) {
        return;
    }
    
    // Find Contact page
    $contact_page = get_page_by_path('contact');
    if (!$contact_page) {
        return;
    }
    
    $contact_id = $contact_page->ID;
    
    // Clear existing blocks first
    delete_post_meta($contact_id, 'content_blocks');
    
    // Update with all three blocks
    $contact_blocks = array(
        array(
            'acf_fc_layout' => 'contact_hero',
            'label' => 'Get In Touch',
            'title' => "Let's Start a <em>Conversation</em>",
            'description' => "Whether you're interested in research partnerships, media inquiries, or exploring how we can collaborate on policy initiatives, we'd love to hear from you.",
            'use_theme_contact_info' => 1,
            'show_social' => 1,
            'form_title' => 'Send Us a Message',
            'form_subtitle' => "Fill out the form below and we'll get back to you within 24-48 hours.",
            'cf7_shortcode' => '',
        ),
        array(
            'acf_fc_layout' => 'contact_map',
            'embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.9084668361367!2d36.79544847496567!3d-1.2316069356316045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f17352ff2bfe9%3A0x7a94a1f7e07e8d65!2sRunda%20Dr%2C%20Nairobi!5e0!3m2!1sen!2ske',
            'location_name' => 'GLOCEPS Headquarters',
            'location_address' => 'Runda Drive, Nairobi',
            'directions_url' => 'https://maps.google.com/?q=Runda+Drive+Nairobi',
        ),
        array(
            'acf_fc_layout' => 'faq_section',
            'title' => 'Frequently Asked Questions',
            'description' => 'Quick answers to common questions about working with GLOCEPS.',
            'faqs' => array(
                array(
                    'question' => 'How can I collaborate with GLOCEPS on research?',
                    'answer' => 'We welcome research partnerships with academic institutions, think tanks, and policy organizations. Contact us through the form above selecting "Research Partnership" as your inquiry type, and our research team will follow up.',
                ),
                array(
                    'question' => 'Do you accept internship applications?',
                    'answer' => 'Yes! We offer internship opportunities for students and early-career professionals interested in policy research, communications, and operations. Check our Careers section or contact us directly for current openings.',
                ),
                array(
                    'question' => 'How can I book a GLOCEPS expert for speaking?',
                    'answer' => 'Our researchers and analysts are available for conferences, workshops, and media appearances. Select "Events & Speaking" in the contact form and include event details. We typically respond within 2-3 business days.',
                ),
                array(
                    'question' => 'Can I subscribe to your publications?',
                    'answer' => 'Absolutely! Subscribe to our newsletter using the form in the footer to receive our Daily and Weekly Influential Briefs, research papers, and policy analysis directly to your inbox.',
                ),
            ),
        ),
    );
    
    update_field('content_blocks', $contact_blocks, $contact_id);
    update_option('gloceps_contact_page_blocks_v5', true);
    
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Contact page updated!</strong> Three blocks added: Hero, Map, and FAQ.</p></div>';
    });
}, 20);

// Only run once - but run additional content if not created
// Also run if events need fixing
if (get_option('gloceps_pages_setup_complete') && get_option('gloceps_all_content_created') && get_option('gloceps_events_fixed')) {
    return;
}

// Quick fix for existing events
if (get_option('gloceps_all_content_created') && !get_option('gloceps_events_fixed')) {
    add_action('admin_init', function() {
        if (!current_user_can('manage_options')) return;
        if (!function_exists('update_field')) return;
        
        gloceps_fix_event_dates();
        update_option('gloceps_events_fixed', true);
    });
}

// If pages done but not all content, create remaining content
if (get_option('gloceps_pages_setup_complete') && !get_option('gloceps_all_content_created')) {
    add_action('admin_init', 'gloceps_auto_create_all_content');
    return;
}

function gloceps_auto_create_all_content() {
    if (!current_user_can('manage_options')) return;
    if (!function_exists('update_field')) return;
    
    gloceps_create_sample_galleries();
    gloceps_create_sample_publications();
    gloceps_create_sample_events();
    gloceps_create_sample_team_members();
    gloceps_create_sample_videos();
    gloceps_create_sample_podcasts();
    gloceps_create_sample_articles();
    gloceps_fix_event_dates(); // Fix existing events
    
    update_option('gloceps_galleries_created', true);
    update_option('gloceps_all_content_created', true);
    
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p><strong>All sample content created!</strong> Publications, Events, Team Members, Videos, Podcasts, Articles, and Galleries.</p></div>';
    });
}

/**
 * Fix event dates - copy start_date to event_date
 */
function gloceps_fix_event_dates() {
    $events = get_posts(array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));
    
    foreach ($events as $event) {
        $start_date = get_field('start_date', $event->ID);
        if ($start_date && !get_field('event_date', $event->ID)) {
            update_field('event_date', $start_date, $event->ID);
        }
    }
}

// Run on admin_init to ensure all functions are available
add_action('admin_init', 'gloceps_auto_setup_pages');

function gloceps_auto_setup_pages() {
    // Only run for admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Check if ACF is active
    if (!function_exists('update_field')) {
        return;
    }
    
    // Create Home page
    $home_page = get_page_by_path('home');
    if (!$home_page) {
        $home_id = wp_insert_post(array(
            'post_title' => 'Home',
            'post_name' => 'home',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        
        if ($home_id && !is_wp_error($home_id)) {
            // Add ACF flexible content blocks
            $home_blocks = array(
                array(
                    'acf_fc_layout' => 'hero_video',
                    'title' => 'Research. <em>Knowledge.</em> Influence.',
                    'description' => 'The Global Centre for Policy and Strategy (GLOCEPS) provides strategic linkage between experience and research, bringing together outstanding professionals, thought leaders, and academia to advance key issues on peace and security.',
                    'cta_text' => 'Explore Our Work',
                    'cta_link' => '#research',
                ),
                array(
                    'acf_fc_layout' => 'research_pillars',
                    'eyebrow' => 'Research Focus Areas',
                    'title' => 'Our Five Pillars',
                    'description' => 'GLOCEPS work cuts across five interconnected pillars addressing the most pressing challenges facing Eastern Africa and the broader region.',
                ),
                array(
                    'acf_fc_layout' => 'publications_feed',
                    'eyebrow' => 'Latest Research',
                    'title' => 'Publications',
                    'description' => 'Access our latest policy briefs, research papers, and strategic analyses shaping discourse on regional and global issues.',
                    'count' => 5,
                ),
                array(
                    'acf_fc_layout' => 'impact_stats',
                    'stats' => array(
                        array('value' => '50+', 'label' => 'Research Publications'),
                        array('value' => '12', 'label' => 'Countries Engaged'),
                        array('value' => '35+', 'label' => 'Policy Dialogues'),
                        array('value' => '20+', 'label' => 'Expert Fellows'),
                    ),
                ),
                array(
                    'acf_fc_layout' => 'events_section',
                    'eyebrow' => 'Upcoming',
                    'title' => 'Events & Dialogues',
                    'description' => 'Join our policy dialogues, roundtables, and expert discussions shaping regional discourse on critical issues.',
                    'count' => 3,
                ),
                array(
                    'acf_fc_layout' => 'cta_section',
                    'title' => 'Access Our Research',
                    'description' => 'Explore in-depth analysis and strategic insights from GLOCEPS.',
                    'primary_button_text' => 'Browse Publications',
                    'secondary_button_text' => 'Subscribe to Updates',
                ),
            );
            
            update_field('content_blocks', $home_blocks, $home_id);
            
            // Set as front page
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_id);
        }
    }
    
    // Create About page
    $about_page = get_page_by_path('about');
    if (!$about_page) {
        $about_id = wp_insert_post(array(
            'post_title' => 'About Us',
            'post_name' => 'about',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        
        if ($about_id && !is_wp_error($about_id)) {
            $about_blocks = array(
                array(
                    'acf_fc_layout' => 'page_header',
                    'eyebrow' => 'Who We Are',
                    'title' => 'About GLOCEPS',
                    'description' => 'A leading centre of excellence in policy influence and strategy formulation.',
                ),
                array(
                    'acf_fc_layout' => 'team_grid',
                    'eyebrow' => 'Leadership',
                    'title' => 'Meet Our Team',
                    'description' => 'Distinguished professionals bringing decades of experience.',
                ),
            );
            update_field('content_blocks', $about_blocks, $about_id);
        }
    }
    
    // Create Contact page
    $contact_page = get_page_by_path('contact');
    if (!$contact_page) {
        $contact_id = wp_insert_post(array(
            'post_title' => 'Contact Us',
            'post_name' => 'contact',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        
        if ($contact_id && !is_wp_error($contact_id)) {
            $contact_blocks = array(
                array(
                    'acf_fc_layout' => 'contact_hero',
                    'label' => 'Get In Touch',
                    'title' => "Let's Start a <em>Conversation</em>",
                    'description' => "Whether you're interested in research partnerships, media inquiries, or exploring how we can collaborate on policy initiatives, we'd love to hear from you.",
                    'use_theme_contact_info' => true,
                    'show_social' => true,
                    'form_title' => 'Send Us a Message',
                    'form_subtitle' => "Fill out the form below and we'll get back to you within 24-48 hours.",
                    'cf7_shortcode' => '', // To be added after Contact Form 7 is configured
                ),
                array(
                    'acf_fc_layout' => 'contact_map',
                    'embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.9084668361367!2d36.79544847496567!3d-1.2316069356316045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f17352ff2bfe9%3A0x7a94a1f7e07e8d65!2sRunda%20Dr%2C%20Nairobi!5e0!3m2!1sen!2ske',
                    'location_name' => 'GLOCEPS Headquarters',
                    'location_address' => 'Runda Drive, Nairobi',
                    'directions_url' => 'https://maps.google.com/?q=Runda+Drive+Nairobi',
                ),
                array(
                    'acf_fc_layout' => 'faq_section',
                    'title' => 'Frequently Asked Questions',
                    'description' => 'Quick answers to common questions about working with GLOCEPS.',
                    'faqs' => array(
                        array(
                            'question' => 'How can I collaborate with GLOCEPS on research?',
                            'answer' => 'We welcome research partnerships with academic institutions, think tanks, and policy organizations. Contact us through the form above selecting "Research Partnership" as your inquiry type, and our research team will follow up.',
                        ),
                        array(
                            'question' => 'Do you accept internship applications?',
                            'answer' => 'Yes! We offer internship opportunities for students and early-career professionals interested in policy research, communications, and operations. Check our Careers section or contact us directly for current openings.',
                        ),
                        array(
                            'question' => 'How can I book a GLOCEPS expert for speaking?',
                            'answer' => 'Our researchers and analysts are available for conferences, workshops, and media appearances. Select "Events & Speaking" in the contact form and include event details. We typically respond within 2-3 business days.',
                        ),
                        array(
                            'question' => 'Can I subscribe to your publications?',
                            'answer' => 'Absolutely! Subscribe to our newsletter using the form in the footer to receive our Daily and Weekly Influential Briefs, research papers, and policy analysis directly to your inbox.',
                        ),
                    ),
                ),
            );
            update_field('content_blocks', $contact_blocks, $contact_id);
        }
    }
    
    // Create Media Hub page
    $media_page = get_page_by_path('media');
    if (!$media_page) {
        $media_id = wp_insert_post(array(
            'post_title' => 'Media Hub',
            'post_name' => 'media',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        
        if ($media_id && !is_wp_error($media_id)) {
            $media_blocks = array(
                array(
                    'acf_fc_layout' => 'media_hero',
                    'eyebrow' => 'Media Hub',
                    'title' => 'Videos, Podcasts & Galleries',
                    'description' => 'Explore our multimedia content.',
                ),
            );
            update_field('content_blocks', $media_blocks, $media_id);
        }
    }
    
    // Create Team page
    $team_page = get_page_by_path('team');
    if (!$team_page) {
        $team_id = wp_insert_post(array(
            'post_title' => 'Our Team',
            'post_name' => 'team',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        
        if ($team_id && !is_wp_error($team_id)) {
            $team_blocks = array(
                array(
                    'acf_fc_layout' => 'page_header',
                    'eyebrow' => 'Our People',
                    'title' => 'Meet the Team',
                    'description' => 'Distinguished professionals bringing decades of experience.',
                ),
                array(
                    'acf_fc_layout' => 'team_grid',
                    'title' => 'Leadership',
                ),
            );
            update_field('content_blocks', $team_blocks, $team_id);
        }
    }
    
    // Create Publications Store page
    $store_page = get_page_by_path('store');
    if (!$store_page) {
        $store_id = wp_insert_post(array(
            'post_title' => 'Publications Store',
            'post_name' => 'store',
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        
        if ($store_id && !is_wp_error($store_id)) {
            $store_blocks = array(
                array(
                    'acf_fc_layout' => 'store_hero',
                    'eyebrow' => 'Publications Store',
                    'title' => 'Premium Research & Analysis',
                    'description' => 'Access comprehensive research papers and policy reports.',
                ),
            );
            update_field('content_blocks', $store_blocks, $store_id);
            
            // Set as WooCommerce shop page
            if (class_exists('WooCommerce')) {
                update_option('woocommerce_shop_page_id', $store_id);
            }
        }
    }
    
    // Create sample galleries
    gloceps_create_sample_galleries();
    
    // Mark setup as complete
    update_option('gloceps_pages_setup_complete', true);
    update_option('gloceps_galleries_created', true);
    
    // Add admin notice
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p><strong>GLOCEPS Setup Complete!</strong> Pages and sample galleries have been created. You can now delete the mu-plugins/gloceps-setup.php file.</p></div>';
    });
}

/**
 * Create sample galleries
 */
function gloceps_create_sample_galleries() {
    // Sample gallery data
    $galleries = array(
        array(
            'title' => 'Annual Policy Conference 2024',
            'content' => 'The Annual Policy Conference 2024 brought together over 200 participants including policymakers, diplomats, researchers, and civil society representatives to discuss critical issues facing Eastern Africa. The two-day conference featured keynote addresses, panel discussions, and networking sessions focused on regional security, economic development, and governance challenges.',
            'event_date' => '2024-12-05',
            'venue' => 'Safari Park Hotel, Nairobi',
            'participant_count' => '200',
            'photographer' => 'GLOCEPS Media Team',
        ),
        array(
            'title' => 'Security Policy Workshop',
            'content' => 'A focused workshop on regional security frameworks and counter-terrorism strategies. Experts from across East Africa gathered to share insights and develop collaborative approaches.',
            'event_date' => '2024-11-15',
            'venue' => 'Serena Hotel, Nairobi',
            'participant_count' => '50',
            'photographer' => 'GLOCEPS Media Team',
        ),
        array(
            'title' => 'Youth Leadership Summit',
            'content' => 'Empowering the next generation of policy leaders through interactive sessions, mentorship, and networking opportunities with established professionals.',
            'event_date' => '2024-10-20',
            'venue' => 'KICC, Nairobi',
            'participant_count' => '150',
            'photographer' => 'GLOCEPS Media Team',
        ),
    );
    
    foreach ($galleries as $gallery_data) {
        // Check if gallery exists
        $existing = get_page_by_title($gallery_data['title'], OBJECT, 'gallery');
        if ($existing) continue;
        
        $gallery_id = wp_insert_post(array(
            'post_title' => $gallery_data['title'],
            'post_content' => $gallery_data['content'],
            'post_type' => 'gallery',
            'post_status' => 'publish',
        ));
        
        if ($gallery_id && !is_wp_error($gallery_id) && function_exists('update_field')) {
            update_field('event_date', $gallery_data['event_date'], $gallery_id);
            update_field('venue', $gallery_data['venue'], $gallery_id);
            update_field('participant_count', $gallery_data['participant_count'], $gallery_id);
            update_field('photographer', $gallery_data['photographer'], $gallery_id);
        }
    }
}

/**
 * Create sample publications
 */
function gloceps_create_sample_publications() {
    $publications = array(
        array(
            'title' => "Kenya's FATF Grey Listing: A Comprehensive Policy Analysis",
            'content' => '<p>This research paper provides an in-depth analysis of Kenya\'s placement on the Financial Action Task Force (FATF) grey list and its implications for the country\'s financial sector, foreign investment, and international relations.</p><p>The paper examines the factors that led to the grey listing, assesses the government\'s response, and provides recommendations for addressing the identified deficiencies in anti-money laundering and counter-terrorism financing frameworks.</p>',
            'excerpt' => 'An in-depth analysis of Kenya\'s FATF grey listing and its implications for financial policy and international relations.',
            'publication_type' => 'Research Paper',
            'research_pillar' => 'Governance & Ethics',
            'publication_date' => '2024-12-01',
            'authors' => 'Dr. James Mwangi, Prof. Sarah Ochieng',
            'pages' => '48',
            'access_type' => 'premium',
        ),
        array(
            'title' => 'Regional Security Dynamics in the Horn of Africa',
            'content' => '<p>This policy brief examines the evolving security landscape in the Horn of Africa, with a focus on the interplay between state actors, non-state armed groups, and external powers.</p><p>Key issues addressed include the Ethiopia-Tigray conflict aftermath, Somalia\'s ongoing challenges with Al-Shabaab, and the implications of the Red Sea tensions for regional stability.</p>',
            'excerpt' => 'Examining the evolving security landscape in the Horn of Africa and implications for regional stability.',
            'publication_type' => 'Policy Brief',
            'research_pillar' => 'Security & Defence',
            'publication_date' => '2024-11-15',
            'authors' => 'Col. (Rtd) Peter Kagwanja',
            'pages' => '24',
            'access_type' => 'free',
        ),
        array(
            'title' => 'East African Economic Integration: Progress and Challenges',
            'content' => '<p>This comprehensive report assesses the state of economic integration within the East African Community, examining trade flows, infrastructure development, and policy harmonization efforts.</p><p>The analysis covers recent developments including the African Continental Free Trade Area (AfCFTA) implementation and its intersection with regional integration initiatives.</p>',
            'excerpt' => 'Assessing economic integration progress within the East African Community and AfCFTA implications.',
            'publication_type' => 'Special Report',
            'research_pillar' => 'Development',
            'publication_date' => '2024-10-20',
            'authors' => 'Dr. Elizabeth Wanjiru, Mr. David Oloo',
            'pages' => '72',
            'access_type' => 'premium',
        ),
        array(
            'title' => 'Weekly Influential Brief: Sudan Crisis Update',
            'content' => '<p>This weekly brief provides an updated analysis of the ongoing crisis in Sudan, examining recent military developments, humanitarian conditions, and diplomatic efforts to resolve the conflict.</p>',
            'excerpt' => 'Weekly analysis of the Sudan crisis including military developments and humanitarian situation.',
            'publication_type' => 'Weekly Brief',
            'research_pillar' => 'Foreign Policy',
            'publication_date' => '2024-12-15',
            'authors' => 'GLOCEPS Research Team',
            'pages' => '8',
            'access_type' => 'free',
        ),
    );
    
    // Ensure taxonomies exist
    $publication_types = array('Research Paper', 'Policy Brief', 'Special Report', 'Weekly Brief', 'Daily Brief', 'Bulletin');
    foreach ($publication_types as $type) {
        if (!term_exists($type, 'publication_type')) {
            wp_insert_term($type, 'publication_type');
        }
    }
    
    foreach ($publications as $pub_data) {
        $existing = get_page_by_title($pub_data['title'], OBJECT, 'publication');
        if ($existing) continue;
        
        $pub_id = wp_insert_post(array(
            'post_title' => $pub_data['title'],
            'post_content' => $pub_data['content'],
            'post_excerpt' => $pub_data['excerpt'],
            'post_type' => 'publication',
            'post_status' => 'publish',
        ));
        
        if ($pub_id && !is_wp_error($pub_id)) {
            // Set taxonomy terms
            wp_set_object_terms($pub_id, $pub_data['publication_type'], 'publication_type');
            wp_set_object_terms($pub_id, $pub_data['research_pillar'], 'research_pillar');
            
            // Set ACF fields
            if (function_exists('update_field')) {
                update_field('publication_date', $pub_data['publication_date'], $pub_id);
                update_field('authors', $pub_data['authors'], $pub_id);
                update_field('page_count', $pub_data['pages'], $pub_id);
                update_field('access_type', $pub_data['access_type'], $pub_id);
            }
        }
    }
}

/**
 * Create sample events
 */
function gloceps_create_sample_events() {
    $events = array(
        array(
            'title' => 'Annual Policy Conference 2025',
            'content' => '<p>Join us for the premier policy conference in Eastern Africa, bringing together thought leaders, policymakers, and researchers to discuss critical regional challenges.</p><p>This year\'s theme focuses on "Building Resilient Institutions for Sustainable Development" with keynote addresses from distinguished speakers and interactive panel discussions.</p>',
            'excerpt' => 'Premier policy conference bringing together thought leaders to discuss regional challenges.',
            'event_type' => 'Conference',
            'start_date' => '2025-03-15',
            'end_date' => '2025-03-16',
            'venue' => 'Safari Park Hotel, Nairobi',
            'registration_link' => 'https://example.com/register',
            'is_featured' => true,
        ),
        array(
            'title' => 'Webinar: Digital Governance in Africa',
            'content' => '<p>An online discussion exploring the opportunities and challenges of digital transformation in African governance systems.</p><p>Expert panelists will examine case studies from Kenya, Rwanda, and South Africa, discussing best practices and lessons learned.</p>',
            'excerpt' => 'Online discussion on digital transformation in African governance systems.',
            'event_type' => 'Webinar',
            'start_date' => '2025-02-10',
            'end_date' => '2025-02-10',
            'venue' => 'Online (Zoom)',
            'registration_link' => 'https://example.com/webinar',
            'is_featured' => false,
        ),
        array(
            'title' => 'Executive Roundtable: Climate Security',
            'content' => '<p>An invitation-only roundtable discussion bringing together senior officials and experts to examine the intersection of climate change and security in Eastern Africa.</p>',
            'excerpt' => 'High-level discussion on climate change and security nexus in Eastern Africa.',
            'event_type' => 'Roundtable',
            'start_date' => '2025-01-25',
            'end_date' => '2025-01-25',
            'venue' => 'GLOCEPS Headquarters, Nairobi',
            'registration_link' => '',
            'is_featured' => false,
        ),
    );
    
    // Ensure event types exist
    $event_types = array('Conference', 'Webinar', 'Workshop', 'Roundtable', 'Seminar', 'Launch');
    foreach ($event_types as $type) {
        if (!term_exists($type, 'event_type')) {
            wp_insert_term($type, 'event_type');
        }
    }
    
    foreach ($events as $event_data) {
        $existing = get_page_by_title($event_data['title'], OBJECT, 'event');
        if ($existing) continue;
        
        $event_id = wp_insert_post(array(
            'post_title' => $event_data['title'],
            'post_content' => $event_data['content'],
            'post_excerpt' => $event_data['excerpt'],
            'post_type' => 'event',
            'post_status' => 'publish',
        ));
        
        if ($event_id && !is_wp_error($event_id)) {
            wp_set_object_terms($event_id, $event_data['event_type'], 'event_type');
            
            if (function_exists('update_field')) {
                // Use both field names for compatibility
                update_field('event_date', $event_data['start_date'], $event_id);
                update_field('start_date', $event_data['start_date'], $event_id);
                update_field('end_date', $event_data['end_date'], $event_id);
                update_field('venue', $event_data['venue'], $event_id);
                update_field('registration_link', $event_data['registration_link'], $event_id);
                update_field('is_featured', $event_data['is_featured'], $event_id);
            }
        }
    }
}

/**
 * Create sample team members
 */
function gloceps_create_sample_team_members() {
    $team_members = array(
        array(
            'title' => 'Dr. Peter Kagwanja',
            'content' => '<p>Dr. Peter Kagwanja is the founding President of GLOCEPS and a distinguished scholar in African politics and international relations. He has over two decades of experience in policy research and has served in various advisory capacities to governments and international organizations.</p><p>His areas of expertise include governance, peace and security, and regional integration in Eastern Africa.</p>',
            'position' => 'President & CEO',
            'category' => 'Leadership',
            'email' => 'p.kagwanja@gloceps.org',
            'linkedin' => 'https://linkedin.com/in/peterkagwanja',
            'twitter' => 'https://twitter.com/pkagwanja',
            'order' => 1,
        ),
        array(
            'title' => 'Prof. Sarah Ochieng',
            'content' => '<p>Prof. Sarah Ochieng is the Director of Research at GLOCEPS, leading the organization\'s research programs on governance and development. She holds a PhD in Political Science from the University of Nairobi and has published extensively on democratic governance in Africa.</p>',
            'position' => 'Director of Research',
            'category' => 'Leadership',
            'email' => 's.ochieng@gloceps.org',
            'linkedin' => 'https://linkedin.com/in/sarahochieng',
            'twitter' => '',
            'order' => 2,
        ),
        array(
            'title' => 'Col. (Rtd) James Mwangi',
            'content' => '<p>Col. (Rtd) James Mwangi is a Senior Research Fellow specializing in security and defence policy. With over 25 years of military service, he brings unique insights into regional security dynamics and counter-terrorism strategies.</p>',
            'position' => 'Senior Research Fellow',
            'category' => 'Research Fellows',
            'email' => 'j.mwangi@gloceps.org',
            'linkedin' => '',
            'twitter' => '',
            'order' => 3,
        ),
        array(
            'title' => 'Dr. Elizabeth Wanjiru',
            'content' => '<p>Dr. Elizabeth Wanjiru is a Research Fellow focusing on economic development and regional integration. She holds a PhD in Economics from LSE and previously worked with the African Development Bank.</p>',
            'position' => 'Research Fellow',
            'category' => 'Research Fellows',
            'email' => 'e.wanjiru@gloceps.org',
            'linkedin' => 'https://linkedin.com/in/elizabethwanjiru',
            'twitter' => '',
            'order' => 4,
        ),
    );
    
    // Ensure team categories exist
    $categories = array('Leadership', 'Research Fellows', 'Advisory Board', 'Staff');
    foreach ($categories as $cat) {
        if (!term_exists($cat, 'team_category')) {
            wp_insert_term($cat, 'team_category');
        }
    }
    
    foreach ($team_members as $member_data) {
        $existing = get_page_by_title($member_data['title'], OBJECT, 'team_member');
        if ($existing) continue;
        
        $member_id = wp_insert_post(array(
            'post_title' => $member_data['title'],
            'post_content' => $member_data['content'],
            'post_type' => 'team_member',
            'post_status' => 'publish',
            'menu_order' => $member_data['order'],
        ));
        
        if ($member_id && !is_wp_error($member_id)) {
            wp_set_object_terms($member_id, $member_data['category'], 'team_category');
            
            if (function_exists('update_field')) {
                update_field('position', $member_data['position'], $member_id);
                update_field('email', $member_data['email'], $member_id);
                update_field('linkedin_url', $member_data['linkedin'], $member_id);
                update_field('twitter_url', $member_data['twitter'], $member_id);
            }
        }
    }
}

/**
 * Create sample videos
 */
function gloceps_create_sample_videos() {
    $videos = array(
        array(
            'title' => 'Understanding the Horn of Africa Security Landscape',
            'content' => '<p>In this comprehensive video analysis, our security experts examine the complex security dynamics in the Horn of Africa, including the Ethiopia-Eritrea relationship, Somalia\'s ongoing challenges, and regional peacekeeping efforts.</p>',
            'excerpt' => 'Expert analysis of security dynamics in the Horn of Africa region.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '45:30',
            'category' => 'Analysis',
        ),
        array(
            'title' => 'Interview: Kenya\'s Foreign Policy Direction',
            'content' => '<p>An exclusive interview with a senior diplomat discussing Kenya\'s foreign policy priorities, regional leadership role, and engagement with international partners.</p>',
            'excerpt' => 'Exclusive interview on Kenya\'s foreign policy priorities and regional leadership.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '28:15',
            'category' => 'Interviews',
        ),
        array(
            'title' => 'Annual Conference 2024 Highlights',
            'content' => '<p>Highlights from the GLOCEPS Annual Policy Conference 2024, featuring keynote addresses, panel discussions, and networking moments from the two-day event.</p>',
            'excerpt' => 'Key moments and insights from the GLOCEPS Annual Conference 2024.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '15:00',
            'category' => 'Events',
        ),
    );
    
    // Ensure video categories exist
    $categories = array('Analysis', 'Interviews', 'Events', 'Lectures', 'Documentaries');
    foreach ($categories as $cat) {
        if (!term_exists($cat, 'video_category')) {
            wp_insert_term($cat, 'video_category');
        }
    }
    
    foreach ($videos as $video_data) {
        $existing = get_page_by_title($video_data['title'], OBJECT, 'video');
        if ($existing) continue;
        
        $video_id = wp_insert_post(array(
            'post_title' => $video_data['title'],
            'post_content' => $video_data['content'],
            'post_excerpt' => $video_data['excerpt'],
            'post_type' => 'video',
            'post_status' => 'publish',
        ));
        
        if ($video_id && !is_wp_error($video_id)) {
            wp_set_object_terms($video_id, $video_data['category'], 'video_category');
            
            if (function_exists('update_field')) {
                update_field('video_url', $video_data['video_url'], $video_id);
                update_field('duration', $video_data['duration'], $video_id);
            }
        }
    }
}

/**
 * Create sample podcasts
 */
function gloceps_create_sample_podcasts() {
    $podcasts = array(
        array(
            'title' => 'Episode 25: The Future of African Diplomacy',
            'content' => '<p>In this episode, we discuss the evolving landscape of African diplomacy, from AU reform efforts to the continent\'s growing voice in global affairs. Our guests share insights on how African nations are reshaping international relations.</p>',
            'excerpt' => 'Exploring the evolving landscape of African diplomacy and global engagement.',
            'audio_url' => 'https://example.com/podcast/episode-25.mp3',
            'duration' => '52:00',
            'episode_number' => '25',
            'season' => '2',
        ),
        array(
            'title' => 'Episode 24: Climate Change and Security',
            'content' => '<p>This episode examines the critical intersection of climate change and security in Eastern Africa, exploring how environmental degradation is fueling conflicts over resources and displacement.</p>',
            'excerpt' => 'Examining how climate change impacts security dynamics in Eastern Africa.',
            'audio_url' => 'https://example.com/podcast/episode-24.mp3',
            'duration' => '45:30',
            'episode_number' => '24',
            'season' => '2',
        ),
        array(
            'title' => 'Episode 23: Youth and Political Participation',
            'content' => '<p>A dynamic discussion on youth engagement in politics across Africa, featuring young leaders who are challenging traditional power structures and driving change in their communities.</p>',
            'excerpt' => 'Young leaders discuss political participation and driving change in Africa.',
            'audio_url' => 'https://example.com/podcast/episode-23.mp3',
            'duration' => '48:15',
            'episode_number' => '23',
            'season' => '2',
        ),
    );
    
    foreach ($podcasts as $podcast_data) {
        $existing = get_page_by_title($podcast_data['title'], OBJECT, 'podcast');
        if ($existing) continue;
        
        $podcast_id = wp_insert_post(array(
            'post_title' => $podcast_data['title'],
            'post_content' => $podcast_data['content'],
            'post_excerpt' => $podcast_data['excerpt'],
            'post_type' => 'podcast',
            'post_status' => 'publish',
        ));
        
        if ($podcast_id && !is_wp_error($podcast_id) && function_exists('update_field')) {
            update_field('audio_url', $podcast_data['audio_url'], $podcast_id);
            update_field('duration', $podcast_data['duration'], $podcast_id);
            update_field('episode_number', $podcast_data['episode_number'], $podcast_id);
            update_field('season', $podcast_data['season'], $podcast_id);
        }
    }
}

/**
 * Create sample articles
 */
function gloceps_create_sample_articles() {
    $articles = array(
        array(
            'title' => 'Africa\'s Role in the New Multipolar World Order',
            'content' => '<p>As the global order shifts towards multipolarity, African nations are increasingly positioning themselves as key players in international affairs. This article examines how the continent is leveraging its demographic dividend, natural resources, and strategic location to assert greater influence on the world stage.</p><p>From the growing engagement with BRICS nations to Africa\'s unified stance at climate negotiations, the continent is demonstrating a new confidence in global diplomacy. However, challenges remain in translating this influence into tangible benefits for African citizens.</p>',
            'excerpt' => 'Examining Africa\'s growing influence in the shifting global order.',
            'source' => 'Daily Nation',
            'original_url' => 'https://nation.africa/example-article',
            'publication_date' => '2024-12-10',
        ),
        array(
            'title' => 'The EAC Single Currency: Dreams and Realities',
            'content' => '<p>The East African Community\'s long-held ambition of a single currency faces both opportunities and significant hurdles. This analysis explores the economic prerequisites, political will, and practical challenges standing between the current situation and monetary union.</p><p>Drawing lessons from the Eurozone experience, we examine what the EAC must get right to avoid the pitfalls that have plagued other currency unions.</p>',
            'excerpt' => 'Analyzing the prospects and challenges of the proposed EAC single currency.',
            'source' => 'The East African',
            'original_url' => 'https://theeastafrican.co.ke/example',
            'publication_date' => '2024-11-25',
        ),
        array(
            'title' => 'Digital Transformation in African Governance',
            'content' => '<p>From Kenya\'s Huduma Namba to Rwanda\'s Irembo platform, African governments are embracing digital solutions to improve service delivery and reduce corruption. This article surveys the landscape of e-governance initiatives across the continent, highlighting successes and cautionary tales.</p>',
            'excerpt' => 'Survey of e-governance initiatives transforming public service delivery across Africa.',
            'source' => 'African Arguments',
            'original_url' => 'https://africanarguments.org/example',
            'publication_date' => '2024-11-15',
        ),
    );
    
    foreach ($articles as $article_data) {
        $existing = get_page_by_title($article_data['title'], OBJECT, 'article');
        if ($existing) continue;
        
        $article_id = wp_insert_post(array(
            'post_title' => $article_data['title'],
            'post_content' => $article_data['content'],
            'post_excerpt' => $article_data['excerpt'],
            'post_type' => 'article',
            'post_status' => 'publish',
        ));
        
        if ($article_id && !is_wp_error($article_id) && function_exists('update_field')) {
            update_field('source', $article_data['source'], $article_id);
            update_field('original_url', $article_data['original_url'], $article_id);
            update_field('publication_date', $article_data['publication_date'], $article_id);
        }
    }
}

// v4 function removed - now using v5 above which is registered before the early returns

