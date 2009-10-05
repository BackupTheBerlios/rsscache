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
tv2_output_html ($d, $start, $num)
{
  global $tv2_isnew,
         $tv2_videos_s,
         $tv2_player_w,
         $tv2_player_h,
         $tv2_related_s,
         $tv2_captcha;

  $category = config_xml_by_category (strtolower ($d['tv2_category'])); // for logo

  $p = '';

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d);

  $p .= '<div style="text-align:right;vertical-align:top;display:table-cell;">';

  // logo
  $p .= '<nobr>&nbsp;'.tv2_button ($category).'&nbsp;</nobr><br>';

  // tv2_include_logo ()
  $p .= '&nbsp;'.tv2_include_logo ($d).'&nbsp;';

  $p .= '</div>';
  $p .= '<div style="text-align:left;vertical-align:top;display:table-cell;height:32px;">';

  $p .= '<nobr>';

  // is new?
  if (time () - $d['rsstool_dl_date'] < $tv2_isnew)
    $p .= '<img src="images/new.png" border="0" alt="New!"> ';

  // link
  if ($d['tv2_demux'] > 0)
    $s = misc_getlink ('', array ('v' => $d['rsstool_url_crc32']), true);
  else
    $s = misc_getlink ($d['rsstool_url'], array (), false);

  // title
  $p .= '<b><a href="'.$s.'">'.$d['rsstool_title'].'</a></b>';

  // duration
//  if ($d['tv2_duration'] > 0)
    $p .= gmstrftime ($d['tv2_duration'] > 3599 ? ' %H:%M:%S' : ' %M:%S', (int) $d['tv2_duration']);

  // player button (embed)
  $p .= tv2_player_button ($d);

  // related
  $p .= tv2_related_button ($d);

  $p .= '</nobr>';

  // description
  $p .= '<div style="width:400px;">';
  $s = tv2_include ($d);
  if (!empty ($s))
    $p .= $s.'<br>';

  // direct link
  $p .= '<nobr><a href="'.urldecode ($d['rsstool_url']).'">'.urldecode ($d['rsstool_url']).'</a></nobr><br>';
  // HACK
  if ($d['tv2_demux'] == 1) // youtube
    $p .= ''
//         .'<nobr><a href="'.urldecode ($d['rsstool_url']).'&fmt=18">'.'&fmt=18'.'</a> HQ</nobr>&nbsp;'
//         .'<nobr><a href="'.urldecode ($d['rsstool_url']).'&fmt=22">'.'&fmt=22'.'</a> HD</nobr><br>'
;

  $p .= tv2_move_form ($d);

  $p .= tv2_keywords ($d);

  $p .= '</div>';

  $p .= '</div>';

  return $p;
}


function
tv2_rss ($d_array)
{
  global $tv2_link;
  global $tv2_name;
  global $tv2_title;

//    header ('Content-type: text/xml');
    header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $rss_title_array[$i] = $d_array[$i]['rsstool_title'];
      $rss_link_array[$i] = $d_array[$i]['rsstool_url'];
      $rss_desc_array[$i] = $d_array[$i]['rsstool_desc'];
    }

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);

  echo generate_rss ($tv2_name,
                     $tv2_link,
                     $tv2_title,
                     $rss_title_array, $rss_link_array, $rss_desc_array);
}






function
tv2_body ()
{
  global $tv2_root,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_logo,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire;
  global $config;
  global $f, $c, $q, $desc, $v, $start, $num;

  // use SQL
  if ($v)
    $d_array = tv2_sql (NULL, NULL, $desc, $f, $v, 0, 0, 0);
  else
    $d_array = tv2_sql ($c, $q, $desc, $f, NULL, $start, $num ? $num : 0);


  // output


  $p = '';

  // top
//  if (!$v)
//    {
//      $p .= widget_carousel ('carousel_xml.php');
//    }
//  else
    {
      // icons
      $s = 0;
      $l = sizeof ($config->category);
      $p .= tv2_button_array ($config, '%s ', $s, $l);
    }

  // formular
  $p .= tv2_search_form ();

  // show page-wise navigation (top)
  if (!$v)
    {
      $p .= '<br>'
           .'<br>'
;

      $s = tv2_page ($start, $num, sizeof ($d_array));

      if ($s)
        {
/*
          // left play all button
          $p .= '&nbsp<a href="'
               .misc_getlink ('', array ('v' => NULL, 'f' => 'play_all'), true)
               .'" title="Play all videos starting from here (TV)">'
               .'<img src="images/play_all32.png" border="0">'
               .'</a>'
;
*/
          $p .= $s;
        }
/*
      // right play all button
      $p .= '&nbsp<a href="'
           .misc_getlink ('', array ('v' => NULL, 'f' => 'play_all'), true)
           .'" title="Play all videos starting from here (TV)">'
           .'<img src="images/play_all32.png" border="0">'
           .'</a>'
;
*/
    }

  $p .= '<br>'
       .'<br>'
;

//  $p .= '<center>';
  $p .= '<div style="display:table;width:100%">';

  // embed player
  if ($v)
    {
      $p .= '<div style="display:table-row;text-align:center">';  
      $p .= '<div style="text-align:right;vertical-align:top;display:table-cell;">';
      $p .= tv2_player ($d);
      $p .= '</div>';
      $p .= '</div>';

      $p .= '<div style="display:table-row;text-align:center;">';
      $p .= tv2_output_html ($d_array[0], 0, 0); // 1 == player
      $p .= '</div>';
    }
  else  
    {
      $p .= '<div style="display:table-row;text-align:center">';
      $p .= '<div style="text-align:right;vertical-align:top;display:table-cell;">';
      $p .= tv2_time_count ($d_array[$i]);
      $p .= '</div>';  
      $p .= '</div>';

      for ($i = 0; isset ($d_array[$i]); $i++)
        {
          $p .= '<div style="display:table-row;text-align:center;">';
          $p .= tv2_output_html ($d_array[$i], $start, $num ? $num : 0); // 0 == no player
          $p .= '</div>';
        }
    }

  $p .= '</div>'; // display:table
//  $p .= '</center>';

  // show page-wise navigation (bottom)
  if (!$v)
    {
      $s = tv2_page ($start, $num, sizeof ($d_array));
      if ($s)
        $p .= '<br>'.$s;
    }

  return $p;
}




// main ()





$f = get_request_value ('f'); // function
  $c = get_request_value ('c'); // category
  $q = get_request_value ('q'); // search query
  $desc = get_request_value ('desc'); // search in desc?
  if (!($desc))
    $desc = 0;
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





if ($use_gzip == 1)
  {
/*
    if (strpos ($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
      {
        header ('Content-Encoding: x-gzip');
        print ("\x1f\x8b\x08\x00\x00\x00\x00\x00");
      }
    else if (strpos ($HTTP_ACCEPT_ENCODING, 'gzip') !== false)
      {
        header ('Content-Encoding: gzip');
        print ("\x1f\x8b\x08\x00\x00\x00\x00\x00");
      }
    else
*/
$use_gzip = 0;
  }


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









// RSS only
if ($f == 'rss')
  {
    $d_array = tv2_sql ($c, $q, $desc, $f, NULL, $start, $num);
    tv2_rss ($d_array);
    exit;
  }












header ('Content-type: text/html; charset=utf-8');


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

if (islocalhost ())
  {
    // admin stuff
  }

$tv2_captcha = widget_captcha (3);


$head = '<html>'
       .'<head>'
       .'<title>'
       .$tv2_title
       .'</title>'
       .'<link rel="stylesheet" type="text/css" media="screen" href="/tv2.css">'
       .'</head>'
       .$tv2_body_tag
;

$body = tv2_body ();

$end = ''
//      .'<br><br><br>'
//      .widget_relate ($tv2_name, misc_getlink ($tv2_link, array (), true), NULL, 0, WIDGET_RELATE_ALL)
//      .time_ms () - $t_ms
      .'</body>'
      .'</html>';

// the _only_ echo
$p = $head.$body.$end;

if ($use_gzip == 1)
  {
    $size = strlen ($p);
    $p = gzcompress ($p, 9);
    $p = substr ($p, 0, $size);
    print ($p);
  }
else echo $p;

// use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($_SERVER['QUERY_STRING']), serialize ($p))
      or die ("memcache: failed to save data at the server");
  }



}


?>