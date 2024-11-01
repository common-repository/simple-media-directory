<?php


$embed_link_button = 1;

/*Load Embed Scripts*/
add_action('wp_enqueue_scripts', 'qcsmd_load_embed_scripts');
if ( ! function_exists( 'qcsmd_load_embed_scripts' ) ) {
  function qcsmd_load_embed_scripts() {
  	
  	wp_enqueue_style('qcsmd-embed-form-css', QCSMD_URL . '/embed/css/embed-form.css');

    wp_enqueue_script('qcsmd-embed-form-script', QCSMD_URL . '/embed/js/embed-form.js', array('jquery'));

  }
}


// Load template for embed link page url
if ( ! function_exists( 'qcsmd_load_embed_link_template' ) ) {
  function qcsmd_load_embed_link_template($template) {
      if (is_page('embed-media')) {
          return dirname(__FILE__) . '/qcsmd-embed-link.php';
      }
      return $template;
  }
}

add_filter('template_include', 'qcsmd_load_embed_link_template', 99);


// Create embed page when plugin install or activate

//register_activation_hook(__FILE__, 'qcsmd_create_embed_page');
//add_action('init', 'qcsmd_create_embed_page');
if ( ! function_exists( 'qcsmd_create_embed_page' ) ) {
  function qcsmd_create_embed_page() {

    $query = new WP_Query(
        array(
            'post_type'              => 'page',
            'title'                  => 'Embed Media',
            'post_status'            => 'all',
            'posts_per_page'         => 1,
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'orderby'                => 'post_date ID',
            'order'                  => 'ASC',
        )
    );

    $page_got_by_title = !empty( $query->post ) ? $query->post : null;

    if ( $page_got_by_title == NULL) {
      //if (get_page_by_title('Embed Media') == NULL) {
          //post status and options
          $post = array(
              'comment_status'  => 'closed',
              'ping_status'     => 'closed',
              'post_author'     => get_current_user_id(),
              'post_date'       => date('Y-m-d H:i:s'),
              'post_status'     => 'publish',
              'post_title'      => 'Embed Media',
              'post_type'       => 'page',
          );
          //insert page and save the id
          $embedPost = wp_insert_post($post, false);
          //save the id in the database
          update_option('hclpage', $embedPost);
      }
  }
}

if ($embed_link_button == 1) {
    add_action('qcsmd_attach_embed_btn', 'qcsmd_custom_embedder');
}

if ( ! function_exists( 'qcsmd_custom_embedder' ) ) {
  function qcsmd_custom_embedder($shortcodeAtts) {
      global $post;
  	
  	$site_title = get_bloginfo('title');
  	$site_link = get_bloginfo('url');

  	if( get_option( 'smd_embed_credit_title' ) != "" ){
  		$site_title = get_option( 'smd_embed_credit_title' );
  	}

  	if( get_option( 'smd_embed_credit_link' ) != "" ){
  		$site_link = get_option( 'smd_embed_credit_link' );
  	}
  	
      $pagename = $post->post_name;

      if ($pagename != 'embed-link') {
  	
          ?>
  <div style="text-align: right;border-bottom: 1px solid #ddd;padding-bottom: 10px;margin-bottom: 10px;">




  <?php if(get_option( 'smd_add_new_button' )=='on' && get_option( 'smd_add_item_link' )!=''): ?>
  <a style="" href="<?php echo get_option( 'smd_add_item_link' ); ?>" class="button-link cls-embed-btn">
  <?php 
  	if(get_option('smd_lan_add_link')!=''){
  		echo get_option('smd_lan_add_link');
  	}else{
  		_e( 'Add New', 'qc-smd' ); 
  	}
  ?>
  </a>
  <?php endif; ?>

  <?php if($shortcodeAtts['enable_embedding'] == 'true'): ?>
  <a class="button-link js-open-modal cls-embed-btn" href="#" data-modal-id="popup"
             data-url="<?php esc_url(bloginfo('url')); ?>/embed-link"
             data-order="<?php echo esc_attr($shortcodeAtts['order']); ?>"
             data-mode="<?php echo esc_attr($shortcodeAtts['mode']); ?>"
             data-list-id="<?php echo esc_attr($shortcodeAtts['list_id']); ?>"
             data-column="<?php echo esc_attr($shortcodeAtts['column']); ?>"
             data-style="<?php echo esc_attr($shortcodeAtts['style']); ?>"
             data-category="<?php echo esc_attr($shortcodeAtts['category']); ?>" 
  		       data-credittitle="<?php echo esc_attr($site_title); ?>"
             data-creditlink="<?php echo esc_url($site_link); ?>"> 
  			<?php 
  				if(get_option('smd_lan_share_list')!=''){
  					echo get_option('smd_lan_share_list');
  				}else{
  					echo __('Share List', 'qc-smd') ;
  				}
  			 ?>
  		   <i class="fa fa-share-alt"></i> </a>
  <?php endif; ?>

  <div id="popup" class="smd-embed-modal modal-box">
    <header> <a href="#" class="js-modal-close close">×</a>
      <h3><?php esc_html_e( 'Generate Embed Code For This List' , 'qc-smd' ); ?></h3>
    </header>
    <div class="modal-body">
      <div class="iframe-css">
        <div class="iframe-main">
          <div class="ifram-row">
            <div class="ifram-sm">
  			<span><?php esc_html_e( "Width: (in '%' or 'px')" , 'qc-smd' ); ?></span>
  			<input id="igwidth" name="igwidth" type="text" value="100">
  		</div>
  		<div class="ifram-sm" style="width: 70px;">
  			<span>&nbsp;</span>
  			<select name="igsizetype" class="iframe-main-select">
  				<option value="%"><?php esc_html_e( '%' , 'qc-smd' ); ?></option>
  				<option value="px"><?php esc_html_e( 'px' , 'qc-smd' ); ?></option>
  			</select>
  		</div>
  		<div class="ifram-sm">
  			<span><?php esc_html_e( 'Height: (in "px")' , 'qc-smd' ); ?></span>
  			<input id="igheight" name="igheight" type="text" value="400">
  		</div>
            <div class="ifram-sm"> <span>&nbsp;</span> <a class="btn icon icon-code" id="generate-igcode" onclick=""><?php esc_html_e( 'Generate & Copy' , 'qc-smd' ); ?></a>
              </select>
            </div>
          </div>
          <div class="ifram-row">
            <div class="ifram-lg"> <span class="qcld-span-label"><?php esc_html_e( 'Generated Code' , 'qc-smd' ); ?></span> <br>
              <textarea id="igcode_textarea" class="igcode_textarea" name="igcode" style="width:100%; height:120px;" readonly="readonly"></textarea>
              <p class="guideline"><?php esc_html_e( 'Hit "Generate & Copy" button to generate embed code. It will be copied to your Clipboard. You can now paste this embed code inside your website\'s HTML where you want to show the List.' , 'qc-smd' ); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <?php }
  }
}
