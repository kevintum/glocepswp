<?php
/**
 * Demo Data Population
 * 
 * This file contains functions to populate the WordPress site with demo data
 * matching the static site content.
 * 
 * Run this once by visiting: yoursite.com/?gloceps_install_demo=1
 * 
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Auto-create essential taxonomies on theme switch
 * This ensures the research pillars always exist
 */
add_action('after_switch_theme', 'gloceps_auto_create_taxonomies');
add_action('init', 'gloceps_maybe_create_essential_taxonomies', 999);

function gloceps_maybe_create_essential_taxonomies() {
    // Only run once per theme activation
    if (get_option('gloceps_essential_taxonomies_created')) {
        return;
    }
    
    // Create taxonomies
    gloceps_create_demo_taxonomies();
    
    // Mark as created
    update_option('gloceps_essential_taxonomies_created', true);
}

function gloceps_auto_create_taxonomies() {
    // Reset the flag on theme switch to ensure taxonomies are created
    delete_option('gloceps_essential_taxonomies_created');
}

/**
 * Hook to install demo data via admin action
 */
add_action('admin_action_gloceps_install_demo', 'gloceps_admin_install_demo');

function gloceps_admin_install_demo() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access.');
    }
    
    // Verify nonce
    check_admin_referer('gloceps_install_demo_nonce');
    
    gloceps_install_demo_data();
    
    wp_redirect(admin_url('tools.php?page=gloceps-demo-data&installed=1'));
    exit;
}

/**
 * Add admin menu for demo data installation
 */
add_action('admin_menu', 'gloceps_add_demo_data_menu');

function gloceps_add_demo_data_menu() {
    add_management_page(
        'GLOCEPS Demo Data',
        'GLOCEPS Demo Data',
        'manage_options',
        'gloceps-demo-data',
        'gloceps_demo_data_admin_page'
    );
}

function gloceps_demo_data_admin_page() {
    // Handle reset action
    if (isset($_POST['reset_demo']) && wp_verify_nonce($_POST['_wpnonce'], 'gloceps_reset_demo')) {
        delete_option('gloceps_demo_data_installed');
        echo '<div class="notice notice-success"><p>Demo flag reset. You can now reinstall.</p></div>';
    }
    
    // Handle direct install (without redirect)
    if (isset($_POST['install_demo_data']) && wp_verify_nonce($_POST['_wpnonce'], 'gloceps_install_demo')) {
        gloceps_install_demo_data();
        echo '<div class="notice notice-success"><p><strong>Demo data installed successfully!</strong></p></div>';
    }
    
    // Check if coming back from successful install
    if (isset($_GET['installed'])) {
        echo '<div class="notice notice-success"><p><strong>Demo data installed successfully!</strong></p></div>';
    }
    
    $installed = get_option('gloceps_demo_data_installed');
    ?>
    <div class="wrap">
        <h1>GLOCEPS Demo Data</h1>
        <?php if ($installed): ?>
        <div class="notice notice-info"><p>Demo data has already been installed.</p></div>
        <form method="post">
            <?php wp_nonce_field('gloceps_reset_demo'); ?>
            <input type="hidden" name="reset_demo" value="1">
            <p><button type="submit" class="button button-secondary" onclick="return confirm('Are you sure? This will reset the demo data flag and allow reinstallation.')">Reset Demo Flag</button></p>
        </form>
        <p><a href="<?php echo esc_url(home_url('/')); ?>" class="button button-primary">View Homepage</a></p>
        <?php else: ?>
        <p>Click the button below to install demo content including:</p>
        <ul style="list-style: disc; margin-left: 20px;">
            <li>Research Pillars &amp; Publication Types (taxonomies)</li>
            <li>Sample Publications</li>
            <li>Sample Events</li>
            <li>Team Members</li>
            <li>Videos &amp; Podcasts</li>
            <li>Demo Pages</li>
            <li>Navigation Menus</li>
        </ul>
        <form method="post">
            <?php wp_nonce_field('gloceps_install_demo'); ?>
            <input type="hidden" name="install_demo_data" value="1">
            <p><button type="submit" class="button button-primary button-hero">Install Demo Data</button></p>
        </form>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Install all demo data
 */
function gloceps_install_demo_data() {
    // Prevent running multiple times
    if (get_option('gloceps_demo_data_installed')) {
        return;
    }

    // Create taxonomies first
    gloceps_create_demo_taxonomies();
    
    // Create content
    gloceps_create_demo_publications();
    gloceps_create_demo_events();
    gloceps_create_demo_team_members();
    gloceps_create_demo_videos();
    gloceps_create_demo_podcasts();
    gloceps_create_demo_pages();
    
    // Create menus
    gloceps_create_demo_menus();
    
    // Mark as installed
    update_option('gloceps_demo_data_installed', true);
}

/**
 * Create taxonomy terms
 */
function gloceps_create_demo_taxonomies() {
    // Publication Types
    $pub_types = array(
        'daily-briefs' => 'Daily Influential Briefs',
        'weekly-briefs' => 'Weekly Influential Briefs',
        'special-focus' => 'Special Focus',
        'bulletins' => 'Influential Bulletins',
        'policy-papers' => 'Policy Papers',
        'conference-papers' => 'Conference Papers & Proceedings',
        'research-papers' => 'Research Papers',
        'media-articles' => 'Mainstream Media Articles',
        'what-others-say' => 'What Others Say',
    );
    
    foreach ($pub_types as $slug => $name) {
        if (!term_exists($slug, 'publication_type')) {
            wp_insert_term($name, 'publication_type', array('slug' => $slug));
        }
    }
    
    // Research Pillars
    $pillars = array(
        'foreign-policy' => 'Foreign Policy',
        'security-defence' => 'Security & Defence',
        'governance-ethics' => 'Governance & Ethics',
        'development' => 'Development',
        'transnational-organised-crimes' => 'Transnational Organised Crimes',
    );
    
    foreach ($pillars as $slug => $name) {
        if (!term_exists($slug, 'research_pillar')) {
            wp_insert_term($name, 'research_pillar', array('slug' => $slug));
        }
    }
    
    // Event Types
    $event_types = array(
        'webinar' => 'Webinar',
        'conference' => 'Conference',
        'symposium' => 'Joint Symposium',
        'launch' => 'Launch Series',
        'breakfast' => 'Breakfast Meeting',
        'workshop' => 'Expert Workshop',
        'courtesy' => 'Courtesy Call',
        'roundtable' => 'Roundtable',
        'csr' => 'CSR',
    );
    
    foreach ($event_types as $slug => $name) {
        if (!term_exists($slug, 'event_type')) {
            wp_insert_term($name, 'event_type', array('slug' => $slug));
        }
    }
    
    // Team Categories
    $team_cats = array(
        'founding-council' => 'Founding Council',
        'leadership' => 'Leadership',
        'research-team' => 'Research Team',
        'associates' => 'Associates',
        'advisory-board' => 'Advisory Board',
    );
    
    foreach ($team_cats as $slug => $name) {
        if (!term_exists($slug, 'team_category')) {
            wp_insert_term($name, 'team_category', array('slug' => $slug));
        }
    }
}

/**
 * Create demo publications
 */
function gloceps_create_demo_publications() {
    $publications = array(
        array(
            'title' => "Kenya's Diplomatic Landscape: Navigating Global Partnerships in 2024",
            'excerpt' => "An analysis of Kenya's evolving diplomatic relations and strategic partnerships in the context of shifting global power dynamics.",
            'content' => "This comprehensive policy paper examines Kenya's diplomatic positioning in the contemporary global order. The analysis covers bilateral relations with major powers, regional integration efforts, and strategic partnerships that shape Kenya's foreign policy agenda.",
            'type' => 'policy-papers',
            'pillar' => 'foreign-policy',
            'access' => 'free',
            'author' => 'Dr. James Mwangi',
            'pages' => 32,
        ),
        array(
            'title' => 'Counter-Terrorism Strategies in the Horn of Africa: A Comprehensive Analysis',
            'excerpt' => 'Examining the effectiveness of regional counter-terrorism efforts and recommendations for enhanced cooperation.',
            'content' => "This research paper provides an in-depth analysis of counter-terrorism frameworks in the Horn of Africa region. It examines current approaches, identifies gaps, and proposes evidence-based recommendations for improved regional security cooperation.",
            'type' => 'research-papers',
            'pillar' => 'security-defence',
            'access' => 'premium',
            'price' => 2500,
            'author' => 'Lt. Gen. (Rtd) L. Sumbeiywo',
            'pages' => 48,
        ),
        array(
            'title' => 'Weekly Influential Brief: Constitutional Reforms and Democratic Consolidation',
            'excerpt' => "This week's analysis covers the ongoing debates around constitutional amendments and their implications for governance.",
            'content' => "Our weekly brief examines the latest developments in constitutional reform debates across Eastern Africa. The analysis highlights key proposals, stakeholder positions, and potential implications for democratic governance.",
            'type' => 'weekly-briefs',
            'pillar' => 'governance-ethics',
            'access' => 'free',
            'author' => 'GLOCEPS Team',
            'pages' => 8,
        ),
        array(
            'title' => 'Special Focus: Climate-Resilient Development Pathways for East Africa',
            'excerpt' => 'A deep dive into sustainable development strategies that address climate change impacts across the region.',
            'content' => "This special focus publication explores innovative approaches to climate-resilient development in Eastern Africa. It presents case studies, policy recommendations, and actionable strategies for sustainable growth.",
            'type' => 'special-focus',
            'pillar' => 'development',
            'access' => 'free',
            'author' => 'Prof. Grace Njoroge',
            'pages' => 24,
        ),
        array(
            'title' => 'Influential Bulletin: Emerging Cybercrime Trends in Eastern Africa',
            'excerpt' => 'Tracking the latest developments in cyber-enabled crimes and their impact on regional security and economic stability.',
            'content' => "This bulletin provides a comprehensive overview of cybercrime trends affecting the Eastern African region. It analyzes attack vectors, victim profiles, and recommends counter-measures for governments and organizations.",
            'type' => 'bulletins',
            'pillar' => 'transnational-organised-crimes',
            'access' => 'free',
            'author' => 'IG (Rtd) J.K. Boinnet',
            'pages' => 16,
        ),
        array(
            'title' => "Kenya's FATF Grey Listing: A Comprehensive Policy Analysis",
            'excerpt' => "This flagship research paper examines the implications of Kenya's continued placement on the FATF grey list.",
            'content' => "A detailed analysis covering economic impact assessments, comparative regional analysis, and a roadmap for achieving compliance. Essential reading for policymakers, financial institutions, and development partners.",
            'type' => 'research-papers',
            'pillar' => 'governance-ethics',
            'access' => 'premium',
            'price' => 4999,
            'author' => 'GLOCEPS Research Team',
            'pages' => 56,
            'featured' => true,
        ),
        array(
            'title' => "Africa's Climate Diplomacy: COP30 Strategy Framework",
            'excerpt' => 'Strategic framework for African climate diplomacy at COP30 and beyond.',
            'content' => "This policy brief outlines a comprehensive strategy for African nations to coordinate their positions and maximize impact at international climate negotiations.",
            'type' => 'policy-papers',
            'pillar' => 'development',
            'access' => 'premium',
            'price' => 2499,
            'author' => 'Dr. Sarah Kimani',
            'pages' => 24,
        ),
        array(
            'title' => 'The Jubaland Crisis: Federal Tensions and Regional Security',
            'excerpt' => 'Analysis of federal tensions in Somalia and implications for regional security architecture.',
            'content' => "This research paper examines the ongoing tensions between Mogadishu and Jubaland, analyzing the implications for Somalia's federal project and broader regional security dynamics.",
            'type' => 'research-papers',
            'pillar' => 'security-defence',
            'access' => 'premium',
            'price' => 3499,
            'author' => 'Dr. Ahmed Hassan',
            'pages' => 36,
        ),
    );
    
    foreach ($publications as $pub) {
        // Check if already exists
        $existing = get_page_by_title($pub['title'], OBJECT, 'publication');
        if ($existing) continue;
        
        // Create the post
        $post_id = wp_insert_post(array(
            'post_title' => $pub['title'],
            'post_excerpt' => $pub['excerpt'],
            'post_content' => $pub['content'],
            'post_type' => 'publication',
            'post_status' => 'publish',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Set taxonomy terms
            wp_set_object_terms($post_id, $pub['type'], 'publication_type');
            wp_set_object_terms($post_id, $pub['pillar'], 'research_pillar');
            
            // Set ACF fields
            if (function_exists('update_field')) {
                update_field('access_type', $pub['access'], $post_id);
                update_field('author', $pub['author'], $post_id);
                update_field('page_count', $pub['pages'], $post_id);
                
                if ($pub['access'] === 'premium' && isset($pub['price'])) {
                    update_field('price', $pub['price'], $post_id);
                }
                
                if (isset($pub['featured'])) {
                    update_field('is_featured', $pub['featured'], $post_id);
                }
            }
        }
    }
}

/**
 * Create demo events
 */
function gloceps_create_demo_events() {
    $events = array(
        array(
            'title' => 'East Africa Security Forum 2025',
            'excerpt' => 'A three-day conference bringing together security experts, policymakers, and practitioners.',
            'content' => "The East Africa Security Forum 2025 will bring together leading security experts, policymakers, and practitioners to discuss emerging security challenges in the East African region and collaborative solutions. The forum will feature keynote addresses, panel discussions, and networking opportunities.",
            'type' => 'conference',
            'date' => date('Y-m-d', strtotime('+35 days')),
            'end_date' => date('Y-m-d', strtotime('+37 days')),
            'time' => '9:00 AM - 5:00 PM EAT',
            'location' => 'Kenyatta International Convention Centre, Nairobi',
            'featured' => true,
        ),
        array(
            'title' => 'Policy Dialogue: Climate Security in the Horn of Africa',
            'excerpt' => 'Join our expert panel as they discuss the intersection of climate change and regional security challenges.',
            'content' => "This webinar brings together climate scientists, security analysts, and policymakers to examine how climate change is affecting security dynamics in the Horn of Africa. Topics include resource conflicts, migration patterns, and adaptation strategies.",
            'type' => 'webinar',
            'date' => date('Y-m-d', strtotime('+14 days')),
            'time' => '2:00 PM - 4:00 PM EAT',
            'location' => 'Virtual (Zoom)',
            'virtual' => true,
        ),
        array(
            'title' => "Annual Strategy Conference: Africa's Global Position",
            'excerpt' => 'Three days of intensive discussions on Africa\'s strategic positioning in global affairs.',
            'content' => "The Annual Strategy Conference brings together thought leaders, diplomats, and academics to examine Africa's evolving role in global affairs. This year's theme focuses on leveraging Africa's strategic position in a multipolar world.",
            'type' => 'conference',
            'date' => date('Y-m-d', strtotime('+60 days')),
            'end_date' => date('Y-m-d', strtotime('+62 days')),
            'time' => '8:30 AM - 6:00 PM EAT',
            'location' => 'Safari Park Hotel, Nairobi',
        ),
        array(
            'title' => 'Executive Breakfast: Kenya-China Relations',
            'excerpt' => 'An intimate breakfast session exploring the evolving dynamics of Kenya-China bilateral relations.',
            'content' => "This executive breakfast session provides a platform for senior business leaders and policymakers to discuss the current state and future prospects of Kenya-China relations. Topics include trade, investment, and strategic partnership.",
            'type' => 'breakfast',
            'date' => date('Y-m-d', strtotime('+21 days')),
            'time' => '7:30 AM - 10:00 AM EAT',
            'location' => 'Villa Rosa Kempinski, Nairobi',
        ),
        array(
            'title' => 'Joint Symposium on Regional Integration',
            'excerpt' => 'A collaborative event examining the state and future of East African integration.',
            'content' => "This joint symposium with partner institutions examines the progress and challenges of East African integration. Experts from across the region share insights on economic cooperation, political harmonization, and social integration.",
            'type' => 'symposium',
            'date' => date('Y-m-d', strtotime('-30 days')),
            'time' => '9:00 AM - 4:00 PM EAT',
            'location' => 'KICC, Nairobi',
        ),
        array(
            'title' => 'Strategic Analysis Workshop: Intelligence Sharing',
            'excerpt' => 'An intensive workshop for practitioners on intelligence sharing frameworks and protocols.',
            'content' => "This closed workshop brings together intelligence professionals to examine best practices in intelligence sharing across borders. Participants will explore frameworks, protocols, and technologies that enhance regional security cooperation.",
            'type' => 'workshop',
            'date' => date('Y-m-d', strtotime('-45 days')),
            'time' => '9:00 AM - 5:00 PM EAT',
            'location' => 'GLOCEPS HQ, Nairobi',
        ),
    );
    
    foreach ($events as $event) {
        // Check if already exists
        $existing = get_page_by_title($event['title'], OBJECT, 'event');
        if ($existing) continue;
        
        // Create the post
        $post_id = wp_insert_post(array(
            'post_title' => $event['title'],
            'post_excerpt' => $event['excerpt'],
            'post_content' => $event['content'],
            'post_type' => 'event',
            'post_status' => 'publish',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Set taxonomy terms
            wp_set_object_terms($post_id, $event['type'], 'event_type');
            
            // Set ACF fields
            if (function_exists('update_field')) {
                update_field('event_date', $event['date'], $post_id);
                update_field('event_time', $event['time'], $post_id);
                update_field('event_location', $event['location'], $post_id);
                
                if (isset($event['end_date'])) {
                    update_field('event_end_date', $event['end_date'], $post_id);
                }
                
                if (isset($event['virtual'])) {
                    update_field('is_virtual', $event['virtual'], $post_id);
                }
                
                if (isset($event['featured'])) {
                    update_field('is_featured', $event['featured'], $post_id);
                }
            }
        }
    }
}

/**
 * Create demo team members
 */
function gloceps_create_demo_team_members() {
    $team = array(
        array(
            'name' => 'Lt. Gen. (Rtd) Lazaro Sumbeiywo',
            'title' => 'Founding Chairman',
            'bio' => "Lt. Gen. (Rtd) Lazaro Sumbeiywo is a distinguished military leader and diplomat who served as Kenya's chief mediator in the Sudan peace process. He brings decades of experience in conflict resolution and strategic leadership to GLOCEPS.",
            'category' => 'founding-council',
            'order' => 1,
        ),
        array(
            'name' => 'IG (Rtd) Joseph K. Boinnet',
            'title' => 'Director General',
            'bio' => "IG (Rtd) Joseph Kipchoge Boinnet previously served as Director of Kenya's National Intelligence Service and Inspector General of Police. He provides strategic direction and leadership for GLOCEPS operations.",
            'category' => 'leadership',
            'order' => 2,
        ),
        array(
            'name' => 'Dr. James Mwangi',
            'title' => 'Senior Research Fellow, Foreign Policy',
            'bio' => "Dr. Mwangi is a leading expert in international relations with over 15 years of experience in foreign policy analysis. He leads research on diplomatic relations and global governance.",
            'category' => 'research-team',
            'order' => 3,
        ),
        array(
            'name' => 'Ms. Jaki Mbogo',
            'title' => 'Head of Communications',
            'bio' => "Ms. Mbogo brings extensive experience in strategic communications and public relations. She oversees GLOCEPS's media relations, publications, and public engagement initiatives.",
            'category' => 'leadership',
            'order' => 4,
        ),
        array(
            'name' => 'Prof. Grace Njoroge',
            'title' => 'Senior Research Fellow, Development',
            'bio' => "Prof. Njoroge is an expert in sustainable development and climate policy. She leads research on development strategies and regional integration.",
            'category' => 'research-team',
            'order' => 5,
        ),
        array(
            'name' => 'Amb. (Rtd) Macharia Kamau',
            'title' => 'Advisory Board Member',
            'bio' => "Ambassador Kamau is a distinguished diplomat who served as Kenya's Permanent Representative to the United Nations. He was instrumental in the development of the Sustainable Development Goals.",
            'category' => 'advisory-board',
            'order' => 6,
        ),
        array(
            'name' => 'Dr. Sarah Kimani',
            'title' => 'Research Fellow, Governance & Ethics',
            'bio' => "Dr. Kimani specializes in governance, accountability, and public sector reform. Her research focuses on strengthening democratic institutions in Eastern Africa.",
            'category' => 'research-team',
            'order' => 7,
        ),
        array(
            'name' => 'Dr. Ahmed Hassan',
            'title' => 'Research Fellow, Security & Defence',
            'bio' => "Dr. Hassan is an expert in regional security dynamics with a focus on the Horn of Africa. His research covers counter-terrorism, peacekeeping, and security cooperation.",
            'category' => 'research-team',
            'order' => 8,
        ),
    );
    
    foreach ($team as $member) {
        // Check if already exists
        $existing = get_page_by_title($member['name'], OBJECT, 'team_member');
        if ($existing) continue;
        
        // Create the post
        $post_id = wp_insert_post(array(
            'post_title' => $member['name'],
            'post_content' => $member['bio'],
            'post_type' => 'team_member',
            'post_status' => 'publish',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            // Set taxonomy terms
            wp_set_object_terms($post_id, $member['category'], 'team_category');
            
            // Set ACF fields
            if (function_exists('update_field')) {
                update_field('job_title', $member['title'], $post_id);
                update_field('biography', $member['bio'], $post_id);
                update_field('display_order', $member['order'], $post_id);
            }
        }
    }
}

/**
 * Create demo videos
 */
function gloceps_create_demo_videos() {
    $videos = array(
        array(
            'title' => 'Regional Security Dynamics: Perspectives from Eastern Africa',
            'excerpt' => 'Expert panelists discuss the evolving security landscape in Eastern Africa.',
            'content' => "In this panel discussion, leading security experts examine the key challenges and opportunities facing Eastern Africa. Topics include counter-terrorism, peacekeeping operations, and regional security cooperation frameworks.",
            'category' => 'panel-discussion',
            'duration' => '45:30',
            'featured' => true,
        ),
        array(
            'title' => "Understanding Ethiopia's BRICS Membership",
            'excerpt' => 'Analysis of what Ethiopia joining BRICS means for the region.',
            'content' => "This expert interview explores the implications of Ethiopia's inclusion in the BRICS grouping. The discussion covers economic opportunities, diplomatic positioning, and potential impacts on regional dynamics.",
            'category' => 'expert-interview',
            'duration' => '28:15',
        ),
        array(
            'title' => 'Climate Security Nexus in the Horn',
            'excerpt' => 'Examining the intersection of climate change and security.',
            'content' => "This webinar recording examines how climate change is affecting security dynamics in the Horn of Africa. Experts discuss resource conflicts, migration, and adaptation strategies.",
            'category' => 'webinar',
            'duration' => '52:00',
        ),
    );
    
    foreach ($videos as $video) {
        // Check if already exists
        $existing = get_page_by_title($video['title'], OBJECT, 'video');
        if ($existing) continue;
        
        // Create the post
        $post_id = wp_insert_post(array(
            'post_title' => $video['title'],
            'post_excerpt' => $video['excerpt'],
            'post_content' => $video['content'],
            'post_type' => 'video',
            'post_status' => 'publish',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            wp_set_object_terms($post_id, $video['category'], 'video_category');
            
            if (function_exists('update_field')) {
                update_field('duration', $video['duration'], $post_id);
                if (isset($video['featured'])) {
                    update_field('is_featured', $video['featured'], $post_id);
                }
            }
        }
    }
}

/**
 * Create demo podcasts
 */
function gloceps_create_demo_podcasts() {
    $podcasts = array(
        array(
            'title' => "Ethiopia's BRICS Membership: What It Means for the Region",
            'excerpt' => "Analyzing the implications of Ethiopia joining BRICS and its impact on regional trade and diplomacy.",
            'content' => "In this episode, we explore the significance of Ethiopia becoming the first East African nation to join BRICS. Our experts discuss the economic opportunities, diplomatic implications, and potential challenges ahead.",
            'episode' => 24,
            'duration' => '32:15',
        ),
        array(
            'title' => 'Climate Security Nexus in the Horn of Africa',
            'excerpt' => 'Exploring the intersection of climate change and security challenges in Eastern Africa.',
            'content' => "This episode examines how climate change is reshaping security dynamics in the Horn of Africa. We discuss resource conflicts, displacement, and the need for integrated policy responses.",
            'episode' => 23,
            'duration' => '45:30',
        ),
        array(
            'title' => "Kenya's Major Non-NATO Ally Status: Strategic Analysis",
            'excerpt' => "What does Kenya's new alliance status mean for regional security architecture?",
            'content' => "We analyze the implications of Kenya's designation as a Major Non-NATO Ally. Our experts discuss the military, diplomatic, and economic dimensions of this strategic development.",
            'episode' => 22,
            'duration' => '38:45',
        ),
    );
    
    foreach ($podcasts as $podcast) {
        // Check if already exists
        $existing = get_page_by_title($podcast['title'], OBJECT, 'podcast');
        if ($existing) continue;
        
        // Create the post
        $post_id = wp_insert_post(array(
            'post_title' => $podcast['title'],
            'post_excerpt' => $podcast['excerpt'],
            'post_content' => $podcast['content'],
            'post_type' => 'podcast',
            'post_status' => 'publish',
        ));
        
        if ($post_id && !is_wp_error($post_id)) {
            if (function_exists('update_field')) {
                update_field('episode_number', $podcast['episode'], $post_id);
                update_field('duration', $podcast['duration'], $post_id);
            }
        }
    }
}

/**
 * Create demo pages with ACF blocks
 */
function gloceps_create_demo_pages() {
    $pages = array(
        // Homepage with ACF blocks
        array(
            'title' => 'Home',
            'slug' => 'home',
            'template' => '',
            'is_front' => true,
            'blocks' => gloceps_get_homepage_blocks(),
        ),
        // About Us page
        array(
            'title' => 'About Us',
            'slug' => 'about',
            'template' => '',
            'blocks' => gloceps_get_about_blocks(),
        ),
        // Contact page
        array(
            'title' => 'Contact',
            'slug' => 'contact',
            'template' => '',
            'blocks' => gloceps_get_contact_blocks(),
        ),
        // Media Hub page
        array(
            'title' => 'Media Hub',
            'slug' => 'media',
            'template' => '',
            'blocks' => gloceps_get_media_blocks(),
        ),
        // Store page
        array(
            'title' => 'Publications Store',
            'slug' => 'store',
            'template' => '',
            'blocks' => gloceps_get_store_blocks(),
        ),
        // Research parent page
        array(
            'title' => 'Research',
            'slug' => 'research',
            'template' => '',
        ),
        // Research Pillar pages
        array(
            'title' => 'Foreign Policy',
            'slug' => 'foreign-policy',
            'template' => '',
            'parent' => 'Research',
            'blocks' => gloceps_get_pillar_blocks('foreign-policy', 'Foreign Policy', 'Analysing diplomatic relations, regional integration through the EAC and IGAD, and international partnerships shaping Eastern Africa\'s global engagement.'),
        ),
        array(
            'title' => 'Security & Defence',
            'slug' => 'security-defence',
            'template' => '',
            'parent' => 'Research',
            'blocks' => gloceps_get_pillar_blocks('security-defence', 'Security & Defence', 'Examining national security frameworks, counter-terrorism, and regional defence cooperation.'),
        ),
        array(
            'title' => 'Governance & Ethics',
            'slug' => 'governance-ethics',
            'template' => '',
            'parent' => 'Research',
            'blocks' => gloceps_get_pillar_blocks('governance-ethics', 'Governance & Ethics', 'Promoting accountability, democratic processes, and ethical leadership in public governance.'),
        ),
        array(
            'title' => 'Development',
            'slug' => 'development',
            'template' => '',
            'parent' => 'Research',
            'blocks' => gloceps_get_pillar_blocks('development', 'Development', 'Driving sustainable economic growth, innovation, and inclusive development strategies.'),
        ),
        array(
            'title' => 'Transnational Organised Crimes',
            'slug' => 'transnational-organised-crimes',
            'template' => '',
            'parent' => 'Research',
            'blocks' => gloceps_get_pillar_blocks('transnational-organised-crimes', 'Transnational Organised Crimes', 'Combating trafficking, money laundering, and cross-border criminal networks.'),
        ),
        // Team page
        array(
            'title' => 'Our Team',
            'slug' => 'team',
            'template' => '',
            'blocks' => gloceps_get_team_blocks(),
        ),
    );
    
    $parent_ids = array();
    
    foreach ($pages as $page) {
        // Check if already exists
        $existing = get_page_by_path($page['slug']);
        if ($existing) {
            $parent_ids[$page['title']] = $existing->ID;
            // Still update blocks if page exists
            if (!empty($page['blocks']) && function_exists('update_field')) {
                update_field('content_blocks', $page['blocks'], $existing->ID);
            }
            continue;
        }
        
        $args = array(
            'post_title' => $page['title'],
            'post_name' => $page['slug'],
            'post_content' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
        );
        
        // Set parent if specified
        if (isset($page['parent']) && isset($parent_ids[$page['parent']])) {
            $args['post_parent'] = $parent_ids[$page['parent']];
        }
        
        $post_id = wp_insert_post($args);
        
        if ($post_id && !is_wp_error($post_id)) {
            $parent_ids[$page['title']] = $post_id;
            
            // Set template
            if (!empty($page['template'])) {
                update_post_meta($post_id, '_wp_page_template', $page['template']);
            }
            
            // Set ACF blocks
            if (!empty($page['blocks']) && function_exists('update_field')) {
                update_field('content_blocks', $page['blocks'], $post_id);
            }
            
            // Set as front page
            if (isset($page['is_front']) && $page['is_front']) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $post_id);
            }
        }
    }
    
    // Create WooCommerce pages
    gloceps_create_woocommerce_pages();
}

/**
 * Get Homepage ACF blocks
 */
function gloceps_get_homepage_blocks() {
    return array(
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
            'description' => 'Explore in-depth analysis and strategic insights from GLOCEPS. Download free briefs or purchase comprehensive research papers to support evidence-based policy making in Eastern Africa.',
            'primary_button_text' => 'Browse Publications',
            'secondary_button_text' => 'Subscribe to Updates',
        ),
    );
}

/**
 * Get About page ACF blocks
 */
function gloceps_get_about_blocks() {
    return array(
        array(
            'acf_fc_layout' => 'page_header',
            'eyebrow' => 'Who We Are',
            'title' => 'About GLOCEPS',
            'description' => 'A leading centre of excellence in policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.',
        ),
        array(
            'acf_fc_layout' => 'content_with_image',
            'title' => 'Our Mission',
            'content' => 'The Global Centre for Policy and Strategy (GLOCEPS) provides strategic linkage between experience and research, bringing together outstanding professionals, thought leaders, and academia to advance key issues on peace and security.',
            'image_position' => 'right',
        ),
        array(
            'acf_fc_layout' => 'team_grid',
            'eyebrow' => 'Leadership',
            'title' => 'Meet Our Team',
            'description' => 'Distinguished professionals bringing decades of experience in diplomacy, security, and policy research.',
        ),
    );
}

/**
 * Get Contact page ACF blocks
 */
function gloceps_get_contact_blocks() {
    return array(
        array(
            'acf_fc_layout' => 'contact_hero',
            'eyebrow' => 'Get In Touch',
            'title' => 'Contact Us',
            'description' => 'Have questions about our research or interested in partnership opportunities? We\'d love to hear from you.',
        ),
        array(
            'acf_fc_layout' => 'contact_form',
            'form_title' => 'Send us a Message',
        ),
        array(
            'acf_fc_layout' => 'contact_map',
            'address' => 'P.O Box 27023-00100, Runda Drive, Nairobi, Kenya',
            'phone' => '+254 112 401 331',
            'email' => 'info@gloceps.org',
        ),
        array(
            'acf_fc_layout' => 'faq_section',
            'title' => 'Frequently Asked Questions',
            'faqs' => array(
                array('question' => 'How can I access your publications?', 'answer' => 'Our publications are available through our Publications page. Many are free to download, while premium research papers can be purchased through our store.'),
                array('question' => 'Do you offer partnership opportunities?', 'answer' => 'Yes, we welcome partnerships with research institutions, policy organizations, and government bodies. Contact us to discuss collaboration opportunities.'),
                array('question' => 'How can I attend your events?', 'answer' => 'Check our Events page for upcoming dialogues and conferences. Registration details are provided for each event.'),
            ),
        ),
    );
}

/**
 * Get Media Hub page ACF blocks
 */
function gloceps_get_media_blocks() {
    return array(
        array(
            'acf_fc_layout' => 'media_hero',
            'eyebrow' => 'Media Hub',
            'title' => 'Videos, Podcasts & Galleries',
            'description' => 'Explore our multimedia content featuring expert discussions, event coverage, and visual documentation of our work.',
        ),
        array(
            'acf_fc_layout' => 'media_categories_grid',
            'categories' => array(
                array('title' => 'Videos', 'description' => 'Expert interviews, panel discussions, and event highlights', 'link' => '/videos/'),
                array('title' => 'Podcasts', 'description' => 'In-depth conversations on policy and security issues', 'link' => '/podcasts/'),
                array('title' => 'Photo Galleries', 'description' => 'Visual documentation of events and activities', 'link' => '/galleries/'),
                array('title' => 'Articles', 'description' => 'Media coverage and press releases', 'link' => '/articles/'),
            ),
        ),
    );
}

/**
 * Get Store page ACF blocks
 */
function gloceps_get_store_blocks() {
    return array(
        array(
            'acf_fc_layout' => 'store_hero',
            'eyebrow' => 'Publications Store',
            'title' => 'Premium Research & Analysis',
            'description' => 'Access comprehensive research papers, strategic analyses, and policy reports from GLOCEPS experts.',
        ),
        array(
            'acf_fc_layout' => 'trust_bar',
            'items' => array(
                array('icon' => 'shield', 'text' => 'Secure Payment'),
                array('icon' => 'download', 'text' => 'Instant Download'),
                array('icon' => 'quality', 'text' => 'Expert Research'),
            ),
        ),
        array(
            'acf_fc_layout' => 'featured_publication',
        ),
        array(
            'acf_fc_layout' => 'products_section',
            'title' => 'All Publications',
            'show_filters' => true,
        ),
        array(
            'acf_fc_layout' => 'institutional_subscriptions',
            'title' => 'Institutional Subscriptions',
            'description' => 'Get unlimited access to all GLOCEPS publications for your organization.',
        ),
    );
}

/**
 * Get Research Pillar page ACF blocks
 */
function gloceps_get_pillar_blocks($slug, $title, $description) {
    return array(
        array(
            'acf_fc_layout' => 'page_header',
            'eyebrow' => 'Research Pillar',
            'title' => $title,
            'description' => $description,
            'background_style' => 'dark',
        ),
        array(
            'acf_fc_layout' => 'publications_feed',
            'eyebrow' => 'Related Publications',
            'title' => 'Research & Analysis',
            'description' => 'Explore our publications in this research area.',
            'filter_by_pillar' => $slug,
            'count' => 10,
        ),
        array(
            'acf_fc_layout' => 'events_section',
            'eyebrow' => 'Related Events',
            'title' => 'Dialogues & Conferences',
            'filter_by_pillar' => $slug,
            'count' => 3,
        ),
    );
}

/**
 * Get Team page ACF blocks
 */
function gloceps_get_team_blocks() {
    return array(
        array(
            'acf_fc_layout' => 'page_header',
            'eyebrow' => 'Our People',
            'title' => 'Meet the Team',
            'description' => 'Distinguished professionals bringing decades of experience in diplomacy, security, and policy research.',
        ),
        array(
            'acf_fc_layout' => 'team_grid',
            'category' => 'leadership',
            'title' => 'Leadership',
        ),
        array(
            'acf_fc_layout' => 'team_grid',
            'category' => 'research-fellows',
            'title' => 'Research Fellows',
        ),
        array(
            'acf_fc_layout' => 'team_grid',
            'category' => 'advisory-board',
            'title' => 'Advisory Board',
        ),
    );
}

/**
 * Create WooCommerce pages with proper templates
 */
function gloceps_create_woocommerce_pages() {
    if (!class_exists('WooCommerce')) return;
    
    // Cart page
    $cart_page = get_page_by_path('cart');
    if (!$cart_page) {
        $cart_id = wp_insert_post(array(
            'post_title' => 'Cart',
            'post_name' => 'cart',
            'post_content' => '[woocommerce_cart]',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        if ($cart_id) {
            update_option('woocommerce_cart_page_id', $cart_id);
        }
    }
    
    // Checkout page
    $checkout_page = get_page_by_path('checkout');
    if (!$checkout_page) {
        $checkout_id = wp_insert_post(array(
            'post_title' => 'Checkout',
            'post_name' => 'checkout',
            'post_content' => '[woocommerce_checkout]',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        if ($checkout_id) {
            update_option('woocommerce_checkout_page_id', $checkout_id);
        }
    }
    
    // My Account page
    $account_page = get_page_by_path('my-account');
    if (!$account_page) {
        $account_id = wp_insert_post(array(
            'post_title' => 'My Account',
            'post_name' => 'my-account',
            'post_content' => '[woocommerce_my_account]',
            'post_type' => 'page',
            'post_status' => 'publish',
        ));
        if ($account_id) {
            update_option('woocommerce_myaccount_page_id', $account_id);
        }
    }
    
    // Shop page (Publications Store)
    $shop_page = get_page_by_path('store');
    if ($shop_page) {
        update_option('woocommerce_shop_page_id', $shop_page->ID);
    }
}

/**
 * Create navigation menus
 */
function gloceps_create_demo_menus() {
    // Check if menu already exists
    $menu_exists = wp_get_nav_menu_object('Primary Menu');
    if ($menu_exists) return;
    
    // Create the menu
    $menu_id = wp_create_nav_menu('Primary Menu');
    
    if (is_wp_error($menu_id)) return;
    
    // Get page IDs
    $home = get_page_by_title('Home');
    $about = get_page_by_title('About Us');
    $research = get_page_by_title('Research');
    $contact = get_page_by_title('Contact');
    
    // Add menu items
    if ($home) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => 'Home',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $home->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ));
    }
    
    if ($about) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => 'About',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $about->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ));
    }
    
    if ($research) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => 'Research',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $research->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ));
    }
    
    // Add Publications CPT archive
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Publications',
        'menu-item-url' => get_post_type_archive_link('publication'),
        'menu-item-type' => 'custom',
        'menu-item-status' => 'publish',
    ));
    
    // Add Events CPT archive
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' => 'Events',
        'menu-item-url' => get_post_type_archive_link('event'),
        'menu-item-type' => 'custom',
        'menu-item-status' => 'publish',
    ));
    
    if ($contact) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => 'Contact',
            'menu-item-object' => 'page',
            'menu-item-object-id' => $contact->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ));
    }
    
    // Assign menu to location
    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

