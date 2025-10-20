/* Stammbaum Manager - Frontend JavaScript */
jQuery(document).ready(function($) {
    console.log('Stammbaum Manager Frontend loaded');
    
    // Pedigree display
    $('.stammbaum-toggle').on('click', function() {
        $(this).next('.stammbaum-details').slideToggle();
    });
    
    // Puppy card interactions
    $('.welpe-card').on('click', function() {
        var puppyId = $(this).data('puppy-id');
        if (puppyId) {
            window.location.href = $(this).data('puppy-url');
        }
    });
});
