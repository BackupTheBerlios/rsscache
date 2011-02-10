<?php

function
gamescast_get_countdown ($desc)
{
// make sure that &tz=0
// http://www.gamescast.tv/rss/rss-events.php?game=ql&tz=0

//Begins: 12/05 7:00pm,
//Ends: 12/05 9:00pm,
//Show Type: Podcast,
//Game Featured: StarCraft 2

  $p = '';
  // enable links in desc
  $s = $desc;
  // DEBUG
//echo '<pre><tt>';
//print_r ($desc);
  $p = substr ($s, strpos ($s, 'Begins: ') + 8);
  $p = substr ($p, 0, strpos ($p, ','));
  $start = $p.' -0000';
  $p = substr ($s, strpos ($s, 'Ends: ') + 6);
  $p = substr ($p, 0, strpos ($p, ','));
  $end = $p.' -0000';
//echo $start;
//echo $end;
  $p = "%m/%d %l:%M%p %z";
//echo $p;
  $t = array ();
//  if (function_exists ('date_parse_from_format'))
//    $func = 'date_parse_from_format';
//  else
    $func = 'strptime';

  $tz = date_default_timezone_get ();
  date_default_timezone_set ('UTC');
  $t[0] = $func (trim ($start), $p);
  $start = mktime ($t[0]['tm_hour'],
                   $t[0]['tm_min'],
                   $t[0]['tm_sec'],
                   $t[0]['tm_mon'] + 1,
                   $t[0]['tm_mday']);
  $t[1] = $func (trim ($end), $p);
  $end = mktime ($t[1]['tm_hour'],
                 $t[1]['tm_min'],
                 $t[1]['tm_sec'],
                 $t[1]['tm_mon'] + 1,
                 $t[1]['tm_mday']);
  date_default_timezone_set ($tz);

  return array ($start, $end);
}

?>