!function(e){function t(r){if(o[r])return o[r].exports;var n=o[r]={i:r,l:!1,exports:{}};return e[r].call(n.exports,n,n.exports,t),n.l=!0,n.exports}var o={};t.m=e,t.c=o,t.d=function(e,o,r){t.o(e,o)||Object.defineProperty(e,o,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(o,"a",o),o},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=0)}([function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0});o(1)},function(e,t,o){"use strict";var r=o(2),n=(o.n(r),o(3)),c=(o.n(n),wp.i18n.__);(0,wp.blocks.registerBlockType)("smd/block-smd-block",{title:c("Simple Media Directory"),icon:"playlist-video",category:"common",keywords:[c("Simple Media Directory"),c("SMD")],attributes:{shortcode:{type:"string",default:""}},edit:function(e){function t(e){jQuery("#smd_shortcode_generator_meta_block").prop("disabled",!0),jQuery(e.target).addClass("currently_editing"),jQuery.post(ajaxurl,{action:"show_qcsmd_shortcodes"},function(e){jQuery("#smd_shortcode_generator_meta_block").prop("disabled",!1),jQuery("#wpwrap").append(e),jQuery("#wpwrap").find("#sm-modal .smd_copy_close").removeClass("smd_copy_close").addClass("smd_block_copy_close")})}function o(e){var t=jQuery("#smd_shortcode_container").val();n({shortcode:t}),console.log({shortcode:t})}var r=e.attributes.shortcode,n=e.setAttributes;return jQuery(document).on("click",".smd_block_copy_close",function(e){e.preventDefault(),jQuery(".currently_editing").next("#insert_shortcode").trigger("click"),jQuery(document).find(".modal-content .close").trigger("click")}),jQuery(document).on("click",".modal-content .close",function(){jQuery(".currently_editing").removeClass("currently_editing")}),wp.element.createElement("div",{className:e.className},wp.element.createElement("input",{type:"button",id:"smd_shortcode_generator_meta_block",onClick:t,className:"button button-primary button-large",value:"Generate SMD Shortcode"}),wp.element.createElement("input",{type:"button",id:"insert_shortcode",onClick:o,className:"button button-primary button-large",value:"Test SMD Shortcode"}),wp.element.createElement("br",null),r)},save:function(e){var t=e.attributes.shortcode;return wp.element.createElement("div",null,t)}})},function(e,t){},function(e,t){}]);