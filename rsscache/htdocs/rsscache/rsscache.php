<?php
/*
tv2.php - tv2 engine

Copyright (c) 2009 - 2011 NoisyB


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
*/
if (!defined ('TV2_PHP'))
{
define ('TV2_PHP', 1);
//phpinfo();
//exit;
//error_reporting(E_ALL | E_STRICT);
require_once ('default.php');
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('rsscache_misc.php');
require_once ('rsscache_sql.php');


// main ()


$f = tv2_get_request_value ('f'); // function
$q = tv2_get_request_value ('q'); // search query
$v = tv2_get_request_value ('v'); // own video
$captcha = tv2_get_request_value ('captcha'); // is request with captcha
$start = tv2_get_request_value ('start'); // offset
if (!($start))
  $start = 0;
$num = tv2_get_request_value ('num'); // number of results
if (!($num))
  {
    if ($f == 'cloud')
      $num = ($tv2_cloud_results > 0) ? $tv2_cloud_results : 200;
    else if ($f == 'wall')
      $num = ($tv2_wall_results > 0) ? $tv2_wall_results : 200;
    else
      $num = $tv2_results;
  }
tv2_sql_open ();
$config = config_xml ();
$c = tv2_get_request_value ('c'); // category


$d_array = NULL;

// category   
$category = config_xml_by_category (strtolower ($c));
if (isset ($category->index) || isset ($category->stripdir))
  {
    $d_array = tv2_stripdir (isset ($category->index) ? $category->index : $category->stripdir, $start, $num ? $num : 0);
  }
else if ($f == 'extern')
  {
//tv2_sql ($c, $q, $f, $v, $start, $num, $table_suffix = NULL)          
    $d_array = tv2_sql ($c, $q, 'extern', NULL, $start, $num);
  }
else
  {
    // use SQL
    if ($v)
      $d_array = tv2_sql (NULL, NULL, $f, $v, 0, 0, $category->table_suffix);
    else
      $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num ? $num : 0, $category->table_suffix);
  }

tv2_sql_close ();

// DEBUG
//echo '<pre><tt>';
//print_r ($d_array);
//exit;


// stats RSS
if ($f == 'stats')
  {
    $p = tv2_stats_rss ();
  }
// RSS only
else
  {
    $p = tv2_rss ($d_array);
  }

// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else
echo $p;


exit;

}


?>