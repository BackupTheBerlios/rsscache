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
require_once ('misc/rss.php');
require_once ('misc/json.php');
require_once ('misc/sql.php');
require_once ('misc/misc.php');
require_once ('misc/youtube.php');
require_once ('misc/wikipedia.php');
require_once ('misc/widget_media.php');
require_once ('rsscache_misc.php');
require_once ('rsscache_sql.php');
require_once ('rsscache_output.php');


// main ()


// remove timezone warning
//$tz = date_default_timezone_get ();
//date_default_timezone_set ('UTC');

// set user agent for downloads
ini_set('user_agent', random_user_agent ());
$rsscache_user_agent = random_user_agent ();


$debug = 0;
$c = rsscache_get_request_value ('c'); // category
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

// NOT admin
if ($rsscache_admin == 0)
  {
    if (in_array ($output, array ('pls', )))
      $output = NULL;
    if (in_array ($f, array ('cache', 'config')))
      $f = NULL;
  }

if ($f == 'robots')
  {
    header ('Content-type: text/plain');
    echo rsscache_write_robots ();
    exit;
  }

rsscache_sql_open ();

$config = config_xml ();
// DEBUG
//echo '<pre><tt>';
//print_r ($config);
//echo generate_rss2 ($config['channel'], $config['item'], 1, 1);
//exit;

if ($f == 'cache') // cache (new) items into database
  {
    if ($c)
      {
        ob_start ();
        rsscache_download_feeds_by_category ($c);
        $p = str_replace ("\n", "<br>\n", ob_get_contents ());
        ob_end_clean ();
        $p .= '<br><br>success';
      }
    else
      $p = '&c=CATEGORY required';
    $a = array ('config' => array ('title' => rsscache_title (), 'description' => $p),
                'item' => NULL);
  }
else if ($f == 'config' || $f == 'stats' || $output == 'sitemap')
  {
    $a = $config;
  }
else
  {
    // use SQL
    if ($item)
      $a = rsscache_sql (NULL, NULL, $f, $item, 0, 0);
    else
      $a = rsscache_sql ($c, $q, $f, NULL, $start, $num ? $num : 0);
  }

rsscache_sql_close ();

// DEBUG
//echo '<pre><tt>';
//print_r ($a);
//exit;


// normalize again
if ($rsscache_admin == 0)
  {
    for ($i = 0; isset ($a['item'][$i]); $i++)
      for ($j = 0; isset ($a['item'][$i]['rsscache:feed_'.$j.'_link']); $j++)
        {
          // hide feeds
          unset ($a['item'][$i]['rsscache:feed_'.$j.'_link']);
          unset ($a['item'][$i]['rsscache:feed_'.$j.'_opts']);
          unset ($a['item'][$i]['rsscache:feed_'.$j.'_client']);
          // hide direct download
          unset ($a['item'][$i]['rsscache:download']);
        }
  }
else
  {
    // this is slow and requires external resources
    for ($i = 0; isset ($a['item'][$i]); $i++)
      {
        if ($output == 'pls')
          $a['item'][$i]['rsscache:download'] = rsscache_download_videos ($a['item'][$i]);

        // enrich with information from wikipedia
//        $a['item'][$i]['rsscache:wikipedia'] = 
      }
  }


// DEBUG
//echo '<pre><tt>';
//print_r ($a);
//exit;

if ($output == 'json')
  {
    $a['channel']['description'] = str_replace (array ('&amp;', '&nbsp;', '<br>'),
                                                array ('&', ' ', "\n"), $a['channel']['description']);
    $p = generate_json ($a['channel'], $a['item'], 1, 1);
  }
else if ($output == 'atom')
  {
  }
else // generate RSS (and transform using XSL)
  {
    $s = NULL;
    if ($output)
      {
        $s = ''
//            .'http://'.$_SERVER['SERVER_NAME']
            .$rsscache_xsl_stylesheet_path
            .'/rsscache_'.basename ($output).'.xsl';

        if (!file_exists ($s))
          {
            $output = NULL;
            $rsscache_xsl_trans = 0;
            $s = NULL;
          }
      }

    $p = generate_rss2 ($a['channel'], $a['item'], 1, 1, $s);

    // XSL transformation
    if ($s)
      {
        if ($rsscache_xsl_trans == 2) // check user-agent and decide
          {
            // TODO
            $rsscache_xsl_trans = 0;
          }
        if ($rsscache_xsl_trans == 0) // transform on server
          {
            $xsl = file_get_contents ($s);
            $xslt = new XSLTProcessor (); 
            $xslt->importStylesheet (new SimpleXMLElement ($xsl));
            $p = $xslt->transformToXml (new SimpleXMLElement ($p));
          }
        else if ($rsscache_xsl_trans == 1) // transform on client
          {
          }
      }
  }


$a = array (
  'js' =>    'Content-type: text/javascript',
  'html' =>  'Content-type: text/html',
  'json' =>  'Content-type: application/json',
  'rss' =>   'Content-type: application/rss+xml', 
//  'rss' => 'Content-type: application/xml',
  'pls' =>   'Content-type: text/plain',
);
if (isset ($a[$output]))
  header ($a[$output]);
else
//  header ('Content-type: text/xml');
  header ('Content-type: application/xml');


// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else
  echo $p;


//date_default_timezone_set ($tz);


exit;
}


?>