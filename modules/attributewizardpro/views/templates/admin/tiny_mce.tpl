{*
 * 2008 - 2017 Presto-Changeo
 *
 * MODULE Attribute Wizard Pro
 *
 * @version   2.0.0
 * @author    Presto-Changeo <info@presto-changeo.com>
 * @link      http://www.presto-changeo.com
 * @copyright Copyright (c) permanent, Presto-Changeo
 * @license   Addons PrestaShop license limitation
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
*}
    <script type="text/javascript" src="{$base_uri}js/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="{$base_uri}js/admin/tinymce.inc.js"></script>

    
    <script type="text/javascript">
            var iso = '{$isoTinyMCE}';
            var pathCSS = '{$theme_css_dir}' ;
            var ad = '{$ad}' ;
            function tinyMCEInit(element, selector)
            {
                    tinyMCE.init({
                            selector: selector,
                            mode : element != "textarea"?"exact":"textareas",
                            theme : "advanced",
                            skin:"cirkuit",
                            plugins : "colorpicker link image filemanager table media placeholder",
                            // Theme options
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "left",
                            theme_advanced_statusbar_location : "bottom",
                            theme_advanced_resizing : false,
                            content_css : pathCSS+"theme.css",
                            document_base_url : ad,
                            width: "600",
                            height: "auto",
                            font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
                            browser_spellcheck : true,
                            toolbar1 : "colorpicker,bold,italic,underline",
                            toolbar2: "strikethrough,blockquote,link,alignleft",
                            toolbar3: "aligncenter,alignright,alignjustify",
                            toolbar4: "bullist,numlist,image",
                            external_filemanager_path: baseAdminDir+"filemanager/",
                            filemanager_title: "File manager" ,
                            external_plugins: { "filemanager" : baseAdminDir+"filemanager/plugin.min.js"},
                            language: iso_user,
                            skin: "prestashop",
                            entity_encoding: "raw",
                            convert_urls : false,
                            language : iso,
                            setup:function(ed) {
                                ed.on('change', function(e) {
                                   awp_update_lang(false);
                                });
                            }
                    });
            }

           
    </script>
