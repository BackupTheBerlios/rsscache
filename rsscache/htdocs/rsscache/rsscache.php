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
require_once ('misc/sql.php');
require_once ('misc/misc.php');
require_once ('misc/youtube.php');
require_once ('misc/json.php');
require_once ('rsscache_misc.php');
require_once ('rsscache_sql.php');
require_once ('rsscache_output.php');


function
rsscache_write_playlist ($channel, $item)
{
$debug = 0;
        $p = '';
        for ($i = 0; isset ($item[$i]); $i++)
          {
//          $figlet = misc_exec ($figlet_exe.' "'.$item[$i]['title'].'"');
            $p .= ''
                 .'#'."\n"
//                 .str_replace ("\n", "\n# ", $figlet)."\n"
//                 .'#'."\n"
                 .'# '.$item[$i]['title']."\n"
                 .'# '.$item[$i]['link']."\n"
//                 .'#'."\n"
;
            $id = youtube_get_videoid ($item[$i]['link']);
            $b = youtube_download_single ($id, 0, $debug);
   
            // DEBUG
//            print_r ($b);
//            exit;
            for ($j = 0; isset ($b[$j]); $j++);

            $p .= $b[max (0, $j - 2)]."\n";
          }
  return $p;
}


// main ()


// set user agent for downloads
ini_set('rsscache_user_agent', random_user_agent ());
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

if ($output)
  {
    $s = ''
//        .'http://'.$_SERVER['SERVER_NAME']
        .$rsscache_xsl_stylesheet_path
        .'/rsscache_'.$output.'.xsl';

    if (!file_exists ($s))
      {
        $output = NULL;
        $rsscache_xsl_trans = 0;
        $rsscache_xsl_stylesheet_path = NULL;
      }
    else $rsscache_xsl_stylesheet_path = $s;
  }

if ($f == 'robots')
  {
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

if ($rsscache_admin == 1 && $f == 'cache') // cache (new) items into database
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
    $p = generate_rss2 (array ('title' => rsscache_title (), 'description' => $p), NULL);
  }
else // write feed
  {
    if ($f == 'stats' ||
        ($rsscache_admin == 1 && $f == 'config')) // dump config (w/ new indentation)
      $a = $config;
    else
      {
        // use SQL
        $d_array = NULL;
        if ($item)
          $d_array = rsscache_sql (NULL, NULL, $f, $item, 0, 0);
        else
          $d_array = rsscache_sql ($c, $q, $f, NULL, $start, $num ? $num : 0);

        // DEBUG
//        echo '<pre><tt>';
//        print_r ($d_array[0]);
//        exit;

        $a = $d_array;
      }

    // DEBUG
//    echo '<pre><tt>';
//    print_r ($a);
//    exit;

    // TODO: generate sitemap without db use
    if ($f == 'sitemap') // generate sitemap.xml from config
      $p = rsscache_write_sitemap ($a['channel'], $a['item']);
    else if ($rsscache_admin == 1 && $output == 'playlist')
      $p = rsscache_write_playlist ($a['channel'], $a['item']);
    else if ($output == 'mediawiki')
      $p = rsscache_write_mediawiki ($a['channel'], $a['item'], 0);
    else if ($output == 'json')  
      {
        $a['channel']['description'] = str_replace (array ('&amp;', '&nbsp;', '<br>'),
                                                    array ('&', ' ', "\n"), $a['channel']['description']);
        $p = generate_json ($a['channel'], $a['item'], 1, 1);
      }
    else
      $p =  generate_rss2 ($a['channel'], $a['item'], 1, 1, $rsscache_xsl_stylesheet_path);
  }


rsscache_sql_close ();


// XSL transformation
if ($output == 'html') // TODO: use XSL for everything
if ($rsscache_xsl_stylesheet_path)
  {
    if ($rsscache_xsl_trans == 2) // check user-agent and decide
      {
        // TODO
        $rsscache_xsl_trans = 0;
      }
    if ($rsscache_xsl_trans == 0) // transform on server
      {
        $xsl = file_get_contents ($rsscache_xsl_stylesheet_path);
        $xslt = new XSLTProcessor (); 
        $xslt->importStylesheet (new SimpleXMLElement ($xsl));
        $p = $xslt->transformToXml (new SimpleXMLElement ($p));
      }
    else if ($rsscache_xsl_trans == 1) // transform on client
      {
      }
  }


if ($output == 'js')
  header ('Content-type: text/javascript');
else if ($output == 'html')
  header ('Content-type: text/html');
else if ($output == 'json')
  header ('Content-type: application/json');
else if ($output == 'rss')
  header ('Content-type: application/rss+xml');
//  header ('Content-type: application/xml');
else if ($rsscache_admin == 1 && $output == 'playlist')
  header ('Content-type: text/plain');
else
//  header ('Content-type: text/xml');
  header ('Content-type: application/xml');


// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else
  echo $p;


exit;

}


?>