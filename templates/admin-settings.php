<?php
/**
 * Admin Settings Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$display_settings = get_option('mrm_display_settings', array(
    'show_photos' => 1,
    'show_dates' => 1,
    'show_platform' => 1,
    'max_reviews' => 10,
    'min_rating' => 1,
    'color_theme' => 'light',
    'photo_size' => 'small'
));
?>

<div class="wrap">
    <h1><?php esc_html_e('Review Display Settings', 'manual-review-manager'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('mrm_settings_group');
        do_settings_sections('mrm_settings_group');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Show Reviewer Photos', 'manual-review-manager'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="mrm_display_settings[show_photos]" value="1" <?php checked($display_settings['show_photos'], 1); ?> />
                        <?php esc_html_e('Display reviewer photos when available', 'manual-review-manager'); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Reviewer Photo Size', 'manual-review-manager'); ?></th>
                <td>
                    <select name="mrm_display_settings[photo_size]">
                        <option value="small" <?php selected(isset($display_settings['photo_size']) ? $display_settings['photo_size'] : 'small', 'small'); ?>>
                            <?php esc_html_e('Small Photos (Compact Layout)', 'manual-review-manager'); ?>
                        </option>
                        <option value="large" <?php selected(isset($display_settings['photo_size']) ? $display_settings['photo_size'] : 'small', 'large'); ?>>
                            <?php esc_html_e('Large Photos (Hero Layout)', 'manual-review-manager'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Small: Compact horizontal layout with small profile photos. Large: Vertical layout with large photos filling the review container width.', 'manual-review-manager'); ?>
                    </p>
                </td>
            </tr>
            
            
            <tr>
                <th scope="row"><?php esc_html_e('Show Review Dates', 'manual-review-manager'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="mrm_display_settings[show_dates]" value="1" <?php checked($display_settings['show_dates'], 1); ?> />
                        <?php esc_html_e('Display review dates (shown as "2 months ago")', 'manual-review-manager'); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Show Platform Badges', 'manual-review-manager'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="mrm_display_settings[show_platform]" value="1" <?php checked($display_settings['show_platform'], 1); ?> />
                        <?php esc_html_e('Show source badges (Google, Yelp, Facebook, etc.) on reviews', 'manual-review-manager'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Displays colored badges showing where each review originally came from. Helps build trust by showing review sources.', 'manual-review-manager'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Color Theme', 'manual-review-manager'); ?></th>
                <td>
                    <select name="mrm_display_settings[color_theme]">
                        <option value="light" <?php selected(isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light', 'light'); ?>>
                            <?php esc_html_e('Light Theme', 'manual-review-manager'); ?>
                        </option>
                        <option value="dark" <?php selected(isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light', 'dark'); ?>>
                            <?php esc_html_e('Dark Theme', 'manual-review-manager'); ?>
                        </option>
                        <option value="auto" <?php selected(isset($display_settings['color_theme']) ? $display_settings['color_theme'] : 'light', 'auto'); ?>>
                            <?php esc_html_e('Auto (System Preference)', 'manual-review-manager'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Choose the color theme for your reviews. Dark theme works perfectly for dark websites, while auto detects the user\'s system preference.', 'manual-review-manager'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Maximum Reviews to Display', 'manual-review-manager'); ?></th>
                <td>
                    <input type="number" name="mrm_display_settings[max_reviews]" value="<?php echo esc_attr($display_settings['max_reviews']); ?>" min="1" max="100" class="small-text" />
                    <p class="description"><?php esc_html_e('Default number of reviews to show (can be overridden in shortcodes)', 'manual-review-manager'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Minimum Rating Filter', 'manual-review-manager'); ?></th>
                <td>
                    <select name="mrm_display_settings[min_rating]">
                        <option value="1" <?php selected($display_settings['min_rating'], 1); ?>><?php esc_html_e('Show all ratings', 'manual-review-manager'); ?></option>
                        <option value="2" <?php selected($display_settings['min_rating'], 2); ?>><?php esc_html_e('2+ stars', 'manual-review-manager'); ?></option>
                        <option value="3" <?php selected($display_settings['min_rating'], 3); ?>><?php esc_html_e('3+ stars', 'manual-review-manager'); ?></option>
                        <option value="4" <?php selected($display_settings['min_rating'], 4); ?>><?php esc_html_e('4+ stars', 'manual-review-manager'); ?></option>
                        <option value="5" <?php selected($display_settings['min_rating'], 5); ?>><?php esc_html_e('5 stars only', 'manual-review-manager'); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e('Only show reviews with this rating or higher', 'manual-review-manager'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Button Color Theme', 'manual-review-manager'); ?></th>
                <td>
                    <select name="mrm_display_settings[button_color]">
                        <option value="blue" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'blue'); ?>>
                            <?php esc_html_e('Blue (Default)', 'manual-review-manager'); ?>
                        </option>
                        <option value="black" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'black'); ?>>
                            <?php esc_html_e('Black', 'manual-review-manager'); ?>
                        </option>
                        <option value="red" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'red'); ?>>
                            <?php esc_html_e('Red', 'manual-review-manager'); ?>
                        </option>
                        <option value="green" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'green'); ?>>
                            <?php esc_html_e('Green', 'manual-review-manager'); ?>
                        </option>
                        <option value="purple" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'purple'); ?>>
                            <?php esc_html_e('Purple', 'manual-review-manager'); ?>
                        </option>
                        <option value="orange" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'orange'); ?>>
                            <?php esc_html_e('Orange', 'manual-review-manager'); ?>
                        </option>
                        <option value="grey" <?php selected(isset($display_settings['button_color']) ? $display_settings['button_color'] : 'blue', 'grey'); ?>>
                            <?php esc_html_e('Grey', 'manual-review-manager'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Choose the color theme for "Leave Your Own Review", "Read More", and other action buttons.', 'manual-review-manager'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php esc_html_e('Review Submission Redirect URL', 'manual-review-manager'); ?></th>
                <td>
                    <input type="url" 
                           name="mrm_display_settings[redirect_after_review]" 
                           value="<?php echo esc_attr(isset($display_settings['redirect_after_review']) ? $display_settings['redirect_after_review'] : home_url()); ?>" 
                           class="regular-text" 
                           placeholder="<?php echo esc_attr(home_url()); ?>" />
                    <p class="description">
                        <?php esc_html_e('URL to redirect users to after successfully submitting a review. Leave empty or use default to redirect to homepage.', 'manual-review-manager'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <hr />
    
    <h2><?php esc_html_e('Theme Preview', 'manual-review-manager'); ?></h2>
    <div class="mrm-theme-preview">
        <h3><?php esc_html_e('Light Theme', 'manual-review-manager'); ?></h3>
        <div class="mrm-preview-container">
            <div class="mrm-review-item" style="background: #ffffff; border: 1px solid #dddddd; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                <div class="mrm-reviewer-name" style="color: #333333; font-weight: 600; margin-bottom: 8px;">John Smith</div>
                <div class="mrm-rating" style="color: #ffa500; margin-bottom: 10px;">★★★★★</div>
                <div class="mrm-review-text" style="color: #333333; line-height: 1.6;">Great service and friendly staff. Highly recommended!</div>
            </div>
        </div>
        
        <h3><?php esc_html_e('Dark Theme', 'manual-review-manager'); ?></h3>
        <div class="mrm-preview-container">
            <div class="mrm-review-item" style="background: #1a1a1a; border: 1px solid #404040; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                <div class="mrm-reviewer-name" style="color: #e0e0e0; font-weight: 600; margin-bottom: 8px;">Sarah Johnson</div>
                <div class="mrm-rating" style="color: #fbbf24; margin-bottom: 10px;">★★★★★</div>
                <div class="mrm-review-text" style="color: #e0e0e0; line-height: 1.6;">Excellent experience! Perfect for dark-themed websites.</div>
            </div>
        </div>
    </div>
    
    <hr />
    
    <h2><?php esc_html_e('Shortcode Examples', 'manual-review-manager'); ?></h2>
    <div class="mrm-shortcode-examples">
        <h3><?php esc_html_e('Basic Display', 'manual-review-manager'); ?></h3>
        <code>[review_manager]</code>
        <p><?php esc_html_e('Shows reviews using default settings', 'manual-review-manager'); ?></p>
        
        <h3><?php esc_html_e('Grid Layout', 'manual-review-manager'); ?></h3>
        <code>[review_manager layout="grid" columns="3" max_reviews="6"]</code>
        <p><?php esc_html_e('Shows 6 reviews in a 3-column grid', 'manual-review-manager'); ?></p>
        
        <h3><?php esc_html_e('Review Slider', 'manual-review-manager'); ?></h3>
        <code>[review_slider autoplay="true" autoplay_speed="5000"]</code>
        <p><?php esc_html_e('Shows reviews in a slider that auto-advances every 5 seconds', 'manual-review-manager'); ?></p>
        
        <h3><?php esc_html_e('Review Statistics', 'manual-review-manager'); ?></h3>
        <code>[review_stats]</code>
        <p><?php esc_html_e('Shows total reviews, average rating, and rating breakdown', 'manual-review-manager'); ?></p>
        
        <h3><?php esc_html_e('Large Photo Layout', 'manual-review-manager'); ?></h3>
        <code>[review_manager photo_size="large" layout="grid" columns="2"]</code>
        <p><?php esc_html_e('Shows reviews with large hero-style photos in a vertical layout', 'manual-review-manager'); ?></p>
        
        <h3><?php esc_html_e('Dark Theme Override', 'manual-review-manager'); ?></h3>
        <code>[review_manager theme="dark" layout="grid" columns="2"]</code>
        <p><?php esc_html_e('Forces dark theme regardless of global setting - perfect for specific sections', 'manual-review-manager'); ?></p>
        
        <h3><?php esc_html_e('All Parameters', 'manual-review-manager'); ?></h3>
        <code>[review_manager layout="grid" columns="3" max_reviews="9" theme="auto" show_photos="true"]</code>
        <p><?php esc_html_e('All available theme options: light, dark, auto (auto detects user system preference)', 'manual-review-manager'); ?></p>
    </div>
</div>

