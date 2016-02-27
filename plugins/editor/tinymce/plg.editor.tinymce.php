<?php

defined('_CHECK_') or die("Access denied");
use maze\base\JsExpression;
use maze\helpers\Json;

class Tinymce_Plugin_Editor extends Plugin {

    public function tinymce($id, $object) {
        $assets = plg\editor\tinymce\AssetTiny::register();
        $this->_doc->setTextScritp('
	$(document).ready(function(){
        var target = $("#' . $id . '").closest("form");
        if(!target.is("form"))  target = $("body")  
        target.submit(function(e){				 
            var content = tinyMCE.get("' . $id . '").getContent();
            $("#' . $id . '").val(content);
            })
	})');

        $settings = [
        'language' => 'ru',
        'mode' => "exact",
        'valid_elements' => "*[*]",
        'elements' => $id,
        'theme' => 'advanced',
        'fix_list_elements' => true,
        'verify_html' => false,
        'force_br_newlines'=>false,
        'force_p_newlines'=>true,
        'forced_root_block'=>false,
        'theme_advanced_default_foreground_color' => "#000000",
        'theme_advanced_default_background_color' => "#FFFF00",
        'protect' => [new JsExpression('/<\?php.*?\?>/g')],
        'convert_urls' => false,
        'plugins'=>implode(',',[
            'codemirror',
            'autolink',
            'lists',
            'style',
            'layer',
            'table',
            //'save',
            //'advhr',
            'advimage',
            'advlink',
            'emotions',
            'iespell',
            //'insertdatetime',
            //'preview',
            'media',
            'searchreplace',
            'contextmenu',
            'paste',
            'directionality',
            'fullscreen',
            'noneditable',
            'visualchars',
            'nonbreaking',
            'xhtmlxtras',
            'template',
            'inlinepopups'
            ]),
            'codemirror'=>[
                'path'=>'CodeMirror',
                'indentOnInit'=>true,
                'config'=>[
                    'mode'=>'application/x-httpd-php',
                    'lineNumbers'=>true,
                    'lineWrapping'=>true,
                    'foldGutter'=>true,
                    'extraKeys'=>['Ctrl-Q'=>new JsExpression('function(cm){ cm.foldCode(cm.getCursor()); }')],
                    'gutters'=>['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                    'tabSize'=>4,
                    'indentUnit'=>4,
                    'indentWithTabs'=>true
                ],
                'jsFiles'=>['mode/clike/clike.js',
                    'mode/php/php.js',
                    'addon/fold/foldcode.js',
                    'addon/fold/foldgutter.js',
                    'addon/fold/brace-fold.js',
                    'addon/fold/xml-fold.js',
                    'addon/fold/comment-fold.js'
                    ],
                'cssFiles'=>['addon/fold/foldgutter.css']
            ],
            'theme_advanced_buttons1'=>'filemanager,|,newdocument,|,codemirror,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
            'theme_advanced_buttons2'=>'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,|,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
            'theme_advanced_buttons3'=>'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
            'theme_advanced_buttons4'=>'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft',
            'theme_advanced_toolbar_location'=>'top',
            'theme_advanced_toolbar_align'=>'left',
            'theme_advanced_statusbar_location'=>'bottom',
            'theme_advanced_resizing'=>true,
            'theme_advanced_resizing_use_cookie'=>false,
            'content_css'=>($this->params->getVar("content_css") ? $this->params->getVar("content_css") : $assets->getAssetBaseUrl().'/js/themes/advanced/skins/default/content.css' ),
            'template_replace_values'=>['username'=>'Some User', 'staffid'=>'991234'],
            'setup'=>new JsExpression('function(ed){ed.addButton("filemanager", '.Json::encode([
                'title'=>Text::_("PLG_EDITOR_TINYMCE_BUTTON_FILEMANAGER"),
                'image'=>'/library/image/icons/application-image.png',
                'onclick'=>new JsExpression('function() {ed.windowManager.open('.Json::encode([
                    'title'=>Text::_("PLG_EDITOR_TINYMCE_BUTTON_FILEMANAGER"),
                    'url'=>'/admin/elfinder/?run=loadDialog&clear=iframe&nonescript=1',
                    'width'=>900,
                    'height'=>407,
                    'inline'=>1
                ]).');$("#"+ed.windowManager.lastId).find("iframe").one("load",function(){'
                    .'document.getElementById($(this).attr("id").toString()).contentWindow.dialogFile(setContentImages)})}')
            ]).')}')
        ];
 
        /* 		$init .='inline_styles : true,
          gecko_spellcheck : true,
          entity_encoding : "raw",
          extended_valid_elements: "hr[id|title|alt|class|width|size|noshade|style],img[class|src|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],a[id|class|name|href|hreflang|target|title|onclick|rel|style]",
          force_br_newlines : false, force_p_newlines : true, forced_root_block : "p",
          invalid_elements : "script,applet,iframe",
          document_base_url : "http://session.lab/", ';
         */
          /* $init .= 'skin : "o2k7",'; */
        // General options
        $this->_doc->setTextScritp(';tinyMCE.init('.Json::encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).');');
    

        // Drop lists for link/image/media/template dialogs
        /* $init .= 'template_external_list_url : "lists/template_list.js",
          external_link_list_url : "lists/link_list.js",
          external_image_list_url : "lists/image_list.js",
          media_external_list_url : "lists/media_list.js",'; */

    }

}

?>