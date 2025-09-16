<?php
/**
 * Manual Review Manager User Reviews Class
 * Handles user-submitted reviews
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRM_User_Reviews {
    
    public function __construct() {
        add_action('init', array($this, 'handle_review_submission'));
        add_action('wp', array($this, 'display_review_form'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_review_scripts'));
        
        // AJAX handlers for logged-in users
        add_action('wp_ajax_mrm_submit_review', array($this, 'ajax_submit_review'));
        
        // Handle file uploads
        add_action('wp_ajax_mrm_upload_review_photo', array($this, 'ajax_upload_review_photo'));
    }
    
    public function enqueue_review_scripts() {
        if (isset($_GET['mrm_action']) && $_GET['mrm_action'] === 'submit_review') {
            wp_enqueue_style('mrm-review-form', MRM_PLUGIN_URL . 'assets/review-form.css', array(), MRM_VERSION);
            wp_enqueue_script('mrm-review-form', MRM_PLUGIN_URL . 'assets/review-form.js', array('jquery'), MRM_VERSION, true);
            
            wp_localize_script('mrm-review-form', 'mrm_review_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mrm_review_nonce'),
                'max_file_size' => wp_max_upload_size(),
                'allowed_types' => array('image/jpeg', 'image/jpg', 'image/png', 'image/gif')
            ));
        }
    }
    
    public function display_review_form() {
        if (isset($_GET['mrm_action']) && $_GET['mrm_action'] === 'submit_review') {
            if (!is_user_logged_in()) {
                wp_redirect(wp_login_url(get_permalink()));
                exit;
            }
            
            add_filter('the_content', array($this, 'inject_review_form'));
        }
    }
    
    public function inject_review_form($content) {
        if (isset($_GET['mrm_action']) && $_GET['mrm_action'] === 'submit_review') {
            $location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;
            $current_user = wp_get_current_user();
            
            // Get locations for dropdown
            $locations = MRM_Database::get_locations();
            
            ob_start();
            ?>
            <div class="mrm-review-form-container">
                <h2><?php _e('Submit Your Review', 'manual-review-manager'); ?></h2>
                
                <?php if (isset($_GET['submitted']) && $_GET['submitted'] === 'success'): 
                    $display_settings = get_option('mrm_display_settings', array());
                    $redirect_url = isset($display_settings['redirect_after_review']) ? $display_settings['redirect_after_review'] : home_url();
                ?>
                    <div class="mrm-success-message">
                        <p><?php _e('Thank you! Your review has been submitted and is pending approval.', 'manual-review-manager'); ?></p>
                        <div class="mrm-buttons">
                            <a href="<?php echo esc_url(remove_query_arg(array('mrm_action', 'location_id', 'submitted'))); ?>" class="mrm-back-btn">
                                <?php _e('Back to Reviews', 'manual-review-manager'); ?>
                            </a>
                            <a href="<?php echo esc_url($redirect_url); ?>" class="mrm-back-btn">
                                <?php _e('Continue Browsing', 'manual-review-manager'); ?>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <form id="mrm-review-form" class="mrm-review-form" enctype="multipart/form-data">
                        <?php wp_nonce_field('mrm_review_nonce', 'mrm_review_nonce'); ?>
                        
                        <div class="mrm-form-group">
                            <label for="reviewer_name"><?php _e('Your Name', 'manual-review-manager'); ?> <span class="required">*</span></label>
                            <input type="text" id="reviewer_name" name="reviewer_name" value="<?php echo esc_attr($current_user->display_name); ?>" required />
                        </div>
                        
                        <div class="mrm-form-group">
                            <label for="reviewer_email"><?php _e('Your Email', 'manual-review-manager'); ?> <span class="required">*</span></label>
                            <input type="email" id="reviewer_email" name="reviewer_email" value="<?php echo esc_attr($current_user->user_email); ?>" required />
                        </div>
                        
                        <?php if (!empty($locations)): ?>
                        <div class="mrm-form-group">
                            <label for="location_id"><?php _e('Location', 'manual-review-manager'); ?> <span class="required">*</span></label>
                            <select id="location_id" name="location_id" required>
                                <option value=""><?php _e('Select a location', 'manual-review-manager'); ?></option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo esc_attr($location->id); ?>" <?php selected($location_id, $location->id); ?>>
                                        <?php echo esc_html($location->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="location_id" value="1" />
                        <?php endif; ?>
                        
                        <div class="mrm-form-group">
                            <label for="rating"><?php _e('Rating', 'manual-review-manager'); ?> <span class="required">*</span></label>
                            <div class="mrm-star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required />
                                    <label for="star<?php echo $i; ?>" class="mrm-star-label">★</label>
                                <?php endfor; ?>
                            </div>
                            <div class="mrm-rating-text"></div>
                        </div>
                        
                        <div class="mrm-form-group">
                            <label for="review_text"><?php _e('Your Review', 'manual-review-manager'); ?> <span class="required">*</span></label>
                            <textarea id="review_text" name="review_text" rows="6" placeholder="<?php esc_attr_e('Share your experience...', 'manual-review-manager'); ?>" required></textarea>
                        </div>
                        
                        <div class="mrm-form-group">
                            <label for="reviewer_photo"><?php _e('Your Photo (Optional)', 'manual-review-manager'); ?></label>
                            <div class="mrm-photo-upload">
                                <input type="file" id="reviewer_photo" name="reviewer_photo" accept="image/*" />
                                <div class="mrm-photo-preview" style="display: none;">
                                    <img src="" alt="Preview" />
                                    <button type="button" class="mrm-remove-photo"><?php _e('Remove', 'manual-review-manager'); ?></button>
                                </div>
                                <div class="mrm-upload-instructions">
                                    <p><?php _e('Upload a profile photo (JPG, PNG, GIF - Max 2MB)', 'manual-review-manager'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mrm-form-actions">
                            <button type="submit" class="mrm-submit-btn"><?php _e('Submit Review', 'manual-review-manager'); ?></button>
                            <a href="<?php echo esc_url(remove_query_arg(array('mrm_action', 'location_id'))); ?>" class="mrm-cancel-btn">
                                <?php _e('Cancel', 'manual-review-manager'); ?>
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <?php
            $form_content = ob_get_clean();
            return $form_content;
        }
        
        return $content;
    }
    
    public function handle_review_submission() {
        // Handle non-AJAX form submission as fallback
        if (isset($_POST['mrm_review_nonce']) && wp_verify_nonce($_POST['mrm_review_nonce'], 'mrm_review_nonce')) {
            if (!is_user_logged_in()) {
                wp_die(__('You must be logged in to submit a review.', 'manual-review-manager'));
            }
            
            $this->process_review_submission();
        }
    }
    
    public function ajax_submit_review() {
        check_ajax_referer('mrm_review_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die(json_encode(array('success' => false, 'message' => __('You must be logged in to submit a review.', 'manual-review-manager'))));
        }
        
        $result = $this->process_review_submission();
        wp_die(json_encode($result));
    }
    
    private function process_review_submission() {
        $current_user = wp_get_current_user();
        
        // Sanitize and validate input
        $reviewer_name = sanitize_text_field($_POST['reviewer_name']);
        $reviewer_email = sanitize_email($_POST['reviewer_email']);
        $location_id = intval($_POST['location_id']);
        $rating = floatval($_POST['rating']);
        $review_text = sanitize_textarea_field($_POST['review_text']);
        
        // Validation
        $errors = array();
        
        if (empty($reviewer_name)) {
            $errors[] = __('Name is required.', 'manual-review-manager');
        }
        
        if (empty($reviewer_email) || !is_email($reviewer_email)) {
            $errors[] = __('Valid email is required.', 'manual-review-manager');
        }
        
        if ($location_id <= 0) {
            $errors[] = __('Please select a location.', 'manual-review-manager');
        }
        
        if ($rating < 1 || $rating > 5) {
            $errors[] = __('Please select a rating.', 'manual-review-manager');
        }
        
        if (empty($review_text)) {
            $errors[] = __('Review text is required.', 'manual-review-manager');
        }
        
        if (!empty($errors)) {
            return array('success' => false, 'message' => implode(' ', $errors));
        }
        
        // Check for duplicate reviews from same user
        $existing_review = $this->check_duplicate_review($current_user->ID, $location_id);
        if ($existing_review) {
            return array('success' => false, 'message' => __('You have already submitted a review for this location.', 'manual-review-manager'));
        }
        
        // Handle photo upload if provided
        $photo_url = '';
        if (!empty($_FILES['reviewer_photo']['name'])) {
            $upload_result = $this->handle_photo_upload($_FILES['reviewer_photo']);
            if (is_wp_error($upload_result)) {
                return array('success' => false, 'message' => $upload_result->get_error_message());
            }
            $photo_url = $upload_result;
        }
        
        // Prepare review data
        $review_data = array(
            'location_id' => $location_id,
            'reviewer_name' => $reviewer_name,
            'reviewer_email' => $reviewer_email,
            'reviewer_photo_url' => $photo_url,
            'rating' => $rating,
            'review_text' => $review_text,
            'review_date' => current_time('Y-m-d'),
            'platform' => 'user_submitted',
            'is_featured' => 0,
            'is_approved' => 0 // User reviews require approval
        );
        
        // Add user ID for tracking
        $review_data['user_id'] = $current_user->ID;
        
        $result = MRM_Database::create_review($review_data);
        
        if ($result) {
            // Send notification to admin
            $this->send_admin_notification($review_data);
            
            if (wp_doing_ajax()) {
                return array('success' => true, 'message' => __('Review submitted successfully! It will be reviewed before being published.', 'manual-review-manager'));
            } else {
                // Redirect with success message
                $redirect_url = add_query_arg(array(
                    'mrm_action' => 'submit_review',
                    'submitted' => 'success'
                ), get_permalink());
                wp_redirect($redirect_url);
                exit;
            }
        } else {
            return array('success' => false, 'message' => __('Failed to submit review. Please try again.', 'manual-review-manager'));
        }
    }
    
    private function check_duplicate_review($user_id, $location_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'mrm_reviews';
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND location_id = %d",
            $user_id,
            $location_id
        ));
    }
    
    private function handle_photo_upload($file) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $uploadedfile = $file;
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
            )
        );
        
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            return $movefile['url'];
        } else {
            return new WP_Error('upload_error', $movefile['error']);
        }
    }
    
    private function send_admin_notification($review_data) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(__('[%s] New Review Submission', 'manual-review-manager'), $site_name);
        
        $message = sprintf(
            __("A new review has been submitted and is pending approval.\n\nReviewer: %s\nEmail: %s\nRating: %s/5\nReview: %s\n\nPlease log in to your admin panel to review and approve this submission.", 'manual-review-manager'),
            $review_data['reviewer_name'],
            $review_data['reviewer_email'],
            $review_data['rating'],
            $review_data['review_text']
        );
        
        wp_mail($admin_email, $subject, $message);
    }
    
    public function ajax_upload_review_photo() {
        check_ajax_referer('mrm_review_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die(json_encode(array('success' => false, 'message' => __('You must be logged in to upload photos.', 'manual-review-manager'))));
        }
        
        if (!empty($_FILES['photo'])) {
            $result = $this->handle_photo_upload($_FILES['photo']);
            
            if (is_wp_error($result)) {
                wp_die(json_encode(array('success' => false, 'message' => $result->get_error_message())));
            } else {
                wp_die(json_encode(array('success' => true, 'url' => $result)));
            }
        }
        
        wp_die(json_encode(array('success' => false, 'message' => __('No file uploaded.', 'manual-review-manager'))));
    }
}
