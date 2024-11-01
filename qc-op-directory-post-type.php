<?php
defined( 'ABSPATH' ) || die( 'Bye bye!' );

//Registering custom post for Team
if ( ! function_exists( 'qcsmd_register_cpt_smd' ) ) {
	function qcsmd_register_cpt_smd() {
		
		$qc_list_labels = array(
			'name'               => _x( 'Manage List Items', 'qc-smd' ),
			'singular_name'      => _x( 'Manage List Item', 'qc-smd' ),
			'add_new'            => _x( 'New List', 'qc-smd' ),
			'add_new_item'       => __( 'Add New List Item' ),
			'edit_item'          => __( 'Edit List Item' ),
			'new_item'           => __( 'New List Item' ),
			'all_items'          => __( 'Manage List Items' ),
			'view_item'          => __( 'View List Item' ),
			'search_items'       => __( 'Search List Item' ),
			'not_found'          => __( 'No List Item found' ),
			'not_found_in_trash' => __( 'No List Item found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Simple Media Directory'
		);
		
		$qc_list_args = array(
			'labels'        => $qc_list_labels,
			'description'   => 'This post type holds all posts for your directory items.',
			'public'        => true,
			'menu_position' => 25,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'supports'      => array( 'title' ),
			'has_archive'   => true,
			'menu_icon' 	=> 'dashicons-playlist-video',
		);
		
		register_post_type( 'smd', $qc_list_args );	
		
		//Register New Taxonomy for Our New Post Type
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'List Categories', 'List Categories', 'qc-smd' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'qc-smd' ),
			'search_items'      => __( 'Search List Categories', 'qc-smd' ),
			'all_items'         => __( 'All List Categories', 'qc-smd' ),
			'parent_item'       => __( 'Parent List Categories', 'qc-smd' ),
			'parent_item_colon' => __( 'Parent List Category:', 'qc-smd' ),
			'edit_item'         => __( 'Edit List Category', 'qc-smd' ),
			'update_item'       => __( 'Update List Category', 'qc-smd' ),
			'add_new_item'      => __( 'Add New List Category', 'qc-smd' ),
			'new_item_name'     => __( 'New List Category Name', 'qc-smd' ),
			'menu_name'         => __( 'List Categories', 'qc-smd' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'smd_cat' ),
		);

		register_taxonomy( 'smd_cat', array( 'smd' ), $args );
		
	}
}

add_action( 'init', 'qcsmd_register_cpt_smd' );

//Require CMB Metabox
if ( ! class_exists( 'CMB_Meta_Box' ) )
{
	require_once QCSMD_INC_DIR . '/cmb/custom-meta-boxes.php';
}

//Metabox Fields
if ( ! function_exists( 'cmb_qcsmd_dir_fields' ) ) {
	function cmb_qcsmd_dir_fields( array $meta_boxes ) {
		
		//Repeatable Fields
		$qcsmd_item_fields = array(
			array( 'id' => 'qcsmd_item_title',  'name' => 'Media Title', 'type' => 'text', 'cols' => 4, 'desc' => 'Title of the list item' ),
			array( 'id' => 'qcsmd_item_link',  'name' => 'Media Link', 'type' => 'text', 'cols' => 4, 'desc' => 'With http://, Example: http://www.google.com' ),
			array( 'id' => 'qcsmd_upvote_count',  'name' => 'Upvote Count', 'type' => 'text', 'cols' => 4, 'default' => '0', 'desc' => 'Total upvote for this element' ),
			array( 'id' => 'qcsmd_item_img', 'name' => 'List Image', 'type' => 'image', 'repeatable' => false, 'show_size' => false, 'cols' => 3, 'desc' => 'Preferred Size: 100X100px'  ),
			//array( 'id' => 'qcsmd_item_nofollow',  'name' => 'No Follow', 'type' => 'checkbox', 'cols' => 3, 'default' => 0 ),
			
			array( 'id' => 'qcsmd_entry_time',  'name' => 'Entry Time', 'type' => 'text', 'cols' => 4, 'default' => ''.date("Y-m-d H:i:s").'' ),	
			array( 'id' => 'qcsmd_timelaps',  'name' => 'Time Laps', 'type' => 'text', 'cols' => 4, 'default' => '' ),	
			
			//array( 'id' => 'qcsmd_item_newtab',  'name' => 'Open Link in a New Tab', 'type' => 'checkbox', 'cols' => 3, 'default' => 0 ),
			array( 'id' => 'qcsmd_featured',  'name' => 'Mark Media as Featured', 'type' => 'checkbox', 'cols' => 3, 'default' => 0, 'desc' => '' ),
			array( 'id' => 'qcsmd_item_subtitle',  'name' => 'Media Subtitle', 'type' => 'text', 'cols' => 9 ),
		);

		$meta_boxes[] = array(
			'title' 	=> __( 'List Elements' ),
			'pages' 	=> 'smd',
			'fields' 	=> array(
				array(
					'id' 			=> 'qcsmd_list_item01',
					'name' 			=> __( 'Create List Elements' ),
					'type' 			=> 'group',
					'repeatable' 	=> true,
					'sortable' 		=> true,
					'fields' 		=> $qcsmd_item_fields,
					'desc' 			=> __( 'Please add your list items here.' )
				)
			)
		);

		return $meta_boxes;

	}
}

add_filter( 'cmb_meta_boxes', 'cmb_qcsmd_dir_fields' );

//Custom Columns for Directory Listing
if ( ! function_exists( 'qcsmd_list_columns_head' ) ) {
	function qcsmd_list_columns_head($defaults) {

	    $new_columns['cb'] = '<input type="checkbox" />';
	    $new_columns['title'] = __('Title');

	    $new_columns['qcsmd_item_count'] = __('Number of Elements');
	    $new_columns['shortcode_col'] = __('Shortcode');

	    $new_columns['date'] = __('Date');

	    return $new_columns;
	}
}
 
//Custom Columns Data for Backend Listing
if ( ! function_exists( 'qcsmd_list_columns_content' ) ) {
	function qcsmd_list_columns_content($column_name, $post_ID) {
	    

	    if ($column_name == 'qcsmd_item_count') {
	        echo count(get_post_meta( $post_ID, 'qcsmd_list_item01' ));
	    }

	    if ($column_name == 'shortcode_col') {
	        echo '[qcsmd-directory mode="one" list_id="'.$post_ID.'"]';
	    }
	}
}

add_filter('manage_smd_posts_columns', 'qcsmd_list_columns_head');
add_action('manage_smd_posts_custom_column', 'qcsmd_list_columns_content', 10, 2);


