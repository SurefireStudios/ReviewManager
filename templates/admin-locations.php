<?php
/**
 * Review Manager Locations Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>
        <?php _e('Manage Locations', 'manual-review-manager'); ?>
        <a href="#" class="page-title-action" id="add-location-btn"><?php _e('Add New Location', 'manual-review-manager'); ?></a>
    </h1>
    
    <?php if (!empty($locations)): ?>
        <div class="mrm-locations-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col"><?php _e('Name', 'manual-review-manager'); ?></th>
                        <th scope="col"><?php _e('Address', 'manual-review-manager'); ?></th>
                        <th scope="col"><?php _e('Phone', 'manual-review-manager'); ?></th>
                        <th scope="col"><?php _e('Reviews', 'manual-review-manager'); ?></th>
                        <th scope="col"><?php _e('Actions', 'manual-review-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $location): ?>
                        <?php
                        $review_count = MRM_Database::get_reviews(array(
                            'location_id' => $location->id,
                            'max_reviews' => 9999,
                            'approved_only' => false
                        ));
                        $count = count($review_count);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($location->name); ?></strong>
                            </td>
                            <td><?php echo esc_html($location->address); ?></td>
                            <td><?php echo esc_html($location->phone); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=mrm-reviews&location=' . $location->id); ?>">
                                    <?php printf(_n('%d review', '%d reviews', $count, 'manual-review-manager'), $count); ?>
                                </a>
                            </td>
                            <td>
                                <button class="button button-small edit-location-btn" 
                                        data-location-id="<?php echo $location->id; ?>"
                                        data-name="<?php echo esc_attr($location->name); ?>"
                                        data-address="<?php echo esc_attr($location->address); ?>"
                                        data-phone="<?php echo esc_attr($location->phone); ?>"
                                        data-website="<?php echo esc_attr($location->website); ?>"
                                        data-description="<?php echo esc_attr($location->description); ?>">
                                    <?php _e('Edit', 'manual-review-manager'); ?>
                                </button>
                                <button class="button button-small button-link-delete delete-location-btn" 
                                        data-location-id="<?php echo $location->id; ?>">
                                    <?php _e('Delete', 'manual-review-manager'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="mrm-empty-state">
            <h2><?php _e('No locations found', 'manual-review-manager'); ?></h2>
            <p><?php _e('Add your first location to start managing reviews.', 'manual-review-manager'); ?></p>
            <button class="button button-primary" id="add-first-location-btn">
                <?php _e('Add Your First Location', 'manual-review-manager'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Location Modal -->
<div id="location-modal" class="mrm-modal" style="display: none;">
    <div class="mrm-modal-content">
        <div class="mrm-modal-header">
            <h2 id="modal-title"><?php _e('Add New Location', 'manual-review-manager'); ?></h2>
            <button class="mrm-modal-close">&times;</button>
        </div>
        
        <form id="location-form">
            <input type="hidden" id="location-id" name="location_id" value="" />
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="location-name"><?php _e('Location Name', 'manual-review-manager'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="location-name" name="name" class="regular-text" required />
                        <p class="description"><?php _e('e.g., "Dragon Mu Sool Reviews"', 'manual-review-manager'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="location-address"><?php _e('Address', 'manual-review-manager'); ?></label>
                    </th>
                    <td>
                        <textarea id="location-address" name="address" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="location-phone"><?php _e('Phone', 'manual-review-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="location-phone" name="phone" class="regular-text" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="location-website"><?php _e('Website', 'manual-review-manager'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="location-website" name="website" class="regular-text" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="location-description"><?php _e('Description', 'manual-review-manager'); ?></label>
                    </th>
                    <td>
                        <textarea id="location-description" name="description" class="large-text" rows="4"></textarea>
                        <p class="description"><?php _e('Optional description for your records.', 'manual-review-manager'); ?></p>
                    </td>
                </tr>
            </table>
            
            <div class="mrm-modal-footer">
                <button type="submit" class="button button-primary">
                    <span id="save-btn-text"><?php _e('Save Location', 'manual-review-manager'); ?></span>
                </button>
                <button type="button" class="button mrm-modal-close">
                    <?php _e('Cancel', 'manual-review-manager'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.mrm-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.mrm-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mrm-modal-content {
    background: #fff;
    border-radius: 4px;
    width: 90%;
    max-width: 600px;
    max-height: 80%;
    overflow-y: auto;
}

.mrm-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #ddd;
}

.mrm-modal-header h2 {
    margin: 0;
}

.mrm-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.mrm-modal-close:hover {
    color: #000;
}

.mrm-modal form {
    padding: 20px;
}

.mrm-modal-footer {
    padding: 20px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.mrm-modal-footer .button {
    margin-left: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Open modal for new location
    $('#add-location-btn, #add-first-location-btn').on('click', function(e) {
        e.preventDefault();
        openLocationModal();
    });
    
    // Open modal for editing location
    $('.edit-location-btn').on('click', function() {
        const data = $(this).data();
        openLocationModal(data);
    });
    
    // Close modal
    $('.mrm-modal-close').on('click', function() {
        closeLocationModal();
    });
    
    // Close modal on background click
    $('#location-modal').on('click', function(e) {
        if (e.target === this) {
            closeLocationModal();
        }
    });
    
    function openLocationModal(data = null) {
        if (data) {
            // Edit mode
            $('#modal-title').text('<?php _e('Edit Location', 'manual-review-manager'); ?>');
            $('#location-id').val(data.locationId);
            $('#location-name').val(data.name);
            $('#location-address').val(data.address);
            $('#location-phone').val(data.phone);
            $('#location-website').val(data.website);
            $('#location-description').val(data.description);
            $('#save-btn-text').text('<?php _e('Update Location', 'manual-review-manager'); ?>');
        } else {
            // Add mode
            $('#modal-title').text('<?php _e('Add New Location', 'manual-review-manager'); ?>');
            $('#location-form')[0].reset();
            $('#location-id').val('');
            $('#save-btn-text').text('<?php _e('Save Location', 'manual-review-manager'); ?>');
        }
        
        $('#location-modal').show();
    }
    
    function closeLocationModal() {
        $('#location-modal').hide();
        $('#location-form')[0].reset();
    }
    
    // Save location
    $('#location-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'mrm_save_location');
        formData.append('nonce', mrm_ajax.nonce);
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $('#save-btn-text').text();
        $('#save-btn-text').text('<?php _e('Saving...', 'manual-review-manager'); ?>');
        $submitBtn.prop('disabled', true);
        
        $.ajax({
            url: mrm_ajax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    location.reload();
                } else {
                    alert('<?php _e('Error: ', 'manual-review-manager'); ?>' + (response.data || '<?php _e('Unknown error occurred.', 'manual-review-manager'); ?>'));
                }
            },
            error: function() {
                alert('<?php _e('Network error. Please try again.', 'manual-review-manager'); ?>');
            },
            complete: function() {
                $('#save-btn-text').text(originalText);
                $submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Delete location
    $('.delete-location-btn').on('click', function() {
        if (!confirm('<?php _e('Are you sure you want to delete this location? This will also delete all associated reviews.', 'manual-review-manager'); ?>')) {
            return;
        }
        
        const locationId = $(this).data('location-id');
        
        $.post(mrm_ajax.ajaxurl, {
            action: 'mrm_delete_location',
            location_id: locationId,
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