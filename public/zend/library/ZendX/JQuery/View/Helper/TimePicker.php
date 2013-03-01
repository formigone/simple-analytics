<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: TimePicker.php 21869 2010-04-16 07:49:18Z beberlei $
 */

/**
 * @see Zend_Registry
 */
require_once "Zend/Registry.php";

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Time Picker View Helper
 *
 * @uses 	   Zend_View_Helper_FormText
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_TimePicker extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create a jQuery UI Widget Time Picker
     *
     * @link   http://docs.jquery.com/UI/Timepicker
     * @param  string $id
     * @param  string $value
     * @param  array  $params jQuery Widget Parameters
     * @param  array  $attribs HTML Element Attributes
     * @return string
     */
    public function TimePicker($id, $value = null, array $params = array(), array $attribs = array())
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        if(!isset($params['timeFormat']) && Zend_Registry::isRegistered('Zend_Locale')) {
            $params['timeFormat'] = self::resolveZendLocaleToTimePickerFormat();
        }

         // 12/24 hour default
        if(!isset($params['ampm']) ) {
            $params['ampm'] = true;
        }

        // default hour
        if(!isset($params['hour']) ) {
        	$params['hour'] = 12;
        }

        if(!isset($params['minute']) ) {
        	$params['minute'] = 0;
        }

        // TODO: Allow translation of TimePicker Text Values to get this action from client to server
        $params = ZendX_JQuery::encodeJson($params);

        $js = sprintf('%s("#%s").timepicker({show24Hours: false});',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $params
        );

		$this->jquery->AddJavascriptFile('/js/jquery-ui-timepicker-addon-0.9.7.js');
        $this->jquery->addStylesheet('/css/timepicker.css');
        $this->jquery->addOnLoad($js);

        return $this->view->formText($id, $value, $attribs);
    }

    /**
     * A Check for Zend_Locale existance has already been done in {@link timePicker()}
     * this function only resolves the default format from Zend Locale to
     * a jQuery Time Picker readable format. This function can be potentially buggy
     * because of its easy nature and is therefore stripped from the core functionality
     * to be easily overriden.
     *
     * @return string
     */
    public static function resolveZendLocaleToTimePickerFormat($format=null)
    {
        if($format == null) {
            $locale = Zend_Registry::get('Zend_Locale');
            if( !($locale instanceof Zend_Locale) ) {
                require_once "ZendX/JQuery/Exception.php";
                throw new ZendX_JQuery_Exception("Cannot resolve Zend Locale format by default, no application wide locale is set.");
            }
            /**
             * @see Zend_Locale_Format
             */
            require_once "Zend/Locale/Format.php";
            $format = Zend_Locale_Format::getTimeFormat($locale);
        }

        $timeFormat = array(
            'EEEEE' => 'D', 'EEEE' => 'DD', 'EEE' => 'D', 'EE' => 'D', 'E' => 'D',
            'MMMM' => 'MM', 'MMM' => 'M', 'MM' => 'mm', 'M' => 'm',
            'YYYYY' => 'yy', 'YYYY' => 'yy', 'YYY' => 'yy', 'YY' => 'y', 'Y' => 'yy',
            'yyyyy' => 'yy', 'yyyy' => 'yy', 'yyy' => 'yy', 'yy' => 'y', 'y' => 'yy',
            'G' => '', 'e' => '', 'a' => '', 'h' => '', 'H' => '', 'm' => '',
            's' => '', 'S' => '', 'z' => '', 'Z' => '', 'A' => '',
        );

        $newFormat = "";
        $isText = false;
        $i = 0;
        while($i < strlen($format)) {
            $chr = $format[$i];
            if($chr == '"' || $chr == "'") {
                $isText = !$isText;
            }
            $replaced = false;
            if($isText == false) {
                foreach($timeFormat AS $zl => $jql) {
                    if(substr($format, $i, strlen($zl)) == $zl) {
                        $chr = $jql;
                        $i += strlen($zl);
                        $replaced = true;
                    }
                }
            }
            if($replaced == false) {
                $i++;
            }
            $newFormat .= $chr;
        }

        return $newFormat;
    }
}
