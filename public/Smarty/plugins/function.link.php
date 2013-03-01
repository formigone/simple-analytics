<?php
/**
 * Virtual path curl function
 *
 * @param array $params
 * @param object $smarty
 * @package com.dnpc.smarty
 */

function smarty_function_link($params, $smarty){
/*  
Parameters
    template    -template path
    oid         -id
    title       -link title
    scheme  -path scheme
    noAnchor    -if set this will not include the anchor tag
        queryString   -complete querystring
*/

    $controller = '';
    $action     = '';
    $scheme     = '';
    $arguments  = array();
    $text       = '';
    $qs         = '';
    $noAnchor   = false;
    

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'controller':
            case 'action':            
            case 'text':           
            case 'qs':           
            case 'scheme':           
                $$_key = (string)$_val;
                break;
            case 'noAnchor':  
                    $$_key = true;
                break;             
            case 'class':
            case 'title':
            case 'style':
                if(!is_array($_val)) {
                    $extra .= "{$_key}/{$_val}/";
                } else {
                    $smarty->trigger_error("CURL: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }            	
            	break;
            default:
                if(!is_array($_val)) {
                    $extra .= "{$_key}/{$_val}/";
                } else {
                    $smarty->trigger_error("CURL: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }
    
    if(!empty($controller)){
        $url = "{$scheme}/{$controller}/{$action}/{$extra}";
        if (is_array($arguments)) {
            foreach ($arguments as $k => $v) {
                $url .= "{$k}/{$v}/";
            }
        }
        if (strlen($qs) > 0) {
            $url .= "?{$qs}";
        }
        if(empty($noAnchor)){
            echo "<a href=\"{$url}\">$text</a>";
        } else {
            echo $url;
        }
    }
}
?>