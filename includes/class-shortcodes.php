<?php
/**
 * Review Manager Shortcodes Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRM_Shortcodes {
    
    private $frontend;
    
    public function __construct() {
        add_shortcode('review_manager', array($this, 'review_manager_shortcode'));
        add_shortcode('review_slider', array($this, 'review_slider_shortcode'));
        add_shortcode('review_grid_slider', array($this, 'review_grid_slider_shortcode'));
        add_shortcode('review_stats', array($this, 'review_stats_shortcode'));
        
        $this->frontend = new MRM_Frontend();
    }
    
    /**
     * Main review display shortcode
     * [review_manager layout="grid" columns="3" max_reviews="10" min_rating="1" platform="all" location_id="0" sort_by="review_date" order="DESC" show_photos="true" show_dates="true" show_platform="true" truncate="50" photo_size="small" show_review_button="false"]
     */
    public function review_manager_shortcode($atts) {
        $atts = shortcode_atts(array(
            'layout' => 'grid',
            'columns' => '3',
            'max_reviews' => '10',
            'min_rating' => '1',
            'platform' => 'all',
            'location_id' => '0',
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'show_photos' => 'true',
            'show_dates' => 'true',
            'show_platform' => 'true',
            'truncate' => '50',
            'theme' => '',
            'photo_size' => '',
            'show_review_button' => 'false'
        ), $atts, 'review_manager');
        
        // Convert string booleans to actual booleans
        $atts['show_photos'] = filter_var($atts['show_photos'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_dates'] = filter_var($atts['show_dates'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_platform'] = filter_var($atts['show_platform'], FILTER_VALIDATE_BOOLEAN);
        
        // Convert numeric strings
        $atts['columns'] = intval($atts['columns']);
        $atts['max_reviews'] = intval($atts['max_reviews']);
        $atts['min_rating'] = floatval($atts['min_rating']);
        $atts['location_id'] = intval($atts['location_id']);
        $atts['truncate'] = intval($atts['truncate']);
        
        $output = $this->frontend->display_reviews($atts);
        
        // Add review button if enabled and user is logged in
        if (filter_var($atts['show_review_button'], FILTER_VALIDATE_BOOLEAN) && is_user_logged_in()) {
            $output .= $this->render_review_button($atts);
        }
        
        return $output;
    }
    
    private function render_review_button($atts) {
        $location_id = intval($atts['location_id']);
        $review_page_url = add_query_arg(array(
            'mrm_action' => 'submit_review',
            'location_id' => $location_id
        ), get_permalink());
        
        $output = '<div class="mrm-review-button-container">';
        $output .= '<a href="' . esc_url($review_page_url) . '" class="mrm-submit-review-btn">';
        $output .= __('Leave Your Own Review', 'manual-review-manager');
        $output .= '</a>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Review slider shortcode
     * [review_slider max_reviews="10" min_rating="1" platform="all" location_id="0" autoplay="true" speed="5000" arrows="true" dots="true" show_photos="true" show_dates="true" show_platform="true"]
     */
    public function review_slider_shortcode($atts) {
        $atts = shortcode_atts(array(
            'max_reviews' => '20',
            'min_rating' => '1',
            'platform' => 'all',
            'location_id' => '0',
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'autoplay' => 'true',
            'speed' => '5000',
            'arrows' => 'true',
            'dots' => 'true',
            'show_photos' => 'true',
            'show_dates' => 'true',
            'show_platform' => 'true',
            'truncate' => '50',
            'theme' => '',
            'photo_size' => ''
        ), $atts, 'review_slider');
        
        // Force layout to slider
        $atts['layout'] = 'slider';
        
        // Convert string booleans to actual booleans
        $atts['autoplay'] = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $atts['arrows'] = filter_var($atts['arrows'], FILTER_VALIDATE_BOOLEAN);
        $atts['dots'] = filter_var($atts['dots'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_photos'] = filter_var($atts['show_photos'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_dates'] = filter_var($atts['show_dates'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_platform'] = filter_var($atts['show_platform'], FILTER_VALIDATE_BOOLEAN);
        
        // Convert numeric strings
        $atts['max_reviews'] = intval($atts['max_reviews']);
        $atts['min_rating'] = floatval($atts['min_rating']);
        $atts['location_id'] = intval($atts['location_id']);
        $atts['speed'] = intval($atts['speed']);
        $atts['truncate'] = intval($atts['truncate']);
        
        return $this->frontend->display_reviews($atts);
    }
    
    /**
     * Review grid slider shortcode
     * [review_grid_slider columns="3" max_reviews="12" min_rating="1" platform="all" location_id="0" autoplay="true" speed="5000" arrows="true" dots="true" show_photos="true" show_dates="true" show_platform="true"]
     */
    public function review_grid_slider_shortcode($atts) {
        $atts = shortcode_atts(array(
            'columns' => '3',
            'max_reviews' => '20',
            'min_rating' => '1',
            'platform' => 'all',
            'location_id' => '0',
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'autoplay' => 'true',
            'speed' => '5000',
            'arrows' => 'true',
            'dots' => 'true',
            'show_photos' => 'true',
            'show_dates' => 'true',
            'show_platform' => 'true',
            'truncate' => '50',
            'theme' => '',
            'photo_size' => ''
        ), $atts, 'review_grid_slider');
        
        // Force layout to grid_slider
        $atts['layout'] = 'grid_slider';
        
        // Convert string booleans to actual booleans
        $atts['autoplay'] = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $atts['arrows'] = filter_var($atts['arrows'], FILTER_VALIDATE_BOOLEAN);
        $atts['dots'] = filter_var($atts['dots'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_photos'] = filter_var($atts['show_photos'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_dates'] = filter_var($atts['show_dates'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_platform'] = filter_var($atts['show_platform'], FILTER_VALIDATE_BOOLEAN);
        
        // Convert numeric strings
        $atts['columns'] = max(1, min(4, intval($atts['columns']))); // Limit to 1-4 columns
        $atts['max_reviews'] = intval($atts['max_reviews']);
        $atts['min_rating'] = floatval($atts['min_rating']);
        $atts['location_id'] = intval($atts['location_id']);
        $atts['speed'] = intval($atts['speed']);
        $atts['truncate'] = intval($atts['truncate']);
        
        return $this->frontend->display_reviews($atts);
    }
    
    /**
     * Review statistics shortcode
     * [review_stats location_id="0" show_total="true" show_average="true" show_breakdown="false"]
     */
    public function review_stats_shortcode($atts) {
        $atts = shortcode_atts(array(
            'location_id' => '0',
            'show_total' => 'true',
            'show_average' => 'true',
            'show_breakdown' => 'false',
            'theme' => ''
        ), $atts, 'review_stats');
        
        // Convert string booleans to actual booleans
        $atts['show_total'] = filter_var($atts['show_total'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_average'] = filter_var($atts['show_average'], FILTER_VALIDATE_BOOLEAN);
        $atts['show_breakdown'] = filter_var($atts['show_breakdown'], FILTER_VALIDATE_BOOLEAN);
        
        // Convert numeric strings
        $atts['location_id'] = intval($atts['location_id']);
        
        return $this->frontend->display_review_stats($atts);
    }
} 