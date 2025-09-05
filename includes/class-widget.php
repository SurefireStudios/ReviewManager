<?php
/**
 * Manual Review Manager Widget Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRM_Latest_Reviews_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'mrm_latest_reviews',
            __('Review Manager: Latest Reviews', 'manual-review-manager'),
            array(
                'description' => __('Display latest reviews from Manual Review Manager', 'manual-review-manager'),
                'classname' => 'mrm-latest-reviews-widget'
            )
        );
        
        // Hook into widget scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_widget_scripts'));
    }
    
    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Reviews', 'manual-review-manager');
        $title = apply_filters('widget_title', $title);
        
        // Get widget settings
        $max_reviews = !empty($instance['max_reviews']) ? intval($instance['max_reviews']) : 3;
        $min_rating = !empty($instance['min_rating']) ? floatval($instance['min_rating']) : 1;
        $location_id = !empty($instance['location_id']) ? intval($instance['location_id']) : 0;
        $show_excerpts = !empty($instance['show_excerpts']) ? (bool)$instance['show_excerpts'] : true;
        $excerpt_length = !empty($instance['excerpt_length']) ? intval($instance['excerpt_length']) : 15;
        $show_ratings = !empty($instance['show_ratings']) ? (bool)$instance['show_ratings'] : true;
        $show_photos = !empty($instance['show_photos']) ? (bool)$instance['show_photos'] : true;
        $show_dates = !empty($instance['show_dates']) ? (bool)$instance['show_dates'] : true;
        $show_platform = !empty($instance['show_platform']) ? (bool)$instance['show_platform'] : false;
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // Build review display arguments
        $review_args = array(
            'layout' => 'widget',
            'max_reviews' => $max_reviews,
            'min_rating' => $min_rating,
            'location_id' => $location_id,
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'show_photos' => $show_photos,
            'show_dates' => $show_dates,
            'show_platform' => $show_platform,
            'truncate' => $show_excerpts ? $excerpt_length : 0,
            'widget_mode' => true,
            'show_ratings' => $show_ratings
        );
        
        echo $this->display_widget_reviews($review_args);
        
        echo $args['after_widget'];
    }
    
    /**
     * Widget Backend
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Reviews', 'manual-review-manager');
        $max_reviews = !empty($instance['max_reviews']) ? $instance['max_reviews'] : 3;
        $min_rating = !empty($instance['min_rating']) ? $instance['min_rating'] : 1;
        $location_id = !empty($instance['location_id']) ? $instance['location_id'] : 0;
        $show_excerpts = !empty($instance['show_excerpts']) ? $instance['show_excerpts'] : 1;
        $excerpt_length = !empty($instance['excerpt_length']) ? $instance['excerpt_length'] : 15;
        $show_ratings = !empty($instance['show_ratings']) ? $instance['show_ratings'] : 1;
        $show_photos = !empty($instance['show_photos']) ? $instance['show_photos'] : 1;
        $show_dates = !empty($instance['show_dates']) ? $instance['show_dates'] : 1;
        $show_platform = !empty($instance['show_platform']) ? $instance['show_platform'] : 0;
        
        // Get available locations for dropdown
        $locations = MRM_Database::get_locations();
        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'manual-review-manager'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('max_reviews'); ?>"><?php _e('Maximum Reviews to Show:', 'manual-review-manager'); ?></label>
            <input class="small-text" id="<?php echo $this->get_field_id('max_reviews'); ?>" name="<?php echo $this->get_field_name('max_reviews'); ?>" type="number" min="1" max="20" value="<?php echo esc_attr($max_reviews); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('min_rating'); ?>"><?php _e('Minimum Rating:', 'manual-review-manager'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('min_rating'); ?>" name="<?php echo $this->get_field_name('min_rating'); ?>">
                <option value="1" <?php selected($min_rating, 1); ?>>1+ <?php _e('Stars', 'manual-review-manager'); ?></option>
                <option value="2" <?php selected($min_rating, 2); ?>>2+ <?php _e('Stars', 'manual-review-manager'); ?></option>
                <option value="3" <?php selected($min_rating, 3); ?>>3+ <?php _e('Stars', 'manual-review-manager'); ?></option>
                <option value="4" <?php selected($min_rating, 4); ?>>4+ <?php _e('Stars', 'manual-review-manager'); ?></option>
                <option value="5" <?php selected($min_rating, 5); ?>>5 <?php _e('Stars', 'manual-review-manager'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('location_id'); ?>"><?php _e('Location:', 'manual-review-manager'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('location_id'); ?>" name="<?php echo $this->get_field_name('location_id'); ?>">
                <option value="0" <?php selected($location_id, 0); ?>><?php _e('All Locations', 'manual-review-manager'); ?></option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo esc_attr($location->id); ?>" <?php selected($location_id, $location->id); ?>><?php echo esc_html($location->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_excerpts); ?> id="<?php echo $this->get_field_id('show_excerpts'); ?>" name="<?php echo $this->get_field_name('show_excerpts'); ?>" value="1">
            <label for="<?php echo $this->get_field_id('show_excerpts'); ?>"><?php _e('Show Review Excerpts', 'manual-review-manager'); ?></label>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Excerpt Length (words):', 'manual-review-manager'); ?></label>
            <input class="small-text" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="number" min="5" max="50" value="<?php echo esc_attr($excerpt_length); ?>">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_ratings); ?> id="<?php echo $this->get_field_id('show_ratings'); ?>" name="<?php echo $this->get_field_name('show_ratings'); ?>" value="1">
            <label for="<?php echo $this->get_field_id('show_ratings'); ?>"><?php _e('Show Star Ratings', 'manual-review-manager'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_photos); ?> id="<?php echo $this->get_field_id('show_photos'); ?>" name="<?php echo $this->get_field_name('show_photos'); ?>" value="1">
            <label for="<?php echo $this->get_field_id('show_photos'); ?>"><?php _e('Show Reviewer Photos', 'manual-review-manager'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_dates); ?> id="<?php echo $this->get_field_id('show_dates'); ?>" name="<?php echo $this->get_field_name('show_dates'); ?>" value="1">
            <label for="<?php echo $this->get_field_id('show_dates'); ?>"><?php _e('Show Review Dates', 'manual-review-manager'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_platform); ?> id="<?php echo $this->get_field_id('show_platform'); ?>" name="<?php echo $this->get_field_name('show_platform'); ?>" value="1">
            <label for="<?php echo $this->get_field_id('show_platform'); ?>"><?php _e('Show Review Platform', 'manual-review-manager'); ?></label>
        </p>
        
        <?php
    }
    
    /**
     * Updating widget replacing old instances with new
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['max_reviews'] = (!empty($new_instance['max_reviews'])) ? intval($new_instance['max_reviews']) : 3;
        $instance['min_rating'] = (!empty($new_instance['min_rating'])) ? floatval($new_instance['min_rating']) : 1;
        $instance['location_id'] = (!empty($new_instance['location_id'])) ? intval($new_instance['location_id']) : 0;
        $instance['show_excerpts'] = (!empty($new_instance['show_excerpts'])) ? 1 : 0;
        $instance['excerpt_length'] = (!empty($new_instance['excerpt_length'])) ? intval($new_instance['excerpt_length']) : 15;
        $instance['show_ratings'] = (!empty($new_instance['show_ratings'])) ? 1 : 0;
        $instance['show_photos'] = (!empty($new_instance['show_photos'])) ? 1 : 0;
        $instance['show_dates'] = (!empty($new_instance['show_dates'])) ? 1 : 0;
        $instance['show_platform'] = (!empty($new_instance['show_platform'])) ? 1 : 0;
        
        return $instance;
    }
    
    /**
     * Enqueue scripts and styles for widget
     */
    public function enqueue_widget_scripts() {
        if (is_active_widget(false, false, $this->id_base)) {
            wp_enqueue_style('mrm-frontend', MRM_PLUGIN_URL . 'assets/frontend.css', array(), MRM_VERSION);
            wp_enqueue_script('mrm-frontend', MRM_PLUGIN_URL . 'assets/frontend.js', array('jquery'), MRM_VERSION, true);
            
            // Localize script for AJAX
            wp_localize_script('mrm-frontend', 'mrm_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mrm_nonce')
            ));
        }
    }
    
    /**
     * Display reviews in widget format
     */
    private function display_widget_reviews($args) {
        // Build database query args
        $db_args = array(
            'max_reviews' => intval($args['max_reviews']),
            'min_rating' => floatval($args['min_rating']),
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'approved_only' => true
        );
        
        if ($args['location_id']) {
            $db_args['location_id'] = intval($args['location_id']);
        }
        
        $reviews = MRM_Database::get_reviews($db_args);
        
        if (empty($reviews)) {
            return '<div class="mrm-widget-no-reviews"><p>' . __('No reviews found.', 'manual-review-manager') . '</p></div>';
        }
        
        $output = '<div class="mrm-widget-reviews">';
        
        foreach ($reviews as $review) {
            $output .= $this->render_widget_review_item($review, $args);
        }
        
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Render a single review item for widget
     */
    private function render_widget_review_item($review, $args) {
        $output = '<div class="mrm-widget-review-item">';
        
        // Header with photo and name
        $output .= '<div class="mrm-widget-review-header">';
        
        if ($args['show_photos'] && !empty($review->reviewer_photo_url)) {
            $output .= '<img src="' . esc_url($review->reviewer_photo_url) . '" alt="' . esc_attr($review->reviewer_name) . '" class="mrm-widget-reviewer-photo" />';
        }
        
        $output .= '<div class="mrm-widget-reviewer-info">';
        $output .= '<h3 class="mrm-widget-reviewer-name">' . esc_html(stripslashes($review->reviewer_name)) . '</h3>';
        
        // Rating stars
        if ($args['show_ratings']) {
            $output .= '<div class="mrm-widget-rating">';
            for ($i = 1; $i <= 5; $i++) {
                $output .= $i <= $review->rating ? '<span class="mrm-star filled">★</span>' : '<span class="mrm-star">☆</span>';
            }
            $output .= '</div>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        
        // Review content
        if ($args['truncate'] > 0) {
            $review_text = wp_trim_words(stripslashes($review->review_text), $args['truncate']);
            $output .= '<div class="mrm-widget-review-content">';
            $output .= '<p class="mrm-widget-review-text">' . nl2br(wp_kses_post($review_text)) . '</p>';
            $output .= '</div>';
        }
        
        // Footer with date and platform
        if ($args['show_dates'] || $args['show_platform']) {
            $output .= '<div class="mrm-widget-review-footer">';
            
            if ($args['show_dates']) {
                $output .= '<span class="mrm-widget-review-date">' . $this->get_relative_time($review->review_date) . '</span>';
            }
            
            if ($args['show_platform'] && $review->platform !== 'manual') {
                $platform_label = ucfirst($review->platform);
                $output .= '<span class="mrm-widget-platform mrm-platform-' . esc_attr($review->platform) . '" title="' . esc_attr($platform_label) . ' Review">';
                $output .= $this->get_platform_svg($review->platform);
                $output .= '</span>';
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Get relative time string
     */
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
    
    /**
     * Get platform SVG icon
     */
    private function get_platform_svg($platform) {
        switch ($platform) {
            case 'google':
                return '<svg width="12" height="12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>';
                
            case 'yelp':
                return '<svg width="12" height="12" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
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
                return '<svg width="12" height="12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    <path fill="#FFFFFF" d="M16.671 15.543l.532-3.47h-3.328v-2.25c0-.949.465-1.874 1.956-1.874h1.513V4.996s-1.374-.235-2.686-.235c-2.741 0-4.533 1.662-4.533 4.669v2.142H7.078v3.47h3.047v8.385a12.118 12.118 0 003.75 0v-8.385h2.796z"/>
                </svg>';
                
            default:
                return '<svg width="12" height="12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#666" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>';
        }
    }
}


