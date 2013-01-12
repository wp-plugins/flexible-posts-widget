/*
 * Flexible Posts Widget
 * Admin Scripts
 * Author: dpe415
 * URI: http://wordpress.org/extend/plugins/flexible-posts-widget/
 */

jQuery(document).ready(function($) {
	
	// Setup the show/hide thumbnails box
	jQuery('input.dpe-fp-thumbnail').each( function() {
		if( this.checked ) {
			jQuery(this).parent().next().slideDown('fast');
		} else {
			jQuery(this).parent().next().slideUp('fast');
		}
	});
	
	
});

// Add event triggers to the show/hide thumbnails box
jQuery(document).on("change", 'input.dpe-fp-thumbnail', function(event) {
	if( this.checked ) {
		jQuery(this).parent().next().slideDown('fast');
	} else {
		jQuery(this).parent().next().slideUp('fast');
	}
});

// Setup the get_terms callback
jQuery(document).on("change", 'select.dpe-fp-taxonomy', function(event) {
	
	var terms_p = jQuery(this).parent().next('p');
	
	// If we're not ignoring Taxonomy & Term...
	if( jQuery(this).val() != 'none' ) {
	
		var terms_select = terms_p.find('select');
		var terms_first_opt = terms_select.children(":first");
		
		terms_first_opt.text('Getting terms...');
		terms_select.attr('disabled', true);
		
		var data = {
			action:		'dpe_fp_get_terms',
			taxonomy:	jQuery(this).val(),
			term:		terms_select.val(),
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			terms_select.html(response);
			terms_p.slideDown();
			terms_select.attr('disabled', false);
		}).error( function() {
			terms_first_opt.text('No terms found...');		
		});
	
	} else {
		terms_p.slideUp();
	}
	
});

// Setup the "Show me everything" warnings
jQuery(document).on("change", 'select.dpe-fp-taxonomy', function(event) {
	if( 'none' == jQuery(this).val() && 'all' == jQuery(this).closest('.getemby').find('.dpe-fp-pt').val() ) {
		jQuery(this).closest('.getemby').find('.warning').slideDown();
	} else {
		jQuery(this).closest('.getemby').find('.warning').slideUp();
	}
});
jQuery(document).on("change", 'select.dpe-fp-pt', function(event) {
	if( 'all' == jQuery(this).val() && 'none' == jQuery(this).closest('.getemby').find('.dpe-fp-taxonomy').val() ) {
		jQuery(this).closest('.getemby').find('.warning').slideDown();
	} else {
		jQuery(this).closest('.getemby').find('.warning').slideUp();
	}
});