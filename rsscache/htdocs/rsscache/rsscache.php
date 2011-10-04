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
//require_once ('misc/wikipedia.php');
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
if (!($output))
  $output = $rsscache_default_output;
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


$config = config_xml ();

// DEBUG
//echo '<pre><tt>';
//print_r ($config);
//echo generate_rss2 ($config['channel'], $config['item'], 1, 1);
//exit;


// NOT admin
if ($rsscache_admin == 0)
  {
    if (in_array ($output, array ('pls', 'm3u', 'ansisql',)))
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
    $a = array ('channel' => rsscache_default_channel (), 'item' => NULL);
    $a['channel']['description'] = $p;
  }
else if ($f == 'config' || $f == 'stats' || $output == 'sitemap')
  {
    $config = rsscache_add_stats ($config);
    $a = $config;
  }
else
  {
    // use SQL
    if ($item)
      $a = rsscache_sql ($c, NULL, $f, $item, 0, 0);
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
      {
        for ($j = 0; isset ($a['item'][$i]['rsscache:feed_'.$j.'_link']); $j++)
          {
            // hide feeds
            unset ($a['item'][$i]['rsscache:feed_'.$j.'_link']);
            unset ($a['item'][$i]['rsscache:feed_'.$j.'_exec']);
            // hide direct download
            unset ($a['item'][$i]['rsscache:download']);
          }

        $a['item'][$i]['cms:button_html'] = 'widget_button()';
        $a['item'][$i]['cms:option_html'] = 'widget_option()';
        $a['item'][$i]['cms:subdomain'] = $a['channel']['cms:subdomain'];
      }
  }
else
  {
    // this is slow and requires external resources
    for ($i = 0; isset ($a['item'][$i]); $i++)
      {
/*
Optional element to specify geographical information about various locations
captured in the content of a media object.  The format conforms to geoRSS.

<media:location description="My house" start="00:01" end="01:00">
  <georss:where>
    <gml:Point>
      <gml:pos>35.669998 139.770004</gml:pos>
    </gml:Point>
  </georss:where>
</media:location>

description description of the place whose location is being specified.

start time at which the reference to a particular location starts in the media object.

end time at which the reference to a particular location ends in the media object.
*/
        // GeoIP of link
//        geoip_load_shared_mem ($rsscache_geoip_dat);
//        $t = geoip_open ($rsscache_geoip_dat, GEOIP_SHARED_MEMORY);
//        $t = geoip_open ($rsscache_geoip_dat, GEOIP_STANDARD);
//        $t = geoip_record_by_addr ($t, '24.24.24.24');
//print_r ($t);
//geoip_close ($t);

/*
        <media:content 
               url="http://www.foo.com/movie.mov" 
               fileSize="12216320" 
               type="video/quicktime"
               medium="video"
               isDefault="true" 
               expression="full" 
               bitrate="128" 
               framerate="25"
               samplingrate="44.1"
               channels="2"
               duration="185" 
               height="200"
               width="300" 
               lang="en" />
*/
        // direct download
        $id = youtube_get_videoid ($a['item'][$i]['link']);
        $b = youtube_get_download_urls ($id, 0, $debug);
        for ($j = 0; isset ($b[$j]); $j++)
          {
            if (in_array ($output, array ('pls', 'm3u',)))
              $a['item'][$i]['media:content_'.$j.'_url'] = urlencode ($b[$j]);
            else
              $a['item'][$i]['media:content_'.$j.'_url'] = $b[$j];
            $a['item'][$i]['media:content_'.$j.'_medium'] = 'video';
//            $a['item'][$i]['media:content_'.$j.'_duration'] = $a['item'][$i]['media:duration'];
//            $a['item'][$i]['media:content_'.$j.'_width'] = 400;
//            $a['item'][$i]['media:content_'.$j.'_height'] = 300;
          }

        // HACK: enrich with information from wikipedia?
//        $a['item'][$i]['rsscache:wikipedia'] = 
      }
  }


// DEBUG
//echo '<pre><tt>';
//print_r ($a);
//echo generate_rss2 ($a['channel'], $a['item'], 1, 1);
//exit;

if ($output == 'json')
  {
    $a['channel']['description'] = str_replace (array ('&amp;', '&nbsp;', '<br>'),
                                                array ('&', ' ', "\n"), $a['channel']['description']);
  }

if ($output == 'ansisql')
  {
    $p = rsscache_write_ansisql ($a, $rsscache_category, $table_suffix, $rsscache_sql_db);
  }
else
  {
    // generate RSS (and transform using XSL)
    $s = NULL;
    if ($output)
      {
        $s = ''
//            .'http://'.$_SERVER['SERVER_NAME'].'/'
            .$rsscache_xsl_stylesheet_path
            .'/rsscache_'.basename ($output).'.xsl';

        if (!file_exists ($s))
          {
            $output = NULL;
            $rsscache_xsl_trans = 0;
            $s = NULL;
          }
      }

    if ($rsscache_xsl_trans > 0 && in_array ($output, array ('html', 'cms',)))
      {
        // turn into XML for stupid browsers that ignore XSL inside RSS
        $t = ''
//            .'http://'.$_SERVER['SERVER_NAME'].'/'
            .$rsscache_xsl_stylesheet_path
            .'/rsscache_xml.xsl';

        $p = generate_rss2 ($a['channel'], $a['item'], 1, 1, $s);
        $original = $rsscache_xsl_trans;
        $rsscache_xsl_trans = 0;
        $p = rsscache_write_xsl ($p, $t);
        $rsscache_xsl_trans = $original;
      }
    else
      $p = generate_rss2 ($a['channel'], $a['item'], 1, 1, $s);

    if ($s)
      $p = rsscache_write_xsl ($p, $s);
  }

$a = array (
  'js' =>      'Content-type: text/javascript',
  'html' =>    'Content-type: text/html',
  'cms' =>     'Content-type: text/html',
  'json' =>    'Content-type: application/json',
  'rss' =>     'Content-type: application/rss+xml',
//  'rss' =>     'Content-type: application/xml',
//  'xml' =>     'Content-type: application/xml',
  'pls' =>     'Content-type: text/plain',
  'm3u' =>     'Content-type: text/plain',
  'ansisql' => 'Content-type: text/plain',
);
if (isset ($a[$output]))
  header ($a[$output]);
else
//  header ('Content-type: text/xml');
  header ('Content-type: application/xml');
//  header ('Content-type: text/plain');
//    header('content-type: application/xml; charset=UTF-8');

// disable any caching by the browser
//header('Expires: Mon, 14 Oct 2002 05:00:00 GMT'); // Date in the past
//header('Last-Modified: ' .gmdate("D, d M Y H:i:s") .' GMT'); // always modified
//header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
//header('Cache-Control: post-check=0, pre-check=0', false);
//header('Pragma: no-cache'); // HTTP 1.0

// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else
  echo $p;


//date_default_timezone_set ($tz);


exit;
}


?>