<?php
defined( 'ABSPATH' ) || die( 'Bye bye!' );

define('OCSMD_TPL_DIR', QCSMD_DIR . "/templates");
//define('QCSMD_TPL_URL', QCSMD_URL . "/templates");

/*Custom Item Sort Logic*/

if ( ! function_exists( 'smd_custom_sort_by_tpl_title' ) ) {
	function smd_custom_sort_by_tpl_title($a, $b) {
	    //return $a['qcsmd_item_title'] > $b['qcsmd_item_title'];
		return strnatcasecmp($a['qcsmd_item_title'], $b['qcsmd_item_title']);
	}
}

if ( ! function_exists( 'smd_custom_sort_by_tpl_upvotes' ) ) {
	function smd_custom_sort_by_tpl_upvotes($a, $b) {
	    //return @($a['qcsmd_upvote_count'] * 1 < $b['qcsmd_upvote_count'] * 1);
		$aTime = isset($a['qcsmd_upvote_count']) && !empty( $a['qcsmd_upvote_count'] ) ? (int)$a['qcsmd_upvote_count'] : 0;
		$bTime = isset($b['qcsmd_upvote_count']) && !empty( $b['qcsmd_upvote_count'] ) ? (int)$b['qcsmd_upvote_count'] : 0;

		if( $aTime === $bTime ){
			return 0;
		}

		return $aTime < $bTime  ? 1 : -1;
	}
}

if ( ! function_exists( 'smd_custom_sort_by_tpl_timestamp' ) ) {
	function smd_custom_sort_by_tpl_timestamp($a, $b) {
		// if( isset($a['qcsmd_timelaps']) && isset($b['qcsmd_timelaps']) ){

		// 	$aTime = (int)$a['qcsmd_timelaps'];
		// 	$bTime = (int)$b['qcsmd_timelaps'];
		// 	return $aTime < $bTime;
		// }

		if( isset($a['qcsmd_timelaps']) && isset($b['qcsmd_timelaps']) ){
			$aTime = isset($a['qcsmd_timelaps']) && !empty( $a['qcsmd_timelaps'] ) ? (int)$a['qcsmd_timelaps'] : 0;
			$bTime = isset($b['qcsmd_timelaps']) && !empty( $b['qcsmd_timelaps'] ) ? (int)$b['qcsmd_timelaps'] : 0;

			if( $aTime === $bTime ){
				return 0;
			}

			return $aTime < $bTime  ? 1 : -1;
		}

	}
}

//For all list elements
add_shortcode('qcsmd-directory', 'qcsmd_directory_full_shortcode');
if ( ! function_exists( 'qcsmd_directory_full_shortcode' ) ) {
	function qcsmd_directory_full_shortcode( $atts = array() ){
		wp_enqueue_script( 'qcsmd-custom-script');
		wp_enqueue_style( 'qcsmd-custom-css');
		ob_start();
	    show_qcsmd_full_list( $atts );
	    $content = ob_get_clean();
	    return $content;
	}
}

if ( ! function_exists( 'show_qcsmd_full_list' ) ) {
	function show_qcsmd_full_list( $atts = array() ) {
		$template_code = "";

		//Defaults & Set Parameters
		extract( shortcode_atts(
			array(
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'mode' => 'all',
				'list_id' => '',
				'column' => '1',
				'style' => 'simple',
				'list_img' => 'true',
				'search' => 'true',
				'category' => "",
				'upvote' => "off",
				'item_count' => "on",
				'top_area' => "on",
				'item_orderby' => "",
				'item_order' => "",
				'mask_url' => "off",
				'enable_embedding' => 'false',
				'title_font_size' => '',
				'subtitle_font_size' => '',
				'title_line_height' => '',
				'subtitle_line_height' => '',
			), $atts
		));

		//ShortCode Atts
		$shortcodeAtts = array(
			'orderby' => $orderby,
			'order' => $order,
			'mode' => $mode,
			'list_id' => $list_id,
			'column' => $column,
			'style' => $style,
			'list_img' => $list_img,
			'search' => $search,
			'category' => $category,
			'upvote' => $upvote,
			'item_count' => $item_count,
			'top_area' => $top_area,
			'item_orderby' => $item_orderby,
			'item_order' => $item_order,
			'mask_url' => $mask_url,
			'enable_embedding' => $enable_embedding,
			'title_font_size' => $title_font_size,
			'subtitle_font_size' => $subtitle_font_size,
			'title_line_height' => $title_line_height,
			'subtitle_line_height' => $subtitle_line_height,
		);
		
		$limit = -1;

		if( $mode == 'one' )
		{
			$limit = 1;	
		}

		if($orderby=='menu_order'){
			$orderby = $orderby.' title';
		}
		
		//Query Parameters
		$list_args = array(
			'post_type' => 'smd',
			'posts_per_page' => $limit,
		);
		if($orderby!='none' or $order!='none'){
			$list_args['orderby'] = $orderby;
			$list_args['order'] = $order;
		}
		

		if( $list_id != "" && $mode == 'one' )
		{
			$list_args = array_merge($list_args, array( 'p' => $list_id ));
		}
		
		if( $category != "" )
		{
			$taxArray = array(
				array(
					'taxonomy' => 'smd_cat',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
			
			$list_args = array_merge($list_args, array( 'tax_query' => $taxArray ));
			
		}
		
		if(get_option('smd_enable_upvote')=='on'){
			$upvote = 'on';
		}
		// The Query
		$list_query = new WP_Query( $list_args );
		
	    if ( isset($atts["style"]) && $atts["style"] )
	        $template_code = $atts["style"];

	    if (!$template_code)
	        $template_code = "simple";

	    if( $mode == 'one' ){
	    	$column = '1';
	    }

	?>

	<?php if(get_option('smd_enable_scroll_to_top')=='on'): 

		$custom_css = ".smd_scrollToTop{
			width: 30px;
		    height: 30px;
		    padding: 10px !important;
		    text-align: center;
		    font-weight: bold;
		    color: #444;
		    text-decoration: none;
		    position: fixed;
		    top: 88%;
		    right: 29px;
		    display: none;
		    background: url('".esc_url(QCSMD_IMG_URL)."/up-arrow.ico') no-repeat 5px 5px;
		    background-size: 20px 20px;
		    text-indent: -99999999px;
		    background-color: #ddd;
		    border-radius: 3px;
			z-index:9999999999;
			box-sizing: border-box;

		}
		.smd_scrollToTop:hover{
		text-decoration:none;
		}
		.filter-area{z-index: 99 !important;
		    padding: 10px 0px;
		    
		}";

		wp_add_inline_style( 'qcsmd-custom-css', $custom_css );

	?>

	<a href="#"class="smd_scrollToTop">Scroll To Top</a>

<?php 
	$qcsmd_custom_js  = "
						jQuery(document).ready(function($){
						  $(window).scroll(function(){
								if ($(this).scrollTop() > 100) {
									$('.smd_scrollToTop').fadeIn();
								} else {
									$('.smd_scrollToTop').fadeOut();
								}
							});

							//Click event to scroll to top
							$('.smd_scrollToTop').click(function(){
								$('html, body').animate({scrollTop : 0},800);
								return false;
							});


						});";
	

	wp_add_inline_script( 'qcsmd-custom-script', $qcsmd_custom_js);

	endif; 
		
		$customjs = get_option( 'smd_custom_js' );
		if(trim($customjs)!=''){
			$custom_js = "
						jQuery(document).ready(function($){
						 
							".$customjs."

						});";
			wp_add_inline_script( 'qcsmd-custom-script', $custom_js);
		}

	    //require ( OCSMD_TPL_DIR . "/$template_code/template.php" );
		echo '<!--  Starting Simple Media Directory Plugin Output -->';
		$tempath = QCSMD_DIR ."/templates/".$template_code."/template.php";
		if( file_exists( $tempath ) ){
	    	require ( $tempath );
		}
		wp_reset_query();

		$customCss = get_option( 'smd_custom_style' );

		if( trim($customCss) != "" ) :
			wp_add_inline_style( 'qcsmd-custom-css', $customCss );
	 	endif; 


		$qcsmd_custom_js = "
					jQuery(window).load(function(){

					    setTimeout(function(){
					    	jQuery('.qc-grid').packery({
						      itemSelector: '.qc-grid-item',
						      gutter: 20
						    });
					    }, 200);
					});";
	

		wp_add_inline_script( 'qcsmd-custom-script', $qcsmd_custom_js);



	}
}