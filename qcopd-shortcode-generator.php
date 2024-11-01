<?php

defined( 'ABSPATH' ) || die( 'Bye bye!' );

/*TinyMCE Shortcode Generator Button - 25-01-2017*/
if ( ! function_exists( 'qcsmd_tinymce_shortcode_button_function' ) ) {
	function qcsmd_tinymce_shortcode_button_function() {
		add_filter ("mce_external_plugins", "qcsmd_shortcode_generator_btn_js");
		add_filter ("mce_buttons", "qcsmd_shortcode_generator_btn");
	}
}

if ( ! function_exists( 'qcsmd_shortcode_generator_btn_js' ) ) {
	function qcsmd_shortcode_generator_btn_js($plugin_array) {
		$plugin_array['qcsmd_shortcode_btn'] = plugins_url('assets/js/qcsmd-tinymce-button.js', __FILE__);
		return $plugin_array;
	}
}

if ( ! function_exists( 'qcsmd_shortcode_generator_btn' ) ) {
	function qcsmd_shortcode_generator_btn($buttons) {
		array_push ($buttons, 'qcsmd_shortcode_btn');
		return $buttons;
	}
}

add_action ('init', 'qcsmd_tinymce_shortcode_button_function');

if ( ! function_exists( 'qcsmd_load_custom_wp_admin_style_free' ) ) {
	function qcsmd_load_custom_wp_admin_style_free() {
	        wp_register_style( 'smd_shortcode_gerator_css', QCSMD_ASSETS_URL . '/css/shortcode-modal.css', false, '1.0.0' );
	        wp_enqueue_style( 'smd_shortcode_gerator_css' );
	}
}
add_action( 'admin_enqueue_scripts', 'qcsmd_load_custom_wp_admin_style_free' );

if ( ! function_exists( 'qcsmd_render_shortcode_modal_free' ) ) {
	function qcsmd_render_shortcode_modal_free() {

		check_ajax_referer( 'ajax_validation_18', 'security' );

		?>

		<div id="sm-modal" class="modal">

			<!-- Modal content -->
			<div class="modal-content">
			
				<span class="close">
					<span class="dashicons dashicons-no"></span>
				</span>
				<h3> 
					<?php esc_html_e( 'SMD - Shortcode Generator' , 'qc-smd' ); ?></h3>
				<hr/>
				
				<div class="sm_shortcode_list">

					<div class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'Mode' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_mode">
							<option value="all"><?php esc_html_e( 'All List' , 'qc-smd' ); ?></option>
							<option value="one"><?php esc_html_e( 'One List' , 'qc-smd' ); ?></option>

						</select>
					</div>
					
					<div id="smd_list_div" class="qcsmd_single_field_shortcode hidden-div">
						<label style="width: 200px;display: inline-block;">
							 <?php esc_html_e( 'Select List' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_list_id">
						
							<option value=""><?php esc_html_e( 'Please Select List' , 'qc-smd' ); ?></option>
							
							<?php
							
								$ilist = new WP_Query( array( 'post_type' => 'smd', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC') );
								if( $ilist->have_posts()){
									while( $ilist->have_posts() ){
										$ilist->the_post();
							?>
							
							<option value="<?php echo esc_attr(get_the_ID()); ?>"><?php echo esc_html(get_the_title()); ?></option>
							
							<?php } } ?>
							
						</select>
					</div>
					
					<div id="smd_list_cat" class="qcsmd_single_field_shortcode hidden-div">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'List Category' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_list_cat_id">
						
							<option value=""><?php esc_html_e( 'Please Select Category' , 'qc-smd' ); ?></option>
							
							<?php
							
								$terms = get_terms( 'smd_cat', array(
									'hide_empty' => true,
								) );
								if( $terms ){
									foreach( $terms as $term ){
							?>
							
							<option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
							
							<?php } } ?>
							
						</select>
					</div>
					
					<div class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'Template Style' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_style">
							<option value="simple"><?php esc_html_e( 'Default Style' , 'qc-smd' ); ?></option>
							<option value="style-1"><?php esc_html_e( 'Style 01' , 'qc-smd' ); ?></option>
							<option value="style-2"><?php esc_html_e( 'Style 02' , 'qc-smd' ); ?></option>
							<option value="style-3"><?php esc_html_e( 'Style 03' , 'qc-smd' ); ?></option>
						</select>
						
						<div id="demo-preview-link">
							 <?php esc_html_e( 'Demo URL:' , 'qc-smd' ); ?>
							<div id="demo-url">
								<a href="<?php echo esc_url('http://dev.quantumcloud.com/simple-media-directory/'); ?>" target="_blank"><?php echo esc_url('http://dev.quantumcloud.com/simple-media-directory/'); ?></a>
							</div>
						</div>
						
					</div>
					
					<div id="smd_column_div" class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'Column' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_column">
							<option value="1"><?php esc_html_e( 'Column 1' , 'qc-smd' ); ?></option>
							<option value="2"><?php esc_html_e( 'Column 2' , 'qc-smd' ); ?></option>
							<option value="3"><?php esc_html_e( 'Column 3' , 'qc-smd' ); ?></option>
							<option value="4"><?php esc_html_e( 'Column 4' , 'qc-smd' ); ?></option>
						</select>
					</div>
	                <div class="qcsmd_single_field_shortcode">
	                    <label style="width: 200px;display: inline-block;">
	                        <?php esc_html_e( 'Title Font Size' , 'qc-smd' ); ?>
	                    </label>
	                    <select style="width: 225px;" id="smd_title_font_size">
	                        <option value=""><?php esc_html_e( 'Default' , 'qc-smd' ); ?></option>
							<?php
							for($i=10;$i<50;$i++){
							?>
								<option value="<?php echo esc_attr( $i.'px'); ?>"> <?php echo esc_html( $i.'px'); ?> </option>
							<?php } ?>
	                    </select>
	                </div>

	                <div class="qcsmd_single_field_shortcode">
	                    <label style="width: 200px;display: inline-block;">
	                        <?php esc_html_e( 'Title Line Height' , 'qc-smd' ); ?>
	                    </label>
	                    <select style="width: 225px;" id="smd_title_line_height">
	                        <option value=""><?php esc_html_e( 'Default' , 'qc-smd' ); ?></option>
							<?php
							for($i=10;$i<50;$i++){
							?>
								<option value="<?php echo esc_attr( $i.'px'); ?>"> <?php echo esc_html( $i.'px'); ?> </option>
							<?php } ?>
	                    </select>
	                </div>

	                <div class="qcsmd_single_field_shortcode">
	                    <label style="width: 200px;display: inline-block;">
	                        <?php esc_html_e( 'Subtitle Font Size' , 'qc-smd' ); ?>
	                    </label>
	                    <select style="width: 225px;" id="smd_subtitle_font_size">
	                        <option value=""><?php esc_html_e( 'Default' , 'qc-smd' ); ?></option>
							<?php
							for($i=10;$i<50;$i++){
							?>
								<option value="<?php echo esc_attr( $i.'px'); ?>"> <?php echo esc_html( $i.'px'); ?> </option>
							<?php } ?>
	                    </select>
	                </div>



	                <div class="qcsmd_single_field_shortcode">
	                    <label style="width: 200px;display: inline-block;">
	                        <?php esc_html_e( 'Subtitle Line Height' , 'qc-smd' ); ?>
	                    </label>
	                    <select style="width: 225px;" id="smd_subtitle_line_height">
	                        <option value=""><?php esc_html_e( 'Default' , 'qc-smd' ); ?></option>
							<?php
							for($i=10;$i<50;$i++){
							?>
								<option value="<?php echo esc_attr( $i.'px'); ?>"> <?php echo esc_html( $i.'px'); ?> </option>
							<?php } ?>
	                    </select>
	                </div>
					<div id="smd_orderby_div" class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'Order By' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_orderby">
							<option value="date"><?php esc_html_e( 'Date' , 'qc-smd' ); ?></option>
							<option value="ID"><?php esc_html_e( 'ID' , 'qc-smd' ); ?></option>
							<option value="title"><?php esc_html_e( 'Title' , 'qc-smd' ); ?></option>
							<option value="modified"><?php esc_html_e( 'Date Modified' , 'qc-smd' ); ?></option>
							<option value="rand"><?php esc_html_e( 'Random' , 'qc-smd' ); ?></option>
							<option value="menu_order"><?php esc_html_e( 'Menu Order' , 'qc-smd' ); ?></option>
						</select>
					</div>
					
					<div id="smd_order_div" class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'Order' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_order">
							<option value="ASC"><?php esc_html_e( 'Ascending' , 'qc-smd' ); ?></option>
							<option value="DESC"><?php esc_html_e( 'Descending' , 'qc-smd' ); ?></option>
						</select>
					</div>
					<div class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
							<?php esc_html_e( 'Item Orderby' , 'qc-smd' ); ?>
						</label>
						<select style="width: 225px;" id="smd_itemorderby">
							<option value="menu_order"><?php esc_html_e( 'Menu Order' , 'qc-smd' ); ?></option>
							<option value="title"><?php esc_html_e( 'Title' , 'qc-smd' ); ?></option>
							<option value="upvotes"><?php esc_html_e( 'Upvotes' , 'qc-smd' ); ?></option>
							<option value="timestamp"><?php esc_html_e( 'Date Modified' , 'qc-smd' ); ?></option>
						</select>
					</div>
					<div class="qcsmd_single_field_shortcode checkbox-smd">
						<label>
							<input class="smd_embeding" name="ckbox" value="true" type="checkbox">
							<?php esc_html_e( 'Enable Embeding' , 'qc-smd' ); ?>
						</label>
					</div>
					
					<div class="qcsmd_single_field_shortcode">
						<label style="width: 200px;display: inline-block;">
						</label>
						<input class="smd-sc-btn" type="button" id="qcsmd_add_shortcode" value="Generate Shortcode" />
					</div>
					
				</div>
				<div class="smd_shortcode_container" style="display:none;">
					<div class="qcsmd_single_field_shortcode">
						<textarea style="width:100%;height:200px" id="smd_shortcode_container"></textarea>
						<p><b><?php esc_html_e( 'Copy' , 'qc-smd' ); ?></b> <?php esc_html_e( 'the shortcode & use it any text block.' , 'qc-smd' ); ?> <button class="smd_copy_close button button-primary button-small" style="float:right"> <?php esc_html_e( 'Copy & Close' , 'qc-smd' ); ?></button></p>
					</div>
				</div>
			</div>

		</div>
		<?php
		exit;
	}
}

add_action( 'wp_ajax_show_qcsmd_shortcodes', 'qcsmd_render_shortcode_modal_free');
