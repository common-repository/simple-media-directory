<?php

//Sample Dummy Contents

//delete_option( 'qcsmd_dummy_stat' );

if( function_exists('get_option') ){
  add_action( 'init', 'qcld_check_current_flag_then_insert_opt' );
  add_action( 'init', 'qcsmd_insert_dummy_post' );
}

if ( ! function_exists( 'qcld_check_current_flag_then_insert_opt' ) ) {
	function qcld_check_current_flag_then_insert_opt(){
	  
	  $currentDummyOption = 0;

	  $currentDummyOption = get_option('qcsmd_dummy_stat');

	  if( $currentDummyOption != 1 ) {

	    global $wpdb;

	    $query = "INSERT INTO $wpdb->options (option_name, option_value)
	    SELECT * FROM (SELECT 'qcsmd_dummy_stat', '1') AS tmp
	    WHERE NOT EXISTS (
	      SELECT option_name, option_value FROM $wpdb->options WHERE option_name = 'qcsmd_dummy_stat' AND option_value = '1'
	    ) LIMIT 1";

	    $inserted = $wpdb->get_var( $query );

	  }
	  
	}
}


if ( ! function_exists( 'qcsmd_insert_dummy_post' ) ) {
	function qcsmd_insert_dummy_post(){
		
		$currentDummyOption = get_option('qcsmd_dummy_stat');
		
		if( $currentDummyOption != 1 ){
		
			$required = array(
				'post_exists' => ABSPATH . 'wp-admin/includes/post.php',
			);

			foreach ( $required as $func => $req_file ) {
				if ( ! function_exists( $func ) )
					require_once $req_file;
			}
			
			if( function_exists('is_user_logged_in') && is_user_logged_in() ) {
			
				$post_arr = array(
					'post_title'   	=> 'Design Directory',
					'post_status'  	=> 'publish',
					'post_author'  	=> get_current_user_id(),
					'post_type' 	=> 'smd',
					'meta_input'   	=> array(
						'qcsmd_list_item01' 		=> array(
							'qcsmd_item_title' 		=> 'dna88',
							'qcsmd_item_link' 		=> esc_url('http://www.dna88.com/ultimate-list-of-free-web-design-resources/'),
							'qcsmd_item_nofollow' 	=> 1,
							'qcsmd_item_newtab' 	=> 1,
							'qcsmd_item_subtitle' 	=> esc_attr('Ultimate List of Free Web Design Resources'),
						)
					),
				);

				wp_insert_post( $post_arr );
			
			}
		
		}
		
	}
}

