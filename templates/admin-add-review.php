<?php
/**
 * Manual Review Entry Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($review);
$page_title = $is_edit ? __('Edit Review', 'manual-review-manager') : __('Add New Review', 'manual-review-manager');
?>

<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    
    <?php if (empty($locations)): ?>
        <div class="notice notice-warning">
            <p>
                <?php _e('You need to add at least one location before you can add reviews.', 'manual-review-manager'); ?>
                <a href="<?php echo admin_url('admin.php?page=mrm-locations'); ?>" class="button">
                    <?php _e('Add Location', 'manual-review-manager'); ?>
                </a>
            </p>
        </div>
    <?php else: ?>
        
        <form id="review-form" method="post" action="">
            <?php wp_nonce_field('mrm_save_review', 'mrm_review_nonce'); ?>
            <input type="hidden" id="review-id" name="review_id" value="<?php echo $is_edit ? $review->id : ''; ?>" />
            
            <div class="mrm-form-container">
                <!-- Left Column - Main Fields -->
                <div class="mrm-form-main">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php _e('Review Details', 'manual-review-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="reviewer-name"><?php _e('Reviewer Name', 'manual-review-manager'); ?> *</label>
                                    </th>
                                    <td>
                                        <input type="text" id="reviewer-name" name="reviewer_name" class="regular-text" 
                                               value="<?php echo $is_edit ? esc_attr($review->reviewer_name) : ''; ?>" required />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="reviewer-email"><?php _e('Reviewer Email', 'manual-review-manager'); ?></label>
                                    </th>
                                    <td>
                                        <input type="email" id="reviewer-email" name="reviewer_email" class="regular-text" 
                                               value="<?php echo $is_edit ? esc_attr($review->reviewer_email) : ''; ?>" />
                                        <p class="description"><?php _e('Optional - for your records only, not displayed publicly.', 'manual-review-manager'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="reviewer-photo"><?php _e('Reviewer Photo URL', 'manual-review-manager'); ?></label>
                                    </th>
                                    <td>
                                        <input type="url" id="reviewer-photo" name="reviewer_photo_url" class="regular-text" 
                                               value="<?php echo $is_edit ? esc_attr($review->reviewer_photo_url) : ''; ?>" />
                                        <button type="button" class="button" id="upload-photo-btn">
                                            <?php _e('Upload Photo', 'manual-review-manager'); ?>
                                        </button>
                                        <p class="description"><?php _e('Optional - provide a URL or upload a photo for the reviewer.', 'manual-review-manager'); ?></p>
                                        
                                        <?php if ($is_edit && !empty($review->reviewer_photo_url)): ?>
                                            <div class="mrm-photo-preview">
                                                <img src="<?php echo esc_url($review->reviewer_photo_url); ?>" 
                                                     alt="<?php echo esc_attr($review->reviewer_name); ?>" 
                                                     style="max-width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" />
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="rating"><?php _e('Rating', 'manual-review-manager'); ?> *</label>
                                    </th>
                                    <td>
                                        <div class="mrm-star-rating" id="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="mrm-star <?php echo ($is_edit && $i <= $review->rating) ? 'active' : ''; ?>" 
                                                      data-rating="<?php echo $i; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" id="rating" name="rating" 
                                               value="<?php echo $is_edit ? $review->rating : '5'; ?>" required />
                                        <p class="description"><?php _e('Click the stars to set the rating.', 'manual-review-manager'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="review-text"><?php _e('Review Text', 'manual-review-manager'); ?> *</label>
                                    </th>
                                    <td>
                                        <textarea id="review-text" name="review_text" rows="8" cols="50" class="large-text" required><?php echo $is_edit ? esc_textarea($review->review_text) : ''; ?></textarea>
                                        <p class="description"><?php _e('The main review content. You can edit this to change business names or other details.', 'manual-review-manager'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="review-date"><?php _e('Review Date', 'manual-review-manager'); ?> *</label>
                                    </th>
                                    <td>
                                        <input type="date" id="review-date" name="review_date" 
                                               value="<?php echo $is_edit ? $review->review_date : date('Y-m-d'); ?>" required />
                                        <p class="description"><?php _e('When was this review originally posted?', 'manual-review-manager'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Settings -->
                <div class="mrm-form-sidebar">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php _e('Review Settings', 'manual-review-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="location-id"><?php _e('Location', 'manual-review-manager'); ?> *</label>
                                    </th>
                                    <td>
                                        <select id="location-id" name="location_id" required>
                                            <option value=""><?php _e('Select a location...', 'manual-review-manager'); ?></option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?php echo $location->id; ?>" 
                                                        <?php echo ($is_edit && $review->location_id == $location->id) ? 'selected' : ''; ?>>
                                                    <?php echo esc_html($location->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="platform"><?php _e('Review Source Platform', 'manual-review-manager'); ?> *</label>
                                    </th>
                                    <td>
                                        <select id="platform" name="platform" style="width: 200px;">
                                            <option value="manual" <?php echo (!$is_edit || $review->platform === 'manual') ? 'selected' : ''; ?>>
                                                <?php _e('📝 Manual Entry', 'manual-review-manager'); ?>
                                            </option>
                                            <option value="google" <?php echo ($is_edit && $review->platform === 'google') ? 'selected' : ''; ?>>
                                                <?php _e('🟦 Google Reviews', 'manual-review-manager'); ?>
                                            </option>
                                            <option value="yelp" <?php echo ($is_edit && $review->platform === 'yelp') ? 'selected' : ''; ?>>
                                                <?php _e('🔴 Yelp Reviews', 'manual-review-manager'); ?>
                                            </option>
                                            <option value="facebook" <?php echo ($is_edit && $review->platform === 'facebook') ? 'selected' : ''; ?>>
                                                <?php _e('🔵 Facebook Reviews', 'manual-review-manager'); ?>
                                            </option>
                                            <option value="other" <?php echo ($is_edit && $review->platform === 'other') ? 'selected' : ''; ?>>
                                                <?php _e('⭐ Other Platform', 'manual-review-manager'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <strong><?php _e('Important:', 'manual-review-manager'); ?></strong> 
                                            <?php _e('Select where this review originally came from. This will display a badge (Google, Yelp, etc.) on your website to show the review source.', 'manual-review-manager'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Status', 'manual-review-manager'); ?></th>
                                    <td>
                                        <fieldset>
                                            <label>
                                                <input type="checkbox" name="is_approved" value="1" 
                                                       <?php echo ($is_edit && $review->is_approved) || !$is_edit ? 'checked' : ''; ?> />
                                                <?php _e('Approved for display', 'manual-review-manager'); ?>
                                            </label><br>
                                            
                                            <label>
                                                <input type="checkbox" name="is_featured" value="1" 
                                                       <?php echo ($is_edit && $review->is_featured) ? 'checked' : ''; ?> />
                                                <?php _e('Featured review', 'manual-review-manager'); ?>
                                            </label>
                                        </fieldset>
                                        <p class="description"><?php _e('Control visibility and prominence of this review.', 'manual-review-manager'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><?php _e('Actions', 'manual-review-manager'); ?></h2>
                        </div>
                        <div class="inside">
                            <div class="mrm-actions">
                                <p class="submit">
                                    <button type="submit" class="button button-primary button-large">
                                        <?php echo $is_edit ? __('Update Review', 'manual-review-manager') : __('Add Review', 'manual-review-manager'); ?>
                                    </button>
                                </p>
                                
                                <?php if ($is_edit): ?>
                                    <p>
                                        <button type="button" class="button button-secondary" id="delete-review-btn" data-review-id="<?php echo $review->id; ?>">
                                            <?php _e('Delete Review', 'manual-review-manager'); ?>
                                        </button>
                                    </p>
                                <?php endif; ?>
                                
                                <p>
                                    <a href="<?php echo admin_url('admin.php?page=mrm-reviews'); ?>" class="button">
                                        <?php _e('Back to Reviews', 'manual-review-manager'); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($is_edit && $review->is_edited): ?>
                        <!-- Original Review Info -->
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2><?php _e('Original Review', 'manual-review-manager'); ?></h2>
                            </div>
                            <div class="inside">
                                <p><strong><?php _e('This review has been edited.', 'manual-review-manager'); ?></strong></p>
                                <p><?php _e('Original text:', 'manual-review-manager'); ?></p>
                                <div class="mrm-original-text">
                                    <?php echo nl2br(esc_html($review->original_review_text)); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        
    <?php endif; ?>
</div>

<style>
.mrm-form-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    margin-top: 20px;
}

.mrm-form-main {
    min-width: 0;
}

.mrm-form-sidebar {
    min-width: 0;
}

.mrm-star-rating {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    user-select: none;
}

.mrm-star-rating .mrm-star {
    transition: color 0.2s ease;
    margin-right: 2px;
}

.mrm-star-rating .mrm-star:hover,
.mrm-star-rating .mrm-star.active {
    color: #ffa500;
}

.mrm-star-rating .mrm-star.hover {
    color: #ffd700;
}

.mrm-photo-preview {
    margin-top: 10px;
}

.mrm-actions {
    text-align: left;
}

.mrm-actions .submit {
    margin-bottom: 10px;
}

.mrm-original-text {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #ddd;
    font-style: italic;
    color: #666;
}

@media (max-width: 768px) {
    .mrm-form-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Star rating functionality
    $('.mrm-star').on('click', function() {
        const rating = $(this).data('rating');
        $('#rating').val(rating);
        
        $('.mrm-star').removeClass('active');
        for (let i = 1; i <= rating; i++) {
            $('.mrm-star[data-rating="' + i + '"]').addClass('active');
        }
    });
    
    // Star rating hover effect
    $('.mrm-star').on('mouseenter', function() {
        const rating = $(this).data('rating');
        
        $('.mrm-star').removeClass('hover');
        for (let i = 1; i <= rating; i++) {
            $('.mrm-star[data-rating="' + i + '"]').addClass('hover');
        }
    });
    
    $('#star-rating').on('mouseleave', function() {
        $('.mrm-star').removeClass('hover');
    });
    
    // Media uploader for photo
    $('#upload-photo-btn').on('click', function(e) {
        e.preventDefault();
        
        const mediaUploader = wp.media({
            title: '<?php _e('Select Reviewer Photo', 'manual-review-manager'); ?>',
            button: {
                text: '<?php _e('Use this photo', 'manual-review-manager'); ?>'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#reviewer-photo').val(attachment.url);
        });
        
        mediaUploader.open();
    });
    
    // Form submission
    $('#review-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'mrm_save_review');
        formData.append('nonce', mrm_ajax.nonce);
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.text('<?php _e('Saving...', 'manual-review-manager'); ?>').prop('disabled', true);
        
        $.ajax({
            url: mrm_ajax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    if (!$('#review-id').val()) {
                        // Redirect to edit page for new reviews
                        window.location.href = '<?php echo admin_url('admin.php?page=mrm-add-review&edit='); ?>' + response.data.review_id;
                    }
                } else {
                    alert('<?php _e('Error: ', 'manual-review-manager'); ?>' + (response.data || '<?php _e('Unknown error occurred.', 'manual-review-manager'); ?>'));
                }
            },
            error: function() {
                alert('<?php _e('Network error. Please try again.', 'manual-review-manager'); ?>');
            },
            complete: function() {
                $submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Delete review
    $('#delete-review-btn').on('click', function() {
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
                window.location.href = '<?php echo admin_url('admin.php?page=mrm-reviews'); ?>';
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