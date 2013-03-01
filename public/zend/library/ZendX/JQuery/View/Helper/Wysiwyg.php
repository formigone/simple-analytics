<?php

/**
 * @see Zend_Registry
 */
require_once "Zend/Registry.php";

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Date Picker View Helper
 *
 * @uses 	   Zend_View_Helper_FormText
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_Wysiwyg extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create a jQuery UI Widget Date Picker
     *
     * @link   http://trentrichardson.com/examples/timepicker/
     * @param  string $id
     * @param  string $value
     * @param  array  $params jQuery Widget Parameters
     * @param  array  $attribs HTML Element Attributes
     * @return string
     */
    public function wysiwyg($id, $value = null, array $params = array(), array $attribs = array())
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
        if( !empty($attribs['class']) ) {
        	$attribs['class'] .=' tinymce';
        } else {
        	$attribs['class'] = 'tinymce';
        }

        //$value = html_entity_decode($value);

        $width = 960;
        $height = 300;
        if( !empty($attribs['width']) ) {
        	$width = (int) $attribs['width'];
        }
        if( !empty($attribs['height']) ) {
        	$height = (int) $attribs['height'];
        }

        $params = ZendX_JQuery::encodeJson($params);

            // disable WYSIWYG on the iPhone
        $js = sprintf('if ((navigator.userAgent.match(/iPhone/i))
                || (navigator.userAgent.match(/iPod/i))
                || (navigator.userAgent.match(/iPad/i))
                ) {
            // >>>>> insert iphone specific code here <<<<<
        } else {
        $(\'textarea.tinymce\').tinymce({
            // Location of TinyMCE script
            script_url : \'/js/lib/tinymce/tiny_mce.js\',

            // General options
            theme : "advanced",
            plugins : "iespell,inlinepopups,preview,print,paste,fullscreen,noneditable,visualchars,wordcount,spellchecker",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,|,pastetext,pasteword,bullist,numlist,|,link,unlink,cleanup,code,|,preview,|,spellchecker",
            theme_advanced_buttons2 : "" ,
            theme_advanced_buttons3 : "" ,
            theme_advanced_buttons4 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_path : false,
            height : "%d",
            width : "%d",

            setup: function(ed) {

            	// WORDS REMAINING
	            var text = \'\';
			    var span = document.getElementById(\'word-count-\' + ed.id);
			    var span2 = $(\'#word-count-\' + ed.id);



			    if(span) {

			        var wordlimit = span.innerHTML;
			        var currentCount = 0;
			        ed.onKeyUp.add(function(ed, e) {

			        	// get count from wysiwyg and do the math
			        	currentCount = $(\'#DS_body-word-count\').text();
			            wordcount = wordlimit - currentCount;
			            if( wordcount > 0 ) {
			            	span2.removeClass(\'go_red\');
							span2.addClass(\'go_green\');
			            } else if (wordcount < 0 ) {
							span2.removeClass(\'go_green\');
							span2.addClass(\'go_red\');
			            } else {
							span2.removeClass(\'go_green\');
							span2.removeClass(\'go_red\');
			            }

			            // update words remaining.
			            span.innerHTML = wordcount;

			        });
			    }

				ed.onKeyUp.add(function(ed, e) {
	                  ed.save();
	                  $(ed.getElement()).keyup();
	                } )
	            }

        });
        }',
                $height,
                $width
        );

        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_ALL ^ ZendX_JQuery::RENDER_LIBRARY);

        require_once('DDM/Functions.php');

        // use tinymce
        $this->jquery->AddJavascriptFile(noCacheFile('/js/lib/tinymce/jquery.tinymce.js'));

        // use cl ed.
        //$this->jquery->AddJavascriptFile(noCacheFile('/js/lib/cleditor/jquery.cleditor.js'));
        //$this->jquery->AddJavascriptFile('/js/lib/cleditor/clplugins.js');
		//$this->jquery->addStylesheet('/js/lib/cleditor/jquery.cleditor.css');

        $this->jquery->addOnLoad($js);

        return $this->view->formTextarea($id, $value, $attribs);
    }
}
