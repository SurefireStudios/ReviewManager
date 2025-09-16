/**
 * Review Form JavaScript
 */
jQuery(document).ready(function($) {
    
    // Star rating functionality
    $('.mrm-star-rating input').on('change', function() {
        const rating = $(this).val();
        const texts = {
            '1': 'Poor',
            '2': 'Fair', 
            '3': 'Good',
            '4': 'Very Good',
            '5': 'Excellent'
        };
        $('.mrm-rating-text').text(texts[rating] || '');
    });
    
    // Photo upload preview
    $('#reviewer_photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > mrm_review_ajax.max_file_size) {
                alert('File size too large. Maximum size is ' + Math.round(mrm_review_ajax.max_file_size / 1024 / 1024) + 'MB');
                $(this).val('');
                return;
            }
            
            if (mrm_review_ajax.allowed_types.indexOf(file.type) === -1) {
                alert('Invalid file type. Please upload a JPG, PNG, or GIF image.');
                $(this).val('');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.mrm-photo-preview img').attr('src', e.target.result);
                $('.mrm-photo-preview').show();
                $('.mrm-upload-instructions').hide();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove photo
    $('.mrm-remove-photo').on('click', function() {
        $('#reviewer_photo').val('');
        $('.mrm-photo-preview').hide();
        $('.mrm-upload-instructions').show();
    });
    
    // Form submission
    $('#mrm-review-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('.mrm-submit-btn');
        const formData = new FormData(this);
        
        // Add AJAX data
        formData.append('action', 'mrm_submit_review');
        formData.append('nonce', mrm_review_ajax.nonce);
        
        // Disable form and show loading
        form.addClass('mrm-form-loading');
        submitBtn.prop('disabled', true);
        
        // Clear previous errors
        $('.mrm-error-message').remove();
        
        $.ajax({
            url: mrm_review_ajax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Redirect to success page instead of showing AJAX message
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('submitted', 'success');
                    window.location.href = currentUrl.toString();
                    
                } else {
                    // Show error message
                    const errorHtml = '<div class="mrm-error-message">' + response.message + '</div>';
                    form.before(errorHtml);
                    
                    // Scroll to error message
                    $('html, body').animate({
                        scrollTop: $('.mrm-error-message').offset().top - 50
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                const errorHtml = '<div class="mrm-error-message">An error occurred. Please try again.</div>';
                form.before(errorHtml);
                
                console.error('Review submission error:', error);
            },
            complete: function() {
                // Re-enable form
                form.removeClass('mrm-form-loading');
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const form = $('#mrm-review-form');
        
        // Clear previous validation
        $('.mrm-form-group').removeClass('has-error');
        
        // Required fields
        const requiredFields = ['reviewer_name', 'reviewer_email', 'location_id', 'rating', 'review_text'];
        
        requiredFields.forEach(function(fieldName) {
            const field = $('[name="' + fieldName + '"]');
            const value = field.val();
            
            if (!value || (fieldName === 'rating' && !$('input[name="rating"]:checked').length)) {
                field.closest('.mrm-form-group').addClass('has-error');
                isValid = false;
            }
        });
        
        // Email validation
        const email = $('[name="reviewer_email"]').val();
        if (email && !isValidEmail(email)) {
            $('[name="reviewer_email"]').closest('.mrm-form-group').addClass('has-error');
            isValid = false;
        }
        
        return isValid;
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Real-time validation
    $('#mrm-review-form input, #mrm-review-form select, #mrm-review-form textarea').on('blur change', function() {
        const field = $(this);
        const group = field.closest('.mrm-form-group');
        
        if (field.val()) {
            group.removeClass('has-error');
        }
        
        // Special validation for email
        if (field.attr('type') === 'email') {
            if (field.val() && !isValidEmail(field.val())) {
                group.addClass('has-error');
            } else {
                group.removeClass('has-error');
            }
        }
    });
    
    // Character counter for review text
    const reviewTextarea = $('#review_text');
    if (reviewTextarea.length) {
        const maxLength = 1000; // Set a reasonable limit
        const counterHtml = '<div class="mrm-char-counter"><span class="mrm-char-count">0</span>/' + maxLength + ' characters</div>';
        reviewTextarea.after(counterHtml);
        
        reviewTextarea.on('input', function() {
            const length = $(this).val().length;
            $('.mrm-char-count').text(length);
            
            if (length > maxLength) {
                $('.mrm-char-counter').addClass('over-limit');
            } else {
                $('.mrm-char-counter').removeClass('over-limit');
            }
        });
    }
    
    // Auto-save draft functionality (optional enhancement)
    let draftTimer;
    $('#mrm-review-form input, #mrm-review-form select, #mrm-review-form textarea').on('input change', function() {
        clearTimeout(draftTimer);
        draftTimer = setTimeout(saveDraft, 2000); // Save after 2 seconds of inactivity
    });
    
    function saveDraft() {
        const formData = $('#mrm-review-form').serialize();
        localStorage.setItem('mrm_review_draft', formData);
    }
    
    function loadDraft() {
        const draft = localStorage.getItem('mrm_review_draft');
        if (draft) {
            const draftData = new URLSearchParams(draft);
            draftData.forEach(function(value, key) {
                const field = $('[name="' + key + '"]');
                if (field.attr('type') === 'radio') {
                    $('[name="' + key + '"][value="' + value + '"]').prop('checked', true).trigger('change');
                } else {
                    field.val(value);
                }
            });
        }
    }
    
    // Load draft on page load
    loadDraft();
    
    // Clear draft on successful submission
    $(document).on('mrm-review-submitted', function() {
        localStorage.removeItem('mrm_review_draft');
    });
});
