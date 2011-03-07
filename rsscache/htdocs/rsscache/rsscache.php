<?php
if (!defined ('TV2_PHP'))
{
define ('TV2_PHP', 1);
//phpinfo();
//error_reporting(E_ALL | E_STRICT);
// language settings
require_once ('config_lang.php');
require_once ('default.php');
require_once ('config.php');
require_once ('misc/misc.php');
//require_once ('misc/widget.php');
require_once ('tv2_output.php');
require_once ('tv2_misc.php');


//$t_ms = time_ms ();


function
tv2_highlight ($s)
{
//  $q = get_request_value ('q');
  // highlight search words
//  $a = explode (array (' ', '+'), $q);
  // DEBUG
//  print_r ($a);
//  for ($i = 0; isset ($a[$i]); $i++)
//    $s = str_ireplace ($a[$i], '<span class="tv2_highlight">'.$a[$i].'</span>', $s);
  return $s;
}


function
tv2_body_item ($i, $d_array)
{
  global $tv2_isnew,
         $tv2_player_w,
         $tv2_player_h,
         $tv2_preview_w,
         $tv2_preview_h;

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
         $tv2_cookie_expire,
         $tv2_enable_search,
         $tv2_related_search;
  global $config;
  global $f, $c, $q, $v, $start, $num, $captcha;

  $p = '';

  $d = $d_array[$i];

  // output
  $d_category = config_xml_by_category (strtolower ($d['tv2_moved'])); // for logo
    
  $p .= '<div class="item">';

  // embed player
  $p .= '<div class="preview"'
       .' style="width:'.$tv2_preview_w.';height:'.$tv2_preview_h.';"'
       .'>';
  $p .= tv2_player_preview ($d);
  $p .= '</div>';

  if ($f == '2cols')
    $p .= '<div class="desc_2cols">';
  else
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

  $s = tv2_duration ($d);
  if ($s)
    {
      $p .= '<span class="duration">';
      if ($s)
        $p .= $s.' min&nbsp;';
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

  if ($f == '2cols')
    $p .= '<span class="desctext_2cols"><p>';
  else
    $p .= '<span class="desctext"><p>';

  // description
  $d['rsstool_desc'] = tv2_highlight ($d['rsstool_desc']);
  $p .= tv2_include ($d);

  $p .= '&nbsp;</p></span>';

  // direct link
//  $p .= tv2_direct_link ($d);

  $s = widget_media_embed_code ($d['rsstool_url']);
  if ($s)
    {
      $p .= '<div class="embed"><p>';
        $p .= '<!-- lang:Embed code -->: '.$s;
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
  $p .= '<!-- lang:Category -->:&nbsp;'.tv2_button ($d_category).'&nbsp;';
  $p .= '&nbsp;'.tv2_include_logo ($d).'&nbsp;';
  $p .= '</div>';

  $p .= '<span class="tv2_tags">Tags: ';
  $p .= tv2_keywords ($d);
  $p .= '</span>';

  // related
  if ($tv2_related_search)
    {
      $p .= '<div class="tv2_related">';
      $p .= tv2_related_search ($d);
      $p .= '</div>';
    }

  $p .= '</div>'; // desc

  $p .= '<div class="clear"></div>';
  $p .= '<div class="clear"></div>';
    
//      if ($d_category->voteable == 1)   
//        $p .= tv2_vote ($d);

//      if (isset ($d['voteable']))
//        if ($d['voteable'] == 1)
//          $p .= tv2_vote_show ($d);

  $p .= '</div>';

  return $p;
}


function
tv2_body_player ($i, $d_array)
{
  global $tv2_isnew,
         $tv2_player_w,
         $tv2_player_h;

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
         $tv2_cookie_expire,
         $tv2_enable_search,
         $tv2_related_search;
  global $config;
  global $f, $c, $q, $v, $start, $num, $captcha;

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
    
  $t = tv2_time_count ($d);
  if ($t)
    {
      $p .= '<span class="age">';
      if ($f != 'mirror')
        $p .= $t;
      $p .= '</span>';
    }
    
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

      // HACK: fix height
      $p .= '<img src="images/trans.png" height="32" width="1">';

      $p .= '</nobr>';

      $p .= '<br>';

      // embed player
          $p .= tv2_player ($d);

//      $p .= '<br>';

      // description
      $d['rsstool_desc'] = tv2_highlight ($d['rsstool_desc']);
      $p .= tv2_include ($d);

      $p .= '<br>';

      // direct link
  $s = widget_media_embed_code ($d['rsstool_url']);
  if ($s)
    {   
      $p .= '<div class="embed"><p>';
        $p .= '<!-- lang:Embed code -->: '.$s;
      $p .= '</p></div>';
    }

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
    
    //      $p .= tv2_prev_video_button ($d);
    //      $p .= tv2_next_video_button ($d);
    
          if ($d_category->voteable == 1)   
            { 
              $p .= '&nbsp;&nbsp;&nbsp;<nobr>';
              $p .= tv2_vote ($d);
              $p .= '</nobr>';
            }

//          if (isset ($d['voteable']))
//            if ($d['voteable'] == 1)
//            {
//              $p .= '&nbsp;&nbsp;&nbsp;<nobr>';
//              $p .= tv2_vote_show ($d);
//              $p .= '</nobr>';
//            }
    
      $p .= '<br>';
  $p .= '<span class="tv2_tags"><!-- lang:Tags -->: ';
  $p .= tv2_keywords ($d);
  $p .= '</span>';  

  // related
  if ($tv2_related_search)
    {
      $p .= '<div class="tv2_related">';
      $p .= tv2_related_search ($d);
      $p .= '</div>';
    }

  $p .= '</div>';

  return $p;
}


function
tv2_body ()
{
  global $tv2_isnew,
         $tv2_player_w,
         $tv2_player_h;

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
         $tv2_cookie_expire,
         $tv2_enable_search,
         $tv2_related_search,
         $tv2_use_database,
         $tv2_collapsed;
  global $config;
  global $f, $c, $q, $v, $start, $num, $captcha;

  $p = '';

// site links at the top
if (file_exists ('site_config.xml'))
  {
    $site_config_xml = simplexml_load_file ('site_config.xml');
    $p .= ''
            .'<span class="tv2_site">'
            .widget_cms (NULL, $site_config_xml, NULL, 4)
            .'</span>'
            .'&nbsp;&nbsp;'
            .widget_gecko_install ();
  }

  // logo  
//  $p .= '<div style="float:right;">';
  $p .= tv2_logo_func ();
//  $p .= '</div>';
//  $p .= '<div class="clear;">';
//  $p .= '</div>';

  $p .= '<br>';
  $p .= '<br>';

  // category buttons
  if ($tv2_collapsed == 2) // never
    {
      $p .= ''
           .'<div class="tv2_button">'
           .widget_cms (NULL, $config, NULL, 8)
           .'</div>'
;
    }
  else if ($tv2_collapsed >= 0)
    {
      $collapsed = $c ? 1 : $tv2_collapsed;
      $p .= ''
           .'<div class="tv2_button">'
//widget_collapse ($label, $s, $collapsed)
           .widget_collapse ('', widget_cms (NULL, $config, NULL, 8), $collapsed)
           .'</div>'
;
    }

  $p .= '<br>'  
//       .'<br>'  
;  
  // category   
  $category = config_xml_by_category (strtolower ($c));

  // embed another page
  if (isset ($category->local))
    return $p.tv2_f_local ();
  else if (isset ($category->iframe))
    return $p.tv2_f_iframe ();
  else if (isset ($category->proxy))
    return $p.tv2_f_proxy ();
  else if (isset ($category->wiki))
    return $p.tv2_f_wiki ();
  else if (isset ($category->localwiki))
    return $p.tv2_f_localwiki ();
  else if (isset ($category->phpbb3))
    return $p.tv2_f_phpbb3 ();
  else if ($f == 'extern')
    return $p.tv2_f_extern ();

  if ($captcha)
    if (widget_captcha_check () || islocalhost ())
      {
        tv2_sql_move ($v, $c);
        $v = NULL;
      }

  if (isset ($category->index) || isset ($category->stripdir))
    {
      $d_array = tv2_stripdir (isset ($category->index) ? $category->index : $category->stripdir, $start, $num ? $num : 0);
      return $p.tv2_player_multi ($d_array);
    }
  else if ($tv2_use_database == 1)
    {
      // use SQL
      if ($v)
        $d_array = tv2_sql (NULL, NULL, $f, $v, 0, 0, 0);
      else
        $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num ? $num : 0);
    }
  else return $p;

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d_array);

/*
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
*/

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
//  $p .= tv2_logo_func ();

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

  // show page-wise navigation (top)
  if (!$v && $f != 'mirror')
    $p .= ' '.tv2_page ($start, $num, sizeof ($d_array));

  if (file_exists ('func_config.xml'))
    {
      $func_config_xml = simplexml_load_file ('func_config.xml');
      $p .= ''
           .'<div class="tv2_func">'
           .widget_cms (NULL, $func_config_xml, http_build_query2 (array (), true), 4)
           .'</div>'
;
      $p .= '<div class="clear"></div>';
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

if ($tv2_use_database)
  if ($f != 'mirror')
  {
    // stats and version
    $p .= '<br><div style="width:100%;text-align:right;">'.tv2_stats ().'</div>';
  }

  return $p;
}


// main ()

if ($tv2_use_database)
  tv2_sql_open ();
$config = config_xml ();
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
//        setcookie ('rw', $_SERVER['HTTP_REFERER'], $tv2_cookie_expire);
      }

    // redirect
//    header ('refresh: 0; url='.get_cookie ('rw'));
//    header ('location:'.get_cookie ('rw'));

    if ($tv2_use_database)
      tv2_sql_close ();

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
    echo tv2_rss ($d);

    if ($tv2_use_database)
      tv2_sql_close ();

    exit;
  }


// sitemap only
if ($f == 'sitemap')
  {
//    $d = tv2_sql ($c, $q, $f, NULL, $start, $num);
    echo tv2_sitemap ($d);

    if ($tv2_use_database)
      tv2_sql_close ();

    exit;
  }


// robots.txt only
if ($f == 'robots')
  {
    echo tv2_robots ();

    if ($tv2_use_database)
      tv2_sql_close ();

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

            if ($tv2_use_database)
              tv2_sql_close ();

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';

        if ($tv2_use_database)
          tv2_sql_close ();

        exit; 
      }
  }

$tv2_captcha = '';   
if (file_exists ('images/captcha/'))
  $tv2_captcha = widget_captcha ('images/captcha/');

$body = tv2_body ();
$template_replace = array (
  '<!-- parse:title -->'    => $tv2_title,
  '<!-- parse:icon -->'     => misc_head_tags ($tv2_icon, 0, $tv2_charset),
  '<!-- parse:head_seo -->' => misc_seo_description ($body),
  '<!-- parse:body -->'     => $body,
  '<!-- parse:body_tag -->' => $tv2_body_tag,
  '<!-- parse:head_tag -->' => $tv2_head_tag,
  '<!-- parse:body_end -->' => tv2_include_end (),
);

if ($tv2_rss_head)  
  $template_replace['<!-- parse:head_rss -->'] =
    '<link rel="alternate" type="application/rss+xml"'
   .' title="'.$tv2_title.'"'
   .' href="?'.http_build_query2 (array ('f' => 'rss'), true).'">';
if (file_exists ('tv2_index.html'))
  $template = file_get_contents ('tv2_index.html');
else
  $template = file_get_contents ('tv2/tv2_index.html');
$p = misc_template ($template, $template_replace);
$p = misc_template ($p, $tv2_translate['default']);

// the _only_ echo
if ($use_gzip == 1)
  echo_gzip ($p);
else echo $p;

if ($tv2_use_database)
  tv2_sql_close ();

// use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']), serialize ($p), 0, $memcache_expire);
  }


}


?>