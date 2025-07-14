(function($) {
    'use strict';

    $(document).ready(function() {
        // Create key active form submission
        $('#create-key-active-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $spinner = $form.find('.spinner');
            const $submitButton = $form.find('input[type="submit"]');
            
            // Show spinner and disable button
            $spinner.addClass('is-active');
            $submitButton.prop('disabled', true);
            
            // Collect form data
            const formData = new FormData();
            formData.append('action', 'create_key_active_post');
            
            // Add all form fields to formData
            const formFields = $form.serializeArray();
            for (let i = 0; i < formFields.length; i++) {
                formData.append(formFields[i].name, formFields[i].value);
            }
            
            // Add checkbox status if unchecked
            if (!$('#status').is(':checked')) {
                formData.append('status', 'off');
            }

            // Send AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        alert('Mã kích hoạt đã được tạo thành công!');
                        // Reload page to show the new key
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra khi tạo mã kích hoạt.');
                    }
                    
                    // Hide spinner and enable button
                    $spinner.removeClass('is-active');
                    $submitButton.prop('disabled', false);
                },
                error: function() {
                    alert('Có lỗi xảy ra khi tạo mã kích hoạt.');
                    
                    // Hide spinner and enable button
                    $spinner.removeClass('is-active');
                    $submitButton.prop('disabled', false);
                }
            });
        });
        
        // Delete post
        $('.delete-post').on('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa mã kích hoạt này không?')) {
                const postId = $(this).data('post-id');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'delete_post',
                        post_id: postId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Mã kích hoạt đã được xóa thành công!');
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra khi xóa mã kích hoạt.');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi xóa mã kích hoạt.');
                    }
                });
            }
        });
    });

})(jQuery);
