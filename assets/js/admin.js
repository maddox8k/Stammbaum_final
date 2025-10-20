/* Stammbaum Manager - Admin JavaScript */
jQuery(document).ready(function($) {
    console.log('Stammbaum Manager Admin loaded');
    
    // Image upload handler
    if (typeof wp !== 'undefined' && wp.media) {
        $('.upload-image-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var imageField = button.siblings('input[type="hidden"]');
            
            var frame = wp.media({
                title: stammbaumManagerAdmin.strings.select_image,
                button: { text: stammbaumManagerAdmin.strings.use_image },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                imageField.val(attachment.url);
                button.siblings('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 200px;">');
            });
            
            frame.open();
        });
    }
});
