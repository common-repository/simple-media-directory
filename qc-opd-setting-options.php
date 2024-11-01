<?php
defined( 'ABSPATH' ) || die( 'Bye bye!' );
//Setting options page
/*******************************
 * Callback function to add the menu
 *******************************/
if ( ! function_exists( 'smd_show_settngs_page_callback_func' ) ) {
	function smd_show_settngs_page_callback_func(){
		add_submenu_page(
			'edit.php?post_type=smd',
			'Settings',
			'Settings',
			'manage_options',
			'smd_settings',
			'qcsmd_settings_page_callback_func'
		);
		add_action( 'admin_init', 'smd_register_plugin_settings' );
	} //show_settings_page_callback_func
}
add_action( 'admin_menu', 'smd_show_settngs_page_callback_func');

if ( ! function_exists( 'smd_register_plugin_settings' ) ) {
	function smd_register_plugin_settings() {


		$args = array(
			'type' => 'string', 
			'sanitize_callback' => 'sanitize_text_field',
			'default' => NULL,
		);	

		$args_email = array(
			'type' => 'string', 
			'sanitize_callback' => 'sanitize_email',
			'default' => NULL,
		);	

		//register our settings
		//general Section
		register_setting( 'qc-smd-plugin-settings-group', 'smd_enable_top_part', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_enable_upvote', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_add_new_button', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_add_item_link', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_enable_click_tracking', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_embed_credit_title', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_embed_credit_link', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_enable_scroll_to_top', $args );
		//Language Settings
		register_setting( 'qc-smd-plugin-settings-group', 'smd_lan_add_link', $args );
		register_setting( 'qc-smd-plugin-settings-group', 'smd_lan_share_list', $args );
		//custom css section
		register_setting( 'qc-smd-plugin-settings-group', 'smd_custom_style', $args );
		//custom js section
		register_setting( 'qc-smd-plugin-settings-group', 'smd_custom_js', $args );
		//help sectio
		
	}
}

if ( ! function_exists( 'qcsmd_settings_page_callback_func' ) ) {
	function qcsmd_settings_page_callback_func(){
		
		?>
		<div class="wrap swpm-admin-menu-wrap">
			<h1><?php esc_html_e( 'SMD Settings Page' , 'qc-smd' ); ?></h1>
		
			<div class="nav-tab-wrapper smd_nav_container">
				<a class="nav-tab smd_click_handle nav-tab-active" href="#general_settings"><?php esc_html_e( 'General Settings' , 'qc-smd' ); ?></a>
				<a class="nav-tab smd_click_handle" href="#language_settings"><?php esc_html_e( 'Language Settings' , 'qc-smd' ); ?></a>
				<a class="nav-tab smd_click_handle" href="#custom_css"><?php esc_html_e( 'Custom Css' , 'qc-smd' ); ?></a>
				<a class="nav-tab smd_click_handle" href="#custom_js"><?php esc_html_e( 'Custom Javascript' , 'qc-smd' ); ?></a>
				<a class="nav-tab smd_click_handle" href="#help"><?php esc_html_e( 'Help' , 'qc-smd' ); ?></a>
			</div>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'qc-smd-plugin-settings-group' ); ?>
				<?php do_settings_sections( 'qc-smd-plugin-settings-group' ); ?>
				<div id="general_settings">
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Enable Top Area' , 'qc-smd' ); ?></th>
							<td>
								<input type="checkbox" name="smd_enable_top_part" value="on" <?php echo (esc_attr( get_option('smd_enable_top_part') )=='on'?'checked="checked"':''); ?> />
								<i><?php esc_html_e( 'Top area includes Embed button (more options coming soon)' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Enable Upvote' , 'qc-smd' ); ?></th>
							<td>
								<input type="checkbox" name="smd_enable_upvote" value="on" <?php echo (esc_attr( get_option('smd_enable_upvote') )=='on'?'checked="checked"':''); ?> />
								<i><?php esc_html_e( 'Turn ON to visible Upvote feature for all templates.' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Enable Add New Button' , 'qc-smd' ); ?></th>
							<td>
								<input type="checkbox" name="smd_add_new_button" value="on" <?php echo (esc_attr( get_option('smd_add_new_button') )=='on'?'checked="checked"':''); ?> />
								
							</td>
						</tr>
						
						
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Add Button Link' , 'qc-smd' ); ?></th>
							<td>
								<input type="text" name="smd_add_item_link" size="100" value="<?php echo esc_attr( get_option('smd_add_item_link') ); ?>"  />
								<i><?php esc_html_e( 'Example: http://www.yourdomain.com' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						 
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Track Outbound Clicks' , 'qc-smd' ); ?></th>
							<td>
								<input type="checkbox" name="smd_enable_click_tracking" value="on" <?php echo (esc_attr( get_option('smd_enable_click_tracking') )=='on'?'checked="checked"':''); ?> />
								<i><?php esc_html_e( 'You need to have the analytics.js' , 'qc-smd' ); ?> [<a href="https://support.google.com/analytics/answer/1008080#GA" target="_blank"> <?php esc_html_e( 'Analytics tracking code in every page of your site' , 'qc-smd' ); ?></a>].</i>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Embed Credit Title' , 'qc-smd' ); ?></th>
							<td>
								<input type="text" name="smd_embed_credit_title" size="100" value="<?php echo esc_attr( get_option('smd_embed_credit_title') ); ?>"  />
								<i><?php esc_html_e( 'This text will be displayed below embedded list in other sites.' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Embed Credit Link' , 'qc-smd' ); ?></th>
							<td>
								<input type="text" name="smd_embed_credit_link" size="100" value="<?php echo esc_attr( get_option('smd_embed_credit_link') ); ?>"  />
								<i><?php esc_html_e( 'This text will be displayed below embedded list in other sites.' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Enable Scroll to Top Button' , 'qc-smd' ); ?></th>
							<td>
								<input type="checkbox" name="smd_enable_scroll_to_top" value="on" <?php echo (esc_attr( get_option('smd_enable_scroll_to_top') )=='on'?'checked="checked"':''); ?> />
								<i><?php esc_html_e( 'Show Scroll to Top.' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						
						
						
					</table>
				</div>
				<div id="language_settings" style="display:none">
					<table class="form-table">

						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Add New' , 'qc-smd' ); ?></th>
							<td>
								<input type="text" name="smd_lan_add_link" size="100" value="<?php echo esc_attr( get_option('smd_lan_add_link') ); ?>"  />
								<i><?php esc_html_e( 'Change the language for Add New' , 'qc-smd' ); ?></i>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Share List' , 'qc-smd' ); ?></th>
							<td>
								<input type="text" name="smd_lan_share_list" size="100" value="<?php echo esc_attr( get_option('smd_lan_share_list') ); ?>"  />
								<i><?php esc_html_e( 'Change the language for Share List' , 'qc-smd' ); ?></i>
							</td>
						</tr>

					</table>
				</div>
				<div id="custom_css" style="display:none">
					<table class="form-table">

						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Custom Css (Use *!important* flag if the changes does not take place)' , 'qc-smd' ); ?></th>
							<td>
								
								<textarea name="smd_custom_style" rows="10" cols="100"><?php echo esc_attr( get_option('smd_custom_style') ); ?></textarea>
								<p><i><?php esc_html_e( 'Write your custom CSS here. Please do not use' , 'qc-smd' ); ?> <b><?php esc_html_e( 'style' , 'qc-smd' ); ?></b> <?php esc_html_e( 'tag in this textarea.' , 'qc-smd' ); ?></i></p>
							</td>
						</tr>

					</table>
				</div>
				<div id="custom_js" style="display:none">
					<table class="form-table">

						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Custom Javascript' , 'qc-smd' ); ?></th>
							<td>
								
								<textarea name="smd_custom_js" rows="10" cols="100"><?php echo esc_attr( get_option('smd_custom_js') ); ?></textarea>
								<p><i><?php esc_html_e( 'Write your custom JS here. Please do not use' , 'qc-smd' ); ?> <b><?php esc_html_e( 'script' , 'qc-smd' ); ?></b> <?php esc_html_e( 'tag in this textarea.' , 'qc-smd' ); ?></i></p>
							</td>
						</tr>

					</table>
				</div>
				<div id="help" style="display:none">
					<table class="form-table">

						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Help' , 'qc-smd' ); ?></th>
							<td>
								<div class="wrap">
			
				<div id="poststuff">
				
					<div id="post-body" class="metabox-holder columns-2">
					
						<div id="post-body-content" style="position: relative;">
					
							<div class="clear"></div>
							
							<h1><?php esc_html_e( 'Welcome to the Simple Media Directory! You are' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'awesome' , 'qc-smd' ); ?></strong>, <?php esc_html_e( 'by the way' , 'qc-smd' ); ?> <img draggable="false" class="emoji" alt="🙂" src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/1f642.svg"></h1>
							<h3><?php esc_html_e( 'Getting Started' , 'qc-smd' ); ?></h3>
															
							<p><?php esc_html_e( 'Getting started with Simple Media Directory is super easy but the plugin works a little different from others - so an introduction is necessary. The most important thing to remember is that the' , 'qc-smd' ); ?> <strong> <?php esc_html_e( 'base pillars of this plugin are Lists' , 'qc-smd' ); ?></strong>, <?php esc_html_e( 'not individual links or categories. A list is simply a niche or subtopic to group your relevant links together. The most common use of SMD is to create and display multiple Lists of Links on specific topics or subtopics on the same page. Everything revolves around the Lists. Once you create a few Lists, you can then display them in many different ways.' , 'qc-smd' ); ?></p>

							<p><?php esc_html_e( 'With that in mind you should start with the following simple steps.' , 'qc-smd' ); ?></p>
							<br />

							<p> <span style="font-weight:bold;" >1.</span> <?php esc_html_e( 'Go to New List and create one by giving it a name. Then simply start adding List items or links by filling up the fields you want. Use the' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'Add New' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'button to add more Listings in your list.' , 'qc-smd' ); ?></p>

							<p> <br />  <span style="font-weight:bold;" >2.</span> <?php esc_html_e( 'Though you can just create one list and use the Single List mode. This directory plugin works the best when you' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'create a few Lists' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'each conatining about' , 'qc-smd' ); ?> <strong><?php esc_html_e( '15-20 List items' , 'qc-smd' ); ?></strong>. <?php esc_html_e( 'This is the most usual use case scenario. But you can do differently once you get the idea.' , 'qc-smd' ); ?></p>

							<p> <br /> <span style="font-weight:bold;" >3.</span> <?php esc_html_e( 'Now go to a page or post where you want to display the directory. On the right sidebar you will see a' , 'qc-smd' ); ?> <strong> <?php esc_html_e( 'ShortCode Generator' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'block. Click the button and a Popup LightBox will appear with all the options that you can select. Choose All Lists, and select a Style. Then Click Add Shortcode button. Shortcode will be generated. Simply' , 'qc-smd' ); ?> <strong> <?php esc_html_e( 'copy paste' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'that to a location on your page where you want the' , 'qc-smd' ); ?> <strong><?php esc_html_e( 'directory to show up' , 'qc-smd' ); ?></strong>.</p>

							<p> <br /> <?php esc_html_e( 'That’s it! The above steps are for the basic usages.' , 'qc-smd' ); ?>  <?php esc_html_e( 'If you had any specific questions about how something works, do not hesitate to contact us from the' , 'qc-smd' ); ?> <a href="<?php echo get_site_url().'/wp-admin/edit.php?post_type=smd&page=qcpro-promo-page-smd-free-page-123za'; ?>"><?php esc_html_e( 'Support Page' , 'qc-smd' ); ?></a>. <img draggable="false" class="emoji" alt="🙂" src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/1f642.svg"></p>
							
							<!-- <h3>Please take a quick look at our <a href="http://dev.quantumcloud.com/smd/tutorials/" class="button button-primary" target="_blank">Video Tutorials</a></h3> -->
							
							<h3><?php esc_html_e( 'Note' , 'qc-smd' ); ?></h3>
							<p><strong><?php esc_html_e( 'If you are having problem with adding more items or saving a list or your changes in the list are not getting saved then it is most likely because of a limitation set in your server. Your server has a limit for how many form fields it will process at a time. So, after you have added a certain number of links, the server refuses to save the List. The server’s configuration that dictates this is max_input_vars. You need to Set it to a high limit like max_input_vars = 15000. Since this is a server setting - you may need to contact your hosting company\'s support for this.' , 'qc-smd' ); ?></strong></p>

							<h3><?php esc_html_e( 'Shortcode Generator' , 'qc-smd' ); ?></h3>
							<p><?php esc_html_e( 'We encourage you to use the ShortCode generator found in the toolbar of your page/post editor in visual mode.' , 'qc-smd' ); ?></p>
		 	 				<img src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/classic.png" />
							
							<p><?php esc_html_e( 'See sample below for where to find it for Gutenberg.' , 'qc-smd' ); ?></p><br>
							<img src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/gutenburg.png" alt="shortcode generator">	
									
							<img src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/gutenburg2.png" alt="shortcode generator">	

							<p><?php esc_html_e( 'This is how the shortcode generator will look like.' , 'qc-smd' ); ?></p>	
							<br>			
							<img src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/shortcode-generator.png" alt="shortcode generator">							

							<div>
								<h3><?php esc_html_e( 'Shortcode Example' , 'qc-smd' ); ?></h3>
								
								<p>
									<strong><?php esc_html_e( 'You can use our given SHORTCODE GENERATOR to generate and insert shortcode easily, titled as "SMD" with WordPress content editor.' , 'qc-smd' ); ?></strong>
								</p>

								<p>
									<strong><u><?php esc_html_e( 'For all the lists:' , 'qc-smd' ); ?></u></strong>
									<br>
									<?php echo esc_attr( '[qcsmd-directory mode="all" column="2" style="simple" orderby="date" order="DESC" enable_embedding="false"]' , 'qc-smd' ); ?>
									<br>
									<br>
									<strong><u><?php esc_html_e( 'For only a single list:' , 'qc-smd' ); ?></u></strong>
									<br>
									<?php echo esc_attr( '[qcsmd-directory mode="one" list_id="75"]' , 'qc-smd' ); ?>
									<br>
									<br>
									<strong><u><?php esc_html_e( 'Available Parameters:' , 'qc-smd' ); ?></u></strong>
									<br>
								</p>
								<p>
									<strong><?php esc_html_e( '1. mode' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Value for this option can be set as "one" or "all".' , 'qc-smd' ); ?>
								</p>
								<p>
									<strong><?php esc_html_e( '2. column' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Avaialble values: "1", "2", "3" or "4".' , 'qc-smd' ); ?>
								</p>
								<p>
									<strong><?php esc_html_e( '3. style' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Avaialble values: "simple", "style-1", "style-2", "style-3".' , 'qc-smd' ); ?>
									<br>
								</p>
								<p>
									<strong><?php esc_html_e( '4. orderby' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( "Compatible order by values: 'ID', 'author', 'title', 'name', 'type', 'date', 'modified', 'rand' and 'menu_order'." , 'qc-smd' ); ?>
								</p>
								<p>
									<strong><?php esc_html_e( '5. order' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Value for this option can be set as "ASC" for Ascending or "DESC" for Descending order.' , 'qc-smd' ); ?>
								</p>
								<p>
									<strong><?php esc_html_e( '6. item_orderby' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Value for this option are "title", "upvotes", "timestamp" that will be set as "ASC" & others will be "DESC" order.' , 'qc-smd' ); ?>
								</p>
								<p>
									<strong><?php esc_html_e( '7. list_id' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Only applicable if you want to display a single list [not all]. You can provide specific list id here as a value. You can also get ready shortcode for a single list under "Manage List Items" menu.' , 'qc-smd' ); ?>
								</p>
								
								<p>
									<strong><?php esc_html_e( '8. enable_embedding' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Allow visitors to embed list in other sites. Supported values - "true", "false".' , 'qc-smd' ); ?>
									<br>
									<?php esc_html_e( 'Example: enable_embedding="true"' , 'qc-smd' ); ?>
								</p>
								<p>
									<strong><?php esc_html_e( '8. upvote' , 'qc-smd' ); ?></strong>
									<br>
									<?php esc_html_e( 'Allow visitors to list item. Supported values - "on", "off".' , 'qc-smd' ); ?>
									<br>
									<?php esc_html_e( 'Example: upvote="on"' , 'qc-smd' ); ?>
								</p>
							</div>

							<div style="padding: 15px 10px; border: 1px solid #ccc; text-align: center; margin-top: 20px;">
								 <?php esc_html_e( 'Crafted By:' , 'qc-smd' ); ?> <a href="<?php echo esc_url('http://www.quantumcloud.com'); ?>" target="_blank"> <?php esc_html_e( 'Web Design Company' , 'qc-smd' ); ?></a> -  <?php echo esc_attr( 'QuantumCloud' , 'qc-smd' ); ?>
							</div>
							
						  </div>
						  <!-- /post-body-content -->	
						  
						  

						</div>
						<!-- /post-body-->

					</div>
					<!-- /poststuff -->

				</div>
								
							</td>
						</tr>

					</table>
				</div>
				
				<?php submit_button(); ?>

			</form>
			
		</div>
	
		
		<?php
		
	}
}