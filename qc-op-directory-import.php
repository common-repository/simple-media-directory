<?php

class qcsmd_BulkImportFree
{

    function __construct()
    {
        add_action('admin_menu', array($this, 'qcsmd_info_menu'));
    }

    public $post_id;

    function qcsmd_info_menu()
    {
        add_submenu_page(
            'edit.php?post_type=smd',
            'Bulk Import',
            'Import',
            'manage_options',
            'qcsmd_bimport_page',
            array(
                $this,
                'qcsmd_bimport_page_content'
            )
        );
    }

    function qcsmd_bimport_page_content()
    {
        ?>
        <div class="wrap">

            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-3">

                    <div id="post-body-content" style="position: relative;">

                        <u>
                            <h1><?php esc_html_e( 'Bulk Import' , 'qc-smd' ); ?></h1>
                        </u>

                        <div>
                            
                            <p>
								<strong><?php esc_html_e( 'Please Note:' , 'qc-smd' ); ?></strong> <?php esc_html_e( 'The import feature is still under development. Right now it only allows importing and creating new Lists. Existing Lists will not get updated. Also, export feature is not available in free version.' , 'qc-smd' ); ?>
							</p>
							
							<p>
                                <strong><?php esc_html_e( 'Sample CSV File:' , 'qc-smd' ); ?></strong>
                                <a href="<?php echo esc_url( QCSMD_ASSETS_URL) . '/file/sample-csv-file.csv'; ?>" target="_blank">
                                    <?php esc_html_e( 'Download' , 'qc-smd' ); ?>
                                </a>
                            </p>

                            <p><strong><?php esc_html_e( 'PROCESS:' , 'qc-smd' ); ?></strong></p>

                            <p>
                                <ol>
                                    <li><?php esc_html_e( 'First download the above CSV file.' , 'qc-smd' ); ?></li>
                                    <li><?php esc_html_e( 'Add/Edit rows on the top of it, by maintaing proper provided format/fields.' , 'qc-smd' ); ?></li>
                                    <li><?php esc_html_e( 'Finally, upload file in the below form.' , 'qc-smd' ); ?></li>
                                </ol>
                            </p>



                            <p><strong><?php esc_html_e( 'NOTES:' , 'qc-smd' ); ?></strong></p>

                            <p>
                                <ol>
                                    <li><?php esc_html_e( 'It should be a simple CSV file.' , 'qc-smd' ); ?></li>
                                    <li><?php esc_html_e( 'File encoding should be in UTF-8' , 'qc-smd' ); ?></li>
                                    <li><?php esc_html_e( 'File must be prepared as per provided sample CSV file.' , 'qc-smd' ); ?></li>
                                </ol>
                            </p>
                            
                        </div>

                        <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">

                        <!-- Handle CSV Upload -->

                        <?php

                        $randomNum = substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 5);

                        if( !empty($_POST) && isset($_POST['upload_csv']) ) 
                        {

                            if ( function_exists('is_user_logged_in') && is_user_logged_in() ) 
							{

                                $tmpName = sanitize_text_field($_FILES['csv_upload']['tmp_name']);
                               
                                $file = fopen($tmpName, "r");
                                $flag = true;
								
								//Reading file and building our array
								
								$baseData = array();

                                $count = 0;

                                while(($data = fgetcsv($file)) !== FALSE) 
                                {
                                    if ($flag) {
                                        $flag = false;
                                        continue;
                                    }
									
									$baseData[$data[0]][] = array(
                                        'list_title'            => sanitize_text_field(utf8_encode(trim($data[0]))),
                                        'qcsmd_item_title'      => sanitize_text_field(utf8_encode(trim($data[1]))),
                                        'qcsmd_item_link'       => sanitize_text_field(utf8_encode(trim($data[2]))),
                                        'qcsmd_item_img'        => '',
                                        'qcsmd_item_nofollow'   => trim($data[3]),
                                        'qcsmd_item_newtab'     => trim($data[4]),
                                        'qcsmd_item_subtitle'   => trim($data[5])
                                    );

                                    $count++;

                                }
                                
                                fclose($file);
								
								//Inserting Data from our built array
								
								$keyCounter = 0;
								$metaCounter = 0;
								
								global $wpdb;
								
								foreach( $baseData as $key => $data ){
								
									$post_arr = array(
										'post_title' => trim($key),
										'post_status' => 'publish',
										'post_author' => get_current_user_id(),
										'post_type' => 'smd',
									);

									wp_insert_post($post_arr);

									$newest_post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type = 'smd' ORDER BY ID DESC LIMIT 1");

									foreach( $data as $k => $item ){										
										add_post_meta(
											$newest_post_id, 
											'qcsmd_list_item01', array(
												'qcsmd_item_title'      => sanitize_text_field($item['qcsmd_item_title']),
												'qcsmd_item_link'       => sanitize_text_field($item['qcsmd_item_link']),
												'qcsmd_item_img'        => '',
												'qcsmd_item_nofollow'   => sanitize_text_field($item['qcsmd_item_nofollow']),
												'qcsmd_item_newtab'     => sanitize_text_field($item['qcsmd_item_newtab']),
												'qcsmd_item_subtitle'   => sanitize_text_field($item['qcsmd_item_subtitle'])
											)
										);
										
										$metaCounter++;
										
									} //end of inner-foreach
									
									$keyCounter++;
								
								} //end of outer-foreach

                                if( $keyCounter > 0 && $metaCounter > 0 )
								{
                                    echo  '<div><span style="color: red; font-weight: bold;">'.esc_html('RESULT:').'</span> <strong>'.esc_attr( $keyCounter ).'</strong> entry with <strong>'.esc_attr( $metaCounter ).'</strong> '. esc_html('element(s) was made successfully.').'</div>';
                                }

                            }

                        } 
                        else 
                        {
							//echo "Attached file is invalid!";
                        }

                        ?>
                            
                            <p>
                                <strong>
                                    <?php esc_html_e('Upload csv file to import', 'qc-smd'); ?>
                                </strong>
                            </p>

                            <form name="uploadfile" id="uploadfile_form" method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
                                <?php wp_nonce_field('qcsmd_import_nonce', 'qc-smd'); ?>

                                <p>
                                    <?php esc_html_e( 'Select file to upload' , 'qc-smd' ); ?>
                                    <input type="file" name="csv_upload" id="csv_upload" size="35" class="uploadfiles"/>
                                </p>
								<p style="color:red;"><?php esc_html_e( '**CSV File & Characters must be saved with UTF-8 encoding**' , 'qc-smd' ); ?></p>
                                <p>
                                    <input class="button-primary" type="submit" name="upload_csv" id="" value="<?php esc_html_e( 'Upload & Process' , 'qc-smd' ); ?>"/>
                                </p>

                            </form>

                        </div>


                        <div style="padding: 15px 10px; border: 1px solid #ccc; text-align: center; margin-top: 20px;">
                            <?php esc_html_e( 'Crafted By:' , 'qc-smd' ); ?> <a href="<?php echo esc_url('http://www.quantumcloud.com'); ?>" target="_blank"><?php esc_html_e( 'Web Design Company' , 'qc-smd' ); ?></a> -
                            <?php echo esc_attr( 'QuantumCloud' , 'qc-smd' ); ?>
                        </div>

                    </div>
                    <!-- /post-body-content -->

                </div>
                <!-- /post-body-->

            </div>
            <!-- /poststuff -->


        </div>
        <!-- /wrap -->

        <?php
    }
}

new qcsmd_BulkImportFree;
