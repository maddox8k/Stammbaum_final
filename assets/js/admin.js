/* Stammbaum Manager - Admin JavaScript */
jQuery(document).ready(function($) {
    console.log('Stammbaum Manager Admin loaded');
    
    // Image upload handler
    if (typeof wp !== 'undefined' && wp.media) {
        $('.upload-image-button').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var container = button.closest('td');
            var imageField = container.find('input[type="text"], input[type="url"], input[type="hidden"]').first();
            var preview = container.find('.image-preview');

            var frame = wp.media({
                title: stammbaumManagerAdmin.strings.select_image,
                button: { text: stammbaumManagerAdmin.strings.use_image },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                if (imageField.length) {
                    imageField.val(attachment.url).trigger('change');
                }
                if (!preview.length && imageField.length) {
                    preview = $('<div class="image-preview" style="margin-top: 10px;"></div>').insertAfter(button);
                }
                if (preview.length) {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; border-radius: 4px;">');
                }
            });

            frame.open();
        });
    }
});
