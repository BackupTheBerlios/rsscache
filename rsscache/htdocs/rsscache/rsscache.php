<?php
/*
rsscache.php - rsscache engine

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
if (!defined ('RSSCACHE_PHP'))
{
define ('RSSCACHE_PHP', 1);
//phpinfo();
//exit;
//error_reporting(E_ALL | E_STRICT);
require_once ('default.php');
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('rsscache_misc.php');
require_once ('rsscache_sql.php');
require_once ('rsscache_output.php');


// main ()


$f = rsscache_get_request_value ('f'); // function
$output = rsscache_get_request_value ('output'); // output
$q = rsscache_get_request_value ('q'); // search query
$item = rsscache_get_request_value ('item'); // item crc32
$start = rsscache_get_request_value ('start'); // offset
if (!($start))
  $start = 0;
$num = rsscache_get_request_value ('num'); // number of results
if (!($num))
  $num = $rsscache_results;
if ($num > $rsscache_max_results)
  $num = $rsscache_max_results;

rsscache_sql_open ();

$config = config_xml ();
$c = rsscache_get_request_value ('c'); // category

if ($f == 'stats') // db statistics as feed
  {
    $p = rsscache_write_stats_rss ();
  }
else if ($f == 'feed') // download or update feed
  {
    // TODO
  }
else // write feed
  {
    $d_array = NULL;

    // category   
    $category = config_xml_by_category (strtolower ($c));

    // use SQL
    if ($item)
      $d_array = rsscache_sql (NULL, NULL, $f, $item, 0, 0, isset ($category->table_suffix) ? $category->table_suffix : NULL);
    else
      $d_array = rsscache_sql ($c, $q, $f, NULL, $start, $num ? $num : 0, isset ($category->table_suffix) ? $category->table_suffix : NULL);

// DEBUG
//echo '<pre><tt>';
//print_r ($d_array);
//exit;

    $p = rsscache_write_rss ($d_array);
  }

rsscache_sql_close ();


// XSL transformation
if ($output == 'html')
  {
    if ($rsscache_xsl_trans == 2) // check user-agent and decide
      {
        // TODO
        $rsscache_xsl_trans = 0;
      }

    if ($rsscache_xsl_trans == 0) // transform on server
      {
        $xsl = file_get_contents ($rsscache_xsl_stylesheet);
        $xslt = new XSLTProcessor (); 
        $xslt->importStylesheet (new  SimpleXMLElement ($xsl));
        $p = $xslt->transformToXml (new SimpleXMLElement ($p));
      }
    else if ($rsscache_xsl_trans == 1) // transform on client
      {
      }
  }
else // if ($output == 'rss')
  {
//    header ('Content-type: text/xml');
    header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');
  }


// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else
  echo $p;


exit;

}


?>