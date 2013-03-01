<?php
/**
* Smarty plugin
*
*/

/**
* Smarty date_age modifier plugin
*
* Type:     modifier<br>
* Name:     duration<br>
* Purpose:  format datestamps as days/hours/min from now<br>
* Input:<br>
*          - string: input date string
*
* @author H Hatfield <hhatfield at deseretdigital com>
* @param string $
* @return string |void
* @uses smarty_make_timestamp()
*/
function smarty_modifier_duration($string, $end = null)
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
  if (null === $end) {
    $etime = time();
  } else {
    $etime = smarty_make_timestamp($end);
  }
    // Year
	$ovalue = intval(($etime - $ctime) / 31536000);
    $olabel = "Year";

	// Month
	if ($ovalue < 1) {
		$ovalue = intval(($etime - $ctime) / 2635200);
		$olabel = "Month";
	}

	// Weeks
	if ($ovalue < 1) {
		$ovalue = intval(($etime - $ctime) / 604800);
		$olabel = "Week";
	}

	// Day
	if ($ovalue < 1) {
		$ovalue = intval(($etime - $ctime) / 86400);
		$olabel = "Day";
	}

	// Hr
	if ($ovalue < 1) {
		$ovalue = intval(($etime - $ctime) / 3600);
		$olabel = "Hr";
	}

	// Min
	if ($ovalue < 1) {
		$ovalue = intval(($etime - $ctime) / 60);
		$olabel = "Min";
	}

	// fix above labels to be plural if needed, so far all work with just adding an s
	if ($ovalue > 1) {
		$olabel .= "s";
	}

	return number_format($ovalue)." $olabel";
}

?>
