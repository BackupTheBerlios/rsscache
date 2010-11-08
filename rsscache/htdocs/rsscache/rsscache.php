<?php
if (!defined ('TV2_PHP'))
{
define ('TV2_PHP', 1);
//phpinfo();
//error_reporting(E_ALL | E_STRICT);
require_once ('default.php');
require_once ('config.php');
require_once ('misc/misc.php');
//require_once ('misc/widget.php');
require_once ('tv2_output.php');
require_once ('tv2_misc.php');


//$t_ms = time_ms ();


function
tv2_body_item ($i, $d_array)
{
  global $tv2_isnew,
         $tv2_videos_s,
         $tv2_player_w,
         $tv2_player_h,
         $tv2_related_s;

  global $tv2_root,
         $tv2_link,
         $tv2_link_static,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_download_video,
         $tv2_logo,
         $tv2_title,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire,
         $tv2_enable_search,
         $tv2_related_search;
  global $config;
  global $embed, $f, $c, $q, $v, $start, $num, $captcha;

  $p = '';

  $d = $d_array[$i];

  // output
  $d_category = config_xml_by_category (strtolower ($d['tv2_moved'])); // for logo
    
  $p .= '<div class="item">';
    

  // embed player
  $p .= '<div class="preview">';
  $p .= tv2_player_preview ($d);
  $p .= '</div>';

  $p .= '<div class="desc">';
    
  // link
  $s = tv2_link ($d);
  // link as title  
  $p .= '<h2 style="font-size:16px;">';
  $p .= widget_button (NULL, $s, str_shorten ($d['rsstool_title'], 80), $d['rsstool_title']);
    
  // is new?
  if (time () - $d[$f == 'new' ? 'rsstool_dl_date' : 'rsstool_date'] < $tv2_isnew && $f != 'mirror')
    $p .= ' <img src="images/new.png" border="0" alt="New!"> ';

  $p .= '</h2>';

  // player button (embed)
//  if ($f != 'mirror')
//    $p .= tv2_player_button ($d);

  // related
//  if ($f != 'mirror')
//    if ($tv2_related_search)
//      $p .= tv2_related_button ($d);

  $s = tv2_duration ($d);
  if ($s)
    {
      $p .= '<span class="duration">';
      if ($s)
        $p .= $s.' min';
      $p .= '</span>';
    }

  $t = tv2_time_count ($d);
  if ($t)
    {
      $p .= '<span class="age">';
      if ($f != 'mirror')
        $p .= $t;
      $p .= '</span>';
    }

  $p .= '<span class="desctext"><p>';

  // description
  $p .= tv2_include ($d);

  $p .= '&nbsp;</p></span>';

  // direct link
//  $p .= tv2_direct_link ($d);

  $s = widget_media_embed_code ($d['rsstool_url']);
  if ($s)
    {
      $p .= '<div class="embed"><p>';
        $p .= 'Embed code: '.$s;
      $p .= '</p></div>';
    }

  if (isset ($d['movable']))
    if ($d['movable'] == 1 && $f != 'mirror')
      $p .= tv2_move_form ($d);
   
  if (isset ($d['reportable']))
    if ($d['reportable'] == 1 && $f != 'mirror')
      $p .= tv2_report_form ($d);

  // logo
  $p .= '<div class="category">';
  $p .= 'Category:&nbsp;'.tv2_button ($d_category).'&nbsp;';
  $p .= '&nbsp;'.tv2_include_logo ($d).'&nbsp;';
  $p .= '</div>';

  $p .= '<span style="color:#bbb;">Tags: ';
  $p .= tv2_keywords ($d);
  $p .= '</span>';

  $p .= '</div>'; // desc

  $p .= '<div class="clear"></div>';
  $p .= '<div class="clear"></div>';
    
/*
  if ($v)
    {
//      $p .= tv2_prev_video_button ($d);
//      $p .= tv2_next_video_button ($d);
    
      if ($d_category->voteable == 1)   
        $p .= tv2_vote ($d);
    }
  else
    {
      if (isset ($d['voteable']))
        if ($d['voteable'] == 1)
          $p .= tv2_vote_show ($d);
    }
*/    
  $p .= '</div>';

  return $p;
}


function
tv2_body_player ($i, $d_array)
{
  global $tv2_isnew,
         $tv2_videos_s,
         $tv2_player_w,
         $tv2_player_h,
         $tv2_related_s;

  global $tv2_root,
         $tv2_link,
         $tv2_link_static,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_download_video,
         $tv2_logo,
         $tv2_title,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire,
         $tv2_enable_search,
         $tv2_related_search;
  global $config;
  global $embed, $f, $c, $q, $v, $start, $num, $captcha;

  $p = '';

      $d = $d_array[$i];
      // output
      $d_category = config_xml_by_category (strtolower ($d['tv2_moved'])); // for logo
    
      if ($f == '2cols')
        {
          $p .= '<div style="float:left;width:50%;">';
          if (!($i & 1))  
            $p .= '<br>';
        }
      else
        $p .= '<div>';
    
      if ($f != 'mirror')
        $p .= tv2_time_count ($d);
    
      // logo
      $p .= '<nobr>&nbsp;'.tv2_button ($d_category).'&nbsp;</nobr>';
    
      // tv2_include_logo ()
      $p .= '&nbsp;'.tv2_include_logo ($d).'&nbsp;';
    
      $p .= '<nobr>';
    
      // is new?
      if (time () - $d[$f == 'new' ? 'rsstool_dl_date' : 'rsstool_date'] < $tv2_isnew && $f != 'mirror')
        $p .= '<img src="images/new.png" border="0" alt="New!"> ';
    
      // link
      $s = tv2_link ($d);
    
      // link as title  
      $p .= '<b style="font-size:16px;">'
           .widget_button (NULL, $s, str_shorten ($d['rsstool_title'], 80), $d['rsstool_title'])
           .'</b>';
    
      // duration
      $p .= ' '.tv2_duration ($d);
    
//      $p .= '&nbsp;';

      // player button (embed)
//      if ($f != 'mirror')
//        $p .= tv2_player_button ($d);

      $p .= '&nbsp;';

      // related
      if ($f != 'mirror')
        if ($tv2_related_search)
        $p .= tv2_related_button ($d);

      // HACK: fix height
      $p .= '<img src="images/trans.png" height="32" width="1">';

      $p .= '</nobr>';

      $p .= '<br>';

      // embed player
      if ($v)
        {
          $p .= tv2_player ($d);
        }
//      else if ($tv2_related_search && $f == 'related') // we sort related by title for playlist
//        {
//        }
      else
        {
          $p .= tv2_player_preview ($d);
          $p .= '<br>';  
        }

//      $p .= '<br>';

      // description
      $p .= tv2_include ($d);

      $p .= '<br>';

      // direct link
      $p .= ' <nobr>';
//      $p .= tv2_direct_link ($d);
      $s = widget_media_embed_code ($d['rsstool_url']);
      if ($s)
        $p .= '&nbsp;Embed code: '.$s;
      $p .= '</nobr>';

      if (isset ($d['movable']))
      if ($d['movable'] == 1 && $f != 'mirror')
        {
          $p .= '<br><nobr>';
          $p .= tv2_move_form ($d);
          $p .= '</nobr>';
        }
    
      if (isset ($d['reportable']))
      if ($d['reportable'] == 1 && $f != 'mirror')
        {
          $p .= '<br><nobr>';
          $p .= tv2_report_form ($d);
          $p .= '</nobr>';
        }
    
      if ($v)
        {
    //      $p .= tv2_prev_video_button ($d);
    //      $p .= tv2_next_video_button ($d);
    
          if ($d_category->voteable == 1)   
            { 
              $p .= '&nbsp;&nbsp;&nbsp;<nobr>';
              $p .= tv2_vote ($d);
              $p .= '</nobr>';
            }
        }
      else
        {
          if (isset ($d['voteable']))
            if ($d['voteable'] == 1)
            {
              $p .= '&nbsp;&nbsp;&nbsp;<nobr>';
              $p .= tv2_vote_show ($d);
              $p .= '</nobr>';
            }
        }
    
      $p .= '<br><span style="color:#bbb;">';
      $p .= tv2_keywords ($d);
      $p .= '</span>';
      $p .= '</div>';
  return $p;
}


function
tv2_body ()
{
  global $tv2_isnew,
         $tv2_videos_s,
         $tv2_player_w,
         $tv2_player_h,
         $tv2_related_s;

  global $tv2_root,
         $tv2_link,
         $tv2_link_static,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_download_video,
         $tv2_logo,
         $tv2_title,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire,
         $tv2_enable_search,
         $tv2_related_search,
         $tv2_use_database;
  global $config;
  global $embed, $f, $c, $q, $v, $start, $num, $captcha;

  $p = '';

  // icons
  if ($f != 'mirror')
    $p .= tv2_button_array ($config, '%s ', 0, sizeof ($config->category));

//  $p .= '<br>'  
//       .'<br>'  
//;  

//  $p .= '</div>'; // #bodyid

  // embed another page
  if ($embed)
    return $p.tv2_embed ();

  // logo
  $p .= '<nobr>';
  $p .= tv2_logo_func ();
  $p .= '</nobr>';

  if ($tv2_use_database == 0)
    return $p;

  if ($captcha)
    if (widget_captcha_check () || islocalhost ())
      {
        tv2_sql_move ($v, $c);
        $v = NULL;
      }

  // use SQL
  if ($v)
    $d_array = tv2_sql (NULL, NULL, $f, $v, 0, 0, 0);
  else
    $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num ? $num : 0);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d_array);

  // category
  $category = config_xml_by_category (strtolower ($c));

  if ($category->background || $f == 'fullscreen') // background image and fullscreen
    {
      $p .= '<style type="text/css">'."\n"
           .'body {';

      if ($category->background)
        $p .= 'background-image:url(\''.$category->background.'\');'
             .'background-attachment:fixed;'
             .'background-repeat:no-repeat;'
             .'background-position:left center;';

      if ($f == 'fullscreen')
        $p .= 'background-color:#000;';

      $p .= "}\n"
           .'</style>';
    }

  // just fullscreen
  if ($v)
    {
      if (strlen ($tv2_title))
        $tv2_title .= ' - ';
      $tv2_title .= $d_array[0]['rsstool_title'];

      if ($f == 'fullscreen')
        {
          $p .= tv2_player ($d_array[0])
               .'<br>';
          return $p;
        }
    }

  $p .= '<div style="display:inline;">';

  // logo
//  $p .= '<nobr>';
//  $p .= tv2_logo_func ();
//  $p .= '</nobr>';

  if ($f != 'mirror')
    {
      // search
      if ($tv2_enable_search)
        {
          $p .= '&nbsp;<nobr>';
          $p .= tv2_search_form ();
          $p .= '</nobr>';
        }
    
//      // stats and version
//      $p .= '<br>'.tv2_stats ();
    }

  $p .= '</div>';

  // show stats of RSS downloads
  if ($f == 'stats')
    {
      $p .= '<br>'
           .'<br>'
;
      $p .= tv2_f_stats ();

      return $p;
    }
  else if ($f == 'upload')
    {
      $p .= '<br>'
           .'<br>'
;
      $p .= tv2_f_upload ();

      return $p;
    }

  // show page-wise navigation (top)
  if (!$v && $f != 'mirror')
    {
//      $p .= '<br>'
//           .'<br>'
//;
      $p .= ' '.tv2_page ($start, $num, sizeof ($d_array));
    }

  if (sizeof ($d_array) == 0)
    $p .= '<br><br>:(';

  $p .= '<br>'
//       .'<br>'
;

  // show as cloud
  if ($f == 'cloud' || $f == 'wall')
    {
      for ($i = 0; isset ($d_array[$i]); $i++)
        {
          $d = $d_array[$i];
          $p .= tv2_thumbnail ($d, 120, 1).' ';
        }

      return $p;
    }

  // playlist
  if ($f == 'playall')
    {
      $p .= tv2_player_playlist ($d_array);
      return $p;
    }

  // media player
  if ($v)
    {
      $p .= tv2_body_player (0, $d_array);
    }
  else
    {
      // items
      if ($f == '2cols')
        $p .= '<div id="double_column_view">';
      for ($i = 0; isset ($d_array[$i]); $i++)
        $p .= tv2_body_item ($i, $d_array);
      if ($f == '2cols')
        {
          $p .= '</div>';
          $p .= '<div class="clear"></div>';
        }
    }
  $p .= '<br>';
 
  // logo
  $p .= '<nobr>'; 
  $p .= tv2_logo_func ();
  $p .= '</nobr>';

  if ($f != 'mirror')  
    {
      // search
      if ($tv2_enable_search)
        {
          $p .= '&nbsp;<nobr>';
          $p .= tv2_search_form ();
          $p .= '</nobr>';  
        }
    }

  // show page-wise navigation (bottom)
  if (!$v && $f != 'mirror')
    {
      $s = ' '.tv2_page ($start, $num, sizeof ($d_array));
      if ($s)
        $p .= $s;
    }

  return $p;
}


// main ()


$config = config_xml ();
$embed = get_request_value ('embed'); // embed other page
$f = get_request_value ('f'); // function
$c = tv2_get_category (); // category
$q = get_request_value ('q'); // search query
$v = get_request_value ('v'); // own video
$captcha = get_request_value ('captcha'); // is request with captcha
$start = get_request_value ('start'); // offset
if (!($start))
  $start = 0;
$num = get_request_value ('num'); // number of results
if (!($num))
  {
    if ($f == 'cloud')
      $num = ($tv2_cloud_results > 0) ? $tv2_cloud_results : 200;
    else if ($f == 'wall')
      $num = ($tv2_wall_results > 0) ? $tv2_wall_results : 200;
    else
      $num = $tv2_results;
  }


if ($f == 'read' ||
    $f == 'write')
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
        setcookie ('rw', $_SERVER['HTTP_REFERER'], $tv2_cookie_expire);
      }

    // redirect
//    header ('refresh: 0; url='.get_cookie ('rw'));
    header ('location:'.get_cookie ('rw'));
    exit;
  }
/*
if ($f == 'fullscreen')
  {
    $a = misc_get_browser_config ();
    setcookie ('w', $a['w'], $tv2_cookie_expire);
    setcookie ('h', $a['h'], $tv2_cookie_expire);
  }
*/


// RSS only
if ($f == 'rss')
  {
    $d = tv2_sql ($c, $q, $f, NULL, $start, $num);
    tv2_rss ($d);
    exit;
  }


if ($f == 'mirror')
  {
    // make static (index.html)
  }


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

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';
        exit; 
      }
  }

if (file_exists ('images/captcha/'))
  $tv2_captcha = widget_captcha ('images/captcha/');
else
  $tv2_captcha = '';

$body = tv2_body ();

$head = '<html>'
       .'<head>'
       .'<title>'
       .$tv2_title
       .'</title>'
       .'<link rel="stylesheet" type="text/css" media="screen" href="tv2/tv2.css">'
       .'<script type="text/javascript" src="misc/jquery.js"></script>'
       .'<script type="text/javascript" src="misc/jquery_ui.js"></script>'
//       .'<script type="text/javascript" src="misc/jquery_easing.js"></script>'
       .'<script type="text/javascript" src="misc/jquery_lavalamp.js"></script>'
       .'<script type="text/javascript" src="misc/misc.js"></script>'
       .'<script type="text/javascript" src="tv2/tv2.js"></script>'
       .misc_seo_description ($body)
;

$head .= misc_head_tags ($tv2_icon, 0, $tv2_charset);

if ($tv2_rss_head)
  {
    $a = array (
//                'c' => $c,
//                'q' => $q,
                'f' => 'rss',
//                'v' => $v,
//                'start' => $start,
//                'num' => $num
);
    $head .= '<link rel="alternate" type="application/rss+xml" title="'.$tv2_title.'" href="?'.http_build_query2 ($a, true).'">';
  }

$head .= $tv2_head_tag;

$head .= '</head>';

// site links at the top
if (file_exists ('site_config.xml'))
  {
    $site_config_xml = simplexml_load_file ('site_config.xml');
    $head .= ''
            .'<span style="font-family:sans-serif;font-size:13px;">'
            .widget_cms (NULL, $site_config_xml)
            .'</span>'
;
  }

$head .= $tv2_body_tag
//        .'<div id="bodyid">'
;

$end = '';

if ($tv2_use_database)
  if ($f != 'mirror')
  {
    // stats and version
    $end .= '<br><div style="width:100%;text-align:right;">'.tv2_stats ().'</div>';
  }


if ($f != 'fullscreen')
  $end .= ''
         .'<br>'
         .tv2_include_end ();

//$end .= '</div>'; // #bodyid

$end .= '</body>'
       .'</html>';


// the _only_ echo
$p = $head.$body.$end;

if ($use_gzip == 1)
  echo_gzip ($p);
else echo $p;

// use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']), serialize ($p), 0, $memcache_expire);
  }


}


?>