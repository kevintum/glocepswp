<?php
/**
 * Newsletter Subscriptions System
 * 
 * Handles newsletter subscriptions from footer and checkout
 * Stores subscriptions in custom post type with export functionality
 * 
 * @package GLOCEPS
 */

/**
 * Register Newsletter Subscription Custom Post Type
 */
function gloceps_register_newsletter_subscription_post_type() {
    $labels = array(
        'name'                  => _x('Newsletter Subscriptions', 'Post Type General Name', 'gloceps'),
        'singular_name'         => _x('Newsletter Subscription', 'Post Type Singular Name', 'gloceps'),
        'menu_name'             => __('Newsletter Subscriptions', 'gloceps'),
        'name_admin_bar'        => __('Newsletter Subscription', 'gloceps'),
        'archives'              => __('Subscription Archives', 'gloceps'),
        'attributes'            => __('Subscription Attributes', 'gloceps'),
        'parent_item_colon'     => __('Parent Subscription:', 'gloceps'),
        'all_items'             => __('All Subscriptions', 'gloceps'),
        'add_new_item'          => __('Add New Subscription', 'gloceps'),
        'add_new'               => __('Add New', 'gloceps'),
        'new_item'              => __('New Subscription', 'gloceps'),
        'edit_item'             => __('Edit Subscription', 'gloceps'),
        'update_item'           => __('Update Subscription', 'gloceps'),
        'view_item'             => __('View Subscription', 'gloceps'),
        'view_items'            => __('View Subscriptions', 'gloceps'),
        'search_items'          => __('Search Subscriptions', 'gloceps'),
        'not_found'             => __('Not found', 'gloceps'),
        'not_found_in_trash'   => __('Not found in Trash', 'gloceps'),
    );
    
    $args = array(
        'label'                 => __('Newsletter Subscription', 'gloceps'),
        'description'           => __('Newsletter subscription entries', 'gloceps'),
        'labels'                => $labels,
        'supports'              => array('title', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-email-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'map_meta_cap'          => true,
        // Don't override capabilities - let WordPress map them from 'post' capability_type
        'show_in_rest'          => false,
        'rewrite'               => false, // Explicitly disable rewrite since it's not public
    );
    
    // Use shorter name to comply with WordPress 20-character limit
    register_post_type('newsletter_sub', $args);
}
add_action('init', 'gloceps_register_newsletter_subscription_post_type', 0);

/**
 * Flush rewrite rules on theme activation to ensure post type is registered
 */
function gloceps_flush_newsletter_subscription_rewrite_rules() {
    gloceps_register_newsletter_subscription_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gloceps_flush_newsletter_subscription_rewrite_rules');

/**
 * Force flush rewrite rules when accessing newsletter subscription admin page
 * This ensures the post type is recognized even if rewrite rules weren't flushed
 */
function gloceps_force_flush_rewrite_rules_on_admin() {
    global $pagenow;
    
    if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'newsletter_sub') {
        // Check if post type exists
        if (!post_type_exists('newsletter_sub')) {
            // Re-register the post type
            gloceps_register_newsletter_subscription_post_type();
            // Flush rewrite rules
            flush_rewrite_rules(false);
        }
    }
}
add_action('admin_init', 'gloceps_force_flush_rewrite_rules_on_admin', 1);

/**
 * Admin notice to flush rewrite rules if post type not showing
 */
function gloceps_newsletter_subscription_admin_notice() {
    global $pagenow;
    
    if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'newsletter_sub') {
        $post_type_obj = get_post_type_object('newsletter_sub');
        if (!$post_type_obj) {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Newsletter Subscription post type not registered. Please go to Settings > Permalinks and click "Save Changes" to flush rewrite rules.', 'gloceps'); ?></p>
            </div>
            <?php
        }
    }
}
add_action('admin_notices', 'gloceps_newsletter_subscription_admin_notice');

/**
 * Add custom columns to newsletter subscription list
 */
function gloceps_newsletter_subscription_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Email', 'gloceps');
    $new_columns['name'] = __('Name', 'gloceps');
    $new_columns['source'] = __('Source', 'gloceps');
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_newsletter_sub_posts_columns', 'gloceps_newsletter_subscription_columns');

/**
 * Populate custom columns
 */
function gloceps_newsletter_subscription_column_content($column, $post_id) {
    switch ($column) {
        case 'name':
            $first_name = get_post_meta($post_id, '_first_name', true);
            $last_name = get_post_meta($post_id, '_last_name', true);
            $name = trim($first_name . ' ' . $last_name);
            echo $name ? esc_html($name) : '—';
            break;
        case 'source':
            $source = get_post_meta($post_id, '_source', true);
            $badge_class = $source === 'ecommerce' ? 'button button-primary' : 'button';
            echo '<span class="' . esc_attr($badge_class) . '">' . esc_html(ucfirst($source ?: 'footer')) . '</span>';
            break;
    }
}
add_action('manage_newsletter_sub_posts_custom_column', 'gloceps_newsletter_subscription_column_content', 10, 2);

/**
 * Make columns sortable
 */
function gloceps_newsletter_subscription_sortable_columns($columns) {
    $columns['source'] = 'source';
    return $columns;
}
add_filter('manage_edit-newsletter_sub_sortable_columns', 'gloceps_newsletter_subscription_sortable_columns');

/**
 * Handle sorting
 */
function gloceps_newsletter_subscription_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ('source' === $query->get('orderby')) {
        $query->set('meta_key', '_source');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'gloceps_newsletter_subscription_orderby');

/**
 * Add filter dropdown for source
 */
function gloceps_newsletter_subscription_add_filters() {
    global $typenow;
    
    if ($typenow === 'newsletter_sub') {
        $selected = isset($_GET['subscription_source']) ? $_GET['subscription_source'] : '';
        ?>
        <select name="subscription_source" id="subscription_source">
            <option value=""><?php _e('All Sources', 'gloceps'); ?></option>
            <option value="footer" <?php selected($selected, 'footer'); ?>><?php _e('Footer', 'gloceps'); ?></option>
            <option value="ecommerce" <?php selected($selected, 'ecommerce'); ?>><?php _e('Ecommerce', 'gloceps'); ?></option>
        </select>
        <?php
    }
}
add_action('restrict_manage_posts', 'gloceps_newsletter_subscription_add_filters');

/**
 * Filter posts by source
 */
function gloceps_newsletter_subscription_filter_posts($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'newsletter_sub' && isset($_GET['subscription_source']) && $_GET['subscription_source'] !== '') {
        $query->query_vars['meta_key'] = '_source';
        $query->query_vars['meta_value'] = sanitize_text_field($_GET['subscription_source']);
    }
}
add_action('parse_query', 'gloceps_newsletter_subscription_filter_posts');

/**
 * Save newsletter subscription
 * 
 * @param string $email Email address
 * @param string $source Source (footer or ecommerce)
 * @param string $first_name First name (optional)
 * @param string $last_name Last name (optional)
 * @param int $order_id Order ID (for ecommerce subscriptions)
 * @return int|WP_Error Post ID on success, WP_Error on failure
 */
function gloceps_save_newsletter_subscription($email, $source = 'footer', $first_name = '', $last_name = '', $order_id = 0) {
    // Check if email already exists
    $existing = get_posts(array(
        'post_type' => 'newsletter_sub',
        'post_status' => 'any',
        'meta_query' => array(
            array(
                'key' => '_email',
                'value' => $email,
                'compare' => '=',
            ),
        ),
        'posts_per_page' => 1,
    ));
    
    if (!empty($existing)) {
        // Update existing subscription
        $post_id = $existing[0]->ID;
        update_post_meta($post_id, '_source', $source);
        if ($first_name) update_post_meta($post_id, '_first_name', $first_name);
        if ($last_name) update_post_meta($post_id, '_last_name', $last_name);
        if ($order_id) update_post_meta($post_id, '_order_id', $order_id);
        return $post_id;
    }
    
    // Create new subscription
    $post_data = array(
        'post_title' => $email,
        'post_status' => 'publish',
        'post_type' => 'newsletter_sub',
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        return $post_id;
    }
    
    // Save meta data
    update_post_meta($post_id, '_email', $email);
    update_post_meta($post_id, '_source', $source);
    update_post_meta($post_id, '_first_name', $first_name);
    update_post_meta($post_id, '_last_name', $last_name);
    if ($order_id) {
        update_post_meta($post_id, '_order_id', $order_id);
    }
    update_post_meta($post_id, '_subscribed_at', current_time('mysql'));
    
    return $post_id;
}

/**
 * Export newsletter subscriptions to CSV
 */
function gloceps_export_newsletter_subscriptions() {
    if (!isset($_GET['export_newsletter_subscriptions']) || !current_user_can('export')) {
        return;
    }
    
    check_admin_referer('export_newsletter_subscriptions');
    
    $source_filter = isset($_GET['subscription_source']) ? sanitize_text_field($_GET['subscription_source']) : '';
    
    $args = array(
        'post_type' => 'newsletter_sub',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    if ($source_filter) {
        $args['meta_query'] = array(
            array(
                'key' => '_source',
                'value' => $source_filter,
                'compare' => '=',
            ),
        );
    }
    
    $subscriptions = get_posts($args);
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter-subscriptions-' . date('Y-m-d') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Output CSV
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Source', 'Subscribed Date', 'Order ID'));
    
    // Data
    foreach ($subscriptions as $subscription) {
        $email = get_post_meta($subscription->ID, '_email', true);
        $first_name = get_post_meta($subscription->ID, '_first_name', true);
        $last_name = get_post_meta($subscription->ID, '_last_name', true);
        $source = get_post_meta($subscription->ID, '_source', true);
        $subscribed_at = get_post_meta($subscription->ID, '_subscribed_at', true);
        $order_id = get_post_meta($subscription->ID, '_order_id', true);
        
        fputcsv($output, array(
            $email,
            $first_name,
            $last_name,
            ucfirst($source ?: 'footer'),
            $subscribed_at,
            $order_id ?: '',
        ));
    }
    
    fclose($output);
    exit;
}
add_action('admin_init', 'gloceps_export_newsletter_subscriptions');

/**
 * Add export button to admin page
 */
function gloceps_newsletter_subscription_export_button() {
    global $typenow;
    
    if ($typenow === 'newsletter_sub') {
        $source_filter = isset($_GET['subscription_source']) ? sanitize_text_field($_GET['subscription_source']) : '';
        $export_url = wp_nonce_url(
            add_query_arg(array('export_newsletter_subscriptions' => '1', 'subscription_source' => $source_filter), admin_url('edit.php')),
            'export_newsletter_subscriptions'
        );
        ?>
        <div class="alignleft actions">
            <a href="<?php echo esc_url($export_url); ?>" class="button">
                <?php _e('Export to CSV', 'gloceps'); ?>
            </a>
        </div>
        <?php
    }
}
add_action('manage_posts_extra_tablenav', 'gloceps_newsletter_subscription_export_button');

/**
 * Handle Contact Form 7 submission for newsletter
 */
function gloceps_handle_cf7_newsletter_submission($contact_form) {
    if (!class_exists('WPCF7_Submission')) {
        return;
    }
    
    $submission = WPCF7_Submission::get_instance();
    
    if (!$submission) {
        return;
    }
    
    $posted_data = $submission->get_posted_data();
    
    // Check if this is the newsletter form
    $form_id = $contact_form->id();
    $footer_form_id = get_field('footer_newsletter_form', 'option');
    
    if ($form_id != $footer_form_id) {
        return;
    }
    
    // Get email from form submission
    $email = '';
    $first_name = '';
    
    foreach ($posted_data as $key => $value) {
        if (!is_string($value)) {
            continue;
        }
        
        $key_lower = strtolower($key);
        $value_trimmed = trim($value);
        
        // Find email field
        if (empty($email) && is_email($value_trimmed)) {
            $email = sanitize_email($value_trimmed);
        }
        // Also check if key contains 'email'
        if (empty($email) && strpos($key_lower, 'email') !== false && is_email($value_trimmed)) {
            $email = sanitize_email($value_trimmed);
        }
        
        // Find first name field
        if (empty($first_name)) {
            // Check for common first name field patterns
            if (strpos($key_lower, 'first') !== false && strpos($key_lower, 'name') !== false) {
                $first_name = sanitize_text_field($value_trimmed);
            } elseif ($key_lower === 'name' || $key_lower === 'your-name' || $key_lower === 'fname') {
                // If it's just "name" and no first name found, use it
                if (empty($first_name)) {
                    $first_name = sanitize_text_field($value_trimmed);
                }
            }
        }
    }
    
    if (!$email) {
        return;
    }
    
    // Save subscription
    gloceps_save_newsletter_subscription($email, 'footer', $first_name, '');
}
add_action('wpcf7_mail_sent', 'gloceps_handle_cf7_newsletter_submission');

/**
 * Handle newsletter subscription from checkout
 */
function gloceps_handle_checkout_newsletter_subscription($order_id) {
    // Check if newsletter checkbox was checked
    if (!isset($_POST['newsletter']) || !$_POST['newsletter']) {
        return;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    $email = $order->get_billing_email();
    if (!$email) {
        return;
    }
    
    $first_name = $order->get_billing_first_name();
    $last_name = $order->get_billing_last_name();
    
    // Save subscription
    gloceps_save_newsletter_subscription($email, 'ecommerce', $first_name, $last_name, $order_id);
}
add_action('woocommerce_checkout_order_processed', 'gloceps_handle_checkout_newsletter_subscription', 10, 1);
