<?php
/**
 * Manual Review Manager Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Manual Review Manager Dashboard', 'manual-review-manager'); ?></h1>
    
    <div class="mrm-dashboard-stats">
        <div class="mrm-stat-box">
            <h3><?php _e('Total Reviews', 'manual-review-manager'); ?></h3>
            <div class="mrm-stat-number"><?php echo number_format($stats->total_reviews ?? 0); ?></div>
        </div>
        
        <div class="mrm-stat-box">
            <h3><?php _e('Average Rating', 'manual-review-manager'); ?></h3>
            <div class="mrm-stat-number"><?php echo number_format($stats->average_rating ?? 0, 1); ?> ★</div>
        </div>
        
        <div class="mrm-stat-box">
            <h3><?php _e('Total Locations', 'manual-review-manager'); ?></h3>
            <div class="mrm-stat-number"><?php echo count($locations); ?></div>
        </div>
        
        <div class="mrm-stat-box">
            <h3><?php _e('5-Star Reviews', 'manual-review-manager'); ?></h3>
            <div class="mrm-stat-number"><?php echo number_format($stats->five_star ?? 0); ?></div>
        </div>
    </div>
    
    <div class="mrm-dashboard-content">
        <!-- Quick Actions -->
        <div class="mrm-dashboard-section">
            <h2><?php _e('Quick Actions', 'manual-review-manager'); ?></h2>
            <div class="mrm-quick-actions">
                <a href="<?php echo admin_url('admin.php?page=mrm-add-review'); ?>" class="button button-primary">
                    <?php _e('Add New Review', 'manual-review-manager'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=mrm-locations'); ?>" class="button">
                    <?php _e('Manage Locations', 'manual-review-manager'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=mrm-reviews'); ?>" class="button">
                    <?php _e('View All Reviews', 'manual-review-manager'); ?>
                </a>
            </div>
        </div>
        
        <!-- Bulk Text Replacement -->
        <div class="mrm-dashboard-section">
            <h2><?php _e('Bulk Text Replacement', 'manual-review-manager'); ?></h2>
            <p><?php _e('Replace text across all reviews (e.g., change "Kuk Sool Won" to "Dragon Mu Sool").', 'manual-review-manager'); ?></p>
            <form id="bulk-replace-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Search for', 'manual-review-manager'); ?></th>
                        <td>
                            <input type="text" id="search-text" class="regular-text" placeholder="<?php _e('e.g., Kuk Sool Won', 'manual-review-manager'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Replace with', 'manual-review-manager'); ?></th>
                        <td>
                            <input type="text" id="replace-text" class="regular-text" placeholder="<?php _e('e.g., Dragon Mu Sool', 'manual-review-manager'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Location', 'manual-review-manager'); ?></th>
                        <td>
                            <select id="replace-location">
                                <option value="0"><?php _e('All Locations', 'manual-review-manager'); ?></option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo $location->id; ?>"><?php echo esc_html($location->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Replace Text', 'manual-review-manager'); ?></button>
                </p>
            </form>
        </div>
        
        <!-- Recent Reviews -->
        <div class="mrm-dashboard-section">
            <h2><?php _e('Recent Reviews', 'manual-review-manager'); ?></h2>
            <?php if (!empty($recent_reviews)): ?>
                <div class="mrm-recent-reviews">
                    <?php foreach ($recent_reviews as $review): ?>
                        <div class="mrm-review-item">
                            <div class="mrm-review-header">
                                <strong><?php echo esc_html($review->reviewer_name); ?></strong>
                                <span class="mrm-rating">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $review->rating ? '★' : '☆';
                                    }
                                    ?>
                                </span>
                                <span class="mrm-platform mrm-platform-<?php echo esc_attr($review->platform); ?>">
                                    <?php echo ucfirst($review->platform); ?>
                                </span>
                            </div>
                            <div class="mrm-review-text">
                                <?php echo esc_html(wp_trim_words($review->review_text, 20)); ?>
                            </div>
                            <div class="mrm-review-meta">
                                <span><?php echo esc_html($review->location_name); ?></span> •
                                <span><?php echo date_i18n(get_option('date_format'), strtotime($review->review_date)); ?></span>
                                <?php if ($review->is_edited): ?>
                                    • <span class="mrm-edited"><?php _e('Edited', 'manual-review-manager'); ?></span>
                                <?php endif; ?>
                                <a href="<?php echo admin_url('admin.php?page=mrm-add-review&edit=' . $review->id); ?>" class="mrm-edit-link">
                                    <?php _e('Edit', 'manual-review-manager'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=mrm-reviews'); ?>">
                        <?php _e('View All Reviews', 'manual-review-manager'); ?> →
                    </a>
                </p>
            <?php else: ?>
                <div class="mrm-empty-state">
                    <h3><?php _e('No reviews yet', 'manual-review-manager'); ?></h3>
                    <p><?php _e('Start by adding your first review manually.', 'manual-review-manager'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=mrm-add-review'); ?>" class="button button-primary">
                        <?php _e('Add First Review', 'manual-review-manager'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- How to Display Reviews -->
        <div class="mrm-dashboard-section mrm-shortcode-info">
            <h2><?php _e('📋 Complete Shortcode Reference', 'manual-review-manager'); ?></h2>
            <p class="description"><?php _e('Copy and paste these shortcodes into any page or post to display your reviews.', 'manual-review-manager'); ?></p>
            
            <div class="mrm-shortcode-examples">
                <!-- Basic Review Display -->
                <div class="mrm-shortcode-card">
                    <h3>🔷 <?php _e('Basic Review Display', 'manual-review-manager'); ?></h3>
                    <code>[review_manager]</code>
                    <p><?php _e('Shows all approved reviews in default grid layout.', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Grid Layout -->
                <div class="mrm-shortcode-card">
                    <h3>📱 <?php _e('Grid Layout (2, 3, or 4 columns)', 'manual-review-manager'); ?></h3>
                    <code>[review_manager layout="grid" columns="3" max_reviews="9"]</code>
                    <p><?php _e('3-column grid showing 9 reviews. Options: columns="1|2|3|4"', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- List Layout -->
                <div class="mrm-shortcode-card">
                    <h3>📝 <?php _e('List Layout', 'manual-review-manager'); ?></h3>
                    <code>[review_manager layout="list" max_reviews="5" min_rating="4"]</code>
                    <p><?php _e('Vertical list showing 5 reviews with 4+ stars.', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Review Slider -->
                <div class="mrm-shortcode-card">
                    <h3>🎠 <?php _e('Review Slider/Carousel', 'manual-review-manager'); ?></h3>
                    <code>[review_slider autoplay="true" speed="5000"]</code>
                    <p><?php _e('Auto-rotating carousel, changes every 5 seconds.', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Grid Slider -->
                <div class="mrm-shortcode-card">
                    <h3>🎯 <?php _e('Grid Slider/Carousel', 'manual-review-manager'); ?></h3>
                    <code>[review_grid_slider columns="3" autoplay="true" speed="4000"]</code>
                    <p><?php _e('Shows 3 reviews at once, slides to next set. Perfect for displaying multiple reviews while saving space.', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Platform Filtering -->
                <div class="mrm-shortcode-card">
                    <h3>🟦 <?php _e('Platform-Specific Reviews', 'manual-review-manager'); ?></h3>
                    <code>[review_manager platform="google" max_reviews="6"]</code>
                    <p><?php _e('Show only Google reviews. Options: "google", "yelp", "facebook", "manual"', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Multiple Platforms -->
                <div class="mrm-shortcode-card">
                    <h3>🔴🟦 <?php _e('Multiple Platforms', 'manual-review-manager'); ?></h3>
                    <code>[review_manager platform="google,yelp" columns="2"]</code>
                    <p><?php _e('Show Google and Yelp reviews only.', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Rating Filter -->
                <div class="mrm-shortcode-card">
                    <h3>⭐ <?php _e('High-Rating Reviews Only', 'manual-review-manager'); ?></h3>
                    <code>[review_manager min_rating="5" max_reviews="4"]</code>
                    <p><?php _e('Show only 5-star reviews. Options: min_rating="1|2|3|4|5"', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Review Statistics -->
                <div class="mrm-shortcode-card">
                    <h3>📊 <?php _e('Review Statistics', 'manual-review-manager'); ?></h3>
                    <code>[review_stats show_breakdown="true"]</code>
                    <p><?php _e('Shows total reviews, average rating, and star breakdown.', 'manual-review-manager'); ?></p>
                </div>
                
                <!-- Simple Stats -->
                <div class="mrm-shortcode-card">
                    <h3>📈 <?php _e('Simple Stats', 'manual-review-manager'); ?></h3>
                    <code>[review_stats show_breakdown="false"]</code>
                    <p><?php _e('Shows just total reviews and average rating.', 'manual-review-manager'); ?></p>
                </div>
            </div>
            
            <!-- Advanced Parameters -->
            <div style="margin-top: 30px; background: #f0f8ff; padding: 20px; border-radius: 8px; border-left: 4px solid #0073aa;">
                <h3>🔧 <?php _e('Advanced Parameters', 'manual-review-manager'); ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                    <div>
                        <strong>layout:</strong> "grid", "list", "slider", "grid_slider"<br>
                        <strong>columns:</strong> 1, 2, 3, 4 (grid & grid_slider)<br>
                        <strong>max_reviews:</strong> Any number (default: 10)<br>
                        <strong>min_rating:</strong> 1, 2, 3, 4, 5 (default: 1)
                    </div>
                    <div>
                        <strong>platform:</strong> "google", "yelp", "facebook", "manual", "other"<br>
                        <strong>autoplay:</strong> "true", "false" (slider only)<br>
                        <strong>autoplay_speed:</strong> Milliseconds (default: 5000)<br>
                        <strong>show_breakdown:</strong> "true", "false" (stats only)
                    </div>
                </div>
            </div>
            
            <!-- Quick Copy Examples -->
            <div style="margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 8px;">
                <h4>🚀 <?php _e('Quick Copy Examples', 'manual-review-manager'); ?></h4>
                <div style="display: grid; gap: 10px;">
                    <div style="font-family: monospace; background: white; padding: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="navigator.clipboard.writeText('[review_manager layout=&quot;grid&quot; columns=&quot;3&quot; max_reviews=&quot;9&quot; min_rating=&quot;4&quot;]')" title="Click to copy">
                        [review_manager layout="grid" columns="3" max_reviews="9" min_rating="4"]
                    </div>
                    <div style="font-family: monospace; background: white; padding: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="navigator.clipboard.writeText('[review_slider autoplay=&quot;true&quot; speed=&quot;4000&quot;]')" title="Click to copy">
                        [review_slider autoplay="true" speed="4000"]
                    </div>
                    <div style="font-family: monospace; background: white; padding: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="navigator.clipboard.writeText('[review_grid_slider columns=&quot;3&quot; autoplay=&quot;true&quot; speed=&quot;3000&quot;]')" title="Click to copy">
                        [review_grid_slider columns="3" autoplay="true" speed="3000"]
                    </div>
                    <div style="font-family: monospace; background: white; padding: 8px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="navigator.clipboard.writeText('[review_manager platform=&quot;google,yelp&quot; layout=&quot;list&quot; max_reviews=&quot;5&quot;]')" title="Click to copy">
                        [review_manager platform="google,yelp" layout="list" max_reviews="5"]
                    </div>
                </div>
                <p style="font-size: 12px; color: #666; margin: 10px 0 0 0;">💡 Click any shortcode above to copy it to your clipboard!</p>
            </div>
        </div>
    </div>
</div>

<style>
.mrm-dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.mrm-stat-box {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-align: center;
}

.mrm-stat-box h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #666;
}

.mrm-stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #0073aa;
}

.mrm-dashboard-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
}

.mrm-dashboard-section {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.mrm-dashboard-section h2 {
    margin-top: 0;
}

.mrm-shortcode-info {
    grid-column: 1 / -1;
}

.mrm-quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.mrm-recent-reviews {
    max-height: 400px;
    overflow-y: auto;
}

.mrm-review-item {
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.mrm-review-item:last-child {
    border-bottom: none;
}

.mrm-review-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.mrm-platform {
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.mrm-platform-google {
    background: #4285f4;
    color: white;
}

.mrm-platform-yelp {
    background: #d32323;
    color: white;
}

.mrm-platform-manual {
    background: #0073aa;
    color: white;
}

.mrm-rating {
    color: #ffa500;
}

.mrm-review-text {
    margin: 8px 0;
    color: #666;
}

.mrm-review-meta {
    font-size: 12px;
    color: #999;
    display: flex;
    gap: 10px;
    align-items: center;
}

.mrm-edited {
    color: #0073aa;
    font-style: italic;
}

.mrm-edit-link {
    color: #0073aa;
    text-decoration: none;
}

.mrm-edit-link:hover {
    text-decoration: underline;
}

.mrm-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.mrm-shortcode-examples {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.mrm-shortcode-card {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.mrm-shortcode-card h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.mrm-shortcode-card code {
    display: block;
    background: #fff;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 13px;
    word-break: break-all;
    margin: 10px 0;
}

@media (max-width: 768px) {
    .mrm-dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .mrm-shortcode-examples {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Bulk text replacement
    $('#bulk-replace-form').on('submit', function(e) {
        e.preventDefault();
        
        const searchText = $('#search-text').val().trim();
        const replaceText = $('#replace-text').val().trim();
        const locationId = $('#replace-location').val();
        
        if (!searchText || !replaceText) {
            alert('<?php _e('Please enter both search and replace text.', 'manual-review-manager'); ?>');
            return;
        }
        
        if (!confirm('<?php _e('Are you sure you want to replace "', 'manual-review-manager'); ?>' + searchText + '" with "' + replaceText + '"?')) {
            return;
        }
        
        $.post(ajaxurl, {
            action: 'mrm_bulk_replace_text',
            search_text: searchText,
            replace_text: replaceText,
            location_id: locationId,
            nonce: '<?php echo wp_create_nonce('mrm_nonce'); ?>'
        })
        .done(function(response) {
            if (response.success) {
                alert('<?php _e('Text replaced successfully! Reviews updated: ', 'manual-review-manager'); ?>' + response.data.updated_count);
                location.reload();
            } else {
                alert('<?php _e('Error replacing text: ', 'manual-review-manager'); ?>' + (response.data || '<?php _e('Unknown error', 'manual-review-manager'); ?>'));
            }
        })
        .fail(function() {
            alert('<?php _e('Network error while replacing text.', 'manual-review-manager'); ?>');
        });
    });
});
</script> 