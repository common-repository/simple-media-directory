<?php
defined( 'ABSPATH' ) || die( 'Bye bye!' );

if ( ! function_exists( 'qcsmd_check_for_shortcode' ) ) {
    function qcsmd_check_for_shortcode($posts) {
        if ( empty($posts) )
            return $posts;
     
        // false because we have to search through the posts first
        $found = false;
     
        // search through each post
        foreach ($posts as $post) {
            // check the post content for the short code
            if ( stripos($post->post_content, 'qcsmd-directory') )
                // we have found a post with the short code
                $found = true;
                // stop the search
                break;
            }
     
        if ($found){
           //Load Script and Stylesheets
           
        }

        return $posts;
    }
}

//perform the check when the_posts() function is called
add_action('the_posts', 'qcsmd_check_for_shortcode');


add_action('template_redirect', 'qcsmd_check_for_shorcode');
function qcsmd_check_for_shorcode(){
    global $wp_query;
    if ( is_singular() ) {
        $post = $wp_query->get_queried_object();
        
        if ( $post && strpos($post->post_content, 'qcsmd-directory' ) !== false ) {
            add_action('wp_enqueue_scripts', 'qcsmd_load_all_scripts');
        }
        
    }
}

add_action('wp_enqueue_scripts', 'qcsmd_load_all_scripts');
if ( ! function_exists( 'qcsmd_load_all_scripts' ) ) {
    function qcsmd_load_all_scripts(){

    	//Scripts
    	wp_enqueue_script( 'jquery', 'jquery');
       // wp_enqueue_script( 'qcsmd-grid-packery', QCSMD_ASSETS_URL . '/js/packery.pkgd.js', array('jquery'),true,true);
    	wp_enqueue_script( 'qcsmd-magnific-script', QCSMD_ASSETS_URL . '/js/jquery.magnific-popup.min.js', array('jquery'));
        wp_register_script( 'qcsmd-custom-script', QCSMD_ASSETS_URL . '/js/directory-script.js', array('jquery'));

        $qcsmd_custom_js = "var qcsmd_ajaxurl = '".admin_url('admin-ajax.php')."'; var qcsmd_ajax_nonce = '".wp_create_nonce('ajax_validation_18')."';";

        wp_add_inline_script( 'qcsmd-custom-script', $qcsmd_custom_js, 'before' );

        qcsmd_add_outbound_click_tracking_script();
        
        //StyleSheets
    	wp_enqueue_style( 'qcsmd-fa-css', QCSMD_ASSETS_URL . '/css/font-awesome.min.css' );
        wp_enqueue_style( 'qcsmd-magnific-css', QCSMD_ASSETS_URL . '/css/magnific-popup.css');
    	wp_register_style( 'qcsmd-custom-css', QCSMD_ASSETS_URL . '/css/directory-style.css');
    	wp_enqueue_style( 'qcsmd-custom-rwd-css', QCSMD_ASSETS_URL . '/css/directory-style-rwd.css');
    	
    }
}

add_action( 'admin_enqueue_scripts', 'qcsmd_admin_enqueue' );
if ( ! function_exists( 'qcsmd_admin_enqueue' ) ) {
    function qcsmd_admin_enqueue(){
    	wp_enqueue_style( 'qcsmd-custom-admin-css', QCSMD_ASSETS_URL . '/css/admin-style.css');
    	wp_enqueue_style( 'jq-slick.css-css', QCSMD_ASSETS_URL . '/css/slick.css');
    	wp_enqueue_style( 'jq-slick-theme-css', QCSMD_ASSETS_URL . '/css/slick-theme.css', array(), '1.0.1');
    	//wp_enqueue_script( 'jq-slick.min-js', QCSMD_ASSETS_URL . '/js/slick.min.js', array('jquery'));
    	wp_enqueue_script( 'smd-admin-common-script', QCSMD_ASSETS_URL . '/js/qcsmd-admin-common.js', array('jquery'));

        $qcsmd_custom_js = "var qcsmd_ajaxurl = '".admin_url('admin-ajax.php')."'; var qcsmd_ajax_nonce = '".wp_create_nonce('ajax_validation_18')."';";

        wp_add_inline_script( 'smd-admin-common-script', $qcsmd_custom_js, 'before' );
    }
}

if ( ! function_exists( 'smd_packery_adding_scripts' ) ) {
    function smd_packery_adding_scripts() {
    	
        wp_register_script('smd-packery-script', QCSMD_ASSETS_URL . '/js/packery.pkgd.js','','1.1', true);
        wp_enqueue_script('smd-packery-script');

    }
}
add_action( 'wp_enqueue_scripts', 'smd_packery_adding_scripts', 100 ); 

if ( ! function_exists( 'smd_is_youtube_video' ) ) {
    function smd_is_youtube_video($link){
        if( isset( $link ) && !empty( $link ) ){
            //parse_str( parse_url( $link, PHP_URL_QUERY ), $my_array_of_vars );
            $method = parse_url( $link, PHP_URL_QUERY ) ?? '';
            parse_str( $method, $my_array_of_vars );
            if(isset($my_array_of_vars['v']) && $my_array_of_vars['v']!=''){
                return true;
            }
            return false;
        }
    }
}

if ( ! function_exists( 'smd_is_vimeo_video' ) ) {
    function smd_is_vimeo_video($link){
        if( isset( $link ) && !empty( $link ) ){
            $urls = parse_url($link);
            if(isset($urls['host']) && $urls['host']=='vimeo.com'){
                return true;
            }
            return false;
        }
    }
}