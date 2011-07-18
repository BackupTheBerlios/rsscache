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
// language settings
if (isset ($tv2_lang_php)) 
  include ($tv2_lang_php);
else
  include ('tv2/tv2_lang.php');
require_once ('tv2_output.php');
require_once ('tv2_output_body.php');
require_once ('tv2_misc.php');
require_once ('tv2_draw.php');


// main ()


// maintenance?
$tv2_maintenance = 0;
if (file_exists ($_SERVER['DOCUMENT_ROOT'].'/maintenance_'.$tv2_subdomain.'.tmp'))
  $tv2_maintenance = 1;
if ($tv2_maintenance == 1)
  {
//    echo 'maintenance - please come back';
//    exit;
    $tv2_use_database = 0;
  }

$f = tv2_get_request_value ('f'); // function
$q = tv2_get_request_value ('q'); // search query

// download youtube video
if ($f == 'download')
  {
    tv2_youtube_download ($q);
    exit;
  }

// QR code image
if ($f == 'qrcode')
  {
    tv2_qrcode ($q, 2);
    exit;
  }

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
if ($tv2_use_database == 1)
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
else if ($tv2_use_database == 1)
  {
    // use SQL
    if ($v)
      $d_array = tv2_sql (NULL, NULL, $f, $v, 0, 0, $category->table_suffix);
    else
      $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num ? $num : 0, $category->table_suffix);
  }


if ($tv2_use_database == 1)
  if ($captcha)
    if (widget_captcha_check () || islocalhost ())
    {
      tv2_sql_move ($v, $c);
      $v = NULL;
    }


if ($f == 'read' || $f == 'write')
  {
    if ($f == 'write')
      {
        // set cookie
/*
        $a = array ('c' => $c,
                    'q' => $q,
                    'f' => $f,
                    'v' => $v,
                    'start' => $start,
                    'num' => $num
);

        setcookie ('rw', http_build_query2 ($a, false), $tv2_cookie_expire);
*/
//        setcookie ('rw', $_SERVER['HTTP_REFERER'], $tv2_cookie_expire);
      }

    // redirect
//    header ('refresh: 0; url='.get_cookie ('rw'));
//    header ('location:'.get_cookie ('rw'));

    if ($tv2_use_database == 1)
      tv2_sql_close ();

    exit;
  }


// generate embeddable banner for item
if ($tv2_banner == 1)
  if ($v && $f == 'banner')
  {
    tv2_draw_banner ($d_array[0]);
    exit;
  }


// RSS only
if ($tv2_use_database == 1)
  if ($f == 'rss')
  {
    echo tv2_rss ($d_array);
    tv2_sql_close ();
    exit;
  }


// stats RSS
if ($tv2_use_database == 1)
  if ($f == 'stats')
  {
    echo tv2_stats_rss ();
    tv2_sql_close ();
    exit;
  }


// sitemap only
if ($tv2_use_database == 1)
  if ($f == 'sitemap')
  {
    echo tv2_sitemap ($d_array);
    tv2_sql_close ();
    exit;
  }


// robots.txt only
if ($tv2_use_database == 1)
  if ($f == 'robots')
  {
    echo tv2_robots ();
    tv2_sql_close ();
    exit;
  }


if ($f == 'mirror')
  {
    // make static (index.html)
  }


/*
if ($tv2_use_database == 1)
  if ($memcache_expire > 0)
  {
    $memcache = new Memcache;
    if ($memcache->connect ('localhost', 11211) == TRUE)
      {
        // data from the cache
        $p = $memcache->get (md5 ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));

        if ($p != FALSE)
          {
            $p = unserialize ($p);

            // DEBUG
//            echo 'cached';

            echo $p;

              tv2_sql_close ();

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';

          tv2_sql_close ();

        exit; 
      }
  }
*/


$tv2_captcha = '';   
if (file_exists ('images/captcha/'))
  $tv2_captcha = widget_captcha ('images/captcha/');

$head_rss = ($tv2_rss_head ? misc_head_rss (tv2_title ($d_array), '?'.http_build_query2 (array ('f' => 'rss'), true))
                    .misc_head_rss ('Statistics', '?'.http_build_query2 (array ('f' => 'stats'), true)) : '');
if ($tv2_maintenance == 1)
  {
    $body = '<br><br><b>maintenance - please come back or wait a few seconds</b><br><br><br>';
    $tv2_head_tag .= '<meta http-equiv="refresh" content="3;URL=http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">';
  }
else
  $body = tv2_body ($d_array);
if ($f == 'fullscreen' || $f == 'popout')
  {
$template_replace = array (
  '<!-- parse:title -->'       => tv2_title ($d_array),
  '<!-- parse:icon -->'        => misc_head_tags ($tv2_icon, 0, $tv2_charset),
  '<!-- parse:head_seo -->'    => misc_seo_description ($body),
  '<!-- parse:head_tag -->'    => $tv2_head_tag,
  '<!-- parse:body_tag -->'    => $tv2_body_tag,
  '<!-- parse:body_header -->' => '',
  '<!-- parse:body -->'        => $body,
  '<!-- parse:body_footer -->' => '',
  '<!-- parse:head_rss -->'    => $head_rss,
  '<!-- parse:small_stats -->' => $config->items.' <!-- lang:items -->&nbsp;&nbsp;'
                                 .$config->days.' <!-- lang:days -->',
);

if (file_exists ('tv2_popout.html'))
  $template = file_get_contents ('tv2_popout.html');
else
  $template = file_get_contents ('tv2/tv2_popout.html');
$p = misc_template ($template, $template_replace);
$p = misc_template ($p, $tv2_translate[$tv2_language ? $tv2_language : 'default']);
  }
else
  {
$template_replace = array (
  '<!-- parse:title -->'       => tv2_title ($d_array),
  '<!-- parse:icon -->'        => misc_head_tags ($tv2_icon, 0, $tv2_charset),
  '<!-- parse:head_seo -->'    => misc_seo_description ($body),
  '<!-- parse:head_tag -->'    => $tv2_head_tag,
  '<!-- parse:body_tag -->'    => $tv2_body_tag,
  '<!-- parse:body_header -->' => tv2_body_header ($d_array),
  '<!-- parse:body -->'        => $body,
  '<!-- parse:body_footer -->' => tv2_body_footer ($d_array),
  '<!-- parse:head_rss -->'    => $head_rss,
  '<!-- parse:small_stats -->' => $config->items.' <!-- lang:items -->&nbsp;&nbsp;'
                                 .$config->days.' <!-- lang:days -->',
);

if (file_exists ('tv2_index.html'))
  $template = file_get_contents ('tv2_index.html');
else
  $template = file_get_contents ('tv2/tv2_index.html');
$p = misc_template ($template, $template_replace);
$p = misc_template ($p, $tv2_translate[$tv2_language ? $tv2_language : 'default']);
  }


// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else echo $p;

if ($tv2_use_database == 1)
  tv2_sql_close ();

// use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']), serialize ($p), 0, $memcache_expire);
  }


}


?>