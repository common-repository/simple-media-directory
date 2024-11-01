<?php
/*
* QuantumCloud Promo + Support Page
* Revised On: 06-01-2017
*/

/*******************************
 * Add Ajax Object at the head part
 *******************************/
add_action('wp_head', 'qc_process_support_form_ajax_header');

if( !function_exists('qc_process_support_form_ajax_header') ) {

	function qc_process_support_form_ajax_header() {

	   echo '<script type="text/javascript">
	           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
	         </script>';

	} //End of qc_process_support_form_ajax_header

} //End of function_exists

/*******************************
 * Handle Ajex Request for Form Processing
 *******************************/
add_action( 'wp_ajax_process_qc_promo_form', 'process_qc_promo_form' );

if( !function_exists('process_qc_promo_form') ) {

	function process_qc_promo_form() {
		
		$data['status'] = 'failed';
		$data['message'] = __('Problem in processing your form submission request! Apologies for the inconveniences.<br> Please email to <span style="color:#22A0C9;font-weight:bold !important;font-size:14px "> quantumcloud@gmail.com </span> with any feedback. We will get back to you right away!', 'qc-smd');

		$name 			= isset( $_POST['post_name'] ) 			? trim(sanitize_text_field($_POST['post_name'])) : '';
		$email 			= isset( $_POST['post_email'] ) 		? trim(sanitize_email($_POST['post_email'])) : '';
		$subject 		= isset( $_POST['post_subject'] ) 		? trim(sanitize_text_field($_POST['post_subject'])) : '';
		$message 		= isset( $_POST['post_message'] ) 		? trim(sanitize_text_field($_POST['post_message'])) : '';
		$plugin_name 	= isset( $_POST['post_plugin_name'] ) 	? trim(sanitize_text_field($_POST['post_plugin_name'])) : '';

		if( $name == "" || $email == "" || $subject == "" || $message == "" )
		{
			$data['message'] = esc_html('Please fill up all the requried form fields.', 'qc-smd' );
		}
		else if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false ) 
		{
			$data['message'] = esc_html('Invalid email address.', 'qc-smd' );
		}
		else
		{

			//build email body

			$bodyContent = "";
				
			$bodyContent .= "<p><strong>".esc_html('Support Request Details:', 'qc-smd')."</strong></p><hr>";

			$bodyContent .= "<p> ".esc_html('Name', 'qc-smd')." : ".esc_html($name)."</p>";
			$bodyContent .= "<p>".esc_html('Email', 'qc-smd')." : ".esc_html($email)."</p>";
			$bodyContent .= "<p>".esc_html('Subject', 'qc-smd')." : ".esc_html($subject)."</p>";
			$bodyContent .= "<p>".esc_html('Message', 'qc-smd')." : ".esc_html($message)."</p>";

			$bodyContent .= "<p>".esc_html('Sent Via the Plugin:', 'qc-smd')." ".esc_html($plugin_name)."</p>";

			$bodyContent .="<p></p><p>".esc_html('Mail sent from: ', 'qc-smd')." <strong>".get_bloginfo('name')."</strong>, URL: [".get_bloginfo('url')."].</p>";
			$bodyContent .="<p>".esc_html('Mail Generated on:', 'qc-smd')." " . date("F j, Y, g:i a") . "</p>";			
			
			$toEmail = "quantumcloud@gmail.com"; //Receivers email address
			//$toEmail = "qc.kadir@gmail.com"; //Receivers email address

			//Extract Domain
			$url = get_site_url();
			$url = parse_url($url);
			$domain = $url['host'];
			

			$fakeFromEmailAddress = "wordpress@" . $domain;
			
			$to = $toEmail;
			$body = $bodyContent;
			$headers = array();
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = 'From: '.$name.' <'.$fakeFromEmailAddress.'>';
			$headers[] = 'Reply-To: '.$name.' <'.$email.'>';

			$finalSubject = esc_html('From Plugin Support Page: ', 'qc-smd'). " " . $subject;
			
			$result = wp_mail( $to, $finalSubject, $body, $headers );

			if( $result )
			{
				$data['status'] = 'success';
				$data['message'] = __('Your email was sent successfully. Thanks!', 'qc-smd');
			}

		}

		ob_clean();

		
		echo json_encode($data);
	
		die();
	}
}





/*******************************
 * Main Class to Display Support
 * form and the promo pages
 *******************************/

if( !class_exists('QcSMDSupportAndPromoPage') ){


	class QcSMDSupportAndPromoPage{
	
		public $plugin_menu_slug = "";
		public $plugin_slug = "smd"; //Should be unique, like: qcsmd_p123
		public $promo_page_title = 'More WordPress Goodies for You!';
		public $promo_menu_title = 'Support';
		public $plugin_name = '';
		
		public $page_slug = "";
		
		public $relative_folder_url;
		
		//public $relative_folder_url = plugin_dir_url( __FILE__ );
		
		function __construct( $plugin_slug = null )
		{
			/*
			if(!function_exists('wp_get_current_user')) {
				include(ABSPATH . "wp-includes/pluggable.php"); 
			}
			*/
			
			$this->page_slug = 'qcpro-promo-page-' . $plugin_slug;
			$this->relative_folder_url = plugin_dir_url( __FILE__ );
			
			add_action('admin_enqueue_scripts', array(&$this, 'include_promo_page_scripts'));
			
			//add_action( 'wp_ajax_process_qc_promo_form', array(&$this,'process_qc_promo_form') );
			
		} //End of Constructor
		
		function include_promo_page_scripts( $hook )
		{                                 
		   
		   wp_enqueue_script( 'jquery' );
		   wp_enqueue_script( 'jquery-ui-core');
		   wp_enqueue_script( 'jquery-ui-tabs' );
		   wp_enqueue_script( 'smd-custom-form-processor', $this->relative_folder_url . '/js/support-form-script.js',  array('jquery', 'jquery-ui-core','jquery-ui-tabs') );
		   
		}
		
		function show_promo_page()
		{
		
			if( $this->plugin_menu_slug == "" ){
			   return;
			}
			
			add_action( 'admin_menu', array(&$this, 'show_promo_page_callback_func') );
			
		  
		} //End of function show_promo_page
		
		/*******************************
		 * Callback function to add the menu
		 *******************************/
		function show_promo_page_callback_func()
		{
			add_submenu_page(
				$this->plugin_menu_slug,
				$this->promo_page_title,
				$this->promo_menu_title,
				'manage_options',
				$this->page_slug,
				array(&$this, 'qcsmd_promo_support_page_callback_func' )
			);
		} //show_promo_page_callback_func
		
		/*******************************
		 * Callback function to show the HTML
		 *******************************/
		function qcsmd_promo_support_page_callback_func()
		{
			
			?>
				<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
				<link href="<?php echo $this->relative_folder_url; ?>/css/font-awesome.min.css" rel="stylesheet" type="text/css">
				<link href="<?php echo $this->relative_folder_url; ?>/css/style.css" rel="stylesheet" type="text/css">
				<link href="<?php echo $this->relative_folder_url; ?>/css/responsive.css" rel="stylesheet" type="text/css">
				
				<div class="qc_support_container"><!--qc_support_container-->
	
    <div class="qc_tabcontent clearfix-div">
    	<div class="qc-row">
            <div class="support-btn-main clearfix-div">
                 <div class="qc-column-12">
					<h4><?php esc_html_e( 'Bug report, feature request or any feedback – we are here for you.' , 'qc-smd' ); ?></h4>
                    <div class="support-btn">
                        <a class="free-support" href="<?php echo esc_url('https://www.quantumcloud.com/resources/free-support/'); ?>" target="_blank"><?php esc_html_e( 'Free Support' , 'qc-smd' ); ?></a>
                    </div>
                </div>
            
                <div class="qc-column-12">
					<h4><?php esc_html_e( 'All our Pro Version users get Premium, Guaranteed Quick, One on One Priority Support.' , 'qc-smd' ); ?></h4>
                    <div class="support-btn">
                        <a class="premium-support" href="<?php echo esc_url('https://qc.ticksy.com/'); ?>" target="_blank"><?php esc_html_e( 'GET PRIORITY SUPPORT' , 'qc-smd' ); ?></a>
                    </div>
					<div class="support-btn">
                        <a class="premium-support" href="javascript:void(0);" target="_blank"><?php esc_html_e( 'Upgrade to Pro' , 'qc-smd' ); ?></a>
                    </div>
                </div>

            </div>
			<h2 class="plugin-title" style="text-align: center;margin-bottom: 60px;"><?php esc_html_e( 'Check Out Some of Our Other Works that Might Make Your Website Better' , 'qc-smd' ); ?></h2>
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/infographic-maker-ilist/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/iList-icon-256x256.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/infographic-maker-ilist/'); ?>" target="_blank"><?php esc_html_e( 'InfoGraphic Maker – iList' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'iList is first of its kind' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'InfoGraphic maker' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'WordPress plugin to create Infographics and elegant Lists effortlessly to visualize data. It is a must have content creation and content curation tool.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/slider-hero/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/slider-hero-icon-256x256.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/slider-hero/'); ?>" target="_blank"><?php esc_html_e( 'Slider Hero' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'Slider Hero is a unique slider plugin that allows you to create' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'Cinematic Product Intro Adverts' , 'qc-smd' ); ?></strong>  <?php esc_html_e( 'and' , 'qc-smd' ); ?>
                        <strong><?php esc_html_e( 'Hero sliders' , 'qc-smd' ); ?></strong> <?php esc_html_e( ' with great Javascript animation effects.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/simple-link-directory/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/sld-icon-256x256.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/simple-link-directory/'); ?>" target="_blank"><?php esc_html_e( 'Simple Link Directory' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'Directory plugin with a unique approach! Simple Media Directory is an advanced WordPress Directory plugin for One Page 
                        directory and Content Curation solution.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/simple-business-directory/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/icon.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/simple-business-directory/'); ?>" target="_blank"><?php esc_html_e( 'Simple Business Directory' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'This innovative and powerful, yet' , 'qc-smd' ); ?><strong> <?php esc_html_e( 'Simple  Multi-purpose Business Directory' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'WordPress PlugIn allows you to create comprehensive Lists of Businesses with maps and tap to call features.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/woocommerce-chatbot-woowbot/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/wowbot-logo.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/woocommerce-chatbot-woowbot/'); ?>" target="_blank"><?php esc_html_e( 'WoowBot WooCommerce ChatBot' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'WooWBot is a stand alone WooCommerce Chat Bot with zero configuration or bot training required. This plug and play chatbot also does not require 
                        any 3rd party service integration like Facebook.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/woocommerce-shop-assistant-jarvis/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/jarvis-icon-256x256.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/woocommerce-shop-assistant-jarvis/'); ?>" target="_blank"><?php esc_html_e( 'WooCommerce Shop Assistant' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'WooCommerce Shop Assistant' , 'qc-smd' ); ?> – <strong><?php esc_html_e( 'JARVIS' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'shows recent user activities, provides advanced search, floating cart, featured products, store notifications, order notifications – all in one place for easy access by buyer and make quick decisions.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/portfolio-x-plugin/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/portfolio-x-logo-dark-2.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/portfolio-x-plugin/'); ?>" target="_blank"><?php esc_html_e( 'Portfolio X' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'Portfolio X is an advanced, responsive portfolio with streamlined workflow and unique designs and templates to show your works or projects.' , 'qc-smd' ); ?>&nbsp;<strong>
                        <?php esc_html_e( 'Portfolio Showcase' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'and' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'Portfolio Widgets' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'are included.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/woo-tabbed-category-product-listing/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/woo-tabbed-icon-256x256.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/woo-tabbed-category-product-listing/'); ?>" target="_blank"><?php esc_html_e( 'Woo Tabbed Category Products' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'WooCommerce plugin that allows you to showcase your products category wise in tabbed format. This is a unique woocommerce plugin that lets dynaimically load your products in tabs based on your product categories .' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/knowledgebase-helpdesk/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/knowledge-base-1.jpg" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/knowledgebase-helpdesk/'); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'KnowledgeBase HelpDesk' , 'qc-smd' ); ?></a></h4>
                        <p><p><?php esc_html_e( 'KnowledgeBase HelpDesk is an advanced Knowledgebase plugin with helpdesk' , 'qc-smd' ); ?><strong>, </strong><?php esc_html_e( 'glossary and FAQ features all in one. KnowledgeBase HelpDesk is extremely simple and easy to use.' , 'qc-smd' ); ?></p></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
            
            <div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/express-shop/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/express-shop.png" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/express-shop/'); ?>" target="_blank"><?php esc_html_e( 'Express Shop' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'Express Shop is a WooCommerce addon to show all products in one page. User can add products to cart and go to checkout. 
						Filtering and search integrated in single page.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
			
			
			<div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/seo-help/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/seo-help.jpg" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/seo-help/'); ?>" target="_blank"><?php esc_html_e( 'SEO Help' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'SEO Help is a unique WordPress plugin to help you write better Link Bait titles. The included LinkBait title generator will take the 
						WordPress post title as Subject and generate alternative ClickBait titles for you to choose from.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
			
			
			<div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/ichart/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/ichart-300x300.jpg" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/ichart/'); ?>" target="_blank"><?php esc_html_e( 'iChart – Easy Charts and Graphs' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'Charts and graphs are now easy to build and add to any WordPress page with just a few clicks and shortcode generator.
						iChart is a Google chartjs implementation to add graphs' , 'qc-smd' ); ?> &amp; 
						<strong><?php esc_html_e( 'charts' , 'qc-smd' ); ?></strong> – <?php esc_html_e( 'directly from WordPress Visual editor.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
			
			
			<div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/analytics-tracking/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/PageSpeed-Friendly-Analytics-Tracking-1-300x300.jpg" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/analytics-tracking/'); ?>" target="_blank"><?php esc_html_e( 'PageSpeed Friendly Analytics Tracking' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'QuantumCloud PageSpeed Friendly Analytics Tracking for Google does the simple job of adding tracking code to your 
						WordPress website in all pages.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
			
			
			<div class="qc-column-4"><!-- qc-column-4 -->
                <!-- Feature Box 1 -->
                <div class="support-block ">
                    <div class="support-block-img">
                        <a href="<?php echo esc_url('https://www.quantumcloud.com/products/comment-link-remove/'); ?>" target="_blank"> <img src="<?php echo esc_url( QCSMD_URL ); ?>/qc-support-promo-page/images/Comment-Link-Remove-300x300.jpg" alt=""></a>
                    </div>
                    <div class="support-block-info">
                        <h4><a href="<?php echo esc_url('https://www.quantumcloud.com/products/comment-link-remove/'); ?>" target="_blank"><?php esc_html_e( 'Comment Link Remove' , 'qc-smd' ); ?></a></h4>
                        <p><?php esc_html_e( 'All in one solution to fight comment spammers. Tired of deleting useless spammy comments from your WordPress blog posts? Comment Link Remove WordPress 
						plugin removes author link and any other links from the user comments.' , 'qc-smd' ); ?></p>

                    </div>
                </div>
            </div><!--/qc-column-4 -->
            
             
            
        </div>
        <!--qc row-->
    </div>
    
    
    
    

</div><!--qc_support_container-->
				
			<?php
		} //End of qcsmd_promo_support_page_callback_function
		
		
	
	} //End of the class QcSMDSupportAndPromoPage


} //End of class_exists


/*
* Create Instance, set instance variables and then call appropriate worker.
*/

//Supply Unique Promo Page Slug as the constructor parameter of the class QcSMDSupportAndPromoPage. ex: smd-page-2124a to the constructor

//Please create an unique instance for your use, example: $instance_smdf2

$instance_smdf = new QcSMDSupportAndPromoPage('smd-free-page-123za');

if( is_admin() )
{
	$instance_smdf->plugin_menu_slug = "edit.php?post_type=smd";
	$instance_smdf->plugin_name = "SMD - Free Version";
	$instance_smdf->show_promo_page();
}
