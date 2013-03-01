<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:    function
 * Version: 1.0
 * Date:    11-09-2011
 * Install: Drop into the plugin directory
 * Author:  Justin Warkentin
 * -------------------------------------------------------------
  */
function smarty_function_page_numbers($params, &$smarty)
{
    /*
    Parameters
        currentPage
        totalPages
        baseURI
        class
    */
    extract($params);

    $numToShow = 15;

    $start = ($start = $currentPage - floor($numToShow / 2)) > 0 ? $start : 1;
    $end = ($end = $start + $numToShow) > $totalPages ? $totalPages + 1 : $end; // exclusive
    if($end - $start != $numToShow) {
        $start = ($start = $end - $numToShow) > 0 ? $start : 1;
    }

    echo '<ul class="' . $class . '">';
    if ($currentPage != 1) {
        $prev = ($currentPage - 1);
        $anchor = (isset($onclick)) ? '<a href="#" onclick="' . $onclick . '(' . $prev . ');">' : '<a href="' . $baseURI . "/page/{$prev}" . '">';
        print '<li>' . $anchor . '&lt;&#160;Previous' . "</a></li>\n";
    }
    for($i = $start; $i < $end; $i++)
    {
        if($i == $start && $i != 1) {
            $num = 1;
        }
        elseif($i == $end - 1 && $i != $totalPages) {
            $num = $totalPages;
            print "<li>...</li>\n";
        }
        else {
            $num = $i;
        }

        if($i == $start + 1 && $i > 2) {
            print "<li>...</li>\n";
        }
        else {
            $selected = $num == $currentPage ? ' class="selected"' : '';
            $anchor = (isset($onclick)) ? '<a href="#" onclick="' . $onclick . '(' . $num . ');">' : '<a href="' . $baseURI . "/page/{$num}" . '">';
            print '<li' . $selected . '>' . $anchor . $num . "</a></li>\n";
        }
    }
    if ($currentPage != $totalPages) {
        $next = ($currentPage + 1);
        $anchor = (isset($onclick)) ? '<a href="#" onclick="' . $onclick . '(' . $next . ');">' : '<a href="' . $baseURI . "/page/{$next}" . '">';
        print '<li>' . $anchor . 'Next&#160;&gt;' . "</a></li>\n";
    }
    echo '</ul>';
}