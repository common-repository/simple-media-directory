;(function( $ ) {
    tinymce.PluginManager.add('qcsmd_shortcode_btn', function( editor,url )
    {
        var shortcodeValues = [];

        editor.addButton('qcsmd_shortcode_btn', {
			title : 'Generate SMD Shortcode',
            //text: 'SMD',
            icon: 'icon qc_smd_btn',
            onclick : function(e){
                $.post(
                    qcsmd_ajaxurl,
                    {
                        action : 'show_qcsmd_shortcodes',
                        security: qcsmd_ajax_nonce
                        
                    },
                    function(data){
                        $('#wpwrap').append(data);
                    }
                )
            },
            values: shortcodeValues
        });
    });

    var selector = '';

	

}(jQuery));
