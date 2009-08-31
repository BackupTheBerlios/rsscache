<?php
header ('Content-type: text/html; charset=utf-8');
//phpinfo();
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('idtech3.php');
require_once ('misc/widget.php');
require_once ('pwnoogle_output.php');
require_once ('pwnoogle_misc.php');

if ($use_gzip)
  ob_start ('ob_gzhandler'); // enable gzip

$t_ms = time_ms ();


function
pwnoogle ()
{
  global $pwnoogle_root,
//         $pwnoogle_lock,
         $pwnoogle_results,
         $pwnoogle_isnew,
         $pwnoogle_name;

  $other = get_request_value ('other'); // show other video flag

  $c = get_request_value ('c'); // category
  $q = get_request_value ('q'); // search query

  $f = get_request_value ('f'); // function

  $v = get_request_value ('v'); // own video

  $start = get_request_value ('start'); // offset
  if (!($start))
    $start = 0;
  $num = get_request_value ('num'); // number of results
  if (!($num))
    $num = $pwnoogle_results;

  $player_name = get_request_value ('player_name'); // index

  // change player_name in cookie
  if (isset ($_GET['player_name']))
    { 
      $player_name = $_GET['player_name'];
      if (strlen (trim ($player_name)) == 0)
        $player_name = get_request_value ('player_name');
    }

/*
  $last_visit = get_request_value ('last_visit');
  $latest_visit = get_request_value ('latest_visit');
  if (!($latest_visit))
    $last_visit = $latest_visit = time ();
  else if ($latest_visit < time () - $pwnoogle_isnew)
    $last_visit = $latest_visit;
*/

  $expire = time() + 3600 * 24 * 180; // 6 months
  $a = array (
           array ('player_name', $player_name),
           array ('last_visit', ''),
           array ('latest_visit', ''),
//           array ('other', $other),
           array ('c', $c),
//           array ('q', $q),
//           array ('f', $f),
//           array ('v', $v),
         );
  $a_size = sizeof ($a);
  for ($i = 0; $i < $a_size; $i++)
    setcookie ($a[$i][0], $a[$i][1], $expire);
//bool setcookie  ( string $name  [, string $value  [, int $expire= 0  [, string $path  [, string $domain  [, bool $secure= false  [, bool $httponly= false  ]]]]]] )

  $config = config_xml ();

/*
  // get progress of conversion
  $progress = NULL;
  $lock = NULL;
  if (file_exists ($pwnoogle_root.'/'.$pwnoogle_lock))
    $lock = fopen ($pwnoogle_root.'/'.$pwnoogle_lock, 'r');
  if ($lock)
    {
      $progress = fgets ($lock);
      fclose ($lock);
      $progress = trim ($progress);
    }
*/

  $p = '<table border="0" cellpadding="0" cellspacing="0"><tr>';

  $p .= '<td valign="top" width="50%"><center>';

//  if (!$v)
//    $p .= widget_carousel ('carousel_xml.php');
//  else
      {
        if ($other == NULL)
          $p .= pwnoogle_button_array ($config, $other, '%s&nbsp;', 0, (int) sizeof ($config->category));
        else
          // print 1st half of icons
          $p .= pwnoogle_button_array ($config, $other, '%s&nbsp;', 0, (int) ceil (sizeof ($config->category) * 0.5) + 2);
      }

  $p .= '</center></td><td valign="top">';

//  $player_name = get_request_value ("player_name");
//  if ($player_name)
//    $p .= '<b><font style="color:#bbb;">Welcome, </font>'.colorize ($player_name, 1).'<font style="color:#bbb;"> to</font></b><br>';

  $p .= '<font style="font-size:16px;font-family:sans;color:#000;"><b>'.$pwnoogle_name.'</b></font><br><br>';

  if (!($player_name))
    $p .= ''
         .'<img src="images/1.png" border="0"> Enter Player Name'
         .'<br>'
         .'<form method="GET" action="'
         .$_SERVER['PHP_SELF']
         .'" style="display:inline;"><input type="text" name="player_name"></form>'
         .'<br>'
         .'<span style="color:#bbb;">Colors: ^0^1^2^3^4^5^6^7, Cookies: On</span>'
         .'<br>';
  else
    {
      $p .= ''
        .'<img src="images/1.png" border="0"> Upload Demo'
        .' <img src="images/2.png" border="0"> Wait'
        .' <img src="images/3.png" border="0"> Watch Demo Online'
        .'<br><nobr>'
        .widget_upload ($pwnoogle_root.'/incoming/', 10000000, NULL, NULL,
                        'Thank you! The demo was uploaded successfully<br>'
                       .'It will be available for download within the next 1-2 minutes<br>'
//                       .'Converting it to a flash movie will take a little longer though'
                       .'Only demos with <2 min duration and selected demos will get converted<br>'
                       .'until we moved to a different server <a href="'
                       .misc_getlink ('', array (), true)
                       .'"><b>Ok</b></a>')
        .'</nobr><br>'
        .'<span style="color:#bbb;">Supported: dm3 ' /* dm_43 dm_45 dm_46 */ .'dm_48 dm_66 dm_67 dm_68 dm_73</span>'
        .'<br>'
;

      // progress
      if ($progress)
        $p .= '<span style="color:#bbb;">Converting: '
             .$progress
             ."</span><br>\n";
    }

  $p .= 'Search Demo/Video:<br><nobr>'
       .'<form method="GET" action="'
       .$_SERVER['PHP_SELF']
       .'" style="display:inline;">'
       .'<select name="c">';

  for ($i = 0; $config->category[$i]; $i++)
    if ($config->category[$i]->select == 1)
      $p .= '<option'
           .' value="'.$config->category[$i]->name.'"'
           .($config->category[$i]->name == $c ? ' selected' : '')
           .' style="background-image:url('
           .'gsdata/logos/'
           .pwnoogle_normalize ($config->category[$i]->name)
           .'_trans16.png'
           .');background-repeat:no-repeat;background-position:bottom left;padding-left:18px;"'
           .'>'

           .$config->category[$i]->title
           .'</option>';

  $p .= '</select>'
      .'<input type="text" name="q"'
      .($q ? ' value="'.$q.'"' : '')
      .'>'
      .'<input type="submit" value="Search">'
      .'<br>'
      .'<span style="color:#bbb;">'
      .'Demos: '
      .get_num_demos (NULL)
      .' Videos: '
      .get_num_videos (NULL)
//      .' Players: 0'
      .' Other Videos:<input type="checkbox" name="other"'
      .($other ? ' checked' : '')
      .' onclick="form.submit();">'
      .'</span>'
      .'</form>&nbsp;</nobr>';

//  $p .= '<br>'.pwnoogle_button_array ($config, $other, '&nbsp;%s', (int) ceil ($i * 0.5), 8);

  $p .= '</td>';


  if ($other != NULL)
    {
      $p .= '<td valign="top" width="50%"><center>';

      // print 2nd half of icons
      $i = sizeof ($config->category);
      $p .= pwnoogle_button_array ($config, $other, '&nbsp;%s', (int) ceil ($i * 0.5) + 2, $i - (int) ceil ($i * 0.5));

      $p .= '</center></td>';
    }

  $p .= '</tr></table>'
;

  // use XML
//  echo widget_index ($pwnoogle_root.'/xml/', 0, '.xml', pwnoogle_index_func);
//exit;

  // use SQL
  if ($v)
    $d_array = pwnoogle_sql ($other, NULL, NULL, $f, $v, 0, 0, 0);
  else
    $d_array = pwnoogle_sql ($other, $c, $q, $f, NULL, $start, $num ? $num : 0);

  if (!($v))
    {
      $p .= '<br>';

      $s = pwnoogle_page ($start, $num, sizeof ($d_array));
      if ($s)
        {
          // left play all button
          $p .= '&nbsp<a href="'
               .misc_getlink ('', array ('v' => NULL, 'other' => $other, 'f' => 'play_all'), true)
               .'" title="Play all videos starting from here (TV)">'
               .'<img src="images/play_all32.png" border="0">'
               .'</a>'
;
          $p .= $s;

        }

          // right play all button
          $p .= '&nbsp<a href="'
               .misc_getlink ('', array ('v' => NULL, 'other' => $other, 'f' => 'play_all'), true)
               .'" title="Play all videos starting from here (TV)">'
               .'<img src="images/play_all32.png" border="0">'
               .'</a>'
;
    }

  $p .= '<br>';

  $p .= '<table border="0" cellpadding="0" cellspacing="0"><tr>';

  if ($v)
    {
      $p .= pwnoogle_output_html ($d_array[0], 1, 0, 0); // 1 == player
    }
  else
    {
      echo $p;
      $p = '';

      for ($i = 0; $d_array[$i]; $i++)
        {
          if ($i > 0)
            echo '</tr><tr>';

          echo pwnoogle_output_html ($d_array[$i], 0, $start, $num ? $num : 0); // 0 == no player
        }
    }

  $p .= '</tr></table>';

  if (!($v))
    {
      $s = pwnoogle_page ($start, $num, sizeof ($d_array));
      if ($s)
        $p .= '<br>'.$s;
    }

  return $p;
}


$f = get_request_value ('f'); // function
if ($f == 'rss')
  {
    $other = get_request_value ('other'); // show other video flag

    $c = get_request_value ('c'); // game filter
    $q = get_request_value ('q'); // search query

    $f = get_request_value ('f'); // function

  $start = get_request_value ('start'); // offset
  if (!($start))
    $start = 0;
  $num = get_request_value ('num'); // number of results
  if (!($num))
    $num = $pwnoogle_results;

    // use SQL
    $d_array = pwnoogle_sql ($other, $c, $q, $f, NULL, $start, $num);
    pwnoogle_output_rss ($d_array);
    exit;
  }


?><html>
<head>
<title><?php echo $pwnoogle_name.' - '.$pwnoogle_title; ?></title>
<!--link href="http://q3eu.com/jack.css" rel="stylesheet" type="text/css"-->
<style type="text/css">

a.thumbnail {
  position: relative;
  z-index: 0;
}

a.thumbnail:hover {
  z-index: 50;
}

a.thumbnail span {
  position: absolute;
  left: -1000px;
  visibility: hidden;
}

a.thumbnail:hover span {
  visibility: visible;
  top: -4px;
  left: 16px;
}

body.watch-lights-off {
  background-color:#323232
}

.watch-lights-off #watch-this-vid-info {
  visibility:hidden
}

</style>
<script type="text/javascript">


function pageScroll ()
{
  window.scrollBy (0,150);
  scrolldelay = setTimeout ('pageScroll()', 100);
}


function toggleLights (lightsOff)
{
  if (lightsOff)
    {
      addClass(document.body, 'watch-lights-off');
    }
  else
    {
      removeClass(document.body, 'watch-lights-off');
    }
}


</script>
</head>
<!-- body onLoad="pageScroll()" -->
<body style="font-family:monospace;<?php

//$v = get_request_value ('v'); // game filter
//echo $v ? 'opacity:0.6;' : '';

?>"><center><?php

echo pwnoogle ();

?><br><br><br></center><?php

echo widget_relate ($pwnoogle_name, misc_getlink ($pwnoogle_link, array (), true), NULL, 0, WIDGET_RELATE_ALL);

//echo time_ms () - $t_ms;

?></body></html><?php

if ($use_gzip)
  ob_end_flush ();

?>