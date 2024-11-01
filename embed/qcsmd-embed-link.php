<?php 
	wp_head();

	wp_enqueue_style('qcsmd-embed-awesome-css', QCSMD_ASSETS_URL . "/css/font-awesome.min.css" );
	wp_enqueue_style('qcsmd-embed-directory-css', QCSMD_ASSETS_URL . "/css/directory-style.css" );
	wp_enqueue_style('qcsmd-embed-form-css', QCSMD_URL . "/embed/css/embed-form.css" );
	wp_enqueue_style('qcsmd-embed-directory-rwd', QCSMD_ASSETS_URL . "/css/directory-style-rwd.css" );

	wp_enqueue_script( 'qcsmd-embed-js', QCSMD_URL . '/embed/js/jquery-1.11.3.js' );
	wp_enqueue_script( 'qcsmd-embed-packery-js', QCSMD_ASSETS_URL . '/js/packery.pkgd.js' );
	wp_enqueue_script( 'qcsmd-embed-form-js', QCSMD_URL . '/embed/js/embed-form.js' );
	wp_enqueue_script( 'qcsmd-embed-directory-script-js', QCSMD_ASSETS_URL . '/js/directory-script.js' );


	$embed_custom_js = "var ajaxurl = '".admin_url('admin-ajax.php')."';";

	wp_add_inline_script( 'qcsmd-embed-form-js', $embed_custom_js);


	$order 		= isset($_POST['order'])        ? sanitize_text_field($_GET['order']) : '';
	$mode 		= isset($_POST['mode'])        	? sanitize_text_field($_GET['mode']) : '';
	$column 	= isset($_POST['column'])       ? sanitize_text_field($_GET['column']) : '';
	$style 		= isset($_POST['style'])        ? sanitize_text_field($_GET['style']) : '';
	$search 	= '';
	$category 	= isset($_POST['category'])     ? sanitize_text_field($_GET['category']) : '';
	$upvote 	= '';
	$list_id 	= isset($_POST['list_id'])      ? sanitize_text_field($_GET['list_id']) : '';

	echo '<div class="clear">';

	echo do_shortcode('[qcsmd-directory mode="' . $mode .  '" list_id="' . $list_id . '" style="' . $style . '" column="' . $column . '" search="' . $search . '" category="' . $category . '" upvote="' . $upvote . '" item_count="on" orderby="date" order="' . $order . '"]'); 

	echo '</div>';

	wp_footer();
?>





