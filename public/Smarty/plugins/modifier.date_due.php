<?php
/**
* Smarty plugin
* 
*/

/**
* Smarty date_due modifier plugin
* 
* Type:     modifier<br>
* Name:     date_due<br>
* Purpose:  format datestamps as days/hours/min from now<br>
* Input:<br>
*          - string: input date string
* 
* @author Andrew Bunker <abunker at deseretdigital com> 
* @param string $ 
* @return string |void
* @uses smarty_make_timestamp()
*/
function smarty_modifier_date_due($string)
{
    if ($string == '0000-00-00 00:00:00') {
        return "NULL";
    }
    require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');
    if ($string != '') {
        $ctime = smarty_make_timestamp($string);
    } else {
        return;
    } 	
    if (date("Y-m-d") == date("Y-m-d", $ctime)) {
        $output = date("g:ia", $ctime);   
    } else if (date("Y") == date("Y", $ctime)) {
        $output = date("M j", $ctime);
    } else {
        $output = date("m/d/y", $ctime);
    }
 //   if ($ctime > time()) {
 //       $output = '(' . $output . ')';
 //   }
    return $output;
} 
