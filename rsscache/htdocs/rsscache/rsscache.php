<?
if (!defined ('TV2_PHP'))
{
define ('TV2_PHP', 1);
//phpinfo();
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
//require_once ('misc/widget.php');
require_once ('tv2_output.php');
require_once ('tv2_misc.php');


//$t_ms = time_ms ();


function
tv2_body ()
{
  global $tv2_isnew,
         $tv2_videos_s,
         $tv2_player_w,
         $tv2_player_h,
         $tv2_related_s;

  global $tv2_root,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_table_tag,
         $tv2_logo,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire;
  global $config;
  global $f, $c, $q, $v, $start, $num;

  // use SQL
  if ($v)
    $d_array = tv2_sql (NULL, NULL, $f, $v, 0, 0, 0);
  else
    $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num ? $num : 0);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d_array);

  $p = '';

  // flash carousel with icons
//  $p .= widget_carousel ('carousel_xml.php', '100%', 150);

  // icons
  $s = 0;
  $l = sizeof ($config->category);
  $p .= tv2_button_array ($config, '%s ', $s, $l);

  $p .= '<br>'  
       .'<br>'  
;  

  $p .= '<div style="display:inline">';

  $p .= '<center>';

  // logo
  $p .= '<nobr>';
  $p .= tv2_logo ();
  $p .= '</nobr>';

  // search
  $p .= '<nobr>';
  $p .= tv2_search_form ();
  $p .= '</nobr>';

  // stats and version
  $p .= '<br>'.tv2_stats ();

  $p .= '</div>';

  $p .= '</center>';

  // show page-wise navigation (top)
  if (!$v)
    {
//      $p .= '<br>'
//           .'<br>'
//;

  $p .= '<center>';

      $s = tv2_page ($start, $num, sizeof ($d_array));

      if ($s)
        {
          // left play all button
//          $p .= '&nbsp'.tv2_play_all_button ();
          $p .= $s;
        }

      // right play all button
//          $p .= '&nbsp'.tv2_play_all_button ();
    }

//  $p .= '</center>';

  $p .= '<br>'
       .'<br>'
;

  $p .= $tv2_table_tag;
  for ($i = 0; isset ($d_array[$i]); $i++)
    {
  $d = $d_array[$i];
  // output
  $category = config_xml_by_category (strtolower ($d['tv2_category'])); // for logo

  $p .= '<tr>';
  $p .= '<td align="right">';

  // embed player
  if ($v)
{
    $p .= tv2_player ($d);
  $p .= '</td>';
  $p .= '<td>';
}
  else  
    $p .= tv2_time_count ($d);

  // logo
  $p .= '<nobr>&nbsp;'.tv2_button ($category).'&nbsp;</nobr><br>';

  // tv2_include_logo ()
  $p .= '&nbsp;'.tv2_include_logo ($d).'&nbsp;';

  $p .= '</td>';
  $p .= '<td>';

  $p .= '<nobr>';

  // is new?
  if (time () - $d['rsstool_dl_date'] < $tv2_isnew)
    $p .= '<img src="images/new.png" border="0" alt="New!"> ';

  // link
  $s = '&seo='.str_replace (' ', '_', tv2_keywords ($d));
  if ($d['tv2_demux'] > 0)
    $s = misc_getlink ('', array ('v' => $d['rsstool_url_crc32']), true).$s;
  else
    $s = misc_getlink ($d['rsstool_url'], array (), false).$s;

  // title
  $p .= '<b><a href="'.$s.'">'.$d['rsstool_title'].'</a></b>';

  // duration
  $p .= tv2_duration ($d);

//  $p .= '&nbsp;';

  // player button (embed)
  $p .= tv2_player_button ($d);

  $p .= '&nbsp;';

  // related
  $p .= tv2_related_button ($d);

  $p .= '</nobr>';

  $p .= '<div style="width:400px;">';

  $p .= tv2_thumbnail ($d);

  // description
  $p .= tv2_include ($d);

//  $p .= '<br>';

  // direct link
  $p .= ' <nobr>';
  $p .= tv2_direct_link ($d);
  $p .= '</nobr>';


  $p .= '<br>';
  $p .= tv2_move_form ($d);

  $p .= '<div style="color:#bbb;">';
  $p .= tv2_keywords ($d);

  $p .= '</div>';

  $p .= '</td>';
  $p .= '</tr>';

    }
  $p .= '</table>';

  

//  $p .= '<center>';

  // show page-wise navigation (bottom)
  if (!$v)
    {
      $s = tv2_page ($start, $num, sizeof ($d_array));
      if ($s)
        $p .= '<br>'.$s;
    }

  $p .= '</center>';

  return $p;
}




// main ()





$f = get_request_value ('f'); // function
  $c = get_request_value ('c'); // category
  $q = get_request_value ('q'); // search query
  $f = get_request_value ('f'); // function
  $v = get_request_value ('v'); // own video
  $start = get_request_value ('start'); // offset
  if (!($start))
    $start = 0;
  $num = get_request_value ('num'); // number of results
  if (!($num))
    $num = $tv2_results;
//  $user_name = get_request_value ("user_name");
//  $last_visit = get_request_value ('last_visit');
//  $latest_visit = get_request_value ('latest_visit');
//  if (!($latest_visit))
//    $last_visit = $latest_visit = time ();
//  else if ($latest_visit < time () - $tv2_isnew)
//    $last_visit = $latest_visit;


$config = config_xml ();

// RSS only
if ($f == 'rss')
  {
    $d = tv2_sql ($c, $q, $f, NULL, $start, $num);
    tv2_rss ($d);
    exit;
  }
else if ($f == 'captcha')
  {
    tv2_captcha_image ('');
    exit;
  }




  // set cookies
//  if (isset ($_GET['user_name'])) // change user_name in cookie
//    { 
//      $user_name = $_GET['user_name'];
//      if (strlen (trim ($user_name)) == 0)
//        $user_name = get_request_value ('user_name');
//    }
  $a = array (
//           array ('user_name', $user_name),
//           array ('last_visit', ''),
//           array ('latest_visit', ''),
//           array ('c', $c),    
           array ('c', ''),
//           array ('q', $q),
//           array ('f', $f),
//           array ('v', $v),
         );
  for ($i = 0; isset ($a[$i]); $i++)
    setcookie ($a[$i][0], $a[$i][1], $tv2_cookie_expire);







if ($memcache_expire > 0)
  {
    $memcache = new Memcache;
    $memcache->connect ('localhost', 11211) or die ("memcache: could not connect");

    // data from the cache
    $p = unserialize ($memcache->get (md5 ($_SERVER['QUERY_STRING'])));

    if ($p)
      {
        // DEBUG
//        echo 'cached';

        echo $p;

        exit;
     }
  }



header ('Content-type: text/html; charset=utf-8');


if (islocalhost ())
  {
    // admin stuff
  }

$body = tv2_body ();

$head = '<html>'
       .'<head>'
       .'<title>'
       .$tv2_title
       .'</title>'
       .'<link rel="stylesheet" type="text/css" media="screen" href="/tv2.css">'
       .'<meta name="google-site-verification" content="akU6AtYoOtUZ5n8IGHTC3s5uc9AOAnPeqxkckHSi224" />'
       .misc_seo_description ($body)
       .'</head>'
       .$tv2_body_tag
;

$end = ''
//      .'<br><br><br>'
//      .widget_relate ($tv2_name, misc_getlink ($tv2_link, array (), true), NULL, 0, WIDGET_RELATE_ALL)
//      .time_ms () - $t_ms
      .'</body>'
      .'</html>';

// the _only_ echo
$p = $head.$body.$end;

if ($use_gzip == 1)
  echo_gzip ($p);
else echo $p;

// use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($_SERVER['QUERY_STRING']), serialize ($p))
      or die ("memcache: failed to save data at the server");
  }



}


?>