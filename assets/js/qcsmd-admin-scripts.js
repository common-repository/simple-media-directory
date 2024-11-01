jQuery(function($) {

	$('#qcsmd-sortable-table tbody').sortable({
		axis: 'y',
		handle: '.column-order img',
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		update: function(event, ui) {
			var theOrder = $(this).sortable('toArray');

			var data = {
				action: 'smd_update_post_order',
				postType: $(this).attr('data-post-type'),
				order: theOrder
			};

			$.post(qcsmd_ajaxurl, data);
		}
	}).disableSelection();
});

jQuery(document).ready(function($){
	$('#smd_shortcode_generator_meta').on('click', function(e){
		 $('#smd_shortcode_generator_meta').prop('disabled', true);
		$.post(
			qcsmd_ajaxurl,
			{
				action : 'show_qcsmd_shortcodes',
            	security: qcsmd_ajax_nonce
				
			},
			function(data){
				 $('#smd_shortcode_generator_meta').prop('disabled', false);
				$('#wpwrap').append(data);
			}
		)
	})
})