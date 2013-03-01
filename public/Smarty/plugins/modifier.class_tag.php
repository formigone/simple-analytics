<?php
/**
* Smarty plugin
* 
*/

/**
* Smarty class_tag modifier plugin
* 
* Type:     modifier<br>
* Name:     date_due<br>
* Purpose:  format text as a class name by removing all non-class tag characters 
*           and converting it to lower case
* Input:
*          - string: input text
* 
* @author Andrew Bunker <abunker at deseretdigital com> 
* @param string $ 
* @return string |void
*/
function smarty_modifier_class_tag($string)
{
    $string = strtolower($string);
    $string = preg_replace("/[^a-z0-9_-]/", "", $string);

    return $string;
} 
