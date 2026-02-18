<?php
/**
 * ACF Field Groups Registration
 * 
 * This file registers all ACF field groups programmatically.
 * Content blocks are implemented using ACF Flexible Content for maximum reusability.
 * 
 * @package GLOCEPS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF Field Groups
 * Use 'init' hook with priority 20 to ensure post types are registered first
 */
add_action('init', 'gloceps_register_acf_fields', 20);

function gloceps_register_acf_fields() {
    // Only run if ACF is active
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    /**
     * =========================================
     * PAGE BUILDER - Flexible Content
     * =========================================
     * Main flexible content field for building pages
     */
    acf_add_local_field_group(array(
        'key' => 'group_page_builder',
        'title' => 'Page Builder',
        'fields' => array(
            array(
                'key' => 'field_page_builder_blocks',
                'label' => 'Content Blocks',
                'name' => 'content_blocks',
                'type' => 'flexible_content',
                'instructions' => 'Add and arrange content blocks to build your page.',
                'layouts' => gloceps_get_flexible_layouts(),
                'button_label' => 'Add Block',
                'min' => '',
                'max' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array('the_content'),
        'active' => true,
    ));

    /**
     * =========================================
     * PUBLICATION FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_publication_fields',
        'title' => 'Publication Details',
        'fields' => array(
            // Tab: Basic Information
            array(
                'key' => 'field_pub_tab_basic',
                'label' => 'Basic Information',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_pub_abstract',
                'label' => 'Abstract',
                'name' => 'abstract',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => 'Brief summary of the publication (used as excerpt if not set separately)',
            ),
            array(
                'key' => 'field_pub_executive_summary',
                'label' => 'Executive Summary',
                'name' => 'executive_summary',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Detailed executive summary of the publication',
            ),
            array(
                'key' => 'field_pub_publication_format',
                'label' => 'Format',
                'name' => 'publication_format',
                'type' => 'select',
                'choices' => array(
                    'pdf' => 'PDF Document',
                    'article' => 'Online Article',
                ),
                'default_value' => 'pdf',
                'instructions' => 'Publication format type',
            ),
            array(
                'key' => 'field_pub_pdf_file',
                'label' => 'PDF File',
                'name' => 'pdf_file',
                'type' => 'file',
                'return_format' => 'array',
                'mime_types' => 'pdf',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_pub_publication_format',
                            'operator' => '==',
                            'value' => 'pdf',
                        ),
                    ),
                ),
            ),
            
            // Tab: Access & Pricing
            array(
                'key' => 'field_pub_tab_access',
                'label' => 'Access & Pricing',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_pub_access_type',
                'label' => 'Access Type',
                'name' => 'access_type',
                'type' => 'select',
                'choices' => array(
                    'free' => 'Free',
                    'premium' => 'Premium',
                ),
                'default_value' => 'free',
            ),
            array(
                'key' => 'field_pub_price',
                'label' => 'Price (KES)',
                'name' => 'price',
                'type' => 'number',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_pub_access_type',
                            'operator' => '==',
                            'value' => 'premium',
                        ),
                    ),
                ),
                'instructions' => 'Price in Kenyan Shillings. Leave empty if linked to WooCommerce product.',
            ),
            array(
                'key' => 'field_pub_wc_product',
                'label' => 'WooCommerce Product',
                'name' => 'wc_product',
                'type' => 'post_object',
                'post_type' => array('product'),
                'return_format' => 'id',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_pub_access_type',
                            'operator' => '==',
                            'value' => 'premium',
                        ),
                    ),
                ),
                'instructions' => 'Link to a WooCommerce product for this publication. If set, price will be pulled from the product.',
            ),
            
            // Tab: Publication Details
            array(
                'key' => 'field_pub_tab_details',
                'label' => 'Publication Details',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_pub_author_type',
                'label' => 'Author Type',
                'name' => 'author_type',
                'type' => 'radio',
                'choices' => array(
                    'team' => 'Team Member',
                    'guest' => 'Guest Author',
                ),
                'default_value' => 'team',
                'layout' => 'horizontal',
            ),
            array(
                'key' => 'field_pub_team_member',
                'label' => 'Select Team Member(s)',
                'name' => 'team_member',
                'type' => 'post_object',
                'post_type' => array('team_member'),
                'return_format' => 'object',
                'multiple' => 1,
                'instructions' => 'Select one or more team members as authors',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_pub_author_type',
                            'operator' => '==',
                            'value' => 'team',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_pub_guest_authors',
                'label' => 'Guest Author(s)',
                'name' => 'guest_authors',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Guest Author',
                'instructions' => 'Add one or more guest authors',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_pub_author_type',
                            'operator' => '==',
                            'value' => 'guest',
                        ),
                    ),
                ),
                'sub_fields' => array(
                    array(
                        'key' => 'field_pub_guest_author_name',
                        'label' => 'Guest Author Name',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_pub_guest_author_title',
                        'label' => 'Guest Author Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_pub_guest_author_bio',
                        'label' => 'Guest Author Bio',
                        'name' => 'bio',
                        'type' => 'textarea',
                        'rows' => 4,
                    ),
                    array(
                        'key' => 'field_pub_guest_author_image',
                        'label' => 'Guest Author Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                    ),
                ),
            ),
            array(
                'key' => 'field_pub_page_count',
                'label' => 'Page Count',
                'name' => 'page_count',
                'type' => 'number',
                'instructions' => 'Number of pages (auto-calculated from PDF if available)',
            ),
            array(
                'key' => 'field_pub_language',
                'label' => 'Language',
                'name' => 'language',
                'type' => 'select',
                'choices' => array(
                    'en' => 'English',
                    'sw' => 'Swahili',
                    'fr' => 'French',
                ),
                'default_value' => 'en',
            ),
            array(
                'key' => 'field_pub_doi',
                'label' => 'DOI (Digital Object Identifier)',
                'name' => 'doi',
                'type' => 'text',
                'placeholder' => '10.1234/gloc.2024.001',
                'instructions' => 'Digital Object Identifier if available',
            ),
            array(
                'key' => 'field_pub_isbn',
                'label' => 'ISBN',
                'name' => 'isbn',
                'type' => 'text',
                'placeholder' => '978-9966-xxx-xx-x',
            ),
            array(
                'key' => 'field_pub_download_count',
                'label' => 'Download Count',
                'name' => 'download_count',
                'type' => 'number',
                'default_value' => 0,
                'instructions' => 'Auto-tracked. Manually adjust if needed.',
                'readonly' => 1,
            ),
            
            // Tab: Additional
            array(
                'key' => 'field_pub_tab_additional',
                'label' => 'Additional',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_pub_is_featured',
                'label' => 'Featured Publication',
                'name' => 'is_featured',
                'type' => 'true_false',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'publication',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));

    /**
     * =========================================
     * EVENT FIELDS - Comprehensive Structure
     * Matches event-single.html layout
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_event_fields',
        'title' => 'Event Details',
        'fields' => array(
            // Tab: Basic Information
            array(
                'key' => 'field_event_tab_basic',
                'label' => 'Basic Information',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_date',
                'label' => 'Start Date',
                'name' => 'event_date',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
                'required' => 1,
            ),
            array(
                'key' => 'field_event_end_date',
                'label' => 'End Date (for multi-day events)',
                'name' => 'event_end_date',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
                'instructions' => 'Leave empty for single-day events',
            ),
            array(
                'key' => 'field_event_time',
                'label' => 'Event Time',
                'name' => 'event_time',
                'type' => 'text',
                'placeholder' => '9:00 AM - 5:00 PM EAT',
                'required' => 1,
            ),
            array(
                'key' => 'field_event_is_virtual',
                'label' => 'Virtual Event',
                'name' => 'is_virtual',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ),
            array(
                'key' => 'field_event_is_featured',
                'label' => 'Featured Event',
                'name' => 'is_featured',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ),
            
            // Tab: Venue & Location
            array(
                'key' => 'field_event_tab_venue',
                'label' => 'Venue & Location',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_venue_name',
                'label' => 'Venue Name',
                'name' => 'venue_name',
                'type' => 'text',
                'placeholder' => 'Kenyatta International Convention Centre',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_event_is_virtual',
                            'operator' => '==',
                            'value' => '0',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_event_venue_address',
                'label' => 'Venue Address',
                'name' => 'venue_address',
                'type' => 'textarea',
                'placeholder' => 'Full address of the venue',
                'rows' => 2,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_event_is_virtual',
                            'operator' => '==',
                            'value' => '0',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_event_location_city',
                'label' => 'City',
                'name' => 'location_city',
                'type' => 'text',
                'placeholder' => 'Nairobi',
            ),
            array(
                'key' => 'field_event_location_country',
                'label' => 'Country',
                'name' => 'location_country',
                'type' => 'text',
                'placeholder' => 'Kenya',
            ),
            array(
                'key' => 'field_event_map_embed_url',
                'label' => 'Google Maps Embed URL',
                'name' => 'map_embed_url',
                'type' => 'url',
                'instructions' => 'Get the embed URL from Google Maps (Share > Embed a map)',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_event_is_virtual',
                            'operator' => '==',
                            'value' => '0',
                        ),
                    ),
                ),
            ),
            
            // Tab: Description & Content
            array(
                'key' => 'field_event_tab_description',
                'label' => 'Description & Content',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_description_lead',
                'label' => 'Lead Description',
                'name' => 'description_lead',
                'type' => 'textarea',
                'instructions' => 'Short introductory paragraph (appears first, in larger text)',
                'rows' => 3,
            ),
            array(
                'key' => 'field_event_key_themes',
                'label' => 'Key Themes',
                'name' => 'key_themes',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Theme',
                'sub_fields' => array(
                    array(
                        'key' => 'field_event_theme_item',
                        'label' => 'Theme',
                        'name' => 'theme',
                        'type' => 'text',
                    ),
                ),
            ),
            array(
                'key' => 'field_event_who_should_attend',
                'label' => 'Who Should Attend',
                'name' => 'who_should_attend',
                'type' => 'wysiwyg',
                'media_upload' => 0,
                'tabs' => 'all',
                'toolbar' => 'basic',
            ),
            
            // Tab: Speakers
            array(
                'key' => 'field_event_tab_speakers',
                'label' => 'Featured Speakers',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_speakers',
                'label' => 'Speakers',
                'name' => 'speakers',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Speaker',
                'sub_fields' => array(
                    array(
                        'key' => 'field_event_speaker_image',
                        'label' => 'Speaker Photo',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_event_speaker_name',
                        'label' => 'Name',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_event_speaker_title',
                        'label' => 'Title/Position',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'GLOCEPS Chairman',
                    ),
                ),
            ),
            
            // Tab: Agenda
            array(
                'key' => 'field_event_tab_agenda',
                'label' => 'Event Agenda',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_agenda',
                'label' => 'Agenda Days',
                'name' => 'agenda',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Day',
                'sub_fields' => array(
                    array(
                        'key' => 'field_event_agenda_day_title',
                        'label' => 'Day Title',
                        'name' => 'day_title',
                        'type' => 'text',
                        'placeholder' => 'Day 1 - January 28',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_event_agenda_items',
                        'label' => 'Agenda Items',
                        'name' => 'items',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'Add Agenda Item',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_event_agenda_time',
                                'label' => 'Time',
                                'name' => 'time',
                                'type' => 'text',
                                'placeholder' => '9:00 - 11:00',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_event_agenda_title',
                                'label' => 'Session Title',
                                'name' => 'title',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_event_agenda_description',
                                'label' => 'Description',
                                'name' => 'description',
                                'type' => 'textarea',
                                'rows' => 2,
                            ),
                        ),
                    ),
                ),
            ),
            
            // Tab: Registration
            array(
                'key' => 'field_event_tab_registration',
                'label' => 'Registration',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_registration_link',
                'label' => 'Registration URL',
                'name' => 'registration_link',
                'type' => 'url',
                'instructions' => 'Link to registration page or form',
            ),
            array(
                'key' => 'field_event_registration_fee',
                'label' => 'Registration Fee',
                'name' => 'registration_fee',
                'type' => 'text',
                'placeholder' => 'KES 15,000',
            ),
            array(
                'key' => 'field_event_registration_includes',
                'label' => 'What\'s Included',
                'name' => 'registration_includes',
                'type' => 'textarea',
                'placeholder' => 'Includes conference materials, meals, and certificate of attendance',
                'rows' => 2,
            ),
            array(
                'key' => 'field_event_registration_deadline',
                'label' => 'Registration Deadline',
                'name' => 'registration_deadline',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
            ),
            array(
                'key' => 'field_event_early_bird_discount',
                'label' => 'Early Bird Discount Info',
                'name' => 'early_bird_discount',
                'type' => 'text',
                'placeholder' => 'Early bird discount: 20% off until January 10, 2025',
            ),
            
            // Tab: Related Content
            array(
                'key' => 'field_event_tab_related',
                'label' => 'Related Content',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_related_publications',
                'label' => 'Related Publications',
                'name' => 'related_publications',
                'type' => 'relationship',
                'post_type' => array('publication'),
                'filters' => array('search'),
                'return_format' => 'object',
            ),
            
            // Tab: Organizer & Partners
            array(
                'key' => 'field_event_tab_organizer',
                'label' => 'Organizer & Partners',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_organizer_name',
                'label' => 'Organizer Name',
                'name' => 'organizer_name',
                'type' => 'text',
                'default_value' => 'GLOCEPS',
            ),
            array(
                'key' => 'field_event_organizer_description',
                'label' => 'Organizer Description',
                'name' => 'organizer_description',
                'type' => 'text',
                'default_value' => 'Global Centre for Policy and Strategy',
            ),
            array(
                'key' => 'field_event_partners',
                'label' => 'Event Partners',
                'name' => 'partners',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Partner',
                'sub_fields' => array(
                    array(
                        'key' => 'field_event_partner_logo',
                        'label' => 'Partner Logo',
                        'name' => 'logo',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_event_partner_name',
                        'label' => 'Partner Name',
                        'name' => 'name',
                        'type' => 'text',
                    ),
                ),
            ),
            
            // Tab: Gallery
            array(
                'key' => 'field_event_tab_gallery',
                'label' => 'Gallery',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_event_gallery',
                'label' => 'Event Gallery',
                'name' => 'gallery',
                'type' => 'gallery',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'insert' => 'append',
                'library' => 'all',
                'instructions' => 'Add photos from the event. These will be displayed in a grid with thumbnail navigation below.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'event',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
    ));

    /**
     * =========================================
     * TEAM MEMBER FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_team_member_fields',
        'title' => 'Team Member Details',
        'fields' => array(
            array(
                'key' => 'field_team_job_title',
                'label' => 'Job Title / Position',
                'name' => 'job_title',
                'type' => 'text',
                'instructions' => 'e.g., Strategic Advisor, Research Director',
            ),
            array(
                'key' => 'field_team_credentials',
                'label' => 'Credentials / Affiliations',
                'name' => 'credentials',
                'type' => 'text',
                'instructions' => 'e.g., MGH, Nsc (AU), PhD, CBS, Rcds (UK)',
            ),
            array(
                'key' => 'field_team_bio',
                'label' => 'Full Biography',
                'name' => 'biography',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Full biography text that appears in the popup modal. Use paragraphs for proper formatting.',
            ),
            array(
                'key' => 'field_team_linkedin',
                'label' => 'LinkedIn URL',
                'name' => 'linkedin_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_team_twitter',
                'label' => 'Twitter/X URL',
                'name' => 'twitter_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_team_email',
                'label' => 'Email',
                'name' => 'email',
                'type' => 'email',
            ),
            array(
                'key' => 'field_team_order',
                'label' => 'Display Order',
                'name' => 'display_order',
                'type' => 'number',
                'default_value' => 10,
                'instructions' => 'Lower numbers appear first. Used for sorting within categories.',
            ),
            array(
                'key' => 'field_team_research_pillars',
                'label' => 'Research Pillars',
                'name' => 'research_pillars',
                'type' => 'taxonomy',
                'taxonomy' => 'research_pillar',
                'field_type' => 'multi_select',
                'allow_null' => 1,
                'return_format' => 'id',
                'instructions' => 'Select research pillars this team member contributes to. Used for filtering on pillar pages.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'team_member',
                ),
            ),
        ),
    ));

    /**
     * =========================================
     * VIDEO FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_video_fields',
        'title' => 'Video Details',
        'fields' => array(
            array(
                'key' => 'field_video_source_type',
                'label' => 'Video Source',
                'name' => 'video_source_type',
                'type' => 'radio',
                'choices' => array(
                    'embed' => 'Embed URL (YouTube/Vimeo)',
                    'upload' => 'Uploaded Video File',
                ),
                'default_value' => 'embed',
                'layout' => 'vertical',
            ),
            array(
                'key' => 'field_video_url',
                'label' => 'Video URL (YouTube/Vimeo)',
                'name' => 'video_url',
                'type' => 'url',
                'instructions' => 'Enter the full URL from YouTube or Vimeo (e.g., https://www.youtube.com/watch?v=...)',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_video_source_type',
                            'operator' => '==',
                            'value' => 'embed',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_video_file',
                'label' => 'Video File',
                'name' => 'video_file',
                'type' => 'file',
                'instructions' => 'Upload a video file (MP4, WebM, etc.)',
                'return_format' => 'array',
                'library' => 'all',
                'mime_types' => 'mp4,webm,ogg',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_video_source_type',
                            'operator' => '==',
                            'value' => 'upload',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_video_duration',
                'label' => 'Duration',
                'name' => 'duration',
                'type' => 'text',
                'instructions' => 'Enter video duration in format MM:SS or HH:MM:SS (e.g., 45:30 or 1:12:45)',
                'placeholder' => '45:30',
                'required' => 1,
            ),
            array(
                'key' => 'field_video_thumbnail',
                'label' => 'Video Thumbnail',
                'name' => 'video_thumbnail',
                'type' => 'image',
                'instructions' => 'Upload a custom thumbnail. If not provided, featured image will be used.',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'video',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
    
    /**
     * =========================================
     * VIDEO ARCHIVE SETTINGS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_video_archive_options',
        'title' => 'Video Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_video_intro_title',
                'label' => 'Page Title',
                'name' => 'video_intro_title',
                'type' => 'text',
                'default_value' => 'Videos',
            ),
            array(
                'key' => 'field_video_intro_description',
                'label' => 'Page Description',
                'name' => 'video_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => 'br',
                'default_value' => 'Watch expert interviews, panel discussions, webinar recordings, and event highlights from GLOCEPS.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-video',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    /**
     * =========================================
     * PODCAST FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_podcast_fields',
        'title' => 'Podcast Details',
        'fields' => array(
            array(
                'key' => 'field_podcast_source_type',
                'label' => 'Podcast Source',
                'name' => 'podcast_source_type',
                'type' => 'radio',
                'choices' => array(
                    'embed' => 'Embed from Platform (Spotify, Apple Podcasts, etc.)',
                    'upload' => 'Uploaded Audio File',
                    'external' => 'External Link (Listen on External Platform)',
                ),
                'default_value' => 'embed',
                'layout' => 'vertical',
            ),
            array(
                'key' => 'field_podcast_url',
                'label' => 'Podcast URL',
                'name' => 'podcast_url',
                'type' => 'url',
                'instructions' => 'Enter the full URL from Spotify, Apple Podcasts, Google Podcasts, or other platforms',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_podcast_source_type',
                            'operator' => '==',
                            'value' => 'embed',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_podcast_external_url',
                'label' => 'External Podcast URL',
                'name' => 'podcast_external_url',
                'type' => 'url',
                'instructions' => 'Enter the URL where users can listen to this podcast on an external platform',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_podcast_source_type',
                            'operator' => '==',
                            'value' => 'external',
                        ),
                    ),
                ),
                'required' => 1,
            ),
            array(
                'key' => 'field_podcast_audio_file',
                'label' => 'Audio File',
                'name' => 'podcast_audio_file',
                'type' => 'file',
                'instructions' => 'Upload an audio file (MP3, M4A, etc.)',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_podcast_source_type',
                            'operator' => '==',
                            'value' => 'upload',
                        ),
                    ),
                ),
                'return_format' => 'array',
                'library' => 'all',
                'mime_types' => 'mp3,m4a,ogg,wav',
            ),
            array(
                'key' => 'field_podcast_episode_number',
                'label' => 'Episode Number',
                'name' => 'episode_number',
                'type' => 'number',
                'instructions' => 'Enter the episode number (e.g., 24)',
                'required' => 0,
                'min' => 1,
            ),
            array(
                'key' => 'field_podcast_duration',
                'label' => 'Duration',
                'name' => 'duration',
                'type' => 'text',
                'placeholder' => '32:15 or 1:15:00',
                'instructions' => 'Enter the podcast duration (e.g., 32:15 for 32 minutes 15 seconds, or 1:15:00 for 1 hour 15 minutes)',
            ),
            array(
                'key' => 'field_podcast_thumbnail',
                'label' => 'Custom Podcast Thumbnail',
                'name' => 'podcast_thumbnail',
                'type' => 'image',
                'instructions' => 'Upload a custom thumbnail image for the podcast. If not set, the featured image will be used.',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'podcast',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    /**
     * =========================================
     * SPEECH FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_speech_fields',
        'title' => 'Speech Details',
        'fields' => array(
            array(
                'key' => 'field_speech_date',
                'label' => 'Speech Date',
                'name' => 'speech_date',
                'type' => 'date_picker',
                'instructions' => 'Select the date when the speech was delivered',
                'required' => 1,
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_speech_file',
                'label' => 'Speech File',
                'name' => 'speech_file',
                'type' => 'file',
                'instructions' => 'Upload the downloadable speech file (PDF, DOC, DOCX, etc.)',
                'required' => 1,
                'return_format' => 'array',
                'library' => 'all',
                'mime_types' => 'pdf,doc,docx,txt',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'speech',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    /**
     * =========================================
     * PODCAST ARCHIVE SETTINGS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_podcast_archive_options',
        'title' => 'Podcast Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_podcast_intro_title',
                'label' => 'Page Title',
                'name' => 'podcast_intro_title',
                'type' => 'text',
                'default_value' => 'Podcasts',
            ),
            array(
                'key' => 'field_podcast_intro_description',
                'label' => 'Page Description',
                'name' => 'podcast_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => 'br',
                'default_value' => 'Listen to in-depth policy discussions, expert analysis, and strategic insights on the go.',
            ),
            array(
                'key' => 'field_podcast_spotify_url',
                'label' => 'Spotify URL',
                'name' => 'podcast_spotify_url',
                'type' => 'url',
                'instructions' => 'Enter the Spotify podcast URL for the subscribe banner',
            ),
            array(
                'key' => 'field_podcast_apple_url',
                'label' => 'Apple Podcasts URL',
                'name' => 'podcast_apple_url',
                'type' => 'url',
                'instructions' => 'Enter the Apple Podcasts URL for the subscribe banner',
            ),
            array(
                'key' => 'field_podcast_google_url',
                'label' => 'Google Podcasts URL',
                'name' => 'podcast_google_url',
                'type' => 'url',
                'instructions' => 'Enter the Google Podcasts URL for the subscribe banner',
            ),
            array(
                'key' => 'field_podcast_rss_url',
                'label' => 'RSS Feed URL',
                'name' => 'podcast_rss_url',
                'type' => 'url',
                'instructions' => 'Enter the RSS feed URL for the subscribe banner',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-podcast',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    /**
     * =========================================
     * SPEECH ARCHIVE SETTINGS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_speech_archive_options',
        'title' => 'Speech Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_speech_intro_title',
                'label' => 'Page Title',
                'name' => 'speech_intro_title',
                'type' => 'text',
                'default_value' => 'Speeches',
            ),
            array(
                'key' => 'field_speech_intro_description',
                'label' => 'Page Description',
                'name' => 'speech_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => 'br',
                'default_value' => 'Access speeches and statements delivered by GLOCEPS leadership and experts on key policy issues.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-speeches',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    /**
     * =========================================
     * ARTICLE FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_article_fields',
        'title' => 'Article Details',
        'fields' => array(
            array(
                'key' => 'field_article_author_type',
                'label' => 'Author Type',
                'name' => 'author_type',
                'type' => 'radio',
                'choices' => array(
                    'team' => 'Team Member',
                    'guest' => 'Guest Author',
                ),
                'default_value' => 'team',
                'layout' => 'vertical',
            ),
            array(
                'key' => 'field_article_team_member',
                'label' => 'Team Member(s)',
                'name' => 'team_member',
                'type' => 'post_object',
                'instructions' => 'Select one or more team members as authors',
                'post_type' => array('team_member'),
                'return_format' => 'object',
                'multiple' => 1,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_article_author_type',
                            'operator' => '==',
                            'value' => 'team',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_article_guest_authors',
                'label' => 'Guest Author(s)',
                'name' => 'guest_authors',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Guest Author',
                'instructions' => 'Add one or more guest authors',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_article_author_type',
                            'operator' => '==',
                            'value' => 'guest',
                        ),
                    ),
                ),
                'sub_fields' => array(
                    array(
                        'key' => 'field_article_guest_name',
                        'label' => 'Guest Author Name',
                        'name' => 'name',
                        'type' => 'text',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_article_guest_title',
                        'label' => 'Guest Author Title/Position',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Senior Research Fellow, GLOCEPS',
                    ),
                    array(
                        'key' => 'field_article_guest_bio',
                        'label' => 'Guest Author Bio',
                        'name' => 'bio',
                        'type' => 'textarea',
                        'rows' => 4,
                    ),
                    array(
                        'key' => 'field_article_guest_image',
                        'label' => 'Guest Author Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Upload author photo. If not set, a placeholder will be used.',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                    ),
                ),
            ),
            array(
                'key' => 'field_article_read_time',
                'label' => 'Read Time (minutes)',
                'name' => 'read_time',
                'type' => 'number',
                'instructions' => 'Estimated reading time in minutes (e.g., 8)',
                'min' => 1,
                'default_value' => 5,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'article',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));

    /**
     * =========================================
     * ARTICLE ARCHIVE SETTINGS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_article_archive_options',
        'title' => 'Article Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_article_intro_title',
                'label' => 'Page Title',
                'name' => 'article_intro_title',
                'type' => 'text',
                'default_value' => 'Articles',
            ),
            array(
                'key' => 'field_article_intro_description',
                'label' => 'Page Description',
                'name' => 'article_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => 'br',
                'default_value' => 'Read opinion pieces, analysis, and commentary from our experts on policy and strategy matters.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-article',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));
    
    // Publications Archive Settings
    acf_add_local_field_group(array(
        'key' => 'group_publications_archive_options',
        'title' => 'Publications Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_publications_intro_title',
                'label' => 'Page Title',
                'name' => 'publications_intro_title',
                'type' => 'text',
                'default_value' => 'Publications',
            ),
            array(
                'key' => 'field_publications_intro_description',
                'label' => 'Page Description',
                'name' => 'publications_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => 'br',
                'default_value' => 'Explore our research papers, policy briefs, bulletins, and analysis on policy and strategy across Eastern Africa. Free resources and premium publications available.',
            ),
            array(
                'key' => 'field_publications_per_page',
                'label' => 'Publications Per Page',
                'name' => 'publications_per_page',
                'type' => 'number',
                'instructions' => 'Number of publications to display per page on the archive page. Default is 12.',
                'default_value' => 12,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'required' => 1,
            ),
            array(
                'key' => 'field_publications_cta_title',
                'label' => 'CTA Title',
                'name' => 'publications_cta_title',
                'type' => 'text',
                'default_value' => 'Access Premium Research',
                'instructions' => 'Title for the call-to-action section at the bottom of the publications archive.',
            ),
            array(
                'key' => 'field_publications_cta_description',
                'label' => 'CTA Description',
                'name' => 'publications_cta_description',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => 'br',
                'default_value' => 'Get exclusive access to in-depth policy papers, research reports, and conference proceedings. Support evidence-based policy making in Eastern Africa.',
                'instructions' => 'Description text for the call-to-action section.',
            ),
            array(
                'key' => 'field_publications_cta_bg_image',
                'label' => 'CTA Background Image',
                'name' => 'publications_cta_bg_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'Optional background image for the CTA section. If not provided, a gradient background will be used.',
            ),
            array(
                'key' => 'field_publications_cta_primary_label',
                'label' => 'Primary Button Label',
                'name' => 'publications_cta_primary_label',
                'type' => 'text',
                'default_value' => 'Browse Premium Publications',
                'instructions' => 'Label for the primary call-to-action button.',
            ),
            array(
                'key' => 'field_publications_cta_primary_link',
                'label' => 'Primary Button Link',
                'name' => 'publications_cta_primary_link',
                'type' => 'url',
                'default_value' => '',
                'instructions' => 'Leave empty to auto-link to publications page with premium filter. Or enter a custom URL.',
            ),
            array(
                'key' => 'field_publications_cta_secondary_label',
                'label' => 'Secondary Button Label',
                'name' => 'publications_cta_secondary_label',
                'type' => 'text',
                'default_value' => 'Get in Touch',
                'instructions' => 'Label for the secondary call-to-action button.',
            ),
            array(
                'key' => 'field_publications_cta_secondary_link',
                'label' => 'Secondary Button Link',
                'name' => 'publications_cta_secondary_link',
                'type' => 'url',
                'default_value' => '',
                'instructions' => 'Leave empty to auto-link to contact page. Or enter a custom URL.',
            ),
            array(
                'key' => 'field_publications_cta_show_secondary',
                'label' => 'Show Secondary Button',
                'name' => 'publications_cta_show_secondary',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
                'instructions' => 'Toggle to show or hide the secondary call-to-action button.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-publications',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));
    
    // General Settings
    acf_add_local_field_group(array(
        'key' => 'group_general_settings',
        'title' => 'General Settings',
        'fields' => array(
            array(
                'key' => 'field_checkout_what_youll_receive_title',
                'label' => 'Checkout: "What you\'ll receive" Title',
                'name' => 'checkout_what_youll_receive_title',
                'type' => 'text',
                'default_value' => 'What you\'ll receive:',
                'instructions' => 'Title for the "What you\'ll receive" section on the checkout page.',
            ),
            array(
                'key' => 'field_checkout_what_youll_receive_items',
                'label' => 'Checkout: "What you\'ll receive" Items',
                'name' => 'checkout_what_youll_receive_items',
                'type' => 'repeater',
                'min' => 1,
                'max' => 10,
                'layout' => 'table',
                'button_label' => 'Add Item',
                'sub_fields' => array(
                    array(
                        'key' => 'field_checkout_item_text',
                        'label' => 'Item Text',
                        'name' => 'text',
                        'type' => 'text',
                        'required' => 1,
                    ),
                ),
                'default_value' => array(
                    array('text' => 'Instant access to download PDF files'),
                    array('text' => 'Email with download links'),
                    array('text' => 'Receipt for your records'),
                    array('text' => 'Ability to re-download anytime'),
                ),
            ),
            array(
                'key' => 'field_google_analytics_code',
                'label' => 'Google Analytics Code',
                'name' => 'google_analytics_code',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => 'Paste your Google Analytics tracking code here. It will be added to the <head> section of all pages.',
            ),
            // Newsletter form field removed - now in Footer Settings (footer_newsletter_form)
            // This prevents conflicting settings between General and Footer
            array(
                'key' => 'field_checkout_enable_coupons',
                'label' => 'Enable Coupon Code on Checkout',
                'name' => 'checkout_enable_coupons',
                'type' => 'true_false',
                'instructions' => 'Enable or disable the coupon code section on the checkout page. When disabled, customers will not see the "Have a coupon?" option.',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_order_email_settings',
                'label' => 'Order Completion Email Settings',
                'name' => 'order_email_settings',
                'type' => 'group',
                'instructions' => 'Configure the email sent to customers when their order is completed.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_order_email_from',
                        'label' => 'From Email Address',
                        'name' => 'from_email',
                        'type' => 'email',
                        'default_value' => 'orders@gloceps.org',
                        'instructions' => 'Email address that will send order completion emails.',
                    ),
                    array(
                        'key' => 'field_order_email_from_name',
                        'label' => 'From Name',
                        'name' => 'from_name',
                        'type' => 'text',
                        'default_value' => 'GLOCEPS',
                        'instructions' => 'Name that will appear as the sender.',
                    ),
                    array(
                        'key' => 'field_order_email_subject',
                        'label' => 'Email Subject',
                        'name' => 'subject',
                        'type' => 'text',
                        'default_value' => 'Your GLOCEPS Publications - Order #{order_number}',
                        'instructions' => 'Subject line for order completion emails. Use {order_number} as placeholder.',
                    ),
                ),
            ),
            array(
                'key' => 'field_checkout_help_section',
                'label' => 'Checkout: Help Section',
                'name' => 'checkout_help_section',
                'type' => 'group',
                'instructions' => 'Customize the "Need help?" section displayed on the checkout page.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_checkout_help_prefix',
                        'label' => 'Help Text Prefix',
                        'name' => 'prefix',
                        'type' => 'text',
                        'default_value' => 'Need help?',
                        'instructions' => 'Text before the support links (e.g., "Need help?")',
                    ),
                    array(
                        'key' => 'field_checkout_help_support_text',
                        'label' => 'Support Link Text',
                        'name' => 'support_text',
                        'type' => 'text',
                        'default_value' => 'Contact support',
                        'instructions' => 'Text for the support link',
                    ),
                    array(
                        'key' => 'field_checkout_help_support_url',
                        'label' => 'Support Link URL',
                        'name' => 'support_url',
                        'type' => 'url',
                        'default_value' => '',
                        'instructions' => 'URL for the support link. Leave empty to use the Contact page URL.',
                    ),
                    array(
                        'key' => 'field_checkout_help_phone_text',
                        'label' => 'Phone Number Text',
                        'name' => 'phone_text',
                        'type' => 'text',
                        'default_value' => '+254 112 401 331',
                        'instructions' => 'Phone number to display',
                    ),
                    array(
                        'key' => 'field_checkout_help_phone_link',
                        'label' => 'Phone Number Link',
                        'name' => 'phone_link',
                        'type' => 'text',
                        'default_value' => '+254112401331',
                        'instructions' => 'Phone number for the tel: link (without spaces or special characters)',
                    ),
                ),
            ),
            array(
                'key' => 'field_typography_settings',
                'label' => 'Typography Settings',
                'name' => 'typography_settings',
                'type' => 'group',
                'instructions' => 'Control fonts and heading sizes for the site.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_heading_font',
                        'label' => 'Heading Font',
                        'name' => 'heading_font',
                        'type' => 'select',
                        'choices' => array(
                            'Effra' => 'Effra (Sans-serif - Official)',
                            'Fraunces' => 'Fraunces (Serif - Default)',
                            'DM Sans' => 'DM Sans (Sans-serif)',
                            'Georgia' => 'Georgia (Serif)',
                            'Playfair Display' => 'Playfair Display (Serif)',
                            'Merriweather' => 'Merriweather (Serif)',
                        ),
                        'default_value' => 'Fraunces',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_body_font',
                        'label' => 'Body Font',
                        'name' => 'body_font',
                        'type' => 'select',
                        'choices' => array(
                            'Effra' => 'Effra (Sans-serif - Official)',
                            'DM Sans' => 'DM Sans (Sans-serif - Default)',
                            'Inter' => 'Inter (Sans-serif)',
                            'Open Sans' => 'Open Sans (Sans-serif)',
                            'Lato' => 'Lato (Sans-serif)',
                            'Roboto' => 'Roboto (Sans-serif)',
                        ),
                        'default_value' => 'DM Sans',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_site_title_font',
                        'label' => 'Site Title Font',
                        'name' => 'site_title_font',
                        'type' => 'select',
                        'choices' => array(
                            'Tahoma' => 'Tahoma (Default)',
                            'Effra' => 'Effra (Sans-serif - Official)',
                            'DM Sans' => 'DM Sans (Sans-serif)',
                            'Inter' => 'Inter (Sans-serif)',
                            'Arial' => 'Arial (Sans-serif)',
                        ),
                        'default_value' => 'Tahoma',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                        'instructions' => 'Font for the site title in the header. Tahoma is the default as specified by the client.',
                    ),
                    array(
                        'key' => 'field_heading_size_h1',
                        'label' => 'H1 Size',
                        'name' => 'heading_size_h1',
                        'type' => 'select',
                        'choices' => array(
                            'text-4xl' => 'Small (2.25rem)',
                            'text-5xl' => 'Medium (3rem - Default)',
                            'text-6xl' => 'Large (4rem)',
                            'text-7xl' => 'Extra Large (5.5rem)',
                        ),
                        'default_value' => 'text-5xl',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_heading_size_h2',
                        'label' => 'H2 Size',
                        'name' => 'heading_size_h2',
                        'type' => 'select',
                        'choices' => array(
                            'text-3xl' => 'Small (1.875rem)',
                            'text-4xl' => 'Medium (2.25rem - Default)',
                            'text-5xl' => 'Large (3rem)',
                            'text-6xl' => 'Extra Large (4rem)',
                        ),
                        'default_value' => 'text-4xl',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_heading_size_h3',
                        'label' => 'H3 Size',
                        'name' => 'heading_size_h3',
                        'type' => 'select',
                        'choices' => array(
                            'text-2xl' => 'Small (1.5rem)',
                            'text-3xl' => 'Medium (1.875rem - Default)',
                            'text-4xl' => 'Large (2.25rem)',
                            'text-5xl' => 'Extra Large (3rem)',
                        ),
                        'default_value' => 'text-3xl',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                    ),
                ),
            ),
            array(
                'key' => 'field_text_truncation',
                'label' => 'Text Truncation Settings',
                'name' => 'text_truncation',
                'type' => 'group',
                'instructions' => 'Control how long titles and descriptions are displayed before being truncated with ellipsis.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_enable_truncation',
                        'label' => 'Enable Text Truncation',
                        'name' => 'enable_truncation',
                        'type' => 'true_false',
                        'default_value' => 1,
                        'ui' => 1,
                        'instructions' => 'When enabled, long titles and descriptions will be truncated with ellipsis. When disabled, full text will be shown.',
                    ),
                    array(
                        'key' => 'field_title_word_limit',
                        'label' => 'Title Word Limit',
                        'name' => 'title_word_limit',
                        'type' => 'number',
                        'default_value' => 10,
                        'min' => 3,
                        'max' => 30,
                        'instructions' => 'Maximum number of words for card titles before truncation (default: 10 words).',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_enable_truncation',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_description_word_limit',
                        'label' => 'Description Word Limit',
                        'name' => 'description_word_limit',
                        'type' => 'number',
                        'default_value' => 20,
                        'min' => 5,
                        'max' => 50,
                        'instructions' => 'Maximum number of words for descriptions/excerpts before truncation (default: 20 words).',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_enable_truncation',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_color_settings',
                'label' => 'Color Settings',
                'name' => 'color_settings',
                'type' => 'group',
                'instructions' => 'Control primary, secondary, and tertiary brand colors.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_color_primary',
                        'label' => 'Primary Color',
                        'name' => 'primary_color',
                        'type' => 'color_picker',
                        'default_value' => '#3f93c1',
                        'instructions' => 'Main brand color used for buttons, links, and accents.',
                    ),
                    array(
                        'key' => 'field_color_secondary',
                        'label' => 'Secondary Color',
                        'name' => 'secondary_color',
                        'type' => 'color_picker',
                        'default_value' => '#70b544',
                        'instructions' => 'Secondary accent color for highlights and special elements.',
                    ),
                    array(
                        'key' => 'field_color_tertiary',
                        'label' => 'Tertiary Color',
                        'name' => 'tertiary_color',
                        'type' => 'color_picker',
                        'default_value' => '#c4a35a',
                        'instructions' => 'Tertiary accent color for additional design elements.',
                    ),
                ),
            ),
            array(
                'key' => 'field_footer_backgrounds',
                'label' => 'Footer Background Colors',
                'name' => 'footer_backgrounds',
                'type' => 'group',
                'instructions' => 'Control background colors for different footer sections.',
                'sub_fields' => array(
                    array(
                        'key' => 'field_footer_cta_bg',
                        'label' => 'Footer CTA Background',
                        'name' => 'footer_cta_bg',
                        'type' => 'color_picker',
                        'default_value' => '#1a1a1a',
                        'instructions' => 'Background color for the "Get in Touch" CTA section.',
                    ),
                    array(
                        'key' => 'field_footer_main_bg',
                        'label' => 'Footer Main Background',
                        'name' => 'footer_main_bg',
                        'type' => 'color_picker',
                        'default_value' => '#0d0d0d',
                        'instructions' => 'Background color for the main footer content area.',
                    ),
                    array(
                        'key' => 'field_footer_bottom_bg',
                        'label' => 'Footer Bottom Background',
                        'name' => 'footer_bottom_bg',
                        'type' => 'color_picker',
                        'default_value' => '#141414',
                        'instructions' => 'Background color for the footer bottom (copyright) area.',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-general',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => '',
    ));
    
    // Populate Contact Form 7 choices for footer newsletter form field
    // Note: Removed filter for old 'newsletter_form' field in General Settings
    // Now only 'footer_newsletter_form' in Footer Settings is used
    function gloceps_populate_cf7_forms($field) {
        $field['choices'] = array();
        if (function_exists('wpcf7_contact_form')) {
            $forms = get_posts(array(
                'post_type' => 'wpcf7_contact_form',
                'posts_per_page' => -1,
                'post_status' => 'publish',
            ));
            foreach ($forms as $form) {
                $field['choices'][$form->ID] = $form->post_title;
            }
        }
        return $field;
    }

    /**
     * =========================================
     * GALLERY FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_gallery_fields',
        'title' => 'Gallery Details',
        'fields' => array(
            array(
                'key' => 'field_gallery_images',
                'label' => 'Gallery Images',
                'name' => 'gallery_images',
                'type' => 'gallery',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'insert' => 'append',
            ),
            array(
                'key' => 'field_gallery_event_date',
                'label' => 'Event Date',
                'name' => 'event_date',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
            ),
            array(
                'key' => 'field_gallery_event_end_date',
                'label' => 'Event End Date (optional)',
                'name' => 'event_end_date',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
                'instructions' => 'Leave empty if event is on a single day',
            ),
            array(
                'key' => 'field_gallery_venue',
                'label' => 'Venue',
                'name' => 'venue',
                'type' => 'text',
            ),
            array(
                'key' => 'field_gallery_participant_count',
                'label' => 'Participant Count',
                'name' => 'participant_count',
                'type' => 'number',
                'instructions' => 'Number of participants (will display with + sign)',
            ),
            array(
                'key' => 'field_gallery_photographer',
                'label' => 'Photographer',
                'name' => 'photographer',
                'type' => 'text',
            ),
            array(
                'key' => 'field_gallery_about_event',
                'label' => 'About This Event',
                'name' => 'about_event',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'instructions' => 'Description of the event. This appears in the "About This Event" section.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'gallery',
                ),
            ),
        ),
    ));

    /**
     * =========================================
     * PODCAST FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_podcast_fields',
        'title' => 'Podcast Details',
        'fields' => array(
            array(
                'key' => 'field_podcast_audio_url',
                'label' => 'Audio URL or Embed',
                'name' => 'audio_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_podcast_audio_file',
                'label' => 'Audio File',
                'name' => 'audio_file',
                'type' => 'file',
                'return_format' => 'array',
                'mime_types' => 'mp3,m4a,wav',
            ),
            array(
                'key' => 'field_podcast_episode',
                'label' => 'Episode Number',
                'name' => 'episode_number',
                'type' => 'number',
            ),
            array(
                'key' => 'field_podcast_duration',
                'label' => 'Duration',
                'name' => 'duration',
                'type' => 'text',
                'placeholder' => '32:15',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'podcast',
                ),
            ),
        ),
    ));

    /**
     * =========================================
     * RESEARCH PILLAR PAGE FIELDS
     * =========================================
     */
    acf_add_local_field_group(array(
        'key' => 'group_research_pillar_fields',
        'title' => 'Research Pillar Details',
        'fields' => array(
            array(
                'key' => 'field_pillar_color',
                'label' => 'Pillar Color',
                'name' => 'pillar_color',
                'type' => 'color_picker',
            ),
            array(
                'key' => 'field_pillar_icon',
                'label' => 'Pillar Icon (SVG)',
                'name' => 'pillar_icon',
                'type' => 'textarea',
                'instructions' => 'Paste SVG icon code here',
            ),
            array(
                'key' => 'field_pillar_lead_text',
                'label' => 'Lead Text',
                'name' => 'lead_text',
                'type' => 'textarea',
            ),
            array(
                'key' => 'field_pillar_stats',
                'label' => 'Statistics',
                'name' => 'pillar_stats',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_pillar_stat_value',
                        'label' => 'Value',
                        'name' => 'value',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_pillar_stat_label',
                        'label' => 'Label',
                        'name' => 'label',
                        'type' => 'text',
                    ),
                ),
            ),
            array(
                'key' => 'field_pillar_focus_areas',
                'label' => 'Focus Areas',
                'name' => 'focus_areas',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_focus_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_focus_description',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                    ),
                    array(
                        'key' => 'field_focus_image',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'array',
                    ),
                    array(
                        'key' => 'field_focus_list',
                        'label' => 'List Items',
                        'name' => 'list_items',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_focus_list_item',
                                'label' => 'Item',
                                'name' => 'item',
                                'type' => 'text',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_pillar_themes',
                'label' => 'Research Themes',
                'name' => 'research_themes',
                'type' => 'repeater',
                'sub_fields' => array(
                    array(
                        'key' => 'field_theme_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_theme_description',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                    ),
                    array(
                        'key' => 'field_theme_icon',
                        'label' => 'Icon (SVG)',
                        'name' => 'icon',
                        'type' => 'textarea',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-templates/template-research-pillar.php',
                ),
            ),
        ),
    ));

    /**
     * =========================================
     * THEME OPTIONS
     * =========================================
     */
    if (function_exists('acf_add_options_page')) {
        // Main Theme Settings page
        acf_add_options_page(array(
            'page_title'    => 'Theme Settings',
            'menu_title'    => 'Theme Settings',
            'menu_slug'     => 'theme-settings',
            'capability'    => 'edit_posts',
            'redirect'      => true, // Redirect to first sub-page
            'icon_url'      => 'dashicons-admin-customizer',
        ));

        // Header Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Header Settings',
            'menu_title'    => 'Header',
            'menu_slug'     => 'theme-settings-header',
            'parent_slug'   => 'theme-settings',
        ));

        // Footer Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Footer Settings',
            'menu_title'    => 'Footer',
            'menu_slug'     => 'theme-settings-footer',
            'parent_slug'   => 'theme-settings',
        ));

        // Contact Information
        acf_add_options_sub_page(array(
            'page_title'    => 'Contact Information',
            'menu_title'    => 'Contact Info',
            'menu_slug'     => 'theme-settings-contact',
            'parent_slug'   => 'theme-settings',
        ));

        // Social Media
        acf_add_options_sub_page(array(
            'page_title'    => 'Social Media',
            'menu_title'    => 'Social Media',
            'menu_slug'     => 'theme-settings-social',
            'parent_slug'     => 'theme-settings',
        ));

        // Events Archive Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Events Archive Settings',
            'menu_title'    => 'Events Archive',
            'menu_slug'     => 'theme-settings-events',
            'parent_slug'   => 'theme-settings',
        ));

        // Team Archive Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Team Archive Settings',
            'menu_title'    => 'Team Archive',
            'menu_slug'     => 'theme-settings-team',
            'parent_slug'   => 'theme-settings',
        ));

        // Jobs/Vacancies Archive Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Jobs Archive Settings',
            'menu_title'    => 'Jobs Archive',
            'menu_slug'     => 'theme-settings-vacancies',
            'parent_slug'   => 'theme-settings',
        ));

        // Gallery Archive Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Gallery Archive Settings',
            'menu_title'    => 'Gallery Archive',
            'menu_slug'     => 'theme-settings-gallery',
            'parent_slug'   => 'theme-settings',
        ));
        
        acf_add_options_sub_page(array(
            'page_title'    => 'Video Archive Settings',
            'menu_title'    => 'Video Archive',
            'menu_slug'     => 'theme-settings-video',
            'parent_slug'   => 'theme-settings',
        ));
        
        acf_add_options_sub_page(array(
            'page_title'    => 'Podcast Archive Settings',
            'menu_title'    => 'Podcast Archive',
            'menu_slug'     => 'theme-settings-podcast',
            'parent_slug'   => 'theme-settings',
        ));
        
        acf_add_options_sub_page(array(
            'page_title'    => 'Article Archive Settings',
            'menu_title'    => 'Article Archive',
            'menu_slug'     => 'theme-settings-article',
            'parent_slug'   => 'theme-settings',
        ));
        
        // Publications Archive Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Publications Archive Settings',
            'menu_title'    => 'Publications Archive',
            'menu_slug'     => 'theme-settings-publications',
            'parent_slug'   => 'theme-settings',
        ));
        
        // Speeches Archive Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'Speeches Archive Settings',
            'menu_title'    => 'Speeches Archive',
            'menu_slug'     => 'theme-settings-speeches',
            'parent_slug'   => 'theme-settings',
        ));
        
        // General Settings
        acf_add_options_sub_page(array(
            'page_title'    => 'General Settings',
            'menu_title'    => 'General',
            'menu_slug'     => 'theme-settings-general',
            'parent_slug'   => 'theme-settings',
        ));
    }

    // Register Header Settings Fields
    acf_add_local_field_group(array(
        'key' => 'group_header_options',
        'title' => 'Header Settings',
        'fields' => array(
            array(
                'key' => 'field_header_logo',
                'label' => 'Logo',
                'name' => 'header_logo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'Upload your site logo (recommended: SVG or PNG with transparency)',
            ),
            array(
                'key' => 'field_header_logo_alt',
                'label' => 'Logo (Light Version)',
                'name' => 'header_logo_light',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'Upload a light/white version for transparent headers',
            ),
            array(
                'key' => 'field_header_cta_text',
                'label' => 'CTA Button Text',
                'name' => 'header_cta_text',
                'type' => 'text',
                'default_value' => 'Publications',
            ),
            array(
                'key' => 'field_header_cta_link',
                'label' => 'CTA Button Link',
                'name' => 'header_cta_link',
                'type' => 'link',
            ),
            array(
                'key' => 'field_header_secondary_text',
                'label' => 'Secondary Button Text',
                'name' => 'header_secondary_text',
                'type' => 'text',
                'default_value' => 'Contact Us',
            ),
            array(
                'key' => 'field_header_secondary_link',
                'label' => 'Secondary Button Link',
                'name' => 'header_secondary_link',
                'type' => 'link',
            ),
            array(
                'key' => 'field_header_top_bar',
                'label' => 'Show Top Bar',
                'name' => 'header_top_bar',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_header_top_bar_text',
                'label' => 'Top Bar Text',
                'name' => 'header_top_bar_text',
                'type' => 'text',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_header_top_bar',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_favicon',
                'label' => 'Favicon / Site Icon',
                'name' => 'favicon',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'instructions' => 'Upload favicon/site icon. This will be used as a placeholder for content types without featured images.',
            ),
            array(
                'key' => 'field_header_show_site_title',
                'label' => 'Show Site Title',
                'name' => 'header_show_site_title',
                'type' => 'true_false',
                'instructions' => 'Display the site title from WordPress General Settings next to the logo.',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_header_show_tagline',
                'label' => 'Show Site Tagline',
                'name' => 'header_show_tagline',
                'type' => 'true_false',
                'instructions' => 'Display the site tagline from WordPress General Settings below the site title.',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_header_logo_size',
                'label' => 'Logo Size',
                'name' => 'header_logo_size',
                'type' => 'select',
                'instructions' => 'Control the size of the logo. Small (1x), Medium (2x), or Large (3x).',
                'choices' => array(
                    '1' => 'Small (1x)',
                    '2' => 'Medium (2x)',
                    '3' => 'Large (3x)',
                ),
                'default_value' => '2',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'ajax' => 0,
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_page_header_bg_image',
                'label' => 'Page Header Background Image',
                'name' => 'page_header_background_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => 'Upload a dark background image for inner page headers. This will be used on all pages with the minimal page header. When set, the header will use light styling (white logo) to contrast with the dark background.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-header',
                ),
            ),
        ),
    ));

    acf_add_local_field_group(array(
        'key' => 'group_contact_options',
        'title' => 'Contact Information',
        'fields' => array(
            array(
                'key' => 'field_contact_phone',
                'label' => 'Phone Number',
                'name' => 'contact_phone',
                'type' => 'text',
                'default_value' => '+254 112 401 331',
            ),
            array(
                'key' => 'field_contact_email',
                'label' => 'Email Address',
                'name' => 'contact_email',
                'type' => 'email',
                'default_value' => 'info@gloceps.org',
            ),
            array(
                'key' => 'field_contact_address',
                'label' => 'Address',
                'name' => 'contact_address',
                'type' => 'textarea',
                'default_value' => "P.O Box 27023-00100\nRunda Drive, Nairobi, Kenya",
            ),
            array(
                'key' => 'field_office_hours',
                'label' => 'Office Hours',
                'name' => 'office_hours',
                'type' => 'text',
                'default_value' => 'Mon - Fri: 8:00am - 5:00pm',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-contact',
                ),
            ),
        ),
    ));

    acf_add_local_field_group(array(
        'key' => 'group_social_options',
        'title' => 'Social Media Links',
        'fields' => array(
            array(
                'key' => 'field_social_linkedin',
                'label' => 'LinkedIn URL',
                'name' => 'social_linkedin',
                'type' => 'url',
            ),
            array(
                'key' => 'field_social_twitter',
                'label' => 'Twitter/X URL',
                'name' => 'social_twitter',
                'type' => 'url',
            ),
            array(
                'key' => 'field_social_facebook',
                'label' => 'Facebook URL',
                'name' => 'social_facebook',
                'type' => 'url',
            ),
            array(
                'key' => 'field_social_youtube',
                'label' => 'YouTube URL',
                'name' => 'social_youtube',
                'type' => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-social',
                ),
            ),
        ),
    ));

    acf_add_local_field_group(array(
        'key' => 'group_footer_options',
        'title' => 'Footer Settings',
        'fields' => array(
            array(
                'key' => 'field_footer_cta_title',
                'label' => 'Footer CTA Title',
                'name' => 'footer_cta_title',
                'type' => 'text',
                'default_value' => "Let's Shape Policy Together",
            ),
            array(
                'key' => 'field_footer_cta_text',
                'label' => 'Footer CTA Text',
                'name' => 'footer_cta_text',
                'type' => 'textarea',
                'default_value' => 'Partner with GLOCEPS to advance evidence-based policy making across Eastern Africa.',
            ),
            array(
                'key' => 'field_footer_cta_button_text',
                'label' => 'Footer CTA Button Text',
                'name' => 'footer_cta_button_text',
                'type' => 'text',
                'default_value' => 'Start a Conversation',
            ),
            array(
                'key' => 'field_footer_cta_button_link',
                'label' => 'Footer CTA Button Link',
                'name' => 'footer_cta_button_link',
                'type' => 'link',
            ),
            array(
                'key' => 'field_footer_description',
                'label' => 'Footer Description',
                'name' => 'footer_description',
                'type' => 'textarea',
                'default_value' => 'A leading centre of excellence in policy influence and strategy formulation, advancing peace, security, and development in Eastern Africa.',
            ),
            array(
                'key' => 'field_footer_marquee_text',
                'label' => 'Marquee Text Items',
                'name' => 'footer_marquee_text',
                'type' => 'repeater',
                'instructions' => 'Add text items to display in the scrolling marquee. Each item will be separated by a dot.',
                'layout' => 'table',
                'button_label' => 'Add Item',
                'sub_fields' => array(
                    array(
                        'key' => 'field_marquee_item_text',
                        'label' => 'Text',
                        'name' => 'text',
                        'type' => 'text',
                        'required' => 1,
                    ),
                ),
                'default_value' => array(
                    array('text' => 'Research'),
                    array('text' => 'Knowledge'),
                    array('text' => 'Influence'),
                    array('text' => 'Policy'),
                    array('text' => 'Strategy'),
                ),
            ),
            array(
                'key' => 'field_footer_copyright',
                'label' => 'Copyright Text',
                'name' => 'footer_copyright',
                'type' => 'text',
                'instructions' => 'Copyright text displayed in footer. Use {year} for current year, {site} for site name, {address} for address.',
                'default_value' => '© {year} {site} — {address}',
            ),
            array(
                'key' => 'field_footer_newsletter_form',
                'label' => 'Newsletter Subscription Form',
                'name' => 'footer_newsletter_form',
                'type' => 'select',
                'instructions' => 'Select the Contact Form 7 form to use for newsletter subscriptions in the footer.',
                'choices' => array(),
                'default_value' => '',
                'allow_null' => 1,
                'multiple' => 0,
                'ui' => 1,
                'ajax' => 0,
                'return_format' => 'value',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-footer',
                ),
            ),
        ),
    ));
    
    // Populate Contact Form 7 choices for footer newsletter form field
    add_filter('acf/load_field/name=footer_newsletter_form', 'gloceps_populate_cf7_forms');

    // Register Events Archive Settings Fields
    acf_add_local_field_group(array(
        'key' => 'group_events_archive_options',
        'title' => 'Events Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_events_intro_title',
                'label' => 'Page Title',
                'name' => 'events_intro_title',
                'type' => 'text',
                'default_value' => 'Events',
            ),
            array(
                'key' => 'field_events_intro_description',
                'label' => 'Page Description',
                'name' => 'events_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Join us for webinars, conferences, workshops, and roundtable discussions that bring together thought leaders, policymakers, and practitioners to address critical issues in policy and strategy.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-events',
                ),
            ),
        ),
    ));

    // Register Team Archive Settings Fields
    acf_add_local_field_group(array(
        'key' => 'group_team_archive_options',
        'title' => 'Team Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_team_intro_title',
                'label' => 'Page Title',
                'name' => 'team_intro_title',
                'type' => 'text',
                'default_value' => 'Our People',
            ),
            array(
                'key' => 'field_team_intro_description',
                'label' => 'Page Description',
                'name' => 'team_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Meet the distinguished professionals, thought leaders, and experts driving GLOCEPS\' mission to advance policy research and strategic dialogue.',
            ),
            array(
                'key' => 'field_team_items_per_page',
                'label' => 'Items Per Page',
                'name' => 'team_items_per_page',
                'type' => 'number',
                'instructions' => 'Number of team members to show per category before pagination. Default: 12',
                'default_value' => 12,
                'min' => 1,
                'max' => 50,
                'step' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-team',
                ),
            ),
        ),
    ));

    // Register Jobs/Vacancies Archive Settings Fields
    acf_add_local_field_group(array(
        'key' => 'group_vacancy_archive_options',
        'title' => 'Jobs Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_vacancy_intro_title',
                'label' => 'Page Title',
                'name' => 'vacancy_intro_title',
                'type' => 'text',
                'default_value' => 'Career Opportunities',
            ),
            array(
                'key' => 'field_vacancy_intro_description',
                'label' => 'Page Description',
                'name' => 'vacancy_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Join our team and contribute to advancing policy research and strategic dialogue.',
            ),
            array(
                'key' => 'field_vacancy_items_per_page',
                'label' => 'Items Per Page',
                'name' => 'vacancy_items_per_page',
                'type' => 'number',
                'instructions' => 'Number of jobs to show per page. Default: 12',
                'default_value' => 12,
                'min' => 1,
                'max' => 50,
                'step' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-vacancies',
                ),
            ),
        ),
    ));

    // Register Gallery Archive Settings Fields
    acf_add_local_field_group(array(
        'key' => 'group_gallery_archive_options',
        'title' => 'Gallery Archive Settings',
        'fields' => array(
            array(
                'key' => 'field_gallery_intro_title',
                'label' => 'Page Title',
                'name' => 'gallery_intro_title',
                'type' => 'text',
                'default_value' => 'Photo Gallery',
            ),
            array(
                'key' => 'field_gallery_intro_description',
                'label' => 'Page Description',
                'name' => 'gallery_intro_description',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Browse photographs from conferences, symposiums, workshops, and official engagements.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings-gallery',
                ),
            ),
        ),
    ));
}

/**
 * Get all flexible content layouts
 */
function gloceps_get_flexible_layouts() {
    return array(
        // Hero Sections
        'hero_video' => array(
            'key' => 'layout_hero_video',
            'name' => 'hero_video',
            'label' => 'Hero - Video Background',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_hero_video_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_video_subtitle',
                    'label' => 'Subtitle',
                    'name' => 'subtitle',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_hero_video_file',
                    'label' => 'Video File',
                    'name' => 'video_file',
                    'type' => 'file',
                    'mime_types' => 'mp4,webm',
                    'instructions' => 'Upload a video file (MP4 or WebM). If not available, use YouTube/Vimeo URL below.',
                ),
                array(
                    'key' => 'field_hero_video_url',
                    'label' => 'YouTube or Vimeo URL',
                    'name' => 'video_url',
                    'type' => 'url',
                    'instructions' => 'Enter a YouTube or Vimeo URL if video file is not available. Example: https://www.youtube.com/watch?v=... or https://vimeo.com/...',
                ),
                array(
                    'key' => 'field_hero_video_poster',
                    'label' => 'Poster Image',
                    'name' => 'poster_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'instructions' => 'Fallback image shown when video is loading or unavailable.',
                ),
                array(
                    'key' => 'field_hero_video_background_image',
                    'label' => 'Content Background Image',
                    'name' => 'content_background_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'instructions' => 'Optional: Upload a background image for the content block (left side). If not provided, the gradient will be used as fallback.',
                ),
                array(
                    'key' => 'field_hero_video_btn1_text',
                    'label' => 'Button 1 Text',
                    'name' => 'button1_text',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_video_btn1_link',
                    'label' => 'Button 1 Link',
                    'name' => 'button1_link',
                    'type' => 'link',
                ),
                array(
                    'key' => 'field_hero_video_btn2_text',
                    'label' => 'Button 2 Text',
                    'name' => 'button2_text',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_video_btn2_link',
                    'label' => 'Button 2 Link',
                    'name' => 'button2_link',
                    'type' => 'link',
                ),
            ),
        ),

        'hero_carousel' => array(
            'key' => 'layout_hero_carousel',
            'name' => 'hero_carousel',
            'label' => 'Hero - Image Carousel',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_hero_carousel_slides',
                    'label' => 'Hero Slides',
                    'name' => 'slides',
                    'type' => 'repeater',
                    'instructions' => 'Add up to 3 hero slides. The first slide is required. If slides 2-3 are empty, the first slide data will be used as defaults.',
                    'required' => 1,
                    'min' => 1,
                    'max' => 3,
                    'layout' => 'block',
                    'button_label' => 'Add Slide',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_hero_carousel_slide_image',
                            'label' => 'Background Image',
                            'name' => 'image',
                            'type' => 'image',
                            'instructions' => 'Recommended size: 1920x1080px (16:9 aspect ratio) for optimal display. Minimum: 1600x900px.',
                            'required' => 1,
                            'return_format' => 'array',
                            'preview_size' => 'medium',
                            'library' => 'all',
                        ),
                        array(
                            'key' => 'field_hero_carousel_slide_headline',
                            'label' => 'Headline',
                            'name' => 'headline',
                            'type' => 'text',
                            'instructions' => 'Main headline text displayed on the left side of the image.',
                            'required' => 1,
                            'placeholder' => 'e.g., Research. Knowledge. Influence.',
                        ),
                        array(
                            'key' => 'field_hero_carousel_slide_description',
                            'label' => 'Description / Tagline',
                            'name' => 'description',
                            'type' => 'textarea',
                            'instructions' => 'Supporting text or tagline displayed below the headline.',
                            'rows' => 3,
                            'placeholder' => 'e.g., The Global Centre for Policy and Strategy...',
                        ),
                        array(
                            'key' => 'field_hero_carousel_slide_button1_text',
                            'label' => 'Button 1 Text',
                            'name' => 'button1_text',
                            'type' => 'text',
                            'instructions' => 'Text for the primary call-to-action button.',
                            'required' => 1,
                            'placeholder' => 'e.g., Explore Our Work',
                        ),
                        array(
                            'key' => 'field_hero_carousel_slide_button1_link',
                            'label' => 'Button 1 Link',
                            'name' => 'button1_link',
                            'type' => 'link',
                            'instructions' => 'Link for the primary call-to-action button.',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_hero_carousel_slide_button2_text',
                            'label' => 'Button 2 Text',
                            'name' => 'button2_text',
                            'type' => 'text',
                            'instructions' => 'Text for the secondary call-to-action button.',
                            'placeholder' => 'e.g., Learn More',
                        ),
                        array(
                            'key' => 'field_hero_carousel_slide_button2_link',
                            'label' => 'Button 2 Link',
                            'name' => 'button2_link',
                            'type' => 'link',
                            'instructions' => 'Link for the secondary call-to-action button.',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_hero_carousel_autoplay',
                    'label' => 'Auto-rotate Speed',
                    'name' => 'autoplay_speed',
                    'type' => 'number',
                    'instructions' => 'Time in seconds before automatically moving to the next slide. Set to 0 to disable auto-rotation. Default: 5 seconds.',
                    'default_value' => 5,
                    'min' => 0,
                    'max' => 30,
                    'step' => 1,
                ),
            ),
        ),

        'hero_split' => array(
            'key' => 'layout_hero_split',
            'name' => 'hero_split',
            'label' => 'Hero - Split Layout',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_hero_split_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_split_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_hero_split_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_hero_split_image',
                    'label' => 'Image',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_hero_split_cta',
                    'label' => 'CTA Link',
                    'name' => 'cta_link',
                    'type' => 'link',
                ),
            ),
        ),

        // Page Header
        'page_header' => array(
            'key' => 'layout_page_header',
            'name' => 'page_header',
            'label' => 'Page Header',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_page_header_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => 'Leave empty to use page title',
                ),
                array(
                    'key' => 'field_page_header_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3,
                ),
            ),
        ),

        // Research Pillars
        'research_pillars' => array(
            'key' => 'layout_research_pillars',
            'name' => 'research_pillars',
            'label' => 'Research Pillars Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_pillars_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Research Pillars',
                ),
                array(
                    'key' => 'field_pillars_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Areas of Focus',
                ),
                array(
                    'key' => 'field_pillars_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                ),
            ),
        ),

        // Grid Cards (Reusable Bento Grid with Images)
        'home_pillars' => array(
            'key' => 'layout_home_pillars',
            'name' => 'home_pillars',
            'label' => 'Grid Cards (Bento Layout)',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_home_pillars_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Research Focus Areas',
                ),
                array(
                    'key' => 'field_home_pillars_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Our Five Pillars',
                ),
                array(
                    'key' => 'field_home_pillars_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'default_value' => 'GLOCEPS work cuts across five interconnected pillars addressing the most pressing challenges facing Eastern Africa and the broader region.',
                ),
                array(
                    'key' => 'field_home_pillars_background_style',
                    'label' => 'Background Style',
                    'name' => 'background_style',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (White)',
                        'gray' => 'Light Gray',
                        'light-blue' => 'Light Blue',
                    ),
                    'default_value' => 'default',
                    'instructions' => 'Choose a background style to visually distinguish this section from others.',
                ),
                array(
                    'key' => 'field_home_pillars_use_pillars',
                    'label' => 'Use Research Pillars as Default',
                    'name' => 'use_pillars',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'If enabled, cards will automatically populate from Research Pillars taxonomy. If disabled, use custom cards below.',
                ),
                array(
                    'key' => 'field_home_pillars_use_page_featured',
                    'label' => 'Use Page Featured Image as Fallback',
                    'name' => 'use_page_featured_image',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'If enabled, cards without images will automatically use the page\'s featured image. You can still override individual card images.',
                ),
                array(
                    'key' => 'field_home_pillars_cards',
                    'label' => 'Custom Cards',
                    'name' => 'cards',
                    'type' => 'repeater',
                    'instructions' => 'Add custom cards. Leave empty to use Research Pillars as default.',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_home_pillars_use_pillars',
                                'operator' => '==',
                                'value' => '0',
                            ),
                        ),
                    ),
                    'min' => 1,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Add Card',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_card_image',
                            'label' => 'Card Image',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'array',
                            'required' => 0,
                            'instructions' => 'Leave empty to use page featured image (if enabled above).',
                        ),
                        array(
                            'key' => 'field_card_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_card_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_card_link',
                            'label' => 'Link',
                            'name' => 'link',
                            'type' => 'link',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_card_is_large',
                            'label' => 'Large Card (First position)',
                            'name' => 'is_large',
                            'type' => 'true_false',
                            'ui' => 1,
                            'default_value' => 0,
                            'instructions' => 'Only the first card should be marked as large.',
                        ),
                    ),
                ),
            ),
        ),

        // Other Research Pillars (excludes current pillar)
        'other_pillars' => array(
            'key' => 'layout_other_pillars',
            'name' => 'other_pillars',
            'label' => 'Other Research Pillars',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_other_pillars_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'EXPLORE MORE',
                ),
                array(
                    'key' => 'field_other_pillars_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Other Research Pillars',
                ),
            ),
        ),

        // Featured Publication
        'featured_publication' => array(
            'key' => 'layout_featured_publication',
            'name' => 'featured_publication',
            'label' => 'Featured Publication',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_feat_pub_select',
                    'label' => 'Select Publication',
                    'name' => 'publication',
                    'type' => 'post_object',
                    'post_type' => array('publication'),
                    'return_format' => 'id',
                ),
                array(
                    'key' => 'field_feat_pub_manual',
                    'label' => 'Or Enter Manually',
                    'name' => 'manual_entry',
                    'type' => 'group',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_feat_pub_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_feat_pub_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                        ),
                        array(
                            'key' => 'field_feat_pub_image',
                            'label' => 'Image',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'array',
                        ),
                        array(
                            'key' => 'field_feat_pub_link',
                            'label' => 'Link',
                            'name' => 'link',
                            'type' => 'link',
                        ),
                    ),
                ),
            ),
        ),

        // Latest Research Publications (Editorial Style Listing)
        'latest_publications' => array(
            'key' => 'layout_latest_publications',
            'name' => 'latest_publications',
            'label' => 'Latest Research Publications (Editorial Listing)',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_latest_pub_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Latest Research',
                ),
                array(
                    'key' => 'field_latest_pub_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Publications',
                ),
                array(
                    'key' => 'field_latest_pub_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'default_value' => 'Access our latest policy briefs, research papers, and strategic analyses shaping discourse on regional and global issues.',
                ),
                array(
                    'key' => 'field_latest_pub_count',
                    'label' => 'Number to Show',
                    'name' => 'count',
                    'type' => 'number',
                    'default_value' => 5,
                ),
                array(
                    'key' => 'field_latest_pub_view_all',
                    'label' => 'View All Link',
                    'name' => 'view_all_link',
                    'type' => 'link',
                ),
            ),
        ),

        // Publications Feed
        'publications_feed' => array(
            'key' => 'layout_publications_feed',
            'name' => 'publications_feed',
            'label' => 'Publications Feed',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_pub_feed_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'From This Pillar',
                ),
                array(
                    'key' => 'field_pub_feed_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Publications',
                ),
                array(
                    'key' => 'field_pub_feed_count',
                    'label' => 'Number to Show',
                    'name' => 'count',
                    'type' => 'number',
                    'default_value' => 6,
                ),
                array(
                    'key' => 'field_pub_feed_filter',
                    'label' => 'Show Filter',
                    'name' => 'show_filter',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ),
                array(
                    'key' => 'field_pub_feed_view_all',
                    'label' => 'View All Link',
                    'name' => 'view_all_link',
                    'type' => 'link',
                ),
                array(
                    'key' => 'field_pub_feed_filter_pillar',
                    'label' => 'Filter by Research Pillar (slug)',
                    'name' => 'filter_by_pillar',
                    'type' => 'text',
                    'instructions' => 'Enter the research pillar slug to filter publications (e.g., foreign-policy)',
                ),
                array(
                    'key' => 'field_pub_feed_style',
                    'label' => 'Background Style',
                    'name' => 'style',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (White)',
                        'gray' => 'Light Gray',
                        'light-blue' => 'Light Blue',
                        'dark' => 'Dark Background',
                    ),
                    'default_value' => 'default',
                    'instructions' => 'Choose a background style to visually distinguish this section from others.',
                ),
            ),
        ),

        // Impact Stats
        'impact_stats' => array(
            'key' => 'layout_impact_stats',
            'name' => 'impact_stats',
            'label' => 'Impact Statistics',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_stats_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'instructions' => 'ID for anchor links (e.g., for navigation).',
                ),
                array(
                    'key' => 'field_stats_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Our Impact',
                ),
                array(
                    'key' => 'field_stats_show_eyebrow',
                    'label' => 'Show Eyebrow Text',
                    'name' => 'show_eyebrow',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Toggle to show/hide the eyebrow text above the title.',
                ),
                array(
                    'key' => 'field_stats_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Impact in Numbers',
                ),
                array(
                    'key' => 'field_stats_show_title',
                    'label' => 'Show Section Title',
                    'name' => 'show_title',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Toggle to show/hide the section title.',
                ),
                array(
                    'key' => 'field_stats_title_color',
                    'label' => 'Title Color',
                    'name' => 'title_color',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (Dark)',
                        'white' => 'White',
                    ),
                    'default_value' => 'default',
                    'instructions' => 'Choose the color for the section title.',
                ),
                array(
                    'key' => 'field_stats_background_image',
                    'label' => 'Background Image',
                    'name' => 'background_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'instructions' => 'Upload a background image for the stats section. If not set, the default gradient will be used.',
                ),
                array(
                    'key' => 'field_stats_items',
                    'label' => 'Statistics',
                    'name' => 'stats',
                    'type' => 'repeater',
                    'min' => 1,
                    'max' => 6,
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_stat_value',
                            'label' => 'Value',
                            'name' => 'value',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_stat_suffix',
                            'label' => 'Suffix (+, %, etc)',
                            'name' => 'suffix',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_stat_label',
                            'label' => 'Label',
                            'name' => 'label',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_stat_icon',
                            'label' => 'Icon (SVG)',
                            'name' => 'icon',
                            'type' => 'textarea',
                        ),
                    ),
                ),
            ),
        ),

        // Events Section
        'events_section' => array(
            'key' => 'layout_events_section',
            'name' => 'events_section',
            'label' => 'Events Section',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_events_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Upcoming',
                ),
                array(
                    'key' => 'field_events_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Events & Dialogues',
                ),
                array(
                    'key' => 'field_events_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'default_value' => 'Join our policy dialogues, roundtables, and expert discussions shaping regional discourse on critical issues.',
                ),
                array(
                    'key' => 'field_events_background_style',
                    'label' => 'Background Style',
                    'name' => 'background_style',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (White)',
                        'gray' => 'Light Gray',
                        'light-blue' => 'Light Blue',
                    ),
                    'default_value' => 'default',
                    'instructions' => 'Choose a background style to visually distinguish this section from others.',
                ),
                array(
                    'key' => 'field_events_count',
                    'label' => 'Number to Show',
                    'name' => 'count',
                    'type' => 'number',
                    'default_value' => 3,
                ),
                array(
                    'key' => 'field_events_show_upcoming',
                    'label' => 'Show Only Upcoming',
                    'name' => 'only_upcoming',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ),
            ),
        ),

        // CTA Section
        'cta_section' => array(
            'key' => 'layout_cta_section',
            'name' => 'cta_section',
            'label' => 'Call to Action',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_cta_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'instructions' => 'ID for anchor links (e.g., for navigation).',
                ),
                array(
                    'key' => 'field_cta_style',
                    'label' => 'Style',
                    'name' => 'style',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (Image Background)',
                        'dark' => 'Dark Background',
                        'primary' => 'Primary Color',
                    ),
                    'default_value' => 'default',
                ),
                array(
                    'key' => 'field_cta_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_cta_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_cta_background',
                    'label' => 'Background Image',
                    'name' => 'background_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_cta_btn1',
                    'label' => 'Primary Button',
                    'name' => 'primary_button',
                    'type' => 'link',
                ),
                array(
                    'key' => 'field_cta_btn2',
                    'label' => 'Secondary Button',
                    'name' => 'secondary_button',
                    'type' => 'link',
                ),
            ),
        ),

        // Team Grid
        'team_grid' => array(
            'key' => 'layout_team_grid',
            'name' => 'team_grid',
            'label' => 'Team Members Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_team_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Our Team',
                ),
                array(
                    'key' => 'field_team_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_team_category',
                    'label' => 'Filter by Category',
                    'name' => 'category',
                    'type' => 'taxonomy',
                    'taxonomy' => 'team_category',
                    'field_type' => 'select',
                    'allow_null' => 1,
                    'return_format' => 'id',
                ),
                array(
                    'key' => 'field_team_count',
                    'label' => 'Number to Show',
                    'name' => 'count',
                    'type' => 'number',
                    'default_value' => -1,
                ),
                array(
                    'key' => 'field_team_pillar_slug',
                    'label' => 'Filter by Research Pillar (slug)',
                    'name' => 'pillar_slug',
                    'type' => 'text',
                    'instructions' => 'Enter research pillar slug to filter team members (e.g., foreign-policy). Leave empty to show all.',
                ),
                array(
                    'key' => 'field_team_view_all',
                    'label' => 'View All Link',
                    'name' => 'view_all_link',
                    'type' => 'link',
                    'instructions' => 'Link to view full team (e.g., "View Full Team" button).',
                ),
                array(
                    'key' => 'field_team_simple_layout',
                    'label' => 'Use Simple Layout',
                    'name' => 'simple_layout',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 0,
                    'instructions' => 'Use simple card layout (no bio link, just name and title). Recommended for pillar contributors.',
                ),
                array(
                    'key' => 'field_team_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'team',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: team',
                ),
                array(
                    'key' => 'field_team_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3,
                    'instructions' => 'Optional description text below the section title.',
                ),
                array(
                    'key' => 'field_team_show_filter',
                    'label' => 'Show Category Filter',
                    'name' => 'show_filter',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 0,
                    'instructions' => 'Show category filter dropdown above the team grid. Only works if no specific category is selected.',
                ),
                array(
                    'key' => 'field_team_secondary_cta',
                    'label' => 'Secondary CTA',
                    'name' => 'secondary_cta',
                    'type' => 'link',
                    'instructions' => 'Optional secondary CTA button (e.g., "Council of Advisors" link).',
                ),
            ),
        ),

        // Timeline
        'timeline' => array(
            'key' => 'layout_timeline',
            'name' => 'timeline',
            'label' => 'Timeline',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_timeline_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'journey',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: journey',
                ),
                array(
                    'key' => 'field_timeline_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_timeline_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_timeline_items',
                    'label' => 'Timeline Items',
                    'name' => 'items',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_timeline_year',
                            'label' => 'Year',
                            'name' => 'year',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_timeline_item_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_timeline_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                        ),
                        array(
                            'key' => 'field_timeline_is_future',
                            'label' => 'Is Future Item',
                            'name' => 'is_future',
                            'type' => 'true_false',
                            'ui' => 1,
                            'default_value' => 0,
                            'instructions' => 'Mark as future item (e.g., "Looking Ahead")',
                        ),
                    ),
                ),
            ),
        ),

        // Partners Section
        'partners_section' => array(
            'key' => 'layout_partners',
            'name' => 'partners_section',
            'label' => 'Partners / Logos',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_partners_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'partners',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: partners',
                ),
                array(
                    'key' => 'field_partners_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Collaborations',
                ),
                array(
                    'key' => 'field_partners_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Our Partners',
                ),
                array(
                    'key' => 'field_partners_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 2,
                ),
                array(
                    'key' => 'field_partners_background_style',
                    'label' => 'Background Style',
                    'name' => 'background_style',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (White)',
                        'gray' => 'Light Gray',
                        'light-blue' => 'Light Blue',
                    ),
                    'default_value' => 'default',
                    'instructions' => 'Choose a background style to visually distinguish this section from others.',
                ),
                array(
                    'key' => 'field_partners_layout',
                    'label' => 'Layout',
                    'name' => 'layout',
                    'type' => 'select',
                    'choices' => array(
                        'grid' => 'Grid',
                        'carousel' => 'Carousel',
                    ),
                    'default_value' => 'grid',
                    'instructions' => 'Choose how to display the partner logos. Grid shows all logos in a grid layout. Carousel displays logos in a scrolling carousel (useful for many logos).',
                ),
                array(
                    'key' => 'field_partners_logos',
                    'label' => 'Partner Logos',
                    'name' => 'logos',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Add Partner',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_partner_name',
                            'label' => 'Name',
                            'name' => 'name',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_partner_logo',
                            'label' => 'Logo',
                            'name' => 'logo',
                            'type' => 'image',
                            'return_format' => 'array',
                        ),
                        array(
                            'key' => 'field_partner_link',
                            'label' => 'Link',
                            'name' => 'link',
                            'type' => 'url',
                        ),
                    ),
                ),
            ),
        ),

        // Text Block
        'text_block' => array(
            'key' => 'layout_text_block',
            'name' => 'text_block',
            'label' => 'Text Block',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_text_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_text_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_text_content',
                    'label' => 'Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_text_layout',
                    'label' => 'Layout',
                    'name' => 'layout',
                    'type' => 'select',
                    'choices' => array(
                        'full' => 'Full Width',
                        'narrow' => 'Narrow (Centered)',
                        'split' => 'Two Columns',
                    ),
                    'default_value' => 'full',
                ),
            ),
        ),

        // Two Column Block
        'two_column' => array(
            'key' => 'layout_two_column',
            'name' => 'two_column',
            'label' => 'Two Column Section',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_two_col_left',
                    'label' => 'Left Column',
                    'name' => 'left_column',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_two_col_right',
                    'label' => 'Right Column',
                    'name' => 'right_column',
                    'type' => 'wysiwyg',
                ),
                array(
                    'key' => 'field_two_col_reverse',
                    'label' => 'Reverse on Mobile',
                    'name' => 'reverse_mobile',
                    'type' => 'true_false',
                    'ui' => 1,
                ),
            ),
        ),

        // Media Categories Grid
        'media_categories' => array(
            'key' => 'layout_media_categories',
            'name' => 'media_categories',
            'label' => 'Media Categories Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_media_show_videos',
                    'label' => 'Show Videos',
                    'name' => 'show_videos',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ),
                array(
                    'key' => 'field_media_show_podcasts',
                    'label' => 'Show Podcasts',
                    'name' => 'show_podcasts',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ),
                array(
                    'key' => 'field_media_show_gallery',
                    'label' => 'Show Gallery',
                    'name' => 'show_gallery',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ),
                array(
                    'key' => 'field_media_show_articles',
                    'label' => 'Show Articles',
                    'name' => 'show_articles',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                ),
            ),
        ),

        // FAQ Section
        'faq_section' => array(
            'key' => 'layout_faq',
            'name' => 'faq_section',
            'label' => 'FAQ Section',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_faq_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Frequently Asked Questions',
                ),
                array(
                    'key' => 'field_faq_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_faq_items',
                    'label' => 'FAQ Items',
                    'name' => 'faqs',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Add FAQ',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_faq_question',
                            'label' => 'Question',
                            'name' => 'question',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_faq_answer',
                            'label' => 'Answer',
                            'name' => 'answer',
                            'type' => 'wysiwyg',
                            'media_upload' => 0,
                            'tabs' => 'all',
                        ),
                    ),
                ),
            ),
        ),

        // Contact Hero (with Form)
        'contact_hero' => array(
            'key' => 'layout_contact_hero',
            'name' => 'contact_hero',
            'label' => 'Contact Hero (with Form)',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_contact_hero_label',
                    'label' => 'Label/Eyebrow',
                    'name' => 'label',
                    'type' => 'text',
                    'default_value' => 'Get In Touch',
                ),
                array(
                    'key' => 'field_contact_hero_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => "Let's Start a <em>Conversation</em>",
                    'instructions' => 'Use <em> tags for italic emphasis.',
                ),
                array(
                    'key' => 'field_contact_hero_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'default_value' => "Whether you're interested in research partnerships, media inquiries, or exploring how we can collaborate on policy initiatives, we'd love to hear from you.",
                ),
                array(
                    'key' => 'field_contact_hero_use_theme_info',
                    'label' => 'Use Theme Contact Info',
                    'name' => 'use_theme_contact_info',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Pull contact info from Theme Settings > Contact Info',
                ),
                array(
                    'key' => 'field_contact_hero_show_social',
                    'label' => 'Show Social Links',
                    'name' => 'show_social',
                    'type' => 'true_false',
                    'ui' => 1,
                    'default_value' => 1,
                    'instructions' => 'Display social media links from Theme Settings > Social Media',
                ),
                array(
                    'key' => 'field_contact_hero_form_title',
                    'label' => 'Form Title',
                    'name' => 'form_title',
                    'type' => 'text',
                    'default_value' => 'Send Us a Message',
                ),
                array(
                    'key' => 'field_contact_hero_form_subtitle',
                    'label' => 'Form Subtitle',
                    'name' => 'form_subtitle',
                    'type' => 'text',
                    'default_value' => "Fill out the form below and we'll get back to you within 24-48 hours.",
                ),
                array(
                    'key' => 'field_contact_hero_cf7_shortcode',
                    'label' => 'Contact Form 7 Shortcode',
                    'name' => 'cf7_shortcode',
                    'type' => 'text',
                    'instructions' => 'Paste your Contact Form 7 shortcode here, e.g. [contact-form-7 id="123" title="Contact form 1"]',
                    'placeholder' => '[contact-form-7 id="123" title="Contact form 1"]',
                ),
            ),
        ),

        // Contact Map
        'contact_map' => array(
            'key' => 'layout_contact_map',
            'name' => 'contact_map',
            'label' => 'Contact Map',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_contact_map_embed_url',
                    'label' => 'Google Maps Embed URL',
                    'name' => 'embed_url',
                    'type' => 'url',
                    'instructions' => 'Paste the Google Maps embed URL (from "Share > Embed a map")',
                    'default_value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.9084668361367!2d36.79544847496567!3d-1.2316069356316045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f17352ff2bfe9%3A0x7a94a1f7e07e8d65!2sRunda%20Dr%2C%20Nairobi!5e0!3m2!1sen!2ske',
                ),
                array(
                    'key' => 'field_contact_map_card_title',
                    'label' => 'Location Card Title',
                    'name' => 'location_name',
                    'type' => 'text',
                    'default_value' => 'GLOCEPS Headquarters',
                ),
                array(
                    'key' => 'field_contact_map_card_address',
                    'label' => 'Location Card Address',
                    'name' => 'location_address',
                    'type' => 'text',
                    'default_value' => 'Runda Drive, Nairobi',
                ),
                array(
                    'key' => 'field_contact_map_directions_url',
                    'label' => 'Get Directions URL',
                    'name' => 'directions_url',
                    'type' => 'url',
                    'default_value' => 'https://maps.google.com/?q=Runda+Drive+Nairobi',
                ),
            ),
        ),

        // Goals Grid
        'goals_grid' => array(
            'key' => 'layout_goals_grid',
            'name' => 'goals_grid',
            'label' => 'Goals Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_goals_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_goals_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_goals_items',
                    'label' => 'Goals',
                    'name' => 'goals',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_goal_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_goal_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                        ),
                        array(
                            'key' => 'field_goal_icon',
                            'label' => 'Icon (SVG)',
                            'name' => 'icon',
                            'type' => 'textarea',
                        ),
                    ),
                ),
            ),
        ),

        // Mission Vision
        'mission_vision' => array(
            'key' => 'layout_mission_vision',
            'name' => 'mission_vision',
            'label' => 'Mission & Vision',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_mv_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'mission-vision',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: mission-vision',
                ),
                array(
                    'key' => 'field_mv_mission_title',
                    'label' => 'Mission Title',
                    'name' => 'mission_title',
                    'type' => 'text',
                    'default_value' => 'Our Mission',
                ),
                array(
                    'key' => 'field_mv_mission_text',
                    'label' => 'Mission Text',
                    'name' => 'mission_text',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_mv_vision_title',
                    'label' => 'Vision Title',
                    'name' => 'vision_title',
                    'type' => 'text',
                    'default_value' => 'Our Vision',
                ),
                array(
                    'key' => 'field_mv_vision_text',
                    'label' => 'Vision Text',
                    'name' => 'vision_text',
                    'type' => 'textarea',
                ),
            ),
        ),

        // Who We Are (Content with Image)
        'who_we_are' => array(
            'key' => 'layout_who_we_are',
            'name' => 'who_we_are',
            'label' => 'Who We Are',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_who_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'who-we-are',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: who-we-are',
                ),
                array(
                    'key' => 'field_who_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Who We Are',
                ),
                array(
                    'key' => 'field_who_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_who_lead',
                    'label' => 'Lead Text',
                    'name' => 'lead_text',
                    'type' => 'textarea',
                    'rows' => 2,
                    'instructions' => 'Bold/introductory paragraph text.',
                ),
                array(
                    'key' => 'field_who_content',
                    'label' => 'Content',
                    'name' => 'content',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ),
                array(
                    'key' => 'field_who_image',
                    'label' => 'Image',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_who_cta',
                    'label' => 'CTA Button',
                    'name' => 'cta',
                    'type' => 'link',
                ),
            ),
        ),

        // Goals Strip (Horizontal List)
        'goals_strip' => array(
            'key' => 'layout_goals_strip',
            'name' => 'goals_strip',
            'label' => 'Goals Strip',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_goals_strip_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'goals',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: goals',
                ),
                array(
                    'key' => 'field_goals_strip_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Strategic Direction',
                ),
                array(
                    'key' => 'field_goals_strip_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Our Goals',
                ),
                array(
                    'key' => 'field_goals_strip_items',
                    'label' => 'Goals',
                    'name' => 'goals',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Add Goal',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_goal_strip_text',
                            'label' => 'Goal Text',
                            'name' => 'text',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
        ),

        // Values Grid (Our Approach)
        'values_grid' => array(
            'key' => 'layout_values_grid',
            'name' => 'values_grid',
            'label' => 'Values / Our Approach Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_values_anchor_id',
                    'label' => 'Anchor ID',
                    'name' => 'anchor_id',
                    'type' => 'text',
                    'default_value' => 'our-approach',
                    'instructions' => 'ID for anchor links (e.g., for navigation). Default: our-approach',
                ),
                array(
                    'key' => 'field_values_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'What We Do',
                ),
                array(
                    'key' => 'field_values_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Our Approach',
                ),
                array(
                    'key' => 'field_values_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 2,
                ),
                array(
                    'key' => 'field_values_items',
                    'label' => 'Values/Approach Items',
                    'name' => 'items',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Add Item',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_value_icon',
                            'label' => 'Icon (SVG)',
                            'name' => 'icon',
                            'type' => 'textarea',
                            'rows' => 3,
                        ),
                        array(
                            'key' => 'field_value_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_value_text',
                            'label' => 'Description',
                            'name' => 'text',
                            'type' => 'textarea',
                            'rows' => 3,
                        ),
                    ),
                ),
            ),
        ),

        // =========================================
        // MEDIA PAGE BLOCKS
        // =========================================

        // Media Hero
        'media_hero' => array(
            'key' => 'layout_media_hero',
            'name' => 'media_hero',
            'label' => 'Media Hero',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_media_hero_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Media Centre',
                ),
                array(
                    'key' => 'field_media_hero_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'default_value' => 'Explore our multimedia content — from expert interviews and policy podcasts to event galleries and in-depth analysis.',
                ),
                array(
                    'key' => 'field_media_hero_background',
                    'label' => 'Background Image',
                    'name' => 'background_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'instructions' => 'Background image for the hero section',
                ),
                array(
                    'key' => 'field_media_hero_stats',
                    'label' => 'Statistics',
                    'name' => 'stats',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'button_label' => 'Add Stat',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_media_stat_number',
                            'label' => 'Number',
                            'name' => 'number',
                            'type' => 'text',
                            'instructions' => 'e.g., "50+" or "24"',
                        ),
                        array(
                            'key' => 'field_media_stat_label',
                            'label' => 'Label',
                            'name' => 'label',
                            'type' => 'text',
                            'instructions' => 'e.g., "Videos" or "Podcast Episodes"',
                        ),
                    ),
                ),
            ),
        ),

        // Media Categories Grid
        'media_categories' => array(
            'key' => 'layout_media_categories',
            'name' => 'media_categories',
            'label' => 'Media Categories Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_media_cat_videos_title',
                    'label' => 'Videos Card Title',
                    'name' => 'videos_title',
                    'type' => 'text',
                    'default_value' => 'Videos',
                ),
                array(
                    'key' => 'field_media_cat_videos_desc',
                    'label' => 'Videos Card Description',
                    'name' => 'videos_description',
                    'type' => 'text',
                    'default_value' => 'Expert interviews, panel discussions & webinars',
                ),
                array(
                    'key' => 'field_media_cat_videos_image',
                    'label' => 'Videos Card Image',
                    'name' => 'videos_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_media_cat_podcasts_title',
                    'label' => 'Podcasts Card Title',
                    'name' => 'podcasts_title',
                    'type' => 'text',
                    'default_value' => 'Podcasts',
                ),
                array(
                    'key' => 'field_media_cat_podcasts_desc',
                    'label' => 'Podcasts Card Description',
                    'name' => 'podcasts_description',
                    'type' => 'text',
                    'default_value' => 'In-depth policy discussions & analysis',
                ),
                array(
                    'key' => 'field_media_cat_podcasts_image',
                    'label' => 'Podcasts Card Image',
                    'name' => 'podcasts_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_media_cat_gallery_title',
                    'label' => 'Gallery Card Title',
                    'name' => 'gallery_title',
                    'type' => 'text',
                    'default_value' => 'Photo Gallery',
                ),
                array(
                    'key' => 'field_media_cat_gallery_desc',
                    'label' => 'Gallery Card Description',
                    'name' => 'gallery_description',
                    'type' => 'text',
                    'default_value' => 'Event highlights & official engagements',
                ),
                array(
                    'key' => 'field_media_cat_gallery_image',
                    'label' => 'Gallery Card Image',
                    'name' => 'gallery_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
                array(
                    'key' => 'field_media_cat_articles_title',
                    'label' => 'Articles Card Title',
                    'name' => 'articles_title',
                    'type' => 'text',
                    'default_value' => 'Articles',
                ),
                array(
                    'key' => 'field_media_cat_articles_desc',
                    'label' => 'Articles Card Description',
                    'name' => 'articles_description',
                    'type' => 'text',
                    'default_value' => 'Opinion, commentary & news coverage',
                ),
                array(
                    'key' => 'field_media_cat_articles_image',
                    'label' => 'Articles Card Image',
                    'name' => 'articles_image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
            ),
        ),

        // Featured Video
        'featured_video' => array(
            'key' => 'layout_featured_video',
            'name' => 'featured_video',
            'label' => 'Featured Video',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_feat_video_title',
                    'label' => 'Section Title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'default_value' => 'Featured Video',
                ),
                array(
                    'key' => 'field_feat_video_description',
                    'label' => 'Section Description',
                    'name' => 'section_description',
                    'type' => 'text',
                    'default_value' => 'Watch our latest expert discussion',
                ),
                array(
                    'key' => 'field_feat_video_select',
                    'label' => 'Select Video',
                    'name' => 'video',
                    'type' => 'post_object',
                    'post_type' => array('video'),
                    'return_format' => 'object',
                    'instructions' => 'Select a video to feature, or leave empty to auto-select latest',
                ),
            ),
        ),

        // Latest Podcasts
        'latest_podcasts' => array(
            'key' => 'layout_latest_podcasts',
            'name' => 'latest_podcasts',
            'label' => 'Latest Podcasts',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_latest_podcasts_title',
                    'label' => 'Section Title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'default_value' => 'Latest Podcasts',
                ),
                array(
                    'key' => 'field_latest_podcasts_description',
                    'label' => 'Section Description',
                    'name' => 'section_description',
                    'type' => 'text',
                    'default_value' => 'Listen to our recent episodes',
                ),
                array(
                    'key' => 'field_latest_podcasts_count',
                    'label' => 'Number to Show',
                    'name' => 'count',
                    'type' => 'number',
                    'default_value' => 3,
                    'min' => 1,
                    'max' => 6,
                ),
            ),
        ),

        // Latest Galleries
        'latest_galleries' => array(
            'key' => 'layout_latest_galleries',
            'name' => 'latest_galleries',
            'label' => 'Latest Photo Galleries',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_latest_galleries_title',
                    'label' => 'Section Title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'default_value' => 'Photo Galleries',
                ),
                array(
                    'key' => 'field_latest_galleries_description',
                    'label' => 'Section Description',
                    'name' => 'section_description',
                    'type' => 'text',
                    'default_value' => 'Highlights from our events',
                ),
                array(
                    'key' => 'field_latest_galleries_count',
                    'label' => 'Number to Show',
                    'name' => 'count',
                    'type' => 'number',
                    'default_value' => 3,
                    'min' => 1,
                    'max' => 6,
                ),
            ),
        ),

        // Latest Articles
        'latest_articles' => array(
            'key' => 'layout_latest_articles',
            'name' => 'latest_articles',
            'label' => 'Latest Articles',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_latest_articles_title',
                    'label' => 'Section Title',
                    'name' => 'section_title',
                    'type' => 'text',
                    'default_value' => 'Latest Articles',
                ),
                array(
                    'key' => 'field_latest_articles_description',
                    'label' => 'Section Description',
                    'name' => 'section_description',
                    'type' => 'text',
                    'default_value' => 'Opinion and analysis from our experts',
                ),
                array(
                    'key' => 'field_latest_articles_background_style',
                    'label' => 'Background Style',
                    'name' => 'background_style',
                    'type' => 'select',
                    'choices' => array(
                        'default' => 'Default (White)',
                        'gray' => 'Light Gray',
                        'light-blue' => 'Light Blue',
                    ),
                    'default_value' => 'default',
                    'instructions' => 'Choose a background style to visually distinguish this section from others.',
                ),
                array(
                    'key' => 'field_latest_articles_layout',
                    'label' => 'Layout',
                    'name' => 'layout',
                    'type' => 'select',
                    'choices' => array(
                        'grid' => 'Grid',
                        'carousel' => 'Carousel',
                    ),
                    'default_value' => 'grid',
                    'instructions' => 'Choose how to display articles. Grid shows articles in a paginated grid. Carousel displays articles in a scrolling carousel.',
                ),
                array(
                    'key' => 'field_latest_articles_categories',
                    'label' => 'Filter by Categories',
                    'name' => 'categories',
                    'type' => 'taxonomy',
                    'taxonomy' => 'article_category',
                    'field_type' => 'multi_select',
                    'allow_null' => 1,
                    'instructions' => 'Select specific article categories to filter. Leave empty to show all categories.',
                ),
                array(
                    'key' => 'field_latest_articles_per_page',
                    'label' => 'Articles Per Page (Grid)',
                    'name' => 'per_page',
                    'type' => 'number',
                    'default_value' => 6,
                    'min' => 1,
                    'max' => 24,
                    'instructions' => 'Number of articles to show per page when using grid layout.',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_latest_articles_layout',
                                'operator' => '==',
                                'value' => 'grid',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_latest_articles_max_limit',
                    'label' => 'Maximum Articles (Grid)',
                    'name' => 'max_articles',
                    'type' => 'number',
                    'default_value' => '',
                    'min' => 1,
                    'max' => 100,
                    'instructions' => 'Optional: Limit the total number of articles to fetch. Useful for landing pages where you want to show a few articles and direct users to the archive page. Leave empty to show all articles with full pagination.',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_latest_articles_layout',
                                'operator' => '==',
                                'value' => 'grid',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_latest_articles_carousel_count',
                    'label' => 'Number to Show (Carousel)',
                    'name' => 'carousel_count',
                    'type' => 'number',
                    'default_value' => 6,
                    'min' => 3,
                    'max' => 12,
                    'instructions' => 'Number of articles to show in carousel layout.',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_latest_articles_layout',
                                'operator' => '==',
                                'value' => 'carousel',
                            ),
                        ),
                    ),
                ),
            ),
        ),

        // Store Hero (Purchase Page)
        'store_hero' => array(
            'key' => 'layout_store_hero',
            'name' => 'store_hero',
            'label' => 'Store Hero',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_store_hero_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'Purchase Publications',
                ),
                array(
                    'key' => 'field_store_hero_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Research That Shapes Policy',
                ),
                array(
                    'key' => 'field_store_hero_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3,
                    'default_value' => 'Access in-depth analysis, strategic insights, and policy recommendations from GLOCEPS experts. Your purchase directly supports independent research advancing peace, security, and development in Eastern Africa.',
                ),
            ),
        ),

        // Trust Bar (Purchase Page)
        'trust_bar' => array(
            'key' => 'layout_trust_bar',
            'name' => 'trust_bar',
            'label' => 'Trust Bar',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_trust_items',
                    'label' => 'Trust Items',
                    'name' => 'trust_items',
                    'type' => 'repeater',
                    'min' => 1,
                    'max' => 6,
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_trust_text',
                            'label' => 'Text',
                            'name' => 'text',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
        ),

        // Featured Publication (Purchase Page - Auto-selects premium)
        'featured_publication' => array(
            'key' => 'layout_featured_publication',
            'name' => 'featured_publication',
            'label' => 'Featured Publication',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_featured_pub_select',
                    'label' => 'Select Publication',
                    'name' => 'publication',
                    'type' => 'post_object',
                    'post_type' => array('publication'),
                    'return_format' => 'id',
                    'instructions' => 'Select a premium publication to feature. If not selected, the most recent premium publication will be used automatically.',
                ),
            ),
        ),

        // Products Grid (Purchase Page - Uses same design as publications archive)
        'products_grid' => array(
            'key' => 'layout_products_grid',
            'name' => 'products_grid',
            'label' => 'Products Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_products_per_page',
                    'label' => 'Publications Per Page',
                    'name' => 'per_page',
                    'type' => 'number',
                    'default_value' => 12,
                    'min' => 1,
                    'max' => 48,
                ),
            ),
        ),

        // Institutional Subscriptions (Purchase Page)
        'institutional_subscriptions' => array(
            'key' => 'layout_institutional_subscriptions',
            'name' => 'institutional_subscriptions',
            'label' => 'Institutional Subscriptions',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_inst_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'For Organisations',
                ),
                array(
                    'key' => 'field_inst_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Institutional Subscriptions',
                ),
                array(
                    'key' => 'field_inst_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3,
                    'default_value' => 'Universities, government agencies, and development organisations can access our full catalogue through institutional licensing.',
                ),
                array(
                    'key' => 'field_inst_details',
                    'label' => 'Additional Details',
                    'name' => 'details',
                    'type' => 'textarea',
                    'rows' => 4,
                    'default_value' => 'Institutional subscribers receive advance access to upcoming publications, priority event invitations, quarterly briefings with GLOCEPS researchers, and custom research commissions at preferred rates.',
                ),
                array(
                    'key' => 'field_inst_button',
                    'label' => 'Button',
                    'name' => 'button',
                    'type' => 'link',
                ),
                array(
                    'key' => 'field_inst_benefits',
                    'label' => 'Benefits List',
                    'name' => 'benefits',
                    'type' => 'repeater',
                    'min' => 1,
                    'max' => 10,
                    'layout' => 'table',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_inst_benefit_text',
                            'label' => 'Benefit Text',
                            'name' => 'text',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
        ),

        // Resend Publications Form
        'resend_form' => array(
            'key' => 'layout_resend_form',
            'name' => 'resend_form',
            'label' => 'Resend Publications Form',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_resend_form_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Resend Your Publications',
                ),
                array(
                    'key' => 'field_resend_form_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 2,
                    'default_value' => 'Didn\'t receive your download email? No problem. Enter your order details below and we\'ll send a fresh copy of your publications.',
                ),
            ),
        ),

        // Pillar Hero Split
        'pillar_hero_split' => array(
            'key' => 'layout_pillar_hero_split',
            'name' => 'pillar_hero_split',
            'label' => 'Pillar Hero - Split Layout',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_pillar_hero_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_pillar_hero_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 3,
                ),
                array(
                    'key' => 'field_pillar_hero_cta_text',
                    'label' => 'CTA Button Text',
                    'name' => 'cta_text',
                    'type' => 'text',
                    'default_value' => 'Explore Our Work',
                ),
                array(
                    'key' => 'field_pillar_hero_cta_link',
                    'label' => 'CTA Button Link',
                    'name' => 'cta_link',
                    'type' => 'link',
                ),
                array(
                    'key' => 'field_pillar_hero_image',
                    'label' => 'Hero Image',
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                ),
            ),
        ),

        // Pillar Intro (with stats)
        'pillar_intro' => array(
            'key' => 'layout_pillar_intro',
            'name' => 'pillar_intro',
            'label' => 'Pillar Introduction',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_pillar_intro_lead',
                    'label' => 'Lead Text',
                    'name' => 'lead_text',
                    'type' => 'textarea',
                    'rows' => 2,
                ),
                array(
                    'key' => 'field_pillar_intro_text',
                    'label' => 'Description Text',
                    'name' => 'text',
                    'type' => 'wysiwyg',
                    'tabs' => 'all',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ),
                array(
                    'key' => 'field_pillar_intro_stats',
                    'label' => 'Statistics',
                    'name' => 'stats',
                    'type' => 'repeater',
                    'min' => 1,
                    'max' => 5,
                    'layout' => 'table',
                    'button_label' => 'Add Stat',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_pillar_stat_value',
                            'label' => 'Value',
                            'name' => 'value',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_pillar_stat_label',
                            'label' => 'Label',
                            'name' => 'label',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
        ),

        // Focus Areas (How We Support)
        'focus_areas' => array(
            'key' => 'layout_focus_areas',
            'name' => 'focus_areas',
            'label' => 'Focus Areas / How We Support',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_focus_areas_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'OUR PROSPECTS',
                ),
                array(
                    'key' => 'field_focus_areas_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'How We Support Foreign Policy',
                ),
                array(
                    'key' => 'field_focus_areas_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 2,
                ),
                array(
                    'key' => 'field_focus_areas_items',
                    'label' => 'Focus Areas',
                    'name' => 'items',
                    'type' => 'repeater',
                    'min' => 1,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Add Focus Area',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_focus_area_number',
                            'label' => 'Number',
                            'name' => 'number',
                            'type' => 'text',
                            'default_value' => '01',
                        ),
                        array(
                            'key' => 'field_focus_area_image',
                            'label' => 'Image',
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'array',
                        ),
                        array(
                            'key' => 'field_focus_area_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_focus_area_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'textarea',
                            'rows' => 3,
                        ),
                        array(
                            'key' => 'field_focus_area_bullets',
                            'label' => 'Bullet Points',
                            'name' => 'bullets',
                            'type' => 'repeater',
                            'min' => 1,
                            'max' => 10,
                            'layout' => 'table',
                            'button_label' => 'Add Bullet',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_focus_bullet_text',
                                    'label' => 'Text',
                                    'name' => 'text',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),

        // Key Areas Grid
        'key_areas_grid' => array(
            'key' => 'layout_key_areas_grid',
            'name' => 'key_areas_grid',
            'label' => 'Key Areas of Focus Grid',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_key_areas_eyebrow',
                    'label' => 'Eyebrow Text',
                    'name' => 'eyebrow',
                    'type' => 'text',
                    'default_value' => 'RESEARCH THEMES',
                ),
                array(
                    'key' => 'field_key_areas_title',
                    'label' => 'Section Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Key Areas of Focus',
                ),
                array(
                    'key' => 'field_key_areas_items',
                    'label' => 'Key Areas',
                    'name' => 'items',
                    'type' => 'repeater',
                    'min' => 1,
                    'max' => 12,
                    'layout' => 'table',
                    'button_label' => 'Add Key Area',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_key_area_icon',
                            'label' => 'Icon (SVG Code)',
                            'name' => 'icon',
                            'type' => 'textarea',
                            'rows' => 3,
                            'instructions' => 'Paste SVG code for the icon',
                        ),
                        array(
                            'key' => 'field_key_area_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_key_area_description',
                            'label' => 'Description',
                            'name' => 'description',
                            'type' => 'text',
                        ),
                    ),
                ),
            ),
        ),

        // Resend Help Section (combined with FAQ)
        'resend_help' => array(
            'key' => 'layout_resend_help',
            'name' => 'resend_help',
            'label' => 'Resend Help & FAQ Section',
            'display' => 'block',
            'sub_fields' => array(
                array(
                    'key' => 'field_resend_help_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'default_value' => 'Need More Help?',
                ),
                array(
                    'key' => 'field_resend_help_email_label',
                    'label' => 'Email Support Label',
                    'name' => 'email_label',
                    'type' => 'text',
                    'default_value' => 'Email Support',
                ),
                array(
                    'key' => 'field_resend_help_email',
                    'label' => 'Email Address',
                    'name' => 'email',
                    'type' => 'email',
                    'default_value' => 'support@gloceps.org',
                ),
                array(
                    'key' => 'field_resend_help_phone_label',
                    'label' => 'Phone Support Label',
                    'name' => 'phone_label',
                    'type' => 'text',
                    'default_value' => 'Phone Support',
                ),
                array(
                    'key' => 'field_resend_help_phone',
                    'label' => 'Phone Number',
                    'name' => 'phone',
                    'type' => 'text',
                    'default_value' => '+254 112 401 331',
                ),
                array(
                    'key' => 'field_resend_help_hours',
                    'label' => 'Operating Hours',
                    'name' => 'hours',
                    'type' => 'text',
                    'default_value' => 'Mon-Fri, 8am-5pm EAT',
                ),
                array(
                    'key' => 'field_resend_help_faq_title',
                    'label' => 'FAQ Section Title',
                    'name' => 'faq_title',
                    'type' => 'text',
                    'default_value' => 'COMMON QUESTIONS',
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                    ),
                ),
                array(
                    'key' => 'field_resend_help_faq_items',
                    'label' => 'FAQ Items',
                    'name' => 'faq_items',
                    'type' => 'repeater',
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'block',
                    'button_label' => 'Add FAQ Item',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_resend_help_faq_question',
                            'label' => 'Question',
                            'name' => 'question',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_resend_help_faq_answer',
                            'label' => 'Answer',
                            'name' => 'answer',
                            'type' => 'textarea',
                            'rows' => 3,
                            'required' => 1,
                        ),
                    ),
                ),
            ),
        ),
    );

    // Register Job/Vacancy Fields - moved to separate function to ensure post types are registered
    // This will be called separately on 'init' hook with priority 30
}

/**
 * Register Vacancy Field Group
 * Separate function to ensure post types are registered first
 */
function gloceps_register_vacancy_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    if (!post_type_exists('vacancy')) {
        return;
    }
    
    $vacancy_field_group = array(
        'key' => 'group_vacancy_fields',
        'title' => 'Job Details',
        'fields' => array(
            array(
                'key' => 'field_vacancy_location',
                'label' => 'Location',
                'name' => 'vacancy_location',
                'type' => 'text',
                'instructions' => 'Job location (e.g., "Nairobi, Kenya" or "Remote"). Optional.',
                'required' => 0,
                'placeholder' => 'e.g., Nairobi, Kenya',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_vacancy_engagement_type',
                'label' => 'Engagement Type',
                'name' => 'vacancy_engagement_type',
                'type' => 'select',
                'instructions' => 'Type of employment engagement. Optional.',
                'required' => 0,
                'choices' => array(
                    'full-time' => 'Full Time',
                    'part-time' => 'Part Time',
                    'contract' => 'Contract',
                    'internship' => 'Internship',
                    'consultancy' => 'Consultancy',
                    'volunteer' => 'Volunteer',
                ),
                'allow_null' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_vacancy_deadline',
                'label' => 'Application Deadline',
                'name' => 'vacancy_deadline',
                'type' => 'date_picker',
                'instructions' => 'Application deadline date. Optional.',
                'required' => 0,
                'display_format' => 'F j, Y',
                'return_format' => 'Y-m-d',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_vacancy_salary_range',
                'label' => 'Salary Range',
                'name' => 'vacancy_salary_range',
                'type' => 'text',
                'instructions' => 'Salary range or compensation details (e.g., "KES 500,000 - KES 700,000" or "Competitive"). Optional.',
                'required' => 0,
                'placeholder' => 'e.g., KES 500,000 - KES 700,000',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_vacancy_writeup',
                'label' => 'Job Description',
                'name' => 'vacancy_writeup',
                'type' => 'wysiwyg',
                'instructions' => 'Detailed job description, requirements, and responsibilities. Optional - you can also use the main content editor.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
            ),
            array(
                'key' => 'field_vacancy_application_url',
                'label' => 'Application URL',
                'name' => 'vacancy_application_url',
                'type' => 'url',
                'instructions' => 'External URL where applicants can apply (e.g., email link or external job board). Optional.',
                'required' => 0,
                'placeholder' => 'https://example.com/apply',
            ),
            array(
                'key' => 'field_vacancy_documents',
                'label' => 'Job Documents',
                'name' => 'vacancy_documents',
                'type' => 'repeater',
                'instructions' => 'Upload PDF documents related to this job (e.g., detailed job description, application form). Optional.',
                'required' => 0,
                'min' => 0,
                'max' => 5,
                'layout' => 'table',
                'button_label' => 'Add Document',
                'sub_fields' => array(
                    array(
                        'key' => 'field_vacancy_document_file',
                        'label' => 'PDF File',
                        'name' => 'file',
                        'type' => 'file',
                        'instructions' => 'Upload a PDF file',
                        'required' => 1,
                        'return_format' => 'array',
                        'library' => 'all',
                        'mime_types' => 'pdf',
                    ),
                    array(
                        'key' => 'field_vacancy_document_label',
                        'label' => 'Label',
                        'name' => 'label',
                        'type' => 'text',
                        'instructions' => 'Label for this document (e.g., "Job Description PDF", "Application Form")',
                        'required' => 1,
                        'placeholder' => 'e.g., Job Description PDF',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'vacancy',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
    );
    
    acf_add_local_field_group($vacancy_field_group);
}

// Register vacancy fields separately on 'init' hook with priority 30
add_action('init', 'gloceps_register_vacancy_fields', 30);
