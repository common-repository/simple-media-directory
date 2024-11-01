<?php
/**
 * Plugin Name: Simple Media Directory
 * Plugin URI: https://wordpress.org/plugins/simple-media-directory
 * Description: Directory WordPress plugin to curate topic based video collections. 
 * Version: 1.4.4
 * Author: QuantumCloud
 * Author URI: https://www.quantumcloud.com/
 * Requires at least: 4.6
 * Tested up to: 6.5
 * Text Domain: qc-smd
 * Domain Path: /lang/
 * License: GPL2
 */


defined('ABSPATH') or die("No direct script access!");

//Custom Constants
if ( ! defined( 'QCSMD_URL' ) ) {
    define('QCSMD_URL', plugin_dir_url(__FILE__));
}
if ( ! defined( 'QCSMD_IMG_URL' ) ) {
    define('QCSMD_IMG_URL', QCSMD_URL . "/assets/images");
}
if ( ! defined( 'QCSMD_ASSETS_URL' ) ) {
    define('QCSMD_ASSETS_URL', QCSMD_URL . "/assets");
}
if ( ! defined( 'QCSMD_DIR' ) ) {
    define('QCSMD_DIR', dirname(__FILE__));
}
if ( ! defined( 'QCSMD_INC_DIR' ) ) {
    define('QCSMD_INC_DIR', QCSMD_DIR . "/inc");
}
if ( ! defined( 'QCSMD_TPL_URL' ) ) {
    define('QCSMD_TPL_URL', QCSMD_URL . "/templates");
}

//Include files and scripts
require_once( 'qc-op-directory-post-type.php' );
require_once( 'qc-op-directory-assets.php' );
require_once( 'qc-op-directory-shortcodes.php' );
require_once( 'embed/embedder.php' );

require_once( 'qcopd-shortcode-generator.php' );
require_once( 'qc-op-directory-import.php' );
require_once( 'qc-opd-ajax-stuffs.php' );

/*05-31-2017*/
require_once('qc-support-promo-page/class-qc-support-promo-page.php');
require_once('class-plugin-deactivate-feedback.php');
require_once('class-qc-free-plugin-upgrade-notice.php');
/*05-31-2017 - Ends*/
/* Option page */
require_once('qc-opd-setting-options.php');

require_once('qc-rating-feature/qc-rating-class.php');

/**
 * Do not forget about translating your plugin
 */
if ( ! function_exists( 'qcsmd_translating_languages' ) ) {
  function qcsmd_translating_languages(){
    load_plugin_textdomain( 'qc-smd', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
  }
}
add_action('init', 'qcsmd_translating_languages');

//Remove Slug Edit Box
add_action('admin_head', 'qcsmd_remove_post_slug_editing');
if ( ! function_exists( 'qcsmd_remove_post_slug_editing' ) ) {
    function qcsmd_remove_post_slug_editing() {
        global $post_type;

        if ($post_type == 'smd') {
            echo "<style>#edit-slug-box {display:none;}#qcsmd_entry_time, #qcsmd_timelaps { display: none; }</style>";
        }
        
        echo '<style>
        .button.qcsmd-promo-link {
          color: #ff0000;
          font-weight: normal;
          margin-left: 0;
          margin-top: 1px !important;
        }
        .clear{ clear: both; }
        </style>';
    }
}


//add_action('wp_head', 'qcsmd_add_outbound_click_tracking_script');
if ( ! function_exists( 'qcsmd_add_outbound_click_tracking_script' ) ) {
    function qcsmd_add_outbound_click_tracking_script(){


      if(!function_exists('wp_get_current_user')) {
        include(ABSPATH . "wp-includes/pluggable.php");
      }
     
     
        if(is_user_logged_in()){
            $current_user = wp_get_current_user();
            if(in_array('administrator',$current_user->roles)){
              return;
            }
        }


        $outbound_conf = get_option( 'smd_enable_click_tracking' );

        if ( isset($outbound_conf) && $outbound_conf == 'on' ) {



            $qc_custom_script_js = 'function _gaLt(event) {

                    if (!ga.hasOwnProperty("loaded") || ga.loaded != true || (event.which != 1 && event.which != 2)) {
                        return;
                    }

                    var el = event.srcElement || event.target;

                    /* Loop up the DOM tree through parent elements if clicked element is not a link (eg: an image inside a link) */
                    while (el && (typeof el.tagName == "undefined" || el.tagName.toLowerCase() != "a" || !el.href)) {
                        el = el.parentNode;
                    }

                    /* if a link with valid href has been clicked */
                    if (el && el.href) {

                        var link = el.href;

                        /* Only if it is an external link */
                        if (link.indexOf(location.host) == -1 && !link.match(/^javascript\:/i)) {

                            /* Is actual target set and not _(self|parent|top)? */
                            var target = (el.target && !el.target.match(/^_(self|parent|top)$/i)) ? el.target : false;

                            /* Assume a target if Ctrl|shift|meta-click */
                            if (event.ctrlKey || event.shiftKey || event.metaKey || event.which == 2) {
                                target = "_blank";
                            }

                            var hbrun = false; // tracker has not yet run

                            /* HitCallback to open link in same window after tracker */
                            var hitBack = function() {
                                /* run once only */
                                if (hbrun) return;
                                hbrun = true;
                                window.location.href = link;
                            };

                            if (target) { /* If target opens a new window then just track */
                                ga(
                                    "send", "event", "Outgoing Links", link,
                                    document.location.pathname + document.location.search
                                );
                            } else { /* Prevent standard click, track then open */
                                event.preventDefault ? event.preventDefault() : event.returnValue = !1;
                                /* send event with callback */
                                ga(
                                    "send", "event", "Outgoing Links", link,
                                    document.location.pathname + document.location.search, {
                                        "hitCallback": hitBack
                                    }
                                );

                                /* Run hitCallback again if GA takes longer than 1 second */
                                setTimeout(hitBack, 1000);
                            }
                        }
                    }
                }

            var _w = window;
            var _gaLtEvt = ("ontouchstart" in _w) ? "click" : "mousedown";
            _w.addEventListener ? _w.addEventListener("load", function() {document.body.addEventListener(_gaLtEvt, _gaLt, !1)}, !1)
                : _w.attachEvent && _w.attachEvent("onload", function() {document.body.attachEvent("on" + _gaLtEvt, _gaLt)});

                
            ';

        wp_add_inline_script('qcsmd-custom-script', $qc_custom_script_js );

        }
    }
}

/**
 * Submenu filter function. Tested with Wordpress 4.1.1
 * Sort and order submenu positions to match your custom order.
 *
 * @author Hendrik Schuster <contact@deviantdev.com>
 */
if ( ! function_exists( 'qclsmdf_order_index_catalog_menu_page' ) ) {
    function qclsmdf_order_index_catalog_menu_page( $menu_ord ) {

      global $submenu;

      // Enable the next line to see a specific menu and it's order positions
      //echo '<pre>'; print_r( $submenu['edit.php?post_type=smd'] ); echo '</pre>'; exit();

      $arr = array();

      $arr[] = $submenu['edit.php?post_type=smd'][5];
      $arr[] = $submenu['edit.php?post_type=smd'][10];
      $arr[] = $submenu['edit.php?post_type=smd'][15];
      $arr[] = $submenu['edit.php?post_type=smd'][16];
      $arr[] = $submenu['edit.php?post_type=smd'][17];
      $arr[] = $submenu['edit.php?post_type=smd'][18];
      
      if( isset($submenu['edit.php?post_type=smd'][300]) ){
        $arr[] = $submenu['edit.php?post_type=smd'][300];
      }

      $submenu['edit.php?post_type=smd'] = $arr;

      return $menu_ord;

    }
}

add_action( 'admin_menu' , 'qcsmd_help_link_submenu', 20 );
if ( ! function_exists( 'qcsmd_help_link_submenu' ) ) {
    function qcsmd_help_link_submenu(){
        global $submenu;
        
        $link_text = "Help";
        $submenu["edit.php?post_type=smd"][250] = array( $link_text, 'activate_plugins' , admin_url('edit.php?post_type=smd&page=smd_settings#help') );
        ksort($submenu["edit.php?post_type=smd"]);
        
        return ($submenu);
    }
}

// add the filter to wordpress
//add_filter( 'custom_menu_order', 'qclsmdf_order_index_catalog_menu_page' );

if ( ! function_exists( 'smd_options_instructions_example' ) ) {
    function smd_options_instructions_example() {
        global $my_admin_page;
        $screen = get_current_screen();
        
        if ( is_admin() && ($screen->post_type == 'smd') ) {
    		wp_enqueue_script( 'jqc-slick.min-js', QCSMD_ASSETS_URL . '/js/slick.min.js', array('jquery'));

            $qc_js = " jQuery(document).ready(function($){

                $('.smd-notice').show();
                $('.smd_info_carousel').slick({
                    dots: false,
                    infinite: true,
                    speed: 1200,
                    slidesToShow: 1,
                    autoplaySpeed: 11000,
                    autoplay: true,
                    slidesToScroll: 1,
                    
                });
                
            });";

            wp_add_inline_script('jqc-slick.min-js', $qc_js );

           

            ?>
            <div class="notice notice-info is-dismissible smd-notice" style="display:none"> 
                <div class="smd_info_carousel">

                    <div class="smd_info_item"> <?php esc_html_e('**SMD Pro Tip: Did you know that you can', 'qc-smd'); ?> <strong style="color: yellow"> <?php esc_html_e('Auto Generate', 'qc-smd'); ?> </strong> <?php esc_html_e('Title, Subtitle & Thumbnail with the Pro Version in Just 2 Clicks?', 'qc-smd'); ?> <strong style="color: yellow"> <?php esc_html_e('Triple Your Link Entry Speed!', 'qc-smd'); ?></strong></div>
                    
                    <div class="smd_info_item"><?php esc_html_e('**SMD Tip: Lists are the base pillars of SMD, not individual links. Group your links into different Lists for the best performance.', 'qc-smd'); ?></div>
                    
                    <div class="smd_info_item"><?php esc_html_e('**SMD Tip: SMD looks the best when you create multiple Lists and use the Show All Lists mode.', 'qc-smd'); ?></div>

                    <div class="smd_info_item"><?php esc_html_e('**SMD Pro Tip: Did you know that SMD Pro version lets you monetize your directory and earn', 'qc-smd'); ?> <strong style="color: yellow"><?php esc_html_e('passive income?', 'qc-smd'); ?></strong> <?php esc_html_e('Upgrade now!', 'qc-smd'); ?></div>
                    
                    <div class="smd_info_item"><?php esc_html_e('**SMD Tip: Try to keep the maximum number of links below 30 per list. Create multiple Lists as needed.', 'qc-smd'); ?></div>

                    <div class="smd_info_item"><?php esc_html_e('**SMD Tip: Use the handy shortcode generator to make life easy. It is a small, blue [SMD] button found at the toolbar of any page\'s visual editor.', 'qc-smd'); ?></div>
                    
                    <div class="smd_info_item"><?php esc_html_e('**SMD Pro Tip: You can display your', 'qc-smd'); ?> <strong style="color: yellow"><?php esc_html_e('Lists by category', 'qc-smd'); ?> </strong> <?php esc_html_e('with the SMD pro version.', 'qc-smd'); ?> <strong style="color: yellow"><?php esc_html_e('16+ Templates, Multi page mode', 'qc-smd'); ?></strong>, <?php esc_html_e('Widgets are also available.', 'qc-smd'); ?></div>
                    
                    <div class="smd_info_item"><?php esc_html_e('**SMD Tip: You can create a page with a contact form and link the Add Link button to that page so people can submit links to your directory by email.', 'qc-smd'); ?></div>

                    <div class="smd_info_item"><?php esc_html_e('**SMD Tip: If you are having problem with adding more items or saving a list then you may need to increase max_input_vars value in server. Check the help section for more details.', 'qc-smd'); ?></div>
                    
                    <div class="smd_info_item"><?php esc_html_e('**SMD Pro Tip: SMD pro version has', 'qc-smd'); ?> <strong style="color: yellow"><?php esc_html_e('front end dashboard', 'qc-smd'); ?></strong> <?php esc_html_e('for user registration and link management. As well as tags and instant search.', 'qc-smd'); ?> <strong style="color: yellow"> <?php esc_html_e('Upgrade to the Pro version now!', 'qc-smd'); ?></strong></div>

                </div>
            </div>
            <?php
        }
    }
}

add_action( 'add_meta_boxes', 'smd_meta_box_video' );
if ( ! function_exists( 'smd_meta_box_video' ) ) {
    function smd_meta_box_video(){					                  // --- Parameters: ---
        add_meta_box( 'qc-smd-meta-box-id', // ID attribute of metabox
                      __('Shortcode Generator for SMD', 'qc-smd'),       // Title of metabox visible to user
                      'smd_meta_box_callback', // Function that prints box in wp-admin
                      'page',              // Show box for posts, pages, custom, etc.
                      'side',            // Where on the page to show the box
                      'high' );            // Priority of box in display order
    }
}

if ( ! function_exists( 'smd_meta_box_callback' ) ) {
    function smd_meta_box_callback( $post ){
        ?>
        <p>
            <label for="sh_meta_box_bg_effect"><p><?php esc_html_e('Click the button below to generate shortcode', 'qc-smd'); ?></p></label>
    		<input type="button" id="smd_shortcode_generator_meta" class="button button-primary button-large" value="<?php esc_html_e('Generate Shortcode', 'qc-smd'); ?>" />
        </p>
        
        <?php
    }
}

//convert previous settings to new settings
add_action( 'plugins_loaded', 'smd_plugin_loaded_fnc' );
if ( ! function_exists( 'smd_plugin_loaded_fnc' ) ) {
    function smd_plugin_loaded_fnc(){

    	if(!get_option('smd_ot_convrt')){
    		$prevOptions = get_option('option_tree');
            if( !empty($prevOptions) ){		
        		if(array_key_exists('smd_enable_top_part', $prevOptions)){
        			
        			foreach($prevOptions as $key=>$val){
        				
        				update_option( $key, $val);
        			}
                }       
    		}		
    		add_option( 'smd_ot_convrt', 'yes');
    	}

    }
}

if ( ! function_exists( 'smd_activation_redirect' ) ) {
    function smd_activation_redirect( $plugin ) {
        $screen = get_current_screen();
        if( ( isset( $screen->base ) && $screen->base == 'plugins' ) && $plugin == plugin_basename( __FILE__ ) ) {
            if( 'cli' !== php_sapi_name() ){
                exit( wp_redirect( admin_url( 'edit.php?post_type=smd&page=smd_settings#help') ) );
            }
        }
    }
}
add_action( 'activated_plugin', 'smd_activation_redirect' );

if ( ! function_exists( 'smd_footer_custom_css' ) ) {
    function smd_footer_custom_css(){

    	$customCss = get_option( 'smd_custom_style' );

    	if( trim($customCss) != "" ) :
    ?>
    	<style type="text/css">
    		<?php echo trim($customCss); ?>
    	</style>

    <?php endif;

    }
}
add_action('wp_footer', 'smd_footer_custom_css', 500);


if( function_exists('register_block_type') ){
    function qcsmd_gutenberg_block() {
        require_once plugin_dir_path( __FILE__ ).'/gutenberg/smd-block/plugin.php';
    }
    add_action( 'init', 'qcsmd_gutenberg_block' );
}


$smd_feedback = new Wp_Usage_Feedback(
			__FILE__,
			'plugins@quantumcloud.com',
			false,
			true

		);