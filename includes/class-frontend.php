<?php
/**
 * Manual Review Manager Frontend Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRM_Frontend {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('wp_head', array($this, 'add_structured_data'));
        add_action('wp_head', array($this, 'add_button_color_styles'));
    }
    
    public function enqueue_frontend_scripts() {
        if ($this->has_review_shortcode()) {
            wp_enqueue_style('mrm-frontend', MRM_PLUGIN_URL . 'assets/frontend.css', array(), MRM_VERSION);
            wp_enqueue_script('mrm-frontend', MRM_PLUGIN_URL . 'assets/frontend.js', array('jquery'), MRM_VERSION, true);
            
            // Localize script for AJAX
            wp_localize_script('mrm-frontend', 'mrm_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mrm_nonce')
            ));
        }
    }
    
    private function has_review_shortcode() {
        global $post;
        if (is_a($post, 'WP_Post')) {
            return has_shortcode($post->post_content, 'review_manager') || 
                   has_shortcode($post->post_content, 'review_slider') || 
                   has_shortcode($post->post_content, 'review_grid_slider') || 
                   has_shortcode($post->post_content, 'review_stats') ||
                   $this->has_review_widget();
        }
        return $this->has_review_widget();
    }
    
    private function has_review_widget() {
        return is_active_widget(false, false, 'mrm_latest_reviews_widget');
    }
    
    public function display_reviews($args = array()) {
        $defaults = array(
            'layout' => 'grid',
            'columns' => 3,
            'max_reviews' => 10,
            'min_rating' => 1,
            'platform' => 'all',
            'location_id' => 0,
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'show_photos' => true,
            'show_dates' => true,
            'show_platform' => true,
            'truncate' => 50
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Get display settings defaults
        $display_settings = get_option('mrm_display_settings', array());
        foreach ($display_settings as $key => $value) {
            if (!isset($args[$key]) || $args[$key] === null) {
                $args[$key] = $value;
            }
        }
        
        // Build database query args
        $db_args = array(
            'max_reviews' => intval($args['max_reviews']),
            'min_rating' => floatval($args['min_rating']),
            'sort_by' => sanitize_text_field($args['sort_by']),
            'order' => strtoupper($args['order']),
            'approved_only' => true
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
        
        // Debug: Log how many reviews were fetched
        error_log('MRM Debug: Fetched ' . count($reviews) . ' reviews for layout: ' . $args['layout']);
        
        if (empty($reviews)) {
            // Get theme setting for empty state - shortcode parameter overrides global setting
            $display_settings = get_option('mrm_display_settings', array());
            $theme = !empty($args['theme']) ? $args['theme'] : (isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light');
            $theme_class = $theme !== 'light' ? 'mrm-theme-' . esc_attr($theme) : '';
            
            return '<div class="mrm-no-reviews ' . $theme_class . '"><p>' . __('No reviews found.', 'manual-review-manager') . '</p></div>';
        }
        
        // Filter by platform if multiple platforms specified
        if ($args['platform'] && $args['platform'] !== 'all' && strpos($args['platform'], ',') !== false) {
            $platforms = array_map('trim', explode(',', $args['platform']));
            $reviews = array_filter($reviews, function($review) use ($platforms) {
                return in_array($review->platform, $platforms);
            });
        }
        
        // Check if there are more reviews available
        $total_args = $db_args;
        $total_args['max_reviews'] = 0; // Get all to count total
        $all_reviews = MRM_Database::get_reviews($total_args);
        $has_more = count($all_reviews) > count($reviews);
        
        // Get theme setting - shortcode parameter overrides global setting
        $display_settings = get_option('mrm_display_settings', array());
        $theme = !empty($args['theme']) ? $args['theme'] : (isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light');
        $theme_class = $theme !== 'light' ? 'mrm-theme-' . esc_attr($theme) : '';
        
        // Get photo size setting - shortcode parameter overrides global setting
        $photo_size = !empty($args['photo_size']) ? $args['photo_size'] : (isset($display_settings['photo_size']) ? $display_settings['photo_size'] : 'small');
        $photo_size_class = $photo_size === 'large' ? 'mrm-large-photos' : '';
        
        $container_id = 'mrm-container-' . uniqid();
        $container_classes = 'mrm-review-container ' . $theme_class . ' ' . $photo_size_class;
        $output = '<div class="' . trim($container_classes) . '" id="' . $container_id . '" data-args="' . esc_attr(json_encode($args)) . '" data-offset="' . count($reviews) . '">';
        
        switch ($args['layout']) {
            case 'list':
                $output .= $this->render_list_layout($reviews, $args);
                break;
            case 'slider':
                $output .= $this->render_slider_layout($reviews, $args);
                break;
            case 'grid_slider':
                $output .= $this->render_grid_slider_layout($reviews, $args);
                break;
            case 'grid':
            default:
                $output .= $this->render_grid_layout($reviews, $args);
                break;
        }
        
        // Add View More button if there are more reviews and it's not a slider
        if ($has_more && $args['layout'] !== 'slider' && $args['layout'] !== 'grid_slider') {
            $output .= '<div class="mrm-view-more-container">';
            $output .= '<button class="mrm-view-more-btn" onclick="mrmLoadMoreReviews(\'' . $container_id . '\')">';
            $output .= __('View More Reviews', 'manual-review-manager');
            $output .= '</button>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }
    
    private function render_grid_layout($reviews, $args) {
        $columns = max(1, min(4, intval($args['columns'])));
        $output = '<div class="mrm-reviews mrm-grid mrm-columns-' . $columns . '">';
        
        foreach ($reviews as $review) {
            $output .= $this->render_review_item($review, $args);
        }
        
        $output .= '</div>';
        return $output;
    }
    
    private function render_list_layout($reviews, $args) {
        $output = '<div class="mrm-reviews mrm-list">';
        
        foreach ($reviews as $review) {
            $output .= $this->render_review_item($review, $args, 'list');
        }
        
        $output .= '</div>';
        return $output;
    }
    
    private function render_slider_layout($reviews, $args) {
        $slider_id = 'mrm-slider-' . uniqid();
        $autoplay = isset($args['autoplay']) ? filter_var($args['autoplay'], FILTER_VALIDATE_BOOLEAN) : true;
        $speed = isset($args['speed']) ? intval($args['speed']) : 5000;
        $arrows = isset($args['arrows']) ? filter_var($args['arrows'], FILTER_VALIDATE_BOOLEAN) : true;
        $dots = isset($args['dots']) ? filter_var($args['dots'], FILTER_VALIDATE_BOOLEAN) : true;
        
        $output = '<div class="mrm-slider-container" id="' . $slider_id . '" data-autoplay="' . ($autoplay ? 'true' : 'false') . '" data-speed="' . $speed . '">';
        $output .= '<div class="mrm-slider">';
        
        // Debug: Log how many slides we're creating
        error_log('MRM Debug: Creating ' . count($reviews) . ' slides for regular slider');
        
        foreach ($reviews as $review) {
            $output .= '<div class="mrm-slide">';
            $output .= $this->render_review_item($review, $args, 'slider');
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        if ($arrows) {
            $output .= '<button class="mrm-prev" aria-label="Previous review">‹</button>';
            $output .= '<button class="mrm-next" aria-label="Next review">›</button>';
        }
        
        if ($dots) {
            $output .= '<div class="mrm-dots">';
            for ($i = 0; $i < count($reviews); $i++) {
                $output .= '<button class="mrm-dot' . ($i === 0 ? ' active' : '') . '" data-slide="' . $i . '"></button>';
            }
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }

    private function render_grid_slider_layout($reviews, $args) {
        $slider_id = 'mrm-grid-slider-' . uniqid();
        $autoplay = isset($args['autoplay']) ? filter_var($args['autoplay'], FILTER_VALIDATE_BOOLEAN) : true;
        $speed = isset($args['speed']) ? intval($args['speed']) : 5000;
        $arrows = isset($args['arrows']) ? filter_var($args['arrows'], FILTER_VALIDATE_BOOLEAN) : true;
        $dots = isset($args['dots']) ? filter_var($args['dots'], FILTER_VALIDATE_BOOLEAN) : true;
        $columns = max(1, min(4, intval($args['columns'])));
        
        // Group reviews into slides based on columns per slide
        $slides = array_chunk($reviews, $columns);
        
        // Debug: Log grid slider details
        error_log('MRM Debug: Creating ' . count($slides) . ' grid slides with ' . $columns . ' columns each from ' . count($reviews) . ' reviews');
        
        $output = '<div class="mrm-grid-slider-container" id="' . $slider_id . '" data-autoplay="' . ($autoplay ? 'true' : 'false') . '" data-speed="' . $speed . '" data-columns="' . $columns . '">';
        $output .= '<div class="mrm-grid-slider">';
        
        foreach ($slides as $slide_reviews) {
            $output .= '<div class="mrm-grid-slide">';
            $output .= '<div class="mrm-grid-slide-content mrm-columns-' . $columns . '">';
            
            foreach ($slide_reviews as $review) {
                $output .= $this->render_review_item($review, $args, 'grid_slider');
            }
            
            $output .= '</div>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        if ($arrows && count($slides) > 1) {
            $output .= '<button class="mrm-prev" aria-label="Previous reviews">‹</button>';
            $output .= '<button class="mrm-next" aria-label="Next reviews">›</button>';
        }
        
        if ($dots && count($slides) > 1) {
            $output .= '<div class="mrm-dots">';
            for ($i = 0; $i < count($slides); $i++) {
                $output .= '<button class="mrm-dot' . ($i === 0 ? ' active' : '') . '" data-slide="' . $i . '"></button>';
            }
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }
    
    public function render_review_item_public($review, $args, $layout = 'grid') {
        return $this->render_review_item($review, $args, $layout);
    }
    
    private function render_review_item($review, $args, $layout = 'grid') {
        $truncate = intval($args['truncate']);
        $clean_review_text = stripslashes($review->review_text);
        $review_text = $truncate > 0 ? wp_trim_words($clean_review_text, $truncate) : $clean_review_text;
        
        $output = '<div class="mrm-review-item mrm-review-' . $layout . '" data-rating="' . $review->rating . '">';
        
        // Header with photo and name
        if ($args['show_photos'] || $review->reviewer_name) {
            $output .= '<div class="mrm-review-header">';
            
            if ($args['show_photos'] && !empty($review->reviewer_photo_url)) {
                $output .= '<img src="' . esc_url($review->reviewer_photo_url) . '" alt="' . esc_attr($review->reviewer_name) . '" class="mrm-reviewer-photo" />';
            }
            
            $output .= '<div class="mrm-reviewer-info">';
            $output .= '<h3 class="mrm-reviewer-name">' . esc_html(stripslashes($review->reviewer_name)) . '</h3>';
            
            // Rating stars
            $output .= '<div class="mrm-rating">';
            for ($i = 1; $i <= 5; $i++) {
                $output .= $i <= $review->rating ? '<span class="mrm-star filled">★</span>' : '<span class="mrm-star">☆</span>';
            }
            $output .= ' <span class="mrm-rating-number">(' . number_format($review->rating, 1) . ')</span>';
            $output .= '</div>';
            
            $output .= '</div>';
            $output .= '</div>';
        }
        
        // Review content  
        $output .= '<div class="mrm-review-content">';
        
        $full_text = $clean_review_text;
        $text_length = strlen($full_text);
        $max_length = 200; // Characters to show before "Read More"
        
        // Use Read More functionality if text is long, regardless of truncate setting
        if ($text_length > $max_length) {
            $short_text = substr($full_text, 0, $max_length);
            // Find the last space to avoid cutting words
            $last_space = strrpos($short_text, ' ');
            if ($last_space !== false) {
                $short_text = substr($short_text, 0, $last_space);
            }
            
            $output .= '<p class="mrm-review-text">';
            $output .= '<span class="mrm-text-short">' . nl2br(wp_kses_post($short_text)) . '...</span>';
            $output .= '<span class="mrm-text-full" style="display: none;">' . nl2br(wp_kses_post($full_text)) . '</span>';
            $output .= '</p>';
            $output .= '<button class="mrm-read-more-btn" onclick="mrmToggleText(this)">' . __('Read More', 'manual-review-manager') . '</button>';
        } else if ($truncate > 0) {
            // Use WordPress truncation if specified and text isn't long enough for Read More
            $output .= '<p class="mrm-review-text">' . nl2br(wp_kses_post($review_text)) . '</p>';
        } else {
            // Show full text if it's short
            $output .= '<p class="mrm-review-text">' . nl2br(wp_kses_post($full_text)) . '</p>';
        }
        
        $output .= '</div>';
        
        // Footer with date and platform
        if ($args['show_dates'] || $args['show_platform']) {
            $output .= '<div class="mrm-review-footer">';
            
            if ($args['show_dates']) {
                $output .= '<span class="mrm-review-date">' . $this->get_relative_time($review->review_date) . '</span>';
            }
            
            if ($args['show_platform'] && $review->platform !== 'manual') {
                $platform_label = ucfirst($review->platform);
                $output .= '<span class="mrm-platform mrm-platform-' . esc_attr($review->platform) . '" title="' . esc_attr($platform_label) . ' Review">';
                $output .= $this->get_platform_svg($review->platform);
                $output .= '</span>';
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }
    
    public function display_review_stats($args = array()) {
        $defaults = array(
            'location_id' => 0,
            'show_total' => true,
            'show_average' => true,
            'show_breakdown' => false
        );
        
        $args = wp_parse_args($args, $defaults);
        $stats = MRM_Database::get_review_stats($args['location_id']);
        
        if (!$stats || $stats->total_reviews == 0) {
            // Get theme setting for empty state - shortcode parameter overrides global setting
            $display_settings = get_option('mrm_display_settings', array());
            $theme = !empty($args['theme']) ? $args['theme'] : (isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light');
            $theme_class = $theme !== 'light' ? 'mrm-theme-' . esc_attr($theme) : '';
            
            return '<div class="mrm-no-stats ' . $theme_class . '"><p>' . __('No review statistics available.', 'manual-review-manager') . '</p></div>';
        }
        
        // Get theme setting - shortcode parameter overrides global setting
        $display_settings = get_option('mrm_display_settings', array());
        $theme = !empty($args['theme']) ? $args['theme'] : (isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light');
        $theme_class = $theme !== 'light' ? 'mrm-theme-' . esc_attr($theme) : '';
        
        $output = '<div class="mrm-review-stats ' . $theme_class . '">';
        
        if ($args['show_total']) {
            $output .= '<div class="mrm-stat-item mrm-total-reviews">';
            $output .= '<span class="mrm-stat-number">' . number_format($stats->total_reviews) . '</span>';
            $output .= '<span class="mrm-stat-label">' . _n('Review', 'Reviews', $stats->total_reviews, 'manual-review-manager') . '</span>';
            $output .= '</div>';
        }
        
        if ($args['show_average']) {
            $output .= '<div class="mrm-stat-item mrm-average-rating">';
            $output .= '<span class="mrm-stat-number">' . number_format($stats->average_rating, 1) . '</span>';
            $output .= '<span class="mrm-stat-label">' . __('Average Rating', 'manual-review-manager') . '</span>';
            $output .= '<div class="mrm-rating">';
            for ($i = 1; $i <= 5; $i++) {
                $output .= $i <= round($stats->average_rating) ? '<span class="mrm-star filled">★</span>' : '<span class="mrm-star">☆</span>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }
        
        if ($args['show_breakdown']) {
            $output .= '<div class="mrm-rating-breakdown">';
            $output .= '<h4>' . __('Rating Breakdown', 'manual-review-manager') . '</h4>';
            
            for ($i = 5; $i >= 1; $i--) {
                $count_property = $this->get_rating_property($i);
                $count = $stats->$count_property;
                $percentage = $stats->total_reviews > 0 ? ($count / $stats->total_reviews) * 100 : 0;
                
                $output .= '<div class="mrm-breakdown-item">';
                $output .= '<span class="mrm-breakdown-stars">' . $i . ' ★</span>';
                $output .= '<div class="mrm-breakdown-bar"><div class="mrm-breakdown-fill" style="width: ' . $percentage . '%"></div></div>';
                $output .= '<span class="mrm-breakdown-count">(' . $count . ')</span>';
                $output .= '</div>';
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }
    
    private function get_rating_property($rating) {
        $ratings = array(
            5 => 'five_star',
            4 => 'four_star',
            3 => 'three_star',
            2 => 'two_star',
            1 => 'one_star'
        );
        return $ratings[$rating];
    }
    
    private function get_relative_time($date) {
        $time = time() - strtotime($date);
        
        if ($time < 60) {
            return __('Just now', 'manual-review-manager');
        } elseif ($time < 3600) {
            $minutes = round($time / 60);
            return sprintf(_n('%d minute ago', '%d minutes ago', $minutes, 'manual-review-manager'), $minutes);
        } elseif ($time < 86400) {
            $hours = round($time / 3600);
            return sprintf(_n('%d hour ago', '%d hours ago', $hours, 'manual-review-manager'), $hours);
        } elseif ($time < 2592000) {
            $days = round($time / 86400);
            return sprintf(_n('%d day ago', '%d days ago', $days, 'manual-review-manager'), $days);
        } elseif ($time < 31536000) {
            $months = round($time / 2592000);
            return sprintf(_n('%d month ago', '%d months ago', $months, 'manual-review-manager'), $months);
        } else {
            $years = round($time / 31536000);
            return sprintf(_n('%d year ago', '%d years ago', $years, 'manual-review-manager'), $years);
        }
    }
    
    private function get_platform_svg($platform) {
        switch ($platform) {
            case 'google':
                return '<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>';
                
            case 'yelp':
                return '<svg width="16" height="16" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <linearGradient id="yelp_grad_16" x1="1.323" x2="44.983" y1="5.864" y2="47.991" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#f52537"></stop>
                        <stop offset=".293" stop-color="#f32536"></stop>
                        <stop offset=".465" stop-color="#ea2434"></stop>
                        <stop offset=".605" stop-color="#dc2231"></stop>
                        <stop offset=".729" stop-color="#c8202c"></stop>
                        <stop offset=".841" stop-color="#ae1e25"></stop>
                        <stop offset=".944" stop-color="#8f1a1d"></stop>
                        <stop offset="1" stop-color="#7a1818"></stop>
                    </linearGradient>
                    <path fill="url(#yelp_grad_16)" d="M10.7,32.7c-0.5,0-0.9-0.3-1.2-0.8c-0.2-0.4-0.3-1-0.4-1.7c-0.2-2.2,0-5.5,0.7-6.5c0.3-0.5,0.8-0.7,1.2-0.7c0.3,0,0.6,0.1,7.1,2.8l1.9,0.8c0.7,0.3,1.1,1,1.1,1.8s-0.5,1.4-1.2,1.6l-2.7,0.9C11.2,32.7,11,32.7,10.7,32.7z M24,36.3c0,6.3,0,6.5-0.1,6.8c-0.2,0.5-0.6,0.8-1.1,0.9c-1.6,0.3-6.6-1.6-7.7-2.8c-0.2-0.3-0.3-0.5-0.4-0.8c0-0.2,0-0.4,0.1-0.6c0.1-0.3,0.3-0.6,4.8-5.9l1.3-1.6c0.4-0.6,1.3-0.7,2-0.5c0.7,0.3,1.2,0.9,1.1,1.6C24,33.5,24,36.3,24,36.3z M22.8,22.9c-0.3,0.1-1.3,0.4-2.5-1.6c0,0-8.1-12.9-8.3-13.3c-0.1-0.4,0-1,0.4-1.4c1.2-1.3,7.7-3.1,9.4-2.7c0.6,0.1,0.9,0.5,1.1,1c0.1,0.6,0.9,12.5,1,15.2C24.1,22.5,23.1,22.8,22.8,22.9z M27.2,25.9c-0.4-0.6-0.4-1.4,0-1.9l1.7-2.3c3.6-5,3.8-5.3,4.1-5.4c0.4-0.3,0.9-0.3,1.4-0.1c1.4,0.7,4.4,5.1,4.6,6.7c0,0,0,0,0,0.1c0,0.6-0.2,1-0.6,1.3c-0.3,0.2-0.5,0.3-7.4,1.9c-1.1,0.3-1.7,0.4-2,0.5v-0.1C28.4,26.9,27.6,26.5,27.2,25.9z M38.9,34.4c-0.2,1.6-3.5,5.8-5.1,6.4c-0.5,0.2-1,0.2-1.4-0.2c-0.3-0.2-0.5-0.6-4.1-6.4l-1.1-1.7c-0.4-0.6-0.3-1.4,0.2-2.1c0.5-0.6,1.2-0.8,1.9-0.6l2.7,0.9c6,2,6.2,2,6.4,2.2C38.8,33.4,39,33.9,38.9,34.4z"/>
                </svg>';
                
            case 'facebook':
                return '<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    <path fill="#FFFFFF" d="M16.671 15.543l.532-3.47h-3.328v-2.25c0-.949.465-1.874 1.956-1.874h1.513V4.996s-1.374-.235-2.686-.235c-2.741 0-4.533 1.662-4.533 4.669v2.142H7.078v3.47h3.047v8.385a12.118 12.118 0 003.75 0v-8.385h2.796z"/>
                </svg>';
                
            case 'user_submitted':
                return '<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#50c878" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>';
                
            default:
                return '<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#666" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>';
        }
    }
    
    public function add_structured_data() {
        if (!$this->has_review_shortcode()) {
            return;
        }
        
        $reviews = MRM_Database::get_reviews(array('max_reviews' => 50, 'approved_only' => true));
        if (empty($reviews)) {
            return;
        }
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => array()
        );
        
        foreach ($reviews as $index => $review) {
            $review_data = array(
                '@type' => 'Review',
                'author' => array(
                    '@type' => 'Person',
                    'name' => stripslashes($review->reviewer_name)
                ),
                'reviewRating' => array(
                    '@type' => 'Rating',
                    'ratingValue' => $review->rating,
                    'bestRating' => 5
                ),
                'reviewBody' => stripslashes($review->review_text),
                'datePublished' => $review->review_date
            );
            
            $structured_data['itemListElement'][] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $review_data
            );
        }
        
        echo '<script type="application/ld+json">' . json_encode($structured_data) . '</script>';
    }
    
    public function add_button_color_styles() {
        if (!$this->has_review_shortcode()) {
            return;
        }
        
        $display_settings = get_option('mrm_display_settings', array());
        $button_color = isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue';
        
        $color_schemes = array(
            'blue' => array('bg' => '#007cba', 'hover' => '#005a87'),
            'black' => array('bg' => '#333333', 'hover' => '#1a1a1a'),
            'red' => array('bg' => '#dc3545', 'hover' => '#c82333'),
            'green' => array('bg' => '#28a745', 'hover' => '#218838'),
            'purple' => array('bg' => '#6f42c1', 'hover' => '#5a32a3'),
            'orange' => array('bg' => '#fd7e14', 'hover' => '#e55100'),
            'grey' => array('bg' => '#6c757d', 'hover' => '#545b62')
        );
        
        $colors = isset($color_schemes[$button_color]) ? $color_schemes[$button_color] : $color_schemes['blue'];
        
        echo '<style>
        :root {
            --mrm-button-bg: ' . $colors['bg'] . ';
            --mrm-button-hover: ' . $colors['hover'] . ';
        }
        
        /* Review submission button */
        .mrm-submit-review-btn {
            background: ' . $colors['bg'] . ' !important;
        }
        .mrm-submit-review-btn:hover {
            background: ' . $colors['hover'] . ' !important;
        }
        
        /* Form buttons */
        .mrm-submit-btn {
            background: ' . $colors['bg'] . ' !important;
        }
        .mrm-submit-btn:hover:not(:disabled) {
            background: ' . $colors['hover'] . ' !important;
        }
        
        /* Success message buttons */
        .mrm-success-message .mrm-back-btn {
            background: ' . $colors['bg'] . ' !important;
        }
        .mrm-success-message .mrm-back-btn:hover {
            background: ' . $colors['hover'] . ' !important;
        }
        </style>';
    }
} 