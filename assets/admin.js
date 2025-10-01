jQuery(document).ready(function($) {
    // Admin JavaScript for Review Manager
    
    // Confirm delete actions
    $('.delete-btn').on('click', function(e) {
        if (!confirm(mrm_ajax.confirm_delete)) {
            e.preventDefault();
            return false;
        }
    });
    
    // Handle AJAX errors
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('AJAX Error:', thrownError);
    });
}); 