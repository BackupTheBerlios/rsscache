<?php
//phpinfo();
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
//require_once ('misc/widget.php');
require_once ('tv2_output.php');
require_once ('tv2_misc.php');


//$t_ms = time_ms ();


function
tv2 ()
{
  global $tv2_root,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_logo,
         $tv2_search_s;

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

  $user_name = get_request_value ('user_name'); // index

  // change user_name in cookie
  if (isset ($_GET['user_name']))
    { 
      $user_name = $_GET['user_name'];
      if (strlen (trim ($user_name)) == 0)
        $user_name = get_request_value ('user_name');
    }

/*
  $last_visit = get_request_value ('last_visit');
  $latest_visit = get_request_value ('latest_visit');
  if (!($latest_visit))
    $last_visit = $latest_visit = time ();
  else if ($latest_visit < time () - $tv2_isnew)
    $last_visit = $latest_visit;
*/

  // set cookies

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
  $a_size = sizeof ($a);
  for ($i = 0; $i < $a_size; $i++)
    setcookie ($a[$i][0], $a[$i][1], $tv2_cookie_expire);

  $config = config_xml ();

  $p = '';

  // top
  $p .= '<div style="display:table;">';
  $p .= '<div style="display:table-row;">';
  $p .= '<div style="width:50%;vertical-align:top;text-align:left;display:table-cell;">';

/*
  if (!$v)
    {
      $p .= widget_carousel ('carousel_xml.php');
    }
  else
*/
    {
      // left icons
      $s = 0;
      $l = (int) ceil (sizeof ($config->category) * 0.5);
      $p .= tv2_button_array ($config, ' <nobr>%s</nobr>', $s, $l);
    }

  $p .= '</div>'; // display:table-cell

  $p .= '<div style="vertical-align:display:table-cell;">';

  // user name
//  $user_name = get_request_value ("user_name");
//  if ($user_name)
//    $p .= '<b><font style="color:#bbb;">Welcome, </font>'.colorize ($user_name, 1).'<font style="color:#bbb;"> to</font></b><br>';

  // logo
  $p .= '<a href="/" style="text-decoration:none;">';
  $p .= $tv2_logo;
  $p .= '</a>';

  // form
  $p .= '<nobr>';

  $p .= '<form method="GET" action="'
       .$_SERVER['PHP_SELF']
       .'" style="display:inline;" name="tv2_form">';

  // select
  $p .= '<select name="c">';
  for ($i = 0; $config->category[$i]; $i++)
    if ($config->category[$i]->select == 1)
      $p .= '<option'
           .' value="'.$config->category[$i]->name.'"'
           .($config->category[$i]->name == $c ? ' selected' : '')
           .' style="background-image:url('
           .'gsdata/logos/'
           .tv2_normalize ($config->category[$i]->name)
           .'_trans16.png'
           .');background-repeat:no-repeat;background-position:bottom left;padding-left:18px;"'
           .'>'
           .$config->category[$i]->title
           .'</option>';
  $p .= '</select>';

  // input search query
  $p .= '<input type="text" name="q"'
       .($q ? ' value="'.$q.'"' : '')
       .'>'
       .'<input type="submit" value="'.$tv2_search_s.'">';

  // focus
  $p .= '<script type="text/javascript">'."\n"
       .'document.tv2_form.q.focus ();'."\n"
       .'</script>';

  $p .= '</form>';

  $p .= '</nobr>';

  // videos total
  $p .= '<span style="color:#bbb;">'
       .' Videos: '
       .tv2_get_num_videos (NULL)
       .', tv2 engine version 0.1pre'
       .'</span>';

  $p .= '<br>';

  // center icons
  $p .= '<span style="text-align:center;vertical-align:bottom;">';
  $s = (int) ceil (sizeof ($config->category) * 0.5);
  $l = 8;
  $p .= tv2_button_array ($config, ' <nobr>%s</nobr>', $s, $l);
  $p .= '</span>';

  $p .= '</div>'; // display:table-cell

  $p .= '<div style="width:50%;vertical-align:top;text-align:right;display:table-cell;">';

  // right icons
  $s = (int) ceil (sizeof ($config->category) * 0.5) + 8;
  $l = sizeof ($config->category) - $s;
  $p .= tv2_button_array ($config, '<nobr>%s</nobr> ', $s, $l);

  $p .= '</div>'; // display:table-cell
  $p .= '</div>'; // display:table-row
  $p .= '</div>'; // display:table

  // use SQL
  if ($v)
    $d_array = tv2_sql (NULL, NULL, $f, $v, 0, 0, 0);
  else
    $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num ? $num : 0);

  // show page-wise navigation (top)
  if (!$v)
    {
      $p .= '<br>';
      $p .= '<br>';

      $s = tv2_page ($start, $num, sizeof ($d_array));

      if ($s)
        {
          // left play all button
          $p .= '&nbsp<a href="'
               .misc_getlink ('', array ('v' => NULL, 'f' => 'play_all'), true)
               .'" title="Play all videos starting from here (TV)">'
               .'<img src="images/play_all32.png" border="0">'
               .'</a>'
;
          $p .= $s;

        }

      // right play all button
      $p .= '&nbsp<a href="'
           .misc_getlink ('', array ('v' => NULL, 'f' => 'play_all'), true)
           .'" title="Play all videos starting from here (TV)">'
           .'<img src="images/play_all32.png" border="0">'
           .'</a>'
;
    }

  $p .= '<br>';
  $p .= '<br>';

  $p .= '<center>';
  $p .= '<div style="display:table;text-align:center;">';
  $p .= '<div style="display:table-row;">';

  if ($v)
    {
      $p .= tv2_output_html ($d_array[0], 1, 0, 0); // 1 == player
    }
  else
    {
      for ($i = 0; $d_array[$i]; $i++)
        {
          if ($i > 0)
            {
              $p .= '</div>';
              $p .= '<div style="display:table-row;text-align:center;">';
            }

          $p .= tv2_output_html ($d_array[$i], 0, $start, $num ? $num : 0); // 0 == no player
        }
    }

  $p .= '</div>'; // display:table-row
  $p .= '</div>'; // display:table
  $p .= '</center>';

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


$f = get_request_value ('f'); // function
if ($f == 'rss')
  {
    $c = get_request_value ('c'); // game filter
    $q = get_request_value ('q'); // search query

    $f = get_request_value ('f'); // function

    $start = get_request_value ('start'); // offset
    if (!($start))
      $start = 0;
    $num = get_request_value ('num'); // number of results
    if (!($num))
      $num = $tv2_results;

    // use SQL
    $d_array = tv2_sql ($c, $q, $f, NULL, $start, $num);
    tv2_output_rss ($d_array);
    exit;
  }


header ('Content-type: text/html; charset=utf-8');


$head = '<html>'
       .'<head>'
       .'<title>'
       .$tv2_title
       .'</title>'
/*
       .'<script type="text/javascript">'."\n"
       ."\n"
       .'function toggleLights (lightsOff)'."\n"
       .'{'."\n"
       .'  if (lightsOff)'."\n"
       .'    {'."\n"
       .'      addClass(document.body, \'watch-lights-off\');'."\n"
       .'    }'."\n"
       .'  else'."\n"
       .'    {'."\n"
       .'      removeClass(document.body, \'watch-lights-off\');'."\n"
       .'    }'."\n"
       .'}'."\n"
       ."\n"
       .'</script>'
*/
       .'</head>'
       .$tv2_body_tag
;

$body = tv2 ();

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

?>