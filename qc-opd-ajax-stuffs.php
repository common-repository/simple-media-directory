<?php
defined( 'ABSPATH' ) || die( 'Bye bye!' );

/*add_action('wp_head', 'qcsmd_ajax_ajaxurl');
add_action('admin_head', 'qcsmd_ajax_ajaxurl');
if ( ! function_exists( 'qcsmd_ajax_ajaxurl' ) ) {
    function qcsmd_ajax_ajaxurl() {

        echo '<script type="text/javascript">
               var ajaxurl = "' . admin_url('admin-ajax.php') . '";
             </script>';
    }
}*/

// Doing ajax action stuff
if ( ! function_exists( 'qcsmd_upvote_ajax_action_stuff' ) ) {
    function qcsmd_upvote_ajax_action_stuff() {

        check_ajax_referer( 'ajax_validation_18', 'security' );

        // Get posted items
        $action         = isset($_POST['action'])       ? sanitize_text_field($_POST['action']) : '';
        $post_id        = isset($_POST['post_id'])      ? sanitize_text_field($_POST['post_id']) : '';
        $meta_title     = isset($_POST['meta_title'])   ? sanitize_text_field($_POST['meta_title']) : '';
        $meta_link      = isset($_POST['meta_link'])    ? esc_url_raw($_POST['meta_link']) : '';
        $li_id          = isset($_POST['li_id'])        ? sanitize_text_field($_POST['li_id']) : '';

        //Check wpdb directly, for all matching meta items
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'qcsmd_list_item01'", $post_id ) );

        //Defaults
        $votes = 0;

        $data['votes'] = 0;
        $data['vote_status'] = 'failed';

        // $exists = @in_array("$li_id", @$_COOKIE['voted_li']);

        if( isset($_COOKIE['voted_li'])){
            $exists = in_array("$li_id", $_COOKIE['voted_li']);
        }else{
            $exists = false;
        }

        //If li-id not exists in the cookie, then prceed to vote
        if (!$exists) {
    		
            //Iterate through items
            foreach ($results as $key => $value) {

                $item = $value;

                $meta_id = $value->meta_id;

                $unserialized = unserialize( $value->meta_value );

                //If meta title and link matches with unserialized data
                if (trim($unserialized['qcsmd_item_title']) == trim($meta_title) && trim($unserialized['qcsmd_item_link']) == trim($meta_link)) {

                    $metaId = $meta_id;

                    //Defaults for current iteration
                    $upvote_count = 0;
                    $new_array = array();
                    $flag = 0;

                    //Check if there already a set value (previous)
                    if (array_key_exists('qcsmd_upvote_count', $unserialized)) {
                        $upvote_count = (int)$unserialized['qcsmd_upvote_count'];
                        $flag = 1;
                    }

                    foreach ($unserialized as $key => $value) {
                        if ($flag) {
                            if ($key == 'qcsmd_upvote_count') {
                                $new_array[$key] = $upvote_count + 1;
                            } else {
                                $new_array[$key] = $value;
                            }
                        } else {
                            $new_array[$key] = $value;
                        }
                    }

                    if (!$flag) {
                        $new_array['qcsmd_upvote_count'] = $upvote_count + 1;
                    }

                    $votes = (int)$new_array['qcsmd_upvote_count'];

                    $updated_value = serialize($new_array);

                    $wpdb->update(
                        $wpdb->postmeta,
                        array(
                            'meta_value' => $updated_value,
                        ),
                        array('meta_id' => $metaId)
                    );

                    $voted_li = array("$li_id");

                    $total = 0;
                    if( isset($_COOKIE['voted_li']) ){
                        $total = count($_COOKIE['voted_li']);
                    }
                    $total = $total + 1;

                    setcookie("voted_li[$total]", $li_id, time() + (86400 * 30), "/");

                    $data['vote_status']    = 'success';
                    $data['votes']          = $votes;
                }

            }
        }

        //$data['cookies'] = $_COOKIE['voted_li'];

        if( isset($_COOKIE['voted_li']) ){
            $data['cookies'] = $_COOKIE['voted_li'];
        }else{
            $data['cookies'] = '';
        }

        echo json_encode($data);


        die(); // stop executing script
    }
}

// Implementing the ajax action for frontend users
add_action('wp_ajax_qcsmd_upvote_action', 'qcsmd_upvote_ajax_action_stuff'); // ajax for logged in users
add_action('wp_ajax_nopriv_qcsmd_upvote_action', 'qcsmd_upvote_ajax_action_stuff'); // ajax for not logged in users


if ( ! function_exists( 'qcsmd_load_video_function' ) ) {
    function qcsmd_load_video_function(){

        check_ajax_referer( 'ajax_validation_18', 'security' );
        
        $video_link = isset( $_POST['videurl'] ) ? trim( sanitize_text_field( $_POST['videurl'] ) ) : '';

        //$video_link = str_replace('watch?v=','embed/',$video_link);

        $urls = parse_url($video_link);
        if(isset($urls['host']) && $urls['host']=='vimeo.com'){
            
            $videoId = explode('/',$video_link);
            
            $video_link = 'https://player.vimeo.com/video/'.end($videoId);
            echo '<iframe width="560" height="315" src="'.esc_url($video_link).'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
        }else{


            $method = parse_url( $video_link, PHP_URL_QUERY ) ?? '';
            parse_str( $method, $my_array_of_vars );
            //parse_str( parse_url( $video_link, PHP_URL_QUERY ), $my_array_of_vars );
            if(isset($my_array_of_vars['v']) && $my_array_of_vars['v']!=''){
                $video_link = 'https://www.youtube.com/embed/'.$my_array_of_vars['v'].'?rel=0&amp;showinfo=0';
                echo '<iframe width="560" height="315" src="'.esc_url($video_link).'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
            }
        }

        die();
    }
}

//Implementing the ajax action for frontend users
add_action('wp_ajax_qcopd_load_video', 'qcsmd_load_video_function'); // ajax for logged in users
add_action('wp_ajax_nopriv_qcopd_load_video', 'qcsmd_load_video_function'); // ajax for not logged in users