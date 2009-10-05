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
tv2_search_form ()
{
  global $tv2_root,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_logo,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire;
  $tv2_version_s = '0.1pre';

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

  $config = config_xml ();

  $p = '';

  // user name
  $p .= '<div style="display:inline"><form method="GET" action="'
       .$_SERVER['PHP_SELF']
       .'" name="tv2_form">';
//  $user_name = get_request_value ("user_name");
//  if ($user_name)
//    $p .= '<b><font style="color:#bbb;">Welcome, </font>'.colorize ($user_name, 1).'<font style="color:#bbb;"> to</font></b><br>';

  // logo
  $p .= '<a href="/" style="text-decoration:none;">';
  $p .= $tv2_logo;
  $p .= '</a><br>';


  // select
  $p .= tv2_select ($c);

  // input search query
  $p .= '<input type="text" name="q"'
       .($q ? ' value="'.$q.'"' : '')
       .'>'
       .'<input type="submit" value="'.$tv2_search_s.'">';

  // focus (javascript)
//  $p .= '<script type="text/javascript">'."\n"
//       .'document.tv2_form.q.focus ();'."\n"
//       .'</script>';

  // search in desc
  $p .= '<input type="hidden" name="desc" value="1">';

  // videos total
  $p .= '<div style="color:#bbb;'
//       .'text-align:left;'
       .'">'
       .tv2_get_num_videos (NULL)
       .' '.$tv2_videos_s;

  // days total
  $p .= ', '
       .tv2_get_num_days (NULL)
       .' days';

  // engine version
  $p .= ', tv2 engine '.$tv2_version_s
       .'</div>';
  $p .= '</form>';
  $p .= '</div>';

  return $p;
}




function
tv2 ()
{
  global $tv2_root,
         $tv2_results,
         $tv2_isnew,
         $tv2_body_tag,
         $tv2_logo,
         $tv2_search_s,
         $tv2_videos_s,
         $tv2_cookie_expire;
  $tv2_version_s = '0.1pre';

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
  for ($i = 0; isset ($a[$i]); $i++)
    setcookie ($a[$i][0], $a[$i][1], $tv2_cookie_expire);

  $config = config_xml ();

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
      $l = (int) ceil (sizeof ($config->category) * 0.5);
      $p .= tv2_button_array ($config, '%s ', $s, $l);
  // more icons
  $s = (int) ceil (sizeof ($config->category) * 0.5);
  $l = sizeof ($config->category) - $s;
  $p .= tv2_button_array ($config, '%s ', $s, $l);
    }




  // formular
  $p .= tv2_search_form ();

  // use SQL
  if ($v)
    $d_array = tv2_sql (NULL, NULL, $desc, $f, $v, 0, 0, 0);
  else
    $d_array = tv2_sql ($c, $q, $desc, $f, NULL, $start, $num ? $num : 0);

  // show page-wise navigation (top)
  if (!$v)
    {
      $p .= '<br>';
      $p .= '<br>';

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

  $p .= '<br>';
  $p .= '<br>';

//  $p .= '<center>';
  $p .= '<div style="display:table;text-align:center;">';
  $p .= '<div style="display:table-row;">';

  if ($v)
    {
      $p .= tv2_output_html ($d_array[0], 1, 0, 0); // 1 == player
    }
  else
    {
      for ($i = 0; isset ($d_array[$i]); $i++)
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
    $desc = get_request_value ('desc'); // search in desc?
    if (!($desc))
      $desc = 0;

    $f = get_request_value ('f'); // function

    $start = get_request_value ('start'); // offset
    if (!($start))
      $start = 0;
    $num = get_request_value ('num'); // number of results
    if (!($num))
      $num = $tv2_results;

    // use SQL
    $d_array = tv2_sql ($c, $q, $desc, $f, NULL, $start, $num);
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
       .'<style type="text/css">'."\n"
/*
       ."\n"
       .'a.thumbnail {'."\n"
       .'  position: relative;'."\n"
       .'  z-index: 0;'."\n"
       .'}'."\n"
       ."\n"
       .'a.thumbnail:hover {'."\n"
       .'  z-index: 50;'."\n"
       .'}'."\n"
       ."\n"
       .'a.thumbnail span {'."\n"
       .'  position: absolute;'."\n"
       .'  left: -1000px;'."\n"
       .'  visibility: hidden;'."\n"
       .'}'."\n"
       ."\n"
       .'a.thumbnail:hover span {'."\n"
       .'  visibility: visible;'."\n"
       .'  top: -4px;'."\n"
        .'  left: 16px;'."\n"
       .'}'."\n"
       ."\n"
*/
.'

a
{
  color:#00f;
  text-decoration:none;
}


a:hover
{
  color:#f40;
//  background-color:#fec;
//  color:WindowText;
//  background-color:Window;
  text-decoration:none;
}'

/*
a span
{
  display:none;
}


a:hover span
{
  font:12px sans-serif,arial;
  position:absolute;
  top:30px;
  left:10px;
  padding:2px;
  display:block;
//  white-space:nowrap;
  color:InfoText;
  background-color:InfoBackground;
  border:1px solid #000;
  text-align:left;
  text-decoration:none;
}

.widget_a_img
{
  position: relative;
  z-index:0;
  text-decoration:none;
}


.widget_a_img:hover
{
  z-index:1;
}


.widget_a_img span
{
  display:none;
}


.widget_a_img:hover span
{
  font:12px sans-serif,arial;
  position:absolute;
  top:30px;
  left:10px;
  padding:2px;
  display:block;
//  white-space:nowrap;
  color:InfoText;
  background-color:InfoBackground;
  border:1px solid #000;
  text-align:left;
  text-decoration:none;
}
*/
       .'</style>'

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



}


?>