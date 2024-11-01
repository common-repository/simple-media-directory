<?php wp_enqueue_style('smd-css-simple', QCSMD_TPL_URL . "/$template_code/template.css" ); ?>


<?php
global $wpdb;
// The Loop
if ( $list_query->have_posts() ) 
{
	
	if(get_option('smd_enable_top_part')=='on') :
		
	 do_action('qcsmd_attach_embed_btn', $shortcodeAtts);
	
	endif;

	//Directory Wrap or Container

	echo '<div class="qcsmd-list-wrapper"><div id="smd-list-holder" class="qc-grid qcsmd-list-holder">';

	$listId = 1;

	while ( $list_query->have_posts() ) 
	{
		$list_query->the_post();

		//$lists = get_post_meta( get_the_ID(), 'qcsmd_list_item01' );
		
		$lists = array();
		//$results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = ".get_the_ID()." AND meta_key = 'qcsmd_list_item01' order by `meta_id` ASC");
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'qcsmd_list_item01' order by `meta_id` ASC", get_the_ID() ) );
		if(!empty($results)){
			foreach($results as $result){
				$unserialize = unserialize($result->meta_value);
				$lists[] = $unserialize;
			}
		}

		$conf = get_post_meta( get_the_ID(), 'qcsmd_list_conf', true );
		
		

		if( $item_orderby == 'title' )
		{
			usort($lists, "smd_custom_sort_by_tpl_title");
		}
		if( $item_orderby == 'upvotes' )
		{
			usort($lists, "smd_custom_sort_by_tpl_upvotes");
		}
		if( $item_orderby == 'timestamp' )
		{
			usort($lists, "smd_custom_sort_by_tpl_timestamp");
		}

		?>

        <style>


            #qcsmd-list-<?php echo $listId .'-'. get_the_ID(); ?>.qcsmd-list-column.simple .smd-ca-menu li .smd-ca-main {

            <?php if($title_font_size!=''): ?>
                font-size:<?php echo $title_font_size; ?> !important;
            <?php endif; ?>

            <?php if($title_line_height!=''): ?>
                line-height:<?php echo $title_line_height; ?> !important;
            <?php endif; ?>
            }


            #qcsmd-list-<?php echo $listId .'-'. get_the_ID(); ?>.qcsmd-list-column.simple .smd-ca-menu li .smd-ca-sub {

            <?php if($subtitle_font_size!=''): ?>
                font-size:<?php echo $subtitle_font_size; ?> !important;
            <?php endif; ?>

            <?php if($subtitle_line_height!=''): ?>
                line-height:<?php echo $subtitle_line_height; ?>!important;
            <?php endif; ?>
            }

        </style>

		<div class="list-and-add qc-grid-item <?php echo esc_attr("smd-list-id-" . get_the_ID()); ?>">

		<div id="qcsmd-list-<?php echo esc_attr($listId .'-'. get_the_ID()); ?>" class="qcsmd-list-column <?php echo esc_attr($style); ?>">

			<div class="qcsmd-single-list-1">
				<h2 class="qcsmd-section-title">
					<?php echo esc_html( get_the_title() ); ?>
				</h2>
				<ul class="smd-ca-menu">
					<?php $count = 1; 
					
					?>
					<?php foreach( $lists as $list ) : ?>
					<?php 
						$canContentClass = "subtitle-present";

						if( !isset($list['qcsmd_item_subtitle']) || $list['qcsmd_item_subtitle'] == "" )
						{
							$canContentClass = "subtitle-absent";
						}
					?>
					<li id="item-<?php echo esc_attr(get_the_ID() ."-". $count); ?>">
						<?php 
							$item_url = $list['qcsmd_item_link'];
							$masked_url = $list['qcsmd_item_link'];
						?>
						<!-- List Anchor -->
						<?php if(smd_is_youtube_video($item_url)): ?>
							<div id="smdvideo-<?php echo esc_attr($count); ?>" class="white-popup mfp-hide">
								<div class="smd_video">
									Loading...
								</div>
							</div>
							<a class="open-mpf-smd-video smd_load_video" href="#" data-mfp-src="#smdvideo-<?php echo esc_attr($count); ?>" data-itemid="<?php echo esc_attr(get_the_ID()); ?>" data-videourl="<?php echo esc_attr($list['qcsmd_item_link']); ?>" data-itemsid="<?php echo isset( $list['qcsmd_timelaps'] ) ? esc_attr($list['qcsmd_timelaps']) : 0; ?>" data-tag="<?php echo (isset($list['qcsmd_tags']) ? esc_attr($list['qcsmd_tags']) :'' ); ?>" >
							
						<?php elseif(smd_is_vimeo_video($item_url)): ?>
							<div id="smdvideo-<?php echo esc_attr($count); ?>" class="white-popup mfp-hide">
								<div class="smd_video">
									Loading...
								</div>
							</div>
							<a class="open-mpf-smd-video smd_load_video" href="#" data-mfp-src="#smdvideo-<?php echo esc_attr($count); ?>" data-itemid="<?php echo esc_attr(get_the_ID()); ?>" data-videourl="<?php echo isset($list['qcsmd_item_link']) ? esc_attr($list['qcsmd_item_link']) : ''; ?>" data-itemsid="<?php echo isset( $list['qcsmd_timelaps'] ) ? esc_attr($list['qcsmd_timelaps']) : 0; ?>" >
							
						<?php else: ?>
							<a <?php echo (isset($list['qcsmd_item_nofollow']) && $list['qcsmd_item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> href="<?php echo esc_url($masked_url); ?>" <?php echo (isset($list['qcsmd_item_newtab']) && $list['qcsmd_item_newtab'] == 1) ? 'target="_blank"' : ''; ?>>
						<?php endif; ?>

							<!-- Image, If Present -->
							<?php if( ($list_img == "true") && isset($list['qcsmd_item_img'])  && $list['qcsmd_item_img'] != "" ) : ?>
								<span class="smd-ca-icon smd-list-img-1">
									<?php 
										$img = wp_get_attachment_image_src($list['qcsmd_item_img'], 'medium');
									?>
									<img src="<?php echo esc_url($img[0]); ?>" alt="">
								</span>
							<?php else : ?>
								<span class="smd-ca-icon smd-list-img-1">
									<img src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/list-image-placeholder.png" alt="">
								</span>
							<?php endif; ?>

							<?php if( $upvote == 'on' ) : ?>

								<!-- upvote section -->
								<div class="smd-upvote-section">
									<span data-post-id="<?php echo esc_attr(get_the_ID()); ?>" data-item-title="<?php echo esc_attr(trim($list['qcsmd_item_title'])); ?>" data-item-link="<?php echo esc_url($list['qcsmd_item_link']); ?>" class="smd-upvote-btn smd-upvote-on">
										<i class="fa fa-thumbs-up"></i>
									</span>
									<span class="smd-upvote-count">
										<?php
										  if( isset($list['qcsmd_upvote_count']) && (int)$list['qcsmd_upvote_count'] > 0 ){
										  	echo (int)$list['qcsmd_upvote_count'];
										  }
										?>
									</span>
								</div>
								<!-- /upvote section -->

							<?php endif; ?>

							<!-- Link Text -->
							<div class="smd-ca-content">
                                <h3 class="smd-ca-main <?php echo esc_attr($canContentClass); ?>">
								<?php 
									echo esc_html( trim($list['qcsmd_item_title']) ); 
								?>
                                </h3>

                                <?php if( isset($list['qcsmd_item_subtitle']) ) : ?>
	                                <p class="smd-ca-sub">
	                                <?php 
										echo esc_html( trim($list['qcsmd_item_subtitle']) ); 
									?>
	                                </p>
	                            <?php endif; ?>
                            </div>

						</a>
						
						<?php if(isset($list['qcsmd_featured']) and $list['qcsmd_featured']==1):?>
							<!-- featured section -->
							<div class="smd-featured-section">
								<i class="fa fa-bolt"></i>
							</div>
							<!-- /featured section -->
						<?php endif; ?>

					</li>
					<?php $count++; endforeach; ?>
				</ul>
			</div>
		</div>

		</div>

		<?php

		$listId++;
	}

	echo '<div class="smd-clearfix"></div>
			</div>
		<div class="smd-clearfix"></div>
	</div>';

}
