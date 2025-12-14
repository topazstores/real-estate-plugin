jQuery(document).ready(function($) {
	if (jQuery('.rem-fixed-cats').length) {
		jQuery('.rem-fixed-cats').find('.rem-cat-avatar').addClass('image-fill');
		var images_height = jQuery('.rem-fixed-cats').data('imagesheight');
		if (images_height != '') {
			jQuery('.rem-fixed-cats').find('.rem-cat-avatar').css('height', images_height);
		}
	}
	if ($('.masonry-cats').length) {
		// images have loaded
		$('.masonry-cats').imagesLoaded( function() {
			$('.masonry-cats').masonry({
				itemSelector: '.rem-cat'
			});
		});		
	}
	if ($('.rem-categories-wrap').length) {
		$('.rem-categories-wrap .row').slick();
	}
});
jQuery(window).on('load', function() {
	// Apply ImageFill	
	jQuery('.rem-categories-wrap .image-fill').each(function(index, el) {
		jQuery(this).imagefill();
	});
});