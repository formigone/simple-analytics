<?php
/**
* Smarty plugin
*
*/

/**
* Smarty time_remaining modifier plugin
*
* Type:     modifier<br>
* Name:     date_age<br>
* Purpose:  format datestamps as days/hours/min from now<br>
* Input:<br>
*          - string: input date string
*
* @author Andrew Bunker <abunker at deseretdigital com>
* @param string $
* @return string |void
* @uses smarty_make_timestamp()
*/
function smarty_modifier_time_left($string)
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

    $diff = time() - $ctime;
    if ($diff < 0) {
        $diff *= -1;
        $sign = false;
    } else {
        $sign = true;
    }
	$ovalue = intval($diff / 86400);
	if ($ovalue == 1) {
		$olabel = "day";
	} else {
		$olabel = "days";
	}
	if ($ovalue < 1) {
		$ovalue = intval($diff / 3600);
		if ($ovalue == 1) {
			$olabel = "hour";
		} else {
			$olabel = "hours";
		}
	}
	if ($ovalue < 1) {
		$ovalue = intval($diff / 60);
		if( $ovalue > 0 ) {
			$olabel = "minutes";
		} else {
			$olabel = "minute";
		}
	}
    $out = number_format($ovalue)." $olabel";
    if ($sign) {
//        $out = 'ASAP';
        $out = '(' . $out . ')';
    }
    return $out;
}

?>
