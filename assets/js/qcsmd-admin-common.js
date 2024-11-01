jQuery(document).ready(function($){
			
	$('.smd_click_handle').on('click', function(e){
		e.preventDefault();
		var obj = $(this);
		container_id = obj.attr('href');
		$('.smd_click_handle').each(function(){
			$(this).removeClass('nav-tab-active');
			$($(this).attr('href')).hide();
		})
		obj.addClass('nav-tab-active');
		$(container_id).show();
	})
	var hash = window.location.hash;
	if(hash!=''){
		$('.smd_click_handle').each(function(){
			
			$($(this).attr('href')).hide();
			if($(this).attr('href')==hash){
				$(this).removeClass('nav-tab-active').addClass('nav-tab-active');
			}else{
				$(this).removeClass('nav-tab-active');
			}
		})
		$(hash).show();
	}

	jQuery('#menu-posts-smd li:last-child').click(function(){
		var hash = '#help';
		$('.smd_click_handle').each(function(){
			
			$(this).removeClass('nav-tab-active');
			$($(this).attr('href')).hide();
			if($(this).attr('href')==hash){
				$(this).removeClass('nav-tab-active').addClass('nav-tab-active');
			}else{
				$(this).removeClass('nav-tab-active');
			}
		})
		$(hash).show();
	});

	
				

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
	});

	$(document).on( 'click', '.smd_copy_close', function(){
        $(this).parent().parent().parent().parent().parent().remove();
    })
	
    $(document).on( 'click', '.modal-content .close', function(){
        $(this).parent().parent().remove();
    }).on( 'click', '#qcsmd_add_shortcode',function(){
	
      var mode = $('#smd_mode').val();
      var column = $('#smd_column').val();
      var style = $('#smd_style').val();
      var upvote = $('.smd_upvote:checked').val();
      var search = $('.smd_search:checked').val();
	  var embeding = $('.smd_embeding:checked').val();
      var count = $('.smd_item_count:checked').val();
      var orderby = $('#smd_orderby').val();
      var order = $('#smd_order').val();
		var title_font_size = $('#smd_title_font_size').val();
		var subtitle_font_size = $('#smd_subtitle_font_size').val();
		var title_line_height = $('#smd_title_line_height').val();
		var subtitle_line_height = $('#smd_subtitle_line_height').val();
		var smd_itemorderby = $('#smd_itemorderby').val();
	  
	  var listId = $('#smd_list_id').val();
	  var catSlug = $('#smd_list_cat_id').val();
	  
	  var shortcodedata = '[qcsmd-directory';
		  		  
		  if( mode !== 'category' ){
			  shortcodedata +=' mode="'+mode+'"';
		  }
		  
		  if( mode == 'one' && listId != "" ){
			  shortcodedata +=' list_id="'+listId+'"';
		  }
		  
		  
		  if( mode == 'category' && catSlug != "" ){
			  shortcodedata +=' category="'+catSlug+'"';
		  }
		  
		  if( style !== '' ){
			  shortcodedata +=' style="'+style+'"';
		  }
		  if( smd_itemorderby !== '' ){
			  shortcodedata +=' item_orderby="'+smd_itemorderby+'"';
		  }
		  
		  var style = $('#smd_style').val();
		
		  if( style == 'simple' || style == 'style-1' || style == 'style-2' || style == 'style-8' || style == 'style-9' ){
		  
			  if( column !== '' ){
				  shortcodedata +=' column="'+column+'"';
			  }
		  
		  }
		  
		  if( typeof(upvote) != 'undefined' ){
			  shortcodedata +=' upvote="'+upvote+'"';
		  }
		  
		  if( typeof(search)!= 'undefined' ){
			  shortcodedata +=' search="'+search+'"';
		  }
		  if( typeof(embeding)!= 'undefined' ){
			  shortcodedata +=' enable_embedding="'+embeding+'"';
		  }else{
			  shortcodedata +=' enable_embedding="false"';
		  }
		  
		  if( typeof(count)!= 'undefined' ){
			  shortcodedata +=' item_count="'+count+'"';
		  }
		  
		  if( orderby !== '' && mode!=='one'){
			  shortcodedata +=' orderby="'+orderby+'"';
		  }
		  
		  if( order !== '' && mode!=='one'){
			  shortcodedata +=' order="'+order+'"';
		  }

        if(typeof(title_font_size)!='undefined' || title_font_size!=''){
            shortcodedata +=' title_font_size="'+title_font_size+'"';
        }
        if(typeof(subtitle_font_size)!='undefined' || subtitle_font_size!=''){
            shortcodedata +=' subtitle_font_size="'+subtitle_font_size+'"';
        }
        if(typeof(title_line_height)!='undefined' || title_line_height!=''){
            shortcodedata +=' title_line_height="'+title_line_height+'"';
        }
        if(typeof(subtitle_line_height)!='undefined' || subtitle_line_height!=''){
            shortcodedata +=' subtitle_line_height="'+subtitle_line_height+'"';
        }
		  
		  shortcodedata += ']';
		
		  /*tinyMCE.activeEditor.selection.setContent(shortcodedata);
		  
		  $('#sm-modal').remove();*/

		$('.sm_shortcode_list').hide();
		$('.smd_shortcode_container').show();
		$('#smd_shortcode_container').val(shortcodedata);
		$('#smd_shortcode_container').select();
		document.execCommand('copy');
		  

    }).on( 'change', '#smd_mode',function(){
	
		var mode = $('#smd_mode').val();
		
		if( mode == 'one' ){
			$('#smd_list_div').css('display', 'block');
			$('#smd_list_cat').css('display', 'none');
			$('#smd_orderby_div').css('display', 'none');
			$('#smd_order_div').css('display', 'none');
		}
		else if( mode == 'category' ){
			$('#smd_list_cat').css('display', 'block');
			$('#smd_list_div').css('display', 'none');
			$('#smd_orderby_div').css('display', 'block');
			$('#smd_order_div').css('display', 'block');
		}
		else{
			$('#smd_list_div').css('display', 'none');
			$('#smd_list_cat').css('display', 'none');
			$('#smd_orderby_div').css('display', 'block');
			$('#smd_order_div').css('display', 'block');
		}
		
	}).on( 'change', '#smd_style',function(){
	
		var style = $('#smd_style').val();
		
		if( style == 'simple' || style == 'style-1' ){
			$('#smd_column_div').css('display', 'block');
		}
		else{
			$('#smd_column_div').css('display', 'none');
		}
		
		if( style == 'simple' ){
		   $('#demo-preview-link #demo-url').html('<a href="http://dev.quantumcloud.com/simple-media-directory/" target="_blank">http://dev.quantumcloud.com/simple-media-directory/</a>');
		}
		else if( style == 'style-1' ){
		   $('#demo-preview-link #demo-url').html('<a href="http://dev.quantumcloud.com/simple-media-directory/style-1/" target="_blank">http://dev.quantumcloud.com/simple-media-directory/style-1/</a>');
		}
		else if( style == 'style-2' ){
		   $('#demo-preview-link #demo-url').html('<a href="http://dev.quantumcloud.com/simple-media-directory/style-2/" target="_blank">http://dev.quantumcloud.com/simple-media-directory/style-2/</a>');
		}
		else if( style == 'style-3' ){
		   $('#demo-preview-link #demo-url').html('<a href="http://dev.quantumcloud.com/simple-media-directory/style-3/" target="_blank">http://dev.quantumcloud.com/simple-media-directory/style-3/</a>');
		}
		else{
		   $('#demo-preview-link #demo-url').html('<a href="http://dev.quantumcloud.com/simple-media-directory/" target="_blank">http://dev.quantumcloud.com/simple-media-directory/</a>');
		}	
		
	});






})