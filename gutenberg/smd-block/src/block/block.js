/**
 * BLOCK: smd-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

function Qcsmd_Shortcode_Preview( { shortcode } ) {
    return(
		<div>
			 {shortcode}
		</div>
    )
}
/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'smd/block-smd-block', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Simple Media Directory' ), // Block title.
	icon: 'playlist-video', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'Simple Media Directory' ),
		__( 'SMD' ),
	],
	attributes: {
        shortcode: {
            type: 'string',
            default: ''
        }
    },

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	edit: function( props ) {
		const { attributes: { shortcode }, setAttributes } = props;

		function showShortcodeModal(e){
			 jQuery('#smd_shortcode_generator_meta_block').prop('disabled', true);
			 jQuery(e.target).addClass('currently_editing');
			jQuery.post(
				ajaxurl,
				{
					action : 'show_qcsmd_shortcodes'
					
				},
				function(data){
					jQuery('#smd_shortcode_generator_meta_block').prop('disabled', false);
					jQuery('#wpwrap').append(data);
					jQuery('#wpwrap').find('#sm-modal .smd_copy_close').removeClass('smd_copy_close').addClass('smd_block_copy_close');
				}
			)
		}

		function insertShortCode(e){
			const shortcode = jQuery('#smd_shortcode_container').val();
			setAttributes( { shortcode: shortcode } );
			//jQuery('#wpwrap').find('#sm-modal').remove();
			console.log({ shortcode });
		}

		jQuery(document).on('click','.smd_block_copy_close', function(e){
			e.preventDefault();
			jQuery('.currently_editing').next('#insert_shortcode').trigger('click');
			jQuery(document).find( '.modal-content .close').trigger('click');
		});

		jQuery(document).on( 'click', '.modal-content .close', function(){
			jQuery('.currently_editing').removeClass('currently_editing');
		});

		return (
			<div className={ props.className }>
				<input type="button" id="smd_shortcode_generator_meta_block" onClick={showShortcodeModal} className="button button-primary button-large" value="Generate SMD Shortcode" />
				<input type="button" id="insert_shortcode" onClick={insertShortCode} className="button button-primary button-large" value="Test SMD Shortcode" />
				<br />
				{ shortcode }
			</div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save: function( props ) {
		const { attributes: { shortcode } } = props;
		return (
			<div>
				{shortcode}
			</div>
		);
	},
} );
