<?php
/**
 * Manual Review Manager Admin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRM_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_mrm_save_review', array($this, 'ajax_save_review'));
        add_action('wp_ajax_mrm_delete_review', array($this, 'ajax_delete_review'));
        add_action('wp_ajax_mrm_save_location', array($this, 'ajax_save_location'));
        add_action('wp_ajax_mrm_delete_location', array($this, 'ajax_delete_location'));
        add_action('wp_ajax_mrm_bulk_replace_text', array($this, 'ajax_bulk_replace_text'));
        add_action('wp_ajax_mrm_get_review', array($this, 'ajax_get_review'));
        add_action('wp_ajax_mrm_load_more_reviews', array($this, 'ajax_load_more_reviews'));
        add_action('wp_ajax_nopriv_mrm_load_more_reviews', array($this, 'ajax_load_more_reviews'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Manual Review Manager', 'manual-review-manager'),
            __('Review Manager', 'manual-review-manager'),
            'manage_options',
            'manual-review-manager',
            array($this, 'dashboard_page'),
            'dashicons-star-filled',
            30
        );
        
        add_submenu_page(
            'manual-review-manager',
            __('Dashboard', 'manual-review-manager'),
            __('Dashboard', 'manual-review-manager'),
            'manage_options',
            'manual-review-manager',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'manual-review-manager',
            __('All Reviews', 'manual-review-manager'),
            __('All Reviews', 'manual-review-manager'),
            'manage_options',
            'mrm-reviews',
            array($this, 'reviews_page')
        );
        
        add_submenu_page(
            'manual-review-manager',
            __('Add Review', 'manual-review-manager'),
            __('Add Review', 'manual-review-manager'),
            'manage_options',
            'mrm-add-review',
            array($this, 'add_review_page')
        );
        
        add_submenu_page(
            'manual-review-manager',
            __('Locations', 'manual-review-manager'),
            __('Locations', 'manual-review-manager'),
            'manage_options',
            'mrm-locations',
            array($this, 'locations_page')
        );
        
        add_submenu_page(
            'manual-review-manager',
            __('Settings', 'manual-review-manager'),
            __('Settings', 'manual-review-manager'),
            'manage_options',
            'mrm-settings',
            array($this, 'settings_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'manual-review-manager') !== false || strpos($hook, 'mrm-') !== false) {
            wp_enqueue_script('jquery');
            wp_enqueue_media();
                    wp_enqueue_script('mrm-admin', MRM_PLUGIN_URL . 'assets/admin.js', array('jquery'), MRM_VERSION, true);
        wp_enqueue_style('mrm-admin', MRM_PLUGIN_URL . 'assets/admin.css', array(), MRM_VERSION);
            
            wp_localize_script('mrm-admin', 'mrm_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mrm_nonce'),
                'confirm_delete' => __('Are you sure you want to delete this item?', 'manual-review-manager'),
                'error_message' => __('An error occurred. Please try again.', 'manual-review-manager')
            ));
        }
    }
    
    public function dashboard_page() {
        $stats = MRM_Database::get_review_stats();
        $locations = MRM_Database::get_locations();
        $recent_reviews = MRM_Database::get_reviews(array('max_reviews' => 5));
        
        include MRM_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    public function reviews_page() {
        $location_filter = isset($_GET['location']) ? intval($_GET['location']) : 0;
        $platform_filter = isset($_GET['platform']) ? sanitize_text_field($_GET['platform']) : '';
        $per_page = 20;
        
        $args = array(
            'max_reviews' => $per_page,
            'approved_only' => false
        );
        
        if ($location_filter) {
            $args['location_id'] = $location_filter;
        }
        
        if ($platform_filter) {
            $args['platform'] = $platform_filter;
        }
        
        $reviews = MRM_Database::get_reviews($args);
        $locations = MRM_Database::get_locations();
        
        include MRM_PLUGIN_DIR . 'templates/admin-reviews.php';
    }
    
    public function add_review_page() {
        $locations = MRM_Database::get_locations();
        $review_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $review = $review_id ? MRM_Database::get_review($review_id) : null;
        
        include MRM_PLUGIN_DIR . 'templates/admin-add-review.php';
    }
    
    public function locations_page() {
        $locations = MRM_Database::get_locations();
        include MRM_PLUGIN_DIR . 'templates/admin-locations.php';
    }
    
    public function settings_page() {
        $display_settings = get_option('mrm_display_settings', array());
        include MRM_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    public function register_settings() {
        register_setting('mrm_settings_group', 'mrm_display_settings', array(
            'sanitize_callback' => array($this, 'sanitize_display_settings')
        ));
    }
    
    public function sanitize_display_settings($input) {
        $sanitized = array();
        
        if (isset($input['show_photos'])) {
            $sanitized['show_photos'] = (int) $input['show_photos'];
        }
        
        if (isset($input['show_dates'])) {
            $sanitized['show_dates'] = (int) $input['show_dates'];
        }
        
        if (isset($input['show_platform'])) {
            $sanitized['show_platform'] = (int) $input['show_platform'];
        }
        
        if (isset($input['max_reviews'])) {
            $sanitized['max_reviews'] = absint($input['max_reviews']);
        }
        
        if (isset($input['min_rating'])) {
            $sanitized['min_rating'] = absint($input['min_rating']);
        }
        
        if (isset($input['color_theme'])) {
            $valid_themes = array('light', 'dark', 'auto');
            $sanitized['color_theme'] = in_array($input['color_theme'], $valid_themes) ? $input['color_theme'] : 'light';
        }
        
        if (isset($input['photo_size'])) {
            $valid_sizes = array('small', 'large');
            $sanitized['photo_size'] = in_array($input['photo_size'], $valid_sizes) ? $input['photo_size'] : 'small';
        }
        
        
        return $sanitized;
    }
    
    // AJAX Handlers
    public function ajax_save_review() {
        check_ajax_referer('mrm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
        $data = array(
            'location_id' => intval($_POST['location_id']),
            'reviewer_name' => sanitize_text_field($_POST['reviewer_name']),
            'reviewer_email' => sanitize_email($_POST['reviewer_email']),
            'reviewer_photo_url' => esc_url_raw($_POST['reviewer_photo_url']),
            'rating' => floatval($_POST['rating']),
            'review_text' => sanitize_textarea_field($_POST['review_text']),
            'review_date' => sanitize_text_field($_POST['review_date']),
            'platform' => sanitize_text_field($_POST['platform']),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_approved' => isset($_POST['is_approved']) ? 1 : 0
        );
        
        if ($review_id) {
            $result = MRM_Database::update_review($review_id, $data);
            $message = __('Review updated successfully!', 'manual-review-manager');
        } else {
            $result = MRM_Database::create_review($data);
            $review_id = $wpdb->insert_id;
            $message = __('Review created successfully!', 'manual-review-manager');
        }
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => $message,
                'review_id' => $review_id
            ));
        } else {
            wp_send_json_error(__('Failed to save review.', 'manual-review-manager'));
        }
    }
    
    public function ajax_delete_review() {
        check_ajax_referer('mrm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $review_id = intval($_POST['review_id']);
        $result = MRM_Database::delete_review($review_id);
        
        if ($result) {
            wp_send_json_success(__('Review deleted successfully!', 'manual-review-manager'));
        } else {
            wp_send_json_error(__('Failed to delete review.', 'manual-review-manager'));
        }
    }
    
    public function ajax_save_location() {
        check_ajax_referer('mrm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'address' => sanitize_textarea_field($_POST['address']),
            'phone' => sanitize_text_field($_POST['phone']),
            'website' => esc_url_raw($_POST['website']),
            'description' => sanitize_textarea_field($_POST['description'])
        );
        
        if ($location_id) {
            $result = MRM_Database::update_location($location_id, $data);
            $message = __('Location updated successfully!', 'manual-review-manager');
        } else {
            $result = MRM_Database::create_location($data);
            $message = __('Location created successfully!', 'manual-review-manager');
        }
        
        if ($result !== false) {
            wp_send_json_success($message);
        } else {
            wp_send_json_error(__('Failed to save location.', 'manual-review-manager'));
        }
    }
    
    public function ajax_delete_location() {
        check_ajax_referer('mrm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $location_id = intval($_POST['location_id']);
        $result = MRM_Database::delete_location($location_id);
        
        if ($result) {
            wp_send_json_success(__('Location and associated reviews deleted successfully!', 'manual-review-manager'));
        } else {
            wp_send_json_error(__('Failed to delete location.', 'manual-review-manager'));
        }
    }
    
    public function ajax_bulk_replace_text() {
        check_ajax_referer('mrm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $search_text = sanitize_text_field($_POST['search_text']);
        $replace_text = sanitize_text_field($_POST['replace_text']);
        $location_id = intval($_POST['location_id']);
        
        $updated_count = MRM_Database::bulk_replace_text($search_text, $replace_text, $location_id);
        
        if ($updated_count !== false) {
            wp_send_json_success(array(
                'updated_count' => $updated_count,
                'message' => sprintf(__('Successfully updated %d reviews.', 'manual-review-manager'), $updated_count)
            ));
        } else {
            wp_send_json_error(__('Failed to update reviews.', 'manual-review-manager'));
        }
    }
    
    public function ajax_get_review() {
        check_ajax_referer('mrm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $review_id = intval($_POST['review_id']);
        $review = MRM_Database::get_review($review_id);
        
        if ($review) {
            wp_send_json_success($review);
        } else {
            wp_send_json_error(__('Review not found.', 'manual-review-manager'));
        }
    }
    
    public function ajax_load_more_reviews() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mrm_nonce')) {
            wp_send_json_error(__('Invalid nonce.', 'manual-review-manager'));
        }
        
        $args = json_decode(stripslashes($_POST['args']), true);
        $offset = intval($_POST['offset']);
        
        // Update args for pagination
        $args['max_reviews'] = 10; // Load 10 more reviews
        
        // Build database query args
        $db_args = array(
            'max_reviews' => intval($args['max_reviews']),
            'min_rating' => floatval($args['min_rating']),
            'sort_by' => sanitize_text_field($args['sort_by']),
            'order' => strtoupper($args['order']),
            'approved_only' => true,
            'offset' => $offset
        );
        
        if ($args['location_id']) {
            $db_args['location_id'] = intval($args['location_id']);
        }
        
        if ($args['platform'] && $args['platform'] !== 'all') {
            $platforms = explode(',', $args['platform']);
            $platforms = array_map('trim', $platforms);
            if (count($platforms) === 1) {
                $db_args['platform'] = $platforms[0];
            }
        }
        
        $reviews = MRM_Database::get_reviews($db_args);
        
        if (empty($reviews)) {
            wp_send_json_error(__('No more reviews found.', 'manual-review-manager'));
        }
        
        // Filter by platform if multiple platforms specified
        if ($args['platform'] && $args['platform'] !== 'all' && strpos($args['platform'], ',') !== false) {
            $platforms = array_map('trim', explode(',', $args['platform']));
            $reviews = array_filter($reviews, function($review) use ($platforms) {
                return in_array($review->platform, $platforms);
            });
        }
        
        $frontend = new MRM_Frontend();
        $html = '';
        
        foreach ($reviews as $review) {
            $html .= $frontend->render_review_item_public($review, $args);
        }
        
        // Check if there are more reviews
        $total_args = $db_args;
        $total_args['max_reviews'] = 0;
        $total_args['offset'] = 0;
        $all_reviews = MRM_Database::get_reviews($total_args);
        $has_more = count($all_reviews) > ($offset + count($reviews));
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $has_more,
            'new_offset' => $offset + count($reviews)
        ));
    }
    
    public function save_settings() {
        check_admin_referer('mrm_settings', 'mrm_settings_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'manual-review-manager'));
        }
        
        $display_settings = array(
            'show_photos' => isset($_POST['show_photos']) ? 1 : 0,
            'show_dates' => isset($_POST['show_dates']) ? 1 : 0,
            'show_platform' => isset($_POST['show_platform']) ? 1 : 0,
            'max_reviews' => intval($_POST['max_reviews']),
            'min_rating' => intval($_POST['min_rating'])
        );
        
        update_option('mrm_display_settings', $display_settings);
        
        wp_redirect(admin_url('admin.php?page=mrm-settings&updated=1'));
        exit;
    }
    
    public function get_platform_svg_admin($platform) {
        switch ($platform) {
            case 'google':
                return '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>';
                
            case 'yelp':
                return '<svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <linearGradient id="yelp_grad_20" x1="1.323" x2="44.983" y1="5.864" y2="47.991" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#f52537"></stop>
                        <stop offset=".293" stop-color="#f32536"></stop>
                        <stop offset=".465" stop-color="#ea2434"></stop>
                        <stop offset=".605" stop-color="#dc2231"></stop>
                        <stop offset=".729" stop-color="#c8202c"></stop>
                        <stop offset=".841" stop-color="#ae1e25"></stop>
                        <stop offset=".944" stop-color="#8f1a1d"></stop>
                        <stop offset="1" stop-color="#7a1818"></stop>
                    </linearGradient>
                    <path fill="url(#yelp_grad_20)" d="M10.7,32.7c-0.5,0-0.9-0.3-1.2-0.8c-0.2-0.4-0.3-1-0.4-1.7c-0.2-2.2,0-5.5,0.7-6.5c0.3-0.5,0.8-0.7,1.2-0.7c0.3,0,0.6,0.1,7.1,2.8l1.9,0.8c0.7,0.3,1.1,1,1.1,1.8s-0.5,1.4-1.2,1.6l-2.7,0.9C11.2,32.7,11,32.7,10.7,32.7z M24,36.3c0,6.3,0,6.5-0.1,6.8c-0.2,0.5-0.6,0.8-1.1,0.9c-1.6,0.3-6.6-1.6-7.7-2.8c-0.2-0.3-0.3-0.5-0.4-0.8c0-0.2,0-0.4,0.1-0.6c0.1-0.3,0.3-0.6,4.8-5.9l1.3-1.6c0.4-0.6,1.3-0.7,2-0.5c0.7,0.3,1.2,0.9,1.1,1.6C24,33.5,24,36.3,24,36.3z M22.8,22.9c-0.3,0.1-1.3,0.4-2.5-1.6c0,0-8.1-12.9-8.3-13.3c-0.1-0.4,0-1,0.4-1.4c1.2-1.3,7.7-3.1,9.4-2.7c0.6,0.1,0.9,0.5,1.1,1c0.1,0.6,0.9,12.5,1,15.2C24.1,22.5,23.1,22.8,22.8,22.9z M27.2,25.9c-0.4-0.6-0.4-1.4,0-1.9l1.7-2.3c3.6-5,3.8-5.3,4.1-5.4c0.4-0.3,0.9-0.3,1.4-0.1c1.4,0.7,4.4,5.1,4.6,6.7c0,0,0,0,0,0.1c0,0.6-0.2,1-0.6,1.3c-0.3,0.2-0.5,0.3-7.4,1.9c-1.1,0.3-1.7,0.4-2,0.5v-0.1C28.4,26.9,27.6,26.5,27.2,25.9z M38.9,34.4c-0.2,1.6-3.5,5.8-5.1,6.4c-0.5,0.2-1,0.2-1.4-0.2c-0.3-0.2-0.5-0.6-4.1-6.4l-1.1-1.7c-0.4-0.6-0.3-1.4,0.2-2.1c0.5-0.6,1.2-0.8,1.9-0.6l2.7,0.9c6,2,6.2,2,6.4,2.2C38.8,33.4,39,33.9,38.9,34.4z"/>
                </svg>';
                
            case 'facebook':
                return '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    <path fill="#FFFFFF" d="M16.671 15.543l.532-3.47h-3.328v-2.25c0-.949.465-1.874 1.956-1.874h1.513V4.996s-1.374-.235-2.686-.235c-2.741 0-4.533 1.662-4.533 4.669v2.142H7.078v3.47h3.047v8.385a12.118 12.118 0 003.75 0v-8.385h2.796z"/>
                </svg>';
                
            case 'manual':
                return '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#666" d="M9 2v2h6V2h2v2h1a1 1 0 011 1v14a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h1V2h2zm0 8v2h2v-2H9zm0 4v2h2v-2H9zm4-4v2h2v-2h-2z"/>
                </svg>';
                
            default:
                return '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#666" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>';
        }
    }
} 