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
* Type:     modifier
* Name:     md5
* Purpose:  convert string to uppercase
*
* @author Mark Sticht
* @param string $
* @param string $
* @return string
*/
function smarty_modifier_md5($string, $append='')
{
    return md5( $string . $append );
}
?>