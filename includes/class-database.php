<?php
/**
 * Manual Review Manager Database Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRM_Database {
    
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Locations table - simplified for manual management
        $locations_table = $wpdb->prefix . 'mrm_locations';
        $locations_sql = "CREATE TABLE $locations_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            address text,
            phone varchar(50),
            website varchar(255),
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Reviews table - for manual entry
        $reviews_table = $wpdb->prefix . 'mrm_reviews';
        $reviews_sql = "CREATE TABLE $reviews_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            location_id mediumint(9) NOT NULL,
            reviewer_name varchar(255) NOT NULL,
            reviewer_email varchar(255),
            reviewer_photo_url varchar(500),
            rating decimal(2,1) NOT NULL,
            review_text text NOT NULL,
            review_date date NOT NULL,
            platform varchar(50) DEFAULT 'manual',
            is_featured tinyint(1) DEFAULT 0,
            is_approved tinyint(1) DEFAULT 1,
            original_review_text text,
            is_edited tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY location_id (location_id),
            KEY rating (rating),
            KEY platform (platform),
            KEY is_approved (is_approved)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($locations_sql);
        dbDelta($reviews_sql);
    }
    
    // Location methods
    public static function get_locations() {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_locations';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY name ASC");
    }
    
    public static function get_location($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_locations';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    public static function create_location($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_locations';
        
        return $wpdb->insert($table, array(
            'name' => sanitize_text_field($data['name']),
            'address' => sanitize_textarea_field($data['address']),
            'phone' => sanitize_text_field($data['phone']),
            'website' => esc_url_raw($data['website']),
            'description' => sanitize_textarea_field($data['description'])
        ));
    }
    
    public static function update_location($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_locations';
        
        return $wpdb->update($table, array(
            'name' => sanitize_text_field($data['name']),
            'address' => sanitize_textarea_field($data['address']),
            'phone' => sanitize_text_field($data['phone']),
            'website' => esc_url_raw($data['website']),
            'description' => sanitize_textarea_field($data['description'])
        ), array('id' => $id));
    }
    
    public static function delete_location($id) {
        global $wpdb;
        $locations_table = $wpdb->prefix . 'mrm_locations';
        $reviews_table = $wpdb->prefix . 'mrm_reviews';
        
        // Delete associated reviews first
        $wpdb->delete($reviews_table, array('location_id' => $id));
        
        // Delete location
        return $wpdb->delete($locations_table, array('id' => $id));
    }
    
    // Review methods
    public static function get_reviews($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        $locations_table = $wpdb->prefix . 'mrm_locations';
        
        $defaults = array(
            'location_id' => 0,
            'platform' => '',
            'min_rating' => 1,
            'max_reviews' => 50,
            'sort_by' => 'review_date',
            'order' => 'DESC',
            'approved_only' => true,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array();
        $where_values = array();
        
        if ($args['approved_only']) {
            $where_conditions[] = "r.is_approved = 1";
        }
        
        if ($args['location_id']) {
            $where_conditions[] = "r.location_id = %d";
            $where_values[] = $args['location_id'];
        }
        
        if ($args['platform']) {
            $where_conditions[] = "r.platform = %s";
            $where_values[] = $args['platform'];
        }
        
        if ($args['min_rating'] > 1) {
            $where_conditions[] = "r.rating >= %f";
            $where_values[] = $args['min_rating'];
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        $order_by = sanitize_sql_orderby($args['sort_by']);
        $order = in_array(strtoupper($args['order']), array('ASC', 'DESC')) ? strtoupper($args['order']) : 'DESC';
        $limit = intval($args['max_reviews']);
        $offset = intval($args['offset']);
        
        $limit_clause = '';
        if ($limit > 0) {
            $limit_clause = "LIMIT $limit";
            if ($offset > 0) {
                $limit_clause .= " OFFSET $offset";
            }
        }
        
        $sql = "SELECT r.*, l.name as location_name 
                FROM $table r 
                LEFT JOIN $locations_table l ON r.location_id = l.id 
                $where_clause 
                ORDER BY r.$order_by $order 
                $limit_clause";
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($sql, $where_values));
        } else {
            return $wpdb->get_results($sql);
        }
    }
    
    public static function get_review($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        $locations_table = $wpdb->prefix . 'mrm_locations';
        
        $sql = "SELECT r.*, l.name as location_name 
                FROM $table r 
                LEFT JOIN $locations_table l ON r.location_id = l.id 
                WHERE r.id = %d";
        
        return $wpdb->get_row($wpdb->prepare($sql, $id));
    }
    
    public static function create_review($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        
        return $wpdb->insert($table, array(
            'location_id' => intval($data['location_id']),
            'reviewer_name' => sanitize_text_field($data['reviewer_name']),
            'reviewer_email' => sanitize_email($data['reviewer_email']),
            'reviewer_photo_url' => esc_url_raw($data['reviewer_photo_url']),
            'rating' => floatval($data['rating']),
            'review_text' => sanitize_textarea_field($data['review_text']),
            'review_date' => sanitize_text_field($data['review_date']),
            'platform' => sanitize_text_field($data['platform']),
            'is_featured' => intval($data['is_featured']),
            'is_approved' => intval($data['is_approved'])
        ));
    }
    
    public static function update_review($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        
        // If review text is being changed, save original if not already saved
        $current_review = self::get_review($id);
        $update_data = array();
        
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'reviewer_name':
                    $update_data[$key] = sanitize_text_field($value);
                    break;
                case 'reviewer_email':
                    $update_data[$key] = sanitize_email($value);
                    break;
                case 'reviewer_photo_url':
                    $update_data[$key] = esc_url_raw($value);
                    break;
                case 'rating':
                    $update_data[$key] = floatval($value);
                    break;
                case 'review_text':
                    $update_data[$key] = sanitize_textarea_field($value);
                    // Save original text if this is the first edit
                    if (!$current_review->is_edited && $current_review->review_text !== $value) {
                        $update_data['original_review_text'] = $current_review->review_text;
                        $update_data['is_edited'] = 1;
                    }
                    break;
                case 'review_date':
                    $update_data[$key] = sanitize_text_field($value);
                    break;
                case 'platform':
                    $update_data[$key] = sanitize_text_field($value);
                    break;
                case 'is_featured':
                case 'is_approved':
                    $update_data[$key] = intval($value);
                    break;
            }
        }
        
        return $wpdb->update($table, $update_data, array('id' => $id));
    }
    
    public static function delete_review($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        return $wpdb->delete($table, array('id' => $id));
    }
    
    public static function bulk_replace_text($search_text, $replace_text, $location_id = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        
        $where_clause = '';
        $where_values = array($replace_text, $search_text, $search_text);
        
        if ($location_id) {
            $where_clause = 'AND location_id = %d';
            $where_values[] = $location_id;
        }
        
        // Update reviews and mark as edited if not already
        $sql = "UPDATE $table 
                SET review_text = REPLACE(review_text, %s, %s),
                    original_review_text = CASE 
                        WHEN is_edited = 0 AND review_text LIKE %s 
                        THEN review_text 
                        ELSE original_review_text 
                    END,
                    is_edited = CASE 
                        WHEN review_text LIKE %s 
                        THEN 1 
                        ELSE is_edited 
                    END
                WHERE review_text LIKE %s $where_clause";
        
        $values = array($replace_text, $search_text, '%' . $search_text . '%', '%' . $search_text . '%', '%' . $search_text . '%');
        if ($location_id) {
            $values[] = $location_id;
        }
        
        $result = $wpdb->query($wpdb->prepare($sql, $values));
        return $result;
    }
    
    public static function get_review_stats($location_id = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        
        $where_clause = 'WHERE is_approved = 1';
        $where_values = array();
        
        if ($location_id) {
            $where_clause .= ' AND location_id = %d';
            $where_values[] = $location_id;
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM $table 
                $where_clause";
        
        if (!empty($where_values)) {
            return $wpdb->get_row($wpdb->prepare($sql, $where_values));
        } else {
            return $wpdb->get_row($sql);
        }
    }
} 