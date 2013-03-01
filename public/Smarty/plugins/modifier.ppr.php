<?php
/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifier
*/

/**
* Smarty ppr modifier plugin
*
* Type:     modifier<br>
* Name:     ppr<br>
* Purpose:  convert string to uppercase
*
* @author Mark Sticht
* @param string $
* @return string
*/
function smarty_modifier_ppr($string)
{
    return "<pre>". print_r($string, true) . "</pre>";
}
?>