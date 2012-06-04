/*
 * Flexible Posts Widget
 * Admin Scripts
 * Author: dpe415
 * URI: http://wordpress.org/extend/plugins/flexible-posts-widget/
 */

jQuery(document).ready(function($) {
	
	// Setup the get posts by select box
	jQuery('select.dpe-fp-getemby').each( function() {
		var thisone = 'div.' + jQuery(this).val();
		jQuery(this).parent().nextAll('.getembies').hide();
		jQuery(this).parent().nextAll(thisone).show();
	});
	
	// Setup the show/hide thumbnails box
	jQuery('input.dpe-fp-thumbnail').each( function() {
		if( this.checked ) {
			jQuery(this).parent().next().slideDown('fast');
		} else {
			jQuery(this).parent().next().slideUp('fast');
		}
	});
	
});


// Add event triggers to the get posts by box
jQuery(document).on("change", 'select.dpe-fp-getemby', function(event) {
	var thisone = 'div.' + jQuery(this).val();
	jQuery(this).parent().nextAll('.getembies').hide();
	jQuery(this).parent().nextAll(thisone).slideDown('fast');
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
	
	var terms_select = jQuery(this).parent().next('p').find('select');
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
		terms_select.attr('disabled', false);
	}).error( function() {
		terms_first_opt.text('No terms found...');		
	});
	
	
	
});