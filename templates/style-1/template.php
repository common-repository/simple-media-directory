<?php wp_enqueue_style('smd-css-style-1', QCSMD_TPL_URL . "/$template_code/template.css" ); ?>


<?php
global $wpdb;
// The Loop
if ( $list_query->have_posts() ) 
{
	
	if(get_option('smd_enable_top_part')=='on') :
		
	 do_action('qcsmd_attach_embed_btn', $shortcodeAtts);
	
	endif;

	//Directory Wrap or Container

	echo '<div class="qcsmd-list-wrapper">
	<div id="smd-list-holder" class="qc-grid qcsmd-list-holder">';

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

		?>

		<?php if( $style == "style-1" ): ?>
        <style>


            #list-item-<?php echo $listId .'-'. get_the_ID(); ?>.qcsmd-list-column.style-1 ul li a{

            <?php if($title_font_size!=''): ?>
                font-size:<?php echo $title_font_size; ?>;
            <?php endif; ?>

            <?php if($title_line_height!=''): ?>
                line-height:<?php echo $title_line_height; ?>;
            <?php endif; ?>

            }


        </style>
		<!-- Individual List Item -->
		<div id="list-item-<?php echo esc_attr($listId .'-'. get_the_ID()); ?>" class="qc-grid-item qcsmd-list-column smd-column-<?php echo esc_attr($column); echo " " . esc_attr($style);?> <?php echo esc_attr("smd-list-id-" . get_the_ID()); ?>">
			<div class="qcsmd-single-list">
				
				<h2 class="qcsmd-section-title">
					<?php echo esc_html( get_the_title() ); ?>
				</h2>
				<ul>
					<?php 
						
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

						$count = 1;
						foreach( $lists as $list ) : 
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
							<a class="open-mpf-smd-video smd_load_video" href="#" data-mfp-src="#smdvideo-<?php echo esc_attr($count); ?>" data-itemid="<?php echo esc_attr(get_the_ID()); ?>" data-videourl="<?php echo esc_attr($list['qcsmd_item_link']); ?>" data-itemsid="<?php echo isset( $list['qcsmd_timelaps'] ) ? esc_attr($list['qcsmd_timelaps']) : ''; ?>" data-tag="<?php echo (isset($list['qcsmd_tags']) ? esc_attr($list['qcsmd_tags']) : '' ); ?>" >
							
						<?php elseif(smd_is_vimeo_video($item_url)): ?>
							<div id="smdvideo-<?php echo esc_attr($count); ?>" class="white-popup mfp-hide">
								<div class="smd_video">
									Loading...
								</div>
							</div>
							<a class="open-mpf-smd-video smd_load_video" href="#" data-mfp-src="#smdvideo-<?php echo esc_attr($count); ?>" data-itemid="<?php echo esc_attr(get_the_ID()); ?>" data-videourl="<?php echo esc_attr($list['qcsmd_item_link']); ?>" data-itemsid="<?php echo isset( $list['qcsmd_timelaps']) ? esc_attr($list['qcsmd_timelaps']) : ''; ?>" >
							
						<?php else: ?>
							<a <?php echo (isset($list['qcsmd_item_nofollow']) && $list['qcsmd_item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> href="<?php echo esc_url($masked_url); ?>"
								<?php echo (isset($list['qcsmd_item_newtab']) && $list['qcsmd_item_newtab'] == 1) ? 'target="_blank"' : ''; ?>>
						<?php endif; ?>

								<!-- Image, If Present -->
								<?php if( ($list_img == "true") && isset($list['qcsmd_item_img'])  && $list['qcsmd_item_img'] != "" ) : ?>
									<span class="smd-list-img has-img">
										<?php 
											$img = wp_get_attachment_image_src($list['qcsmd_item_img'], 'medium');
										?>
										<img src="<?php echo esc_url($img[0]); ?>" alt="">
									</span>
								<?php else : ?>
									<span class="smd-list-img no-img">
										<img src="<?php echo esc_url( QCSMD_IMG_URL ); ?>/list-image-placeholder.png" alt="">
									</span>
								<?php endif; ?>

								<!-- Link Text -->
								<?php 
									echo esc_html($list['qcsmd_item_title']); 
								?>

							</a>
						<?php if( $upvote == 'on' ) : ?>

							<!-- upvote section -->
							<div class="smd-upvote-section">
								<span data-post-id="<?php echo esc_attr(get_the_ID()); ?>" data-item-title="<?php echo esc_attr(trim($list['qcsmd_item_title'])); ?>" data-item-link="<?php echo esc_attr($list['qcsmd_item_link']); ?>" class="smd-upvote-btn smd-upvote-on">
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
		<!-- /Individual List Item -->

		<?php endif; ?>

		<?php

		$listId++;
	}

	echo '<div class="smd-clearfix"></div>
			</div>
		<div class="smd-clearfix"></div>
	</div>';

}
