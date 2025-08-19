<?php
/**
 * Admin Reviews Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$location_filter = isset($_GET['location']) ? intval($_GET['location']) : 0;
$platform_filter = isset($_GET['platform']) ? sanitize_text_field($_GET['platform']) : '';
$search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Build filter args
$filter_args = array(
    'max_reviews' => 9999,
    'approved_only' => false
);

if ($location_filter) {
    $filter_args['location_id'] = $location_filter;
}

$reviews = MRM_Database::get_reviews($filter_args);

// Apply additional filters
if ($platform_filter) {
    $reviews = array_filter($reviews, function($review) use ($platform_filter) {
        return $review->platform === $platform_filter;
    });
}

if ($search_term) {
    $reviews = array_filter($reviews, function($review) use ($search_term) {
        return stripos($review->reviewer_name, $search_term) !== false ||
               stripos($review->review_text, $search_term) !== false;
    });
}
?>

<div class="wrap">
    <h1>
        <?php _e('Manage Reviews', 'manual-review-manager'); ?>
        <a href="<?php echo admin_url('admin.php?page=mrm-add-review'); ?>" class="page-title-action">
            <?php _e('Add New Review', 'manual-review-manager'); ?>
        </a>
    </h1>
    
    <!-- Filters -->
    <div class="tablenav top">
        <form method="get" action="">
            <input type="hidden" name="page" value="mrm-reviews" />
            
            <select name="location">
                <option value=""><?php _e('All Locations', 'manual-review-manager'); ?></option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo $location->id; ?>" <?php selected($location_filter, $location->id); ?>>
                        <?php echo esc_html($location->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="platform">
                <option value=""><?php _e('All Platforms', 'manual-review-manager'); ?></option>
                <option value="google" <?php selected($platform_filter, 'google'); ?>><?php _e('Google', 'manual-review-manager'); ?></option>
                <option value="yelp" <?php selected($platform_filter, 'yelp'); ?>><?php _e('Yelp', 'manual-review-manager'); ?></option>
                <option value="manual" <?php selected($platform_filter, 'manual'); ?>><?php _e('Manual', 'manual-review-manager'); ?></option>
            </select>
            
            <input type="search" name="search" value="<?php echo esc_attr($search_term); ?>" placeholder="<?php _e('Search reviews...', 'manual-review-manager'); ?>" />
            
            <button type="submit" class="button"><?php _e('Filter', 'manual-review-manager'); ?></button>
            
            <?php if ($location_filter || $platform_filter || $search_term): ?>
                <a href="<?php echo admin_url('admin.php?page=mrm-reviews'); ?>" class="button">
                    <?php _e('Clear Filters', 'manual-review-manager'); ?>
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if (!empty($reviews)): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" style="width: 200px;"><?php _e('Reviewer', 'manual-review-manager'); ?></th>
                    <th scope="col" style="width: 80px;"><?php _e('Rating', 'manual-review-manager'); ?></th>
                    <th scope="col"><?php _e('Review Text', 'manual-review-manager'); ?></th>
                    <th scope="col" style="width: 120px;"><?php _e('Date', 'manual-review-manager'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Platform', 'manual-review-manager'); ?></th>
                    <th scope="col" style="width: 80px;"><?php _e('Status', 'manual-review-manager'); ?></th>
                    <th scope="col" style="width: 120px;"><?php _e('Actions', 'manual-review-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if (!empty($review->reviewer_photo_url)): ?>
                                    <img src="<?php echo esc_url($review->reviewer_photo_url); ?>" 
                                         alt="<?php echo esc_attr($review->reviewer_name); ?>" 
                                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" />
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo esc_html($review->reviewer_name); ?></strong>
                                    <?php if (!empty($review->location_name)): ?>
                                        <br><small><?php echo esc_html($review->location_name); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="color: #ffa500;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php echo $i <= $review->rating ? '★' : '☆'; ?>
                                <?php endfor; ?>
                            </div>
                            <small>(<?php echo number_format($review->rating, 1); ?>)</small>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                <?php echo esc_html(wp_trim_words($review->review_text, 20)); ?>
                            </div>
                        </td>
                        <td>
                            <?php echo date_i18n('M j, Y', strtotime($review->review_date)); ?>
                        </td>
                        <td>
                            <span class="mrm-platform-admin mrm-platform-admin-<?php echo esc_attr($review->platform); ?>">
                                <?php echo $this->get_platform_svg_admin($review->platform); ?>
                                <span class="mrm-platform-name"><?php echo ucfirst($review->platform); ?></span>
                            </span>
                        </td>
                        <td>
                            <?php if ($review->is_approved): ?>
                                <span style="color: #46b450;">✓ <?php _e('Approved', 'manual-review-manager'); ?></span>
                            <?php else: ?>
                                <span style="color: #dc3232;">✗ <?php _e('Pending', 'manual-review-manager'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=mrm-add-review&edit=' . $review->id); ?>" 
                               class="button button-small">
                                <?php _e('Edit', 'manual-review-manager'); ?>
                            </a>
                            <button class="button button-small button-link-delete delete-review-btn" 
                                    data-review-id="<?php echo $review->id; ?>">
                                <?php _e('Delete', 'manual-review-manager'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="mrm-empty-state">
            <h2><?php _e('No reviews found', 'manual-review-manager'); ?></h2>
            <p><?php _e('Try adjusting your filters or add your first review.', 'manual-review-manager'); ?></p>
            <a href="<?php echo admin_url('admin.php?page=mrm-add-review'); ?>" class="button button-primary">
                <?php _e('Add Your First Review', 'manual-review-manager'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.mrm-platform {
    background: #f0f0f0;
    color: #666;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
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
</style>

<script>
jQuery(document).ready(function($) {
    $('.delete-review-btn').on('click', function() {
        if (!confirm('<?php _e('Are you sure you want to delete this review? This action cannot be undone.', 'manual-review-manager'); ?>')) {
            return;
        }
        
        const reviewId = $(this).data('review-id');
        
        $.post(mrm_ajax.ajaxurl, {
            action: 'mrm_delete_review',
            review_id: reviewId,
            nonce: mrm_ajax.nonce
        })
        .done(function(response) {
            if (response.success) {
                alert(response.data);
                location.reload();
            } else {
                alert('<?php _e('Error: ', 'manual-review-manager'); ?>' + (response.data || '<?php _e('Unknown error occurred.', 'manual-review-manager'); ?>'));
            }
        })
        .fail(function() {
            alert('<?php _e('Network error. Please try again.', 'manual-review-manager'); ?>');
        });
    });
});
</script> 