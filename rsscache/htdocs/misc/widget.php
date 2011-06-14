<?php
/*
widget.php - new HTML widgets

Copyright (c) 2006 - 2008 NoisyB


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
if (!defined ('MISC_WIDGET_PHP'))
{
define ('MISC_WIDGET_PHP', 1);  
//phpinfo ();
//error_reporting(E_ALL | E_STRICT);
include ('misc.php');
if (file_exists ('embed.php'))
  include ('embed.php');

/*
CSS: canvas image
  <img style="width:20%;clip:rect(10px 150px 150px 70px);position:absolute;" src="widget_relate_refrigator.png">
*/


//$widget_output = 0;
$widget_step_count = 0;


function
widget_gecko_install ($target = NULL)
{
  $p = '';

//  if (!stristr ($_SERVER['HTTP_USER_AGENT'], 'gecko'))
  if (stristr ($_SERVER['HTTP_USER_AGENT'], 'msie'))
    {
      $p .= '<a href="http://www.mozilla.com/firefox/" border="0"><img src="images/widget/firefox.jpg"></a><br><br>';
      if ($target)
        $p .= '<a href="'.$target.'">I have to continue using MSIE</a>';
    }
//  else if ($target)
//    header ('Location: '.$target);
  return $p;
}


function
widget_filename_escape ($s)
{
  $f = array ("/\.[^\.]+$/", "/[^\d\w\s-]/", "/\s\s+/", "/[-]+/", "/[_]+/");
  $r = array ('', '', ' ', '-', '_');
  $s = basename ($s);
  $s = trim (preg_replace ($f, $r, $s));
//  $s = str_replace (' ', '_', $s);
  return $s;
}


function
widget_count_steps ()
{
  global $widget_step_count;

  $p = '';
  $p .= '<img src="images/'.($widget_step_count + 1).'.png" border="0">';
  $widget_step_count++; 

  return $p;
}


define ('WIDGET_BUTTON_SMALL', 1);
define ('WIDGET_BUTTON_ONLY', 2);
//define ('WIDGET_BUTTON_STATIC', 4);
function
widget_button ($icon, $query, $label, $tooltip, $link_suffix = NULL, $flags = 0)
{
  $p = '';

  // detect if button is active/selected
  $t = array (array (), array ());
  $a = parse_url ($query);
  if (isset ($a['query']))
    parse_str ($a['query'], $t[0]);
  else if (isset ($a['path']))
    parse_str ($a['path'], $t[0]);
  parse_str ($_SERVER['QUERY_STRING'], $t[1]);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($t);
//  $a = array_keys ($t[0]);

  $selected = 1; // black-out link

  // check query
//  if (isset ($t[0][0]))
  if (count (array_diff ($t[0], $t[1])) > 0)
    $selected = 0;

  // check domain
  if ($selected == 1)
    if (!isset ($t[0][0]))
    {
      $t = parse_url ($query);
//      echo $t['host'].', '.$_SERVER['HTTP_HOST'].'<br>';  
      if (isset ($t['host']))
        if ($t['host'] != '')
          if (strcasecmp ($t['host'], $_SERVER['HTTP_HOST']))
            $selected = 0;
    }

  if ($link_suffix)
    {
       $t = array ();
       parse_str ($query, $t[0]);
       parse_str ($link_suffix, $t[1]);
       $a = array_merge ($t[1], $t[0]);
       $query = http_build_query2 ($a, false);
    }

//  if ($selected)
//    $p .= '<span class="tooltip"';
//  else
    {
      $p .= '<a';
      if ($selected)
        $p .= ' class="tv2_selected" style="color:#000;"';
      else
        $p .= ' class="tooltip"';
//      if (!strncasecmp ($query, 'http://', 7))
      if (strstr ($query, '://') ||
          !strncasecmp ($query, 'mailto:', 7))
        $p .= ' href="'.$query.'"';
      else
        $p .= ' href="?'.$query.'"';

      $p .= ''
//           .' title="'.$tooltip.'"'
           .' alt="'.$label.'"';
    }

  if ($tooltip)
    if (trim ($tooltip) != '')
      $p .= ''
//      .' onmouseover="tv2_tt_show(\''.htmlentities ($tooltip, ENT_QUOTES).'\');"'
      .' onmouseover="tv2_tt_show(\''.str_replace ('\'', '\\\'', $tooltip).'\');"'
      .' onmouseout="tv2_tt_hide();"'
;

//  if (!$icon)
//      $p .= '';

  $p .= '>';

  $s = '';
  if ($icon)
    {
      $s .= '<img src="'.$icon.'" border="0" alt=""';
//      if (!($selected))
//        $s .= ' style="opacity:0.5;"';

      if ($flags & WIDGET_BUTTON_SMALL)
        $s .= ' height="16"';
      $s .= ''
           .' onerror="this.parentNode.removeChild(this);"'
           .'>';
    }

//  if ($icon && $label)
//    $p .= '&nbsp;';

//  if ($flags & WIDGET_BUTTON_STATIC)
//    return ($icon ? $s : '');

  $p .= $s;

  if (!($flags & WIDGET_BUTTON_ONLY))
    {
//      if ($selected)
//        $p .= '<span class="tv2_selected">';
      $p .= ''
           .$label
;
//      if ($selected)
//        $p .= '</span>';
    }

//  if ($tooltip)
//    if (trim ($tooltip) != '')
//      $p .= '<span>'.$tooltip.'</span>';

//  if ($selected)
//    $p .= '</span>';
//  else
    $p .= '</a>';

  return $p;
}


/*
<?xml version="1.0" encoding="UTF-8"?>
<!-- config for aa2map_php -->
<categories>
  <category>
    <id>qscore</id>
    <title>qscore suite</title>
    <tooltip>generate high scores and statistics from game server logs and some other useful game server admin tools</tooltip>
    <query>embed=aa2map_ascii.php</query>
    <new>0</new>
    <separate>1</separate>
  </category>
*/
define ('WIDGET_CMS_ROW', 4); // row with buttons (default)
define ('WIDGET_CMS_RC', 8);  // row with columns of buttons
define ('WIDGET_CMS_COL', 16);  // column with buttons
define ('WIDGET_CMS_BUTTON_ONLY', 32); // show button only; not text
define ('WIDGET_CMS_BUTTON16', 64);
define ('WIDGET_CMS_BUTTON32', 128);
function
widget_cms_row_func ($s, $i, $logo, $config_xml)
{
  $category = $config_xml->category[$i];
  $p = '';
      // <separate>   
      if ($category->separate == 1)
        $p .= '<br>';
      else if ($category->separate == 2)
        $p .= '<hr>';
  $p .= $s;
  return $p; 
}


function
widget_cms_col_func ($s, $i, $logo, $config_xml)
{
      $category = $config_xml->category[$i];
  $p = '';
      // <separate>
      if ($category->separate == 1)
        $p .= '<br>';
      else if ($category->separate == 2)
        $p .= '<hr>';
  $p .= $s;
    $p .= '<br>'
//         .($logo ? '<img src="'.$logo.'" border="0">' : '')
//         .'<br>'  
//         .'<br>'
;
  return $p; 
}


function
widget_cms_rc_func ($s, $i, $logo, $config_xml)
{
  $category = $config_xml->category[$i];

  $p = '';

  if ($i == 0)
    $p .= '<div style="float:left;'
          // DEBUG
//         .'border:1px solid #000;'
         .'">';

      // <separate>
      if ($category->separate > 1)
        $p .= '</div><div style="float:left;'
              // DEBUG
//             .'border:1px solid #000;'
             .'">';
      else if ($category->separate > 0)
        $p .= '<br>';
  $p .= $s;
  $p .= '<br>';

  $next = '';
  if (isset ($config_xml->category[$i + 1]))
    if ($config_xml->category[$i + 1]->title)
      $next = $config_xml->category[$i + 1]->title;

  if ($next != '')
    {
//      // <separate>
//      if ($category->separate > 1)
//        $p .= '</div><div style="float:left;'
//              // DEBUG
////             .'border:1px solid #000;'
//             .'">';
//      else if ($category->separate > 0)
//        $p .= '<br>';
    }
  else
    {
      $p .= '</div>';
      $p .= '<div class="clear"></div>';
    }

  return $p;
}


function
widget_cms ($logo, $config_xml, $link_suffix = NULL, $flags = 4)
{
  // DEBUG
//  echo '<pre><tt>';
//  echo $flags;
//  print_r ($config_xml);

  $p = '';

  // categories  
  for ($i = 0; isset ($config_xml->category[$i]); $i++)
    if ($config_xml->category[$i]->select < 2)
    {
//  echo '<pre><tt>';
//print_r ($config_xml->category[$i]).'<br>';
      $category = $config_xml->category[$i];
//  echo '<pre><tt>';
//print_r ($category).'<br>';
//echo ((string) $category->tooltip).'<br>';

      $s = '';
      if ($category->query)
        {   
          if ($category->buttononly == 1 || $flags & WIDGET_CMS_BUTTON_ONLY)
            $f = WIDGET_BUTTON_ONLY;
          else
            $f = WIDGET_BUTTON_SMALL;

          $s .= widget_button (($category->logo ? $category->logo : NULL),
                               $category->query,
                               $category->title,
                               $category->tooltip, // ? $category->tooltip : $category->title),
                               $link_suffix,
                               $f);
        }
      else // <title> (no link)
        {
          if ($category->logo)
            {
              $s .= '<img src="'.$category->logo.'" border="0" alt=""';
//              if ($flags & WIDGET_BUTTON_SMALL)
                $s .= ' height="16"';
              $s .= ''
                   .' onerror="this.parentNode.removeChild(this);"'
                   .'>';
            }
          $s .= $category->title;
        }

      // <status>
      if ($category->status == 1)
        $s .= '&nbsp;<img src="images/new.png">';
      else if ($category->status == 2)
        $s .= '&nbsp;<img src="images/soon.png">';
      else if ($category->status == 3)
        $s .= '&nbsp;<img src="images/preview.png">';
      else if ($category->status == 4)
        $s .= '&nbsp;<img src="images/update.png">';

      $s .= '&nbsp;&nbsp;';

      if ($flags & WIDGET_CMS_COL)
        $p .= widget_cms_col_func ($s, $i, $logo, $config_xml);
      else if ($flags & WIDGET_CMS_RC)
        $p .= widget_cms_rc_func ($s, $i, $logo, $config_xml);
      else // if ($flags & WIDGET_CMS_ROW)
        $p .= widget_cms_row_func ($s, $i, $logo, $config_xml);
    }

  return $p;
}  


function
widget_select_option ($icon, $value, $label, $tooltip, $selected = 0)
{
  $p = '';

      // DEBUG
//      echo $selected.' '.$label.'<br>';

  $p .= '<option'
       .($selected == 1 ? ' selected="selected"' : '')
       .($tooltip ? ' title="'.$tooltip.'"' : '')
       .' value="'.$value.'"';

  if ($icon != '')
    $p .= ' style="background-image:url('.$icon.');'
         .'background-repeat:no-repeat;background-position:left;'
         .'padding-left:18px;'
//         .'width:16px;'
         .'height:16px;'
         .'"';

//  $p .= ' onchange="javascript:this.form.submit();"';

  $p .= '>';

  $p .= $label
       .'</option>';

  return $p;
}


/*
  $a = array (array ('value', 'label', 'logo.png'))
*/
function
widget_select ($a, $name = 'wselect', $selected = NULL, $active = 1, $do_submit = 0)
{
  $p = '';
  $p .= '<select name="'.$name.'"'.($active == 1 ? '' : ' disabled="disabled"');

  if ($do_submit == 1)
    $p .= ' onchange="javascript:this.form.submit();"';

  $p .= '>';

  $sel = 0;
  for ($i = 0; isset ($a[$i]); $i++)
    {
      if ($selected)
        if (!strcasecmp ($a[$i][0], $selected) && $sel == 0)
          $sel = 1;

      $p .= widget_select_option ($a[$i][2], $a[$i][0], $a[$i][1], '', $sel == 1 ? 1 : 0);

      if ($sel == 1)
        $sel = 2;
      // DEBUG
//      echo $sel.' '.$selected.' '.$a[$i][0].'<br>';
    }
  $p .= '</select>';

  return $p;
}


/*
  takes image with e.g. 16x16 font tiles and maps them to ascii codes
  generates CSS code to show text with it by using clip()ing
  put CSS code into a span or div

  tile_rows, tile_cols
  16x16 are 256 characters/tiles

  enclose this in a div tag to place it on the page
    e.g. <div style="position:absolute;top:100px;left:200px;">
*/
function
widget_fontiles ($image_url, $image_width, $image_height, $text, $file_cols = 16, $file_rows = 16)
{
  $char_w = $image_width / $file_cols; 
  $char_h = $image_height / $file_rows;

  $p = '';
  for ($i = 0; $i < strlen ($text); $i++)
    {
      $c = ord ($text[$i]);

      $left = $c % $file_cols;
      $left *= $char_w;

      $top = (int) ($c / $file_rows);
      $top *= $char_h;

      $right = $left + $char_w;
      $bottom = $top + $char_h;

      $pos_left = $i * $char_w - $left;
      $pos_top = 0 - $top; 

      $p .= '<img src="'.$image_url.'" style="'
           .'position:absolute;'
           .'clip:rect('.$top.'px,'.$right.'px,'.$bottom.'px,'.$left.'px);'
           .'top:'.$pos_top.'px;left:'.$pos_left.'px;'
           .'width:'.$image_width.';height:'.$image_height.';'
           .'">'."\n";
    }

  return $p;
}


/*
function
widget_table ($title_array, $content_array)
{
  // $cols == number of titles in $title_array 
  $cols = sizeof ($title_array);
  $rows = $cols * 0.5;

  $p = '';

  $p .= '<table class="widget_table" border="0" cellpadding="1" cellspacing="0">';

  // titles
  $p .= '<tr class="widget_table_title">';
  for ($i = 0; $title_array[$i]; $i++)
    $p .= '<td class="widget_table_td">'.$title_array[$i].'</td>';
  $p .= '</tr>';

  // content
  for ($i = 0; $i < $rows; $i++)
    {
      $p .= '<tr class="widget_table_tr'.(($i & 1) + 1).'">';
      for ($j = $rows * $cols; $j < $cols; $j++)
        $p .= '<td class="widget_table_td">'.$content_array[$j].'</td>';
      $p .= '</tr>';
    }

  $p .= '</table>';

  return $p;
}
*/


function
widget_collapse ($label, $s, $collapsed = 0)
{
  $r = rand ();

  $j = 'javascript:'
       .'e=document.getElementById(\''.$r.'\');'
       .'f=document.getElementById(\'_'.$r.'\');'
       .'if(e.style.display==\'none\'){'
       .'e.style.display=\'block\';'
       .'f.innerHTML=\'-\';'
       .'}else{'
       .'e.style.display=\'none\';'
       .'f.innerHTML=\'+\';'
       .'}';

  $p = '';

  $p .= ''
//       .'<span style="font-family:monospace;">'
       .'[<a id="_'.$r.'" href="javascript:void(0);" onclick="'.$j.'">'.($collapsed ? '+' : '-').'</a>]'
//       .'</span>'
;
  if ($label != '')
    $p .= ' <a href="javascript:void(0);" onclick="'.$j.'">'.$label.'</a>:';

  $p .= '<br>';

  $p .= ''
       .'<div id="'.$r.'"'.($collapsed ? ' style="display:none;"' : '').'>'
       .$s
       .'</div>';
  
  return $p;
}


function
widget_onhover_link ($url, $image1, $image2)
{
//  $name = rand (0, 99999999).crc32 ($url.$image1.$image2);

  $p = '';
//  $p .= '<a href="'.$url.'"'
//       .' onmouseover="document.'.$name.'.src='.$image1.'"'
//       .' onmouseout="document.'.$name.'.src='.$image2.'"'
//       .'><img src="'.$image1.'" border="0" name="'.$name.'"></a>';
  $p .= '<a href="'.$url.'"'
       .' onmouseover="this.src='.$image1.'"'
       .' onmouseout="this.src='.$image2.'"'
       .'><img src="'.$image1.'" border="0"></a>';

  return $p;
}


function
get_country ($latitude, $longitude)
{
  $i = 0;
// iso 3166 country, latitude, longitude
  $iso_3166 = array (
    array ('AD', 42.5000, 1.5000),
    array ('AE', 24.0000, 54.0000),
    array ('AF', 33.0000, 65.0000),
    array ('AG', 17.0500, -61.8000),
    array ('AI', 18.2500, -63.1667),
    array ('AL', 41.0000, 20.0000),
    array ('AM', 40.0000, 45.0000),
    array ('AN', 12.2500, -68.7500),
    array ('AO', -12.5000, 18.5000),
    array ('AP', 35.0000, 105.0000),
    array ('AQ', -90.0000, 0.0000),
    array ('AR', -34.0000, -64.0000),
    array ('AS', -14.3333, -170.0000),
    array ('AT', 47.3333, 13.3333),
    array ('AU', -27.0000, 133.0000),
    array ('AW', 12.5000, -69.9667),
    array ('AZ', 40.5000, 47.5000),
    array ('BA', 44.0000, 18.0000),
    array ('BB', 13.1667, -59.5333),
    array ('BD', 24.0000, 90.0000),
    array ('BE', 50.8333, 4.0000),
    array ('BF', 13.0000, -2.0000),
    array ('BG', 43.0000, 25.0000),
    array ('BH', 26.0000, 50.5500),
    array ('BI', -3.5000, 30.0000),
    array ('BJ', 9.5000, 2.2500),
    array ('BM', 32.3333, -64.7500),
    array ('BN', 4.5000, 114.6667),
    array ('BO', -17.0000, -65.0000),
    array ('BR', -10.0000, -55.0000),
    array ('BS', 24.2500, -76.0000),
    array ('BT', 27.5000, 90.5000),
    array ('BV', -54.4333, 3.4000),
    array ('BW', -22.0000, 24.0000),
    array ('BY', 53.0000, 28.0000),
    array ('BZ', 17.2500, -88.7500),
    array ('CA', 60.0000, -95.0000),
    array ('CC', -12.5000, 96.8333),
    array ('CD', 0.0000, 25.0000),
    array ('CF', 7.0000, 21.0000),
    array ('CG', -1.0000, 15.0000),
    array ('CH', 47.0000, 8.0000),
    array ('CI', 8.0000, -5.0000),
    array ('CK', -21.2333, -159.7667),
    array ('CL', -30.0000, -71.0000),
    array ('CM', 6.0000, 12.0000),
    array ('CN', 35.0000, 105.0000),
    array ('CO', 4.0000, -72.0000),
    array ('CR', 10.0000, -84.0000),
    array ('CU', 21.5000, -80.0000),
    array ('CV', 16.0000, -24.0000),
    array ('CX', -10.5000, 105.6667),
    array ('CY', 35.0000, 33.0000),
    array ('CZ', 49.7500, 15.5000),
    array ('DE', 51.0000, 9.0000),
    array ('DJ', 11.5000, 43.0000),
    array ('DK', 56.0000, 10.0000),
    array ('DM', 15.4167, -61.3333),
    array ('DO', 19.0000, -70.6667),
    array ('DZ', 28.0000, 3.0000),
    array ('EC', -2.0000, -77.5000),
    array ('EE', 59.0000, 26.0000),
    array ('EG', 27.0000, 30.0000),
    array ('EH', 24.5000, -13.0000),
    array ('ER', 15.0000, 39.0000),
    array ('ES', 40.0000, -4.0000),
    array ('ET', 8.0000, 38.0000),
    array ('EU', 47.0000, 8.0000),
    array ('FI', 64.0000, 26.0000),
    array ('FJ', -18.0000, 175.0000),
    array ('FK', -51.7500, -59.0000),
    array ('FM', 6.9167, 158.2500),
    array ('FO', 62.0000, -7.0000),
    array ('FR', 46.0000, 2.0000),
    array ('GA', -1.0000, 11.7500),
    array ('GB', 54.0000, -2.0000),
    array ('GD', 12.1167, -61.6667),
    array ('GE', 42.0000, 43.5000),
    array ('GF', 4.0000, -53.0000),
    array ('GH', 8.0000, -2.0000),
    array ('GI', 36.1833, -5.3667),
    array ('GL', 72.0000, -40.0000),
    array ('GM', 13.4667, -16.5667),
    array ('GN', 11.0000, -10.0000),
    array ('GP', 16.2500, -61.5833),
    array ('GQ', 2.0000, 10.0000),
    array ('GR', 39.0000, 22.0000),
    array ('GS', -54.5000, -37.0000),
    array ('GT', 15.5000, -90.2500),
    array ('GU', 13.4667, 144.7833),
    array ('GW', 12.0000, -15.0000),
    array ('GY', 5.0000, -59.0000),
    array ('HK', 22.2500, 114.1667),
    array ('HM', -53.1000, 72.5167),
    array ('HN', 15.0000, -86.5000),
    array ('HR', 45.1667, 15.5000),
    array ('HT', 19.0000, -72.4167),
    array ('HU', 47.0000, 20.0000),
    array ('ID', -5.0000, 120.0000),
    array ('IE', 53.0000, -8.0000),
    array ('IL', 31.5000, 34.7500),
    array ('IN', 20.0000, 77.0000),
    array ('IO', -6.0000, 71.5000),
    array ('IQ', 33.0000, 44.0000),
    array ('IR', 32.0000, 53.0000),
    array ('IS', 65.0000, -18.0000),
    array ('IT', 42.8333, 12.8333),
    array ('JM', 18.2500, -77.5000),
    array ('JO', 31.0000, 36.0000),
    array ('JP', 36.0000, 138.0000),
    array ('KE', 1.0000, 38.0000),
    array ('KG', 41.0000, 75.0000),
    array ('KH', 13.0000, 105.0000),
    array ('KI', 1.4167, 173.0000),
    array ('KM', -12.1667, 44.2500),
    array ('KN', 17.3333, -62.7500),
    array ('KP', 40.0000, 127.0000),
    array ('KR', 37.0000, 127.5000),
    array ('KW', 29.3375, 47.6581),
    array ('KY', 19.5000, -80.5000),
    array ('KZ', 48.0000, 68.0000),
    array ('LA', 18.0000, 105.0000),
    array ('LB', 33.8333, 35.8333),
    array ('LC', 13.8833, -61.1333),
    array ('LI', 47.1667, 9.5333),
    array ('LK', 7.0000, 81.0000),
    array ('LR', 6.5000, -9.5000),
    array ('LS', -29.5000, 28.5000),
    array ('LT', 56.0000, 24.0000),
    array ('LU', 49.7500, 6.1667),
    array ('LV', 57.0000, 25.0000),
    array ('LY', 25.0000, 17.0000),
    array ('MA', 32.0000, -5.0000),
    array ('MC', 43.7333, 7.4000),
    array ('MD', 47.0000, 29.0000),
    array ('ME', 42.0000, 19.0000),
    array ('MG', -20.0000, 47.0000),
    array ('MH', 9.0000, 168.0000),
    array ('MK', 41.8333, 22.0000),
    array ('ML', 17.0000, -4.0000),
    array ('MM', 22.0000, 98.0000),
    array ('MN', 46.0000, 105.0000),
    array ('MO', 22.1667, 113.5500),
    array ('MP', 15.2000, 145.7500),
    array ('MQ', 14.6667, -61.0000),
    array ('MR', 20.0000, -12.0000),
    array ('MS', 16.7500, -62.2000),
    array ('MT', 35.8333, 14.5833),
    array ('MU', -20.2833, 57.5500),
    array ('MV', 3.2500, 73.0000),
    array ('MW', -13.5000, 34.0000),
    array ('MX', 23.0000, -102.0000),
    array ('MY', 2.5000, 112.5000),
    array ('MZ', -18.2500, 35.0000),
    array ('NA', -22.0000, 17.0000),
    array ('NC', -21.5000, 165.5000),
    array ('NE', 16.0000, 8.0000),
    array ('NF', -29.0333, 167.9500),
    array ('NG', 10.0000, 8.0000),
    array ('NI', 13.0000, -85.0000),
    array ('NL', 52.5000, 5.7500),
    array ('NO', 62.0000, 10.0000),
    array ('NP', 28.0000, 84.0000),
    array ('NR', -0.5333, 166.9167),
    array ('NU', -19.0333, -169.8667),
    array ('NZ', -41.0000, 174.0000),
    array ('OM', 21.0000, 57.0000),
    array ('PA', 9.0000, -80.0000),
    array ('PE', -10.0000, -76.0000),
    array ('PF', -15.0000, -140.0000),
    array ('PG', -6.0000, 147.0000),
    array ('PH', 13.0000, 122.0000),
    array ('PK', 30.0000, 70.0000),
    array ('PL', 52.0000, 20.0000),
    array ('PM', 46.8333, -56.3333),
    array ('PR', 18.2500, -66.5000),
    array ('PS', 32.0000, 35.2500),
    array ('PT', 39.5000, -8.0000),
    array ('PW', 7.5000, 134.5000),
    array ('PY', -23.0000, -58.0000),
    array ('QA', 25.5000, 51.2500),
    array ('RE', -21.1000, 55.6000),
    array ('RO', 46.0000, 25.0000),
    array ('RS', 44.0000, 21.0000),
    array ('RU', 60.0000, 100.0000),
    array ('RW', -2.0000, 30.0000),
    array ('SA', 25.0000, 45.0000),
    array ('SB', -8.0000, 159.0000),
    array ('SC', -4.5833, 55.6667),
    array ('SD', 15.0000, 30.0000),
    array ('SE', 62.0000, 15.0000),
    array ('SG', 1.3667, 103.8000),
    array ('SH', -15.9333, -5.7000),
    array ('SI', 46.0000, 15.0000),
    array ('SJ', 78.0000, 20.0000),
    array ('SK', 48.6667, 19.5000),
    array ('SL', 8.5000, -11.5000),
    array ('SM', 43.7667, 12.4167),
    array ('SN', 14.0000, -14.0000),
    array ('SO', 10.0000, 49.0000),
    array ('SR', 4.0000, -56.0000),
    array ('ST', 1.0000, 7.0000),
    array ('SV', 13.8333, -88.9167),
    array ('SY', 35.0000, 38.0000),
    array ('SZ', -26.5000, 31.5000),
    array ('TC', 21.7500, -71.5833),
    array ('TD', 15.0000, 19.0000),
    array ('TF', -43.0000, 67.0000),
    array ('TG', 8.0000, 1.1667),
    array ('TH', 15.0000, 100.0000),
    array ('TJ', 39.0000, 71.0000),
    array ('TK', -9.0000, -172.0000),
    array ('TM', 40.0000, 60.0000),
    array ('TN', 34.0000, 9.0000),
    array ('TO', -20.0000, -175.0000),
    array ('TR', 39.0000, 35.0000),
    array ('TT', 11.0000, -61.0000),
    array ('TV', -8.0000, 178.0000),
    array ('TW', 23.5000, 121.0000),
    array ('TZ', -6.0000, 35.0000),
    array ('UA', 49.0000, 32.0000),
    array ('UG', 1.0000, 32.0000),
    array ('UM', 19.2833, 166.6000),
    array ('US', 38.0000, -97.0000),
    array ('UY', -33.0000, -56.0000),
    array ('UZ', 41.0000, 64.0000),
    array ('VA', 41.9000, 12.4500),
    array ('VC', 13.2500, -61.2000),
    array ('VE', 8.0000, -66.0000),
    array ('VG', 18.5000, -64.5000),
    array ('VI', 18.3333, -64.8333),
    array ('VN', 16.0000, 106.0000),
    array ('VU', -16.0000, 167.0000),
    array ('WF', -13.3000, -176.2000),
    array ('WS', -13.5833, -172.3333),
    array ('YE', 15.0000, 48.0000),
    array ('YT', -12.8333, 45.1667),
    array ('ZA', -29.0000, 24.0000),
    array ('ZM', -15.0000, 30.0000),
    array ('ZW', -20.0000, 30.0000),
  );
  return $a[$i];
}


function
widget_map ($latitude, $longitude, $w = '100%', $h = '100%')
{
/*
<script type="text/javascript">

function
load ()
{
  if (GBrowserIsCompatible ())
    {
      var map = new GMap2(document.getElementById('map'));
      map.addControl(new GSmallMapControl());
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng(37.4419, -122.1419), 5);
    }
}

//var e = document.getElementById ('map');

//e.onload = load ();
//e.onunload = GUnload ();

</script>
<body onload="load()" onunload="GUnload()">
<div id="map" style="width:1024px; height:768px;"></div>
</body>
*/

  $p = '';

/*
  http://maps.google.com/?ie=UTF8&ll=37.0625,-95.677068&spn=31.013085,55.634766&t=h&z=4
google maps:
<script src="http://maps.google.com/maps?file=api&amp;v=1&amp;key=ABQIAAAAlcas_tJrQ_gomLSHqBRCnBQYpFxnnyEufMyseyjz1mBk8L_GRRSQsWgBwtk2YQwlC2qH5W9s3t0xbQ" type="text/javascript">
</script>
*/
      $p .= ''
           .'<iframe width="'.$w.'" height="'.$h.'"'
           .' frameborder="0"'
           .' scrolling="no"'
           .' marginheight="0"'
           .' marginwidth="0"'
           .' src="http://www.openstreetmap.org/export/embed.html?bbox='
             .($longitude - 0.005).','
             .($latitude - 0.005).','
             .($longitude + 0.005).','
             .($latitude + 0.005).'&layer=mapnik"'
           .'>'
           .'</iframe>'
;
  return $p;
}


function 
widget_textarea ($name, $s = '', $cols = 80, $rows = 10, $disabled = 0) 
{
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($_REQUEST);

  $p = '';

  if ($disabled == 0)
  $p .= ''
       .'<script type="text/javascript" src="ckeditor/ckeditor.js"></script>';

  $p .= ''
//       .'<script src="ckeditor/_samples/sample.js" type="text/javascript"></script>' 
//       .'<link href="ckeditor/_samples/sample.css" rel="stylesheet" type="text/css">'
       .'<textarea class="ckeditor" id="'.$name.'" name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'" wrap="soft"'
       .($disabled == 1 ? ' disabled="disabled"' : '')
       .'>'.$s.'</textarea>';
  return $p;
} 


function
widget_shoutbox ($name, $s = '', $cols = 80, $rows = 10, $disabled = 0)
{
  return widget_textarea ($name, $s, $cols, $rows, $disabled);
}


/*
  In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
  of $_FILES. Use phpversion() for version information.

  $_FILES['userfile']['name']
    The original name of the file on the client machine. 
  $_FILES['userfile']['type']
    The mime type of the file, if the browser
    provided this information. An example would be "image/gif". This mime
    type is however not checked on the PHP side and therefore don't take its
    value for granted.
  $_FILES['userfile']['size']
    The size, in bytes, of the uploaded file. 
  $_FILES['userfile']['tmp_name']
    The temporary filename of the file in which the uploaded file was stored on the server. 
  $_FILES['userfile']['error']
    The error code associated with this file upload. This element was added in PHP 4.2.0 

  UPLOAD_ERR_OK          0; There is no error, the file uploaded with success. 
  UPLOAD_ERR_INI_SIZE    1; The uploaded file exceeds the upload_max_filesize directive in php.ini. 
  UPLOAD_ERR_FORM_SIZE   2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. 
  UPLOAD_ERR_PARTIAL     3; The uploaded file was only partially uploaded. 
  UPLOAD_ERR_NO_FILE     4; No file was uploaded. 
  UPLOAD_ERR_NO_TMP_DIR  6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3. 
  UPLOAD_ERR_CANT_WRITE  7; Failed to write file to disk. Introduced in PHP 5.1.0. 
  UPLOAD_ERR_EXTENSION   8; File upload stopped by extension. Introduced in PHP 5.2.0. 

  related php.ini settings
    if (post_max_size > upload_max_filesize) in php.ini
      otherwise you will not be able to report the correct error in case of a
      too big upload ! Also check the max-execution-time (upload-time could be
      added to execution-time)

    if (post >post_max_size) in php.ini
      $_FILES and $_POST will return empty

  The data encoding type, enctype, MUST be specified as enctype="multipart/form-data"
  MAX_FILE_SIZE must precede the file input field
  Name of input element determines name in $_FILES array
*/
/*
function
widget_upload ($name, $upload_path, $max_file_size, $mime_type, $submit_button_html, $uploaded_html)
{
  $debug = 0;
  $p = '';

  if (!$_FILES)
    return '<form action="?'.http_build_query2 (array (), true)
      .'" method="POST" enctype="multipart/form-data"'
//      .' style="margin:0;"'
      .' style="display:inline;"'
      .'>'
      .'<input type="hidden" name="MAX_FILE_SIZE" value="'
      .$max_file_size
      .'">'
      .'<input type="file"'
      .' name="'.$name.'"'
//      .' title="'
//      .$tooltip
//      .'"'
      .($max_file_size ? ' maxlength="'.$max_file_size.'"' : '')
      .($mime_type ? ' accept="'.$mime_type.'"' : '')
      .'>'
      .($submit_button_html ? $submit_button_html :
       '<input type="submit" name="'.$name.'" value="Upload"'
//      .' tooltip="'
//      .$tooltip
//      .'"'
)
      .'>'
      .'</form>'
;

  if ($debug)
    {
      $p .= '<pre><tt>'
           .sprint_r ($_FILES);
    }

  $d = $upload_path.'/'
//      .str_replace (' ', '_', basename($_FILES[''.$name.'']['name']));
      .basename($_FILES[''.$name.'']['name']);

  if (file_exists ($d))
    $p .= 'ERROR: file already exists';
  else if (move_uploaded_file ($_FILES[''.$name.'']['tmp_name'], $d) == FALSE)
    $p .= 'ERROR: move_uploaded_file() failed';

  $s = Array (
           UPLOAD_ERR_OK =>         'OK',
           UPLOAD_ERR_INI_SIZE =>   'The uploaded file exceeds the upload_max_filesize directive ('
                                   .ini_get ('upload_max_filesize')
                                   .') in php.ini',
           UPLOAD_ERR_FORM_SIZE =>  'The uploaded file exceeds the MAX_FILE_SIZE directive ('
                                   .$max_file_size
                                   .') that was specified in the HTML form',
           UPLOAD_ERR_PARTIAL =>    'The uploaded file was only partially uploaded',
           UPLOAD_ERR_NO_FILE =>    'No file was uploaded',
           UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
//           UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
//           UPLOAD_ERR_EXTENSION =>  'File upload stopped by extension'
         );

  if (!empty ($_FILES[''.$name.'']) &&
      $_FILES[''.$name.'']['error'] == UPLOAD_ERR_OK)
    {
      $p .= $uploaded_html;
    }
  else
    {
      $e = $s[$_FILES[''.$name.'']['error']];
      if (!$e)
        $e .= 'An unknown error occured';
      $p .= 'ERROR: '.$e;
    }

  if ($debug)
    {
      $p .= '<pre><tt>'
           .sprint_r ($s)
           .sprint_r ($_FILES);
    }

  return $p;
}
*/
function
widget_upload ($name, $upload_path, $max_files = 1, $max_file_size = -1, $mime_type = NULL, $whitelist = NULL, 
               $submit_button_html = NULL)
{
  $debug = 0;
  $s = Array (
           UPLOAD_ERR_OK =>         'OK',
           UPLOAD_ERR_INI_SIZE =>   'The uploaded file exceeds the upload_max_filesize directive ('
                                   .ini_get ('upload_max_filesize')
                                   .') in php.ini',
           UPLOAD_ERR_FORM_SIZE =>  'The uploaded file exceeds the MAX_FILE_SIZE directive ('
                                   .$max_file_size
                                   .') that was specified in the HTML form',
           UPLOAD_ERR_PARTIAL =>    'The uploaded file was only partially uploaded',
           UPLOAD_ERR_NO_FILE =>    'No file was uploaded',
           UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
//           UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
//           UPLOAD_ERR_EXTENSION =>  'File upload stopped by extension'
         );

  if (!$submit_button_html)
    $submit_button_html = '<input type="submit" value="Upload"'
//                         .' tooltip="'.$tooltip.'"'
                         .'>';

  $p = '';

  $p .= '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" enctype="multipart/form-data"'
       .' style="display:inline;"'
       .'>';

  if ($max_file_size > 0)
    $p .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'"> ';

  for ($i = 0; $i < $max_files; $i++)
    {
      $p .= '<input type="file"'
           .' name="'.$name.'[]"'
//           .' title="'.$tooltip.'"'
;
      if ($max_file_size > 0)
        $p .= ' maxlength="'.$max_file_size.'"';
      if ($mime_type) 
        $p .= ' accept="'.$mime_type.'"';
      $p .= '> ';
    }

  $p .= $submit_button_html; 

  if ($whitelist)
    $p .= '<br>Allowed: '.implode ($whitelist, ', ').'<br> ';

  $p .= '</form>';

  if (!$_FILES)
    return $p;    
  if (empty ($_FILES[$name]))
    return $p.'ERROR: an unknown error occured';

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($_FILES);

  for ($i = 0; $i < $max_files; $i++)
    {
      $suffix = get_suffix ($_FILES[$name]['name'][$i]);
      $f = widget_filename_escape ($_FILES[$name]['name'][$i]);

      if ($f == '')
//        $p .= 'ERROR: empty filename ';
        continue;
      else if (!in_array ($suffix, $whitelist))
        {
          $p .= 'ERROR: *'.$suffix.' files not allowed ';
          continue;
        }
      else
      if (file_exists ($d))
        $p .= 'ERROR: file '.$f.$suffix.' already exists ';
      else if ($_FILES[$name]['error'][$i] != UPLOAD_ERR_OK && $max_files == 1)
        {
          $e = $s[$_FILES[$name]['error'][$i]];
          if (!$e)
            $e .= 'An unknown error occured ';
          $p .= 'ERROR: '.$e;
        }

      $d = $upload_path.'/'.$f.$suffix;
      // DEBUG
//      echo $_FILES[$name]['tmp_name'][$i].' '.$d;
      if (!move_uploaded_file ($_FILES[$name]['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'].'/'.$d))
        {
//          $p .= 'ERROR: move_uploaded_file() failed ';
        }
      else
        $p .= '<a href="'.$d.'">'.basename ($d).'</a> ';
    }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($_FILES);

  return $p;
}


function
widget_captcha ($captcha_path)
{
  // use random captcha image
  if (!($handle = opendir ($captcha_path)))
    return 'ERROR: problem with CAPTCHA';

  $a = array ();
  $count = 0;
  while (false !== ($p = readdir ($handle)))
    if (get_suffix ($p) == '.jpg')
      $a[$count++] = $p;

  closedir ($handle);

  srand (microtime () * time ());
  $r = rand (0, sizeof ($a) - 1);

  $captcha_md5 = set_suffix ($a[$r], '');
  $widget_captcha_key = md5 ($captcha_md5.$_SERVER['REMOTE_ADDR']); // key
  $img = $captcha_path.'/'.$captcha_md5.'.jpg'; // image name is md5 of the captcha in the image

  $p = '';
  $p .= '<input type="hidden" name="widget_captcha_key" value="'.$widget_captcha_key.'">';
  $p .= '<img src="'.$img.'" border="0" title="enter this CAPTCHA in the field to the right">';
  $p .= '<input type="text" size="3" maxsize="3" name="widget_captcha" title="enter the CAPTCHA you see left from here">';

  return $p;
}


function
widget_captcha_check ()
{
  $widget_captcha = get_request_value ('widget_captcha');
  $widget_captcha_key = get_request_value ('widget_captcha_key');

  // DEBUG
//  echo md5 (md5 ($widget_captcha).$_SERVER["REMOTE_ADDR"]).' == '.$widget_captcha_key.'<br>';

  if (md5 (md5 ($widget_captcha).$_SERVER["REMOTE_ADDR"]) == $widget_captcha_key)
    return TRUE;
  return FALSE;
}


// public relation widget
function
widget_relate ($title, $url = NULL, $rss_feed_url = NULL)
{
/*
  if (!($url))
    {
      $url = 'http://'.$_SERVER['HTTP_HOST'];
      $url .= $_SERVER["REQUEST_URI"];   
//      if (strncmp ($_SERVER['SCRIPT_NAME'], '/index.', 7))
//        $url .= $_SERVER['SCRIPT_NAME'];
      $url = urlencode ($url);
    }

  $title = htmlentities (trim ($title));
  $url = htmlentities (trim ($url));
  $relate_config_xml = '<?xml version="1.0" encoding="UTF-8"?>
<categories>
  <category>
    <title>Share</title>
    <tooltip></tooltip>
    <separate>0</separate>
    <buttononly>0</buttononly>
    <logo>images/widget/widget_relate_tellafriend.png</logo>
    <query>mailto:?body='.$url.'&amp;subject='.$title.'</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category>
  <!-- category>
    <title>Bookmark</title>
    <tooltip></tooltip>   
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_star.png</logo>
    <query>javascript:js_bookmark (\''.$url.'\', \''.$title.'\');</query>
    <button>1</button>
    <select>1</select>  
    <movable>0</movable>  
    <voteable>0</voteable>
  </category -->
  <!-- category>
    <title>Make us your start page</title>
    <tooltip></tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_home.png</logo>
    <query>this.style.behavior=\'url(#default#homepage)\';this.setHomePage(\'http://torrent-finder.com\');</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>  
    <voteable>0</voteable>
  </category -->
  <!-- category>
    <title>RSS feed</title>
    <tooltip></tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_rss.png</logo>
    <query>'.$rss_feed_url.'</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category -->
  <!-- category>
    <title>Donate</title>
    <tooltip>* * *   D O N A T I O N S   A R E   A C C E P T E D   * * *

images/widget_relate_refrigator.jpg

Individuals and companies can now donate funds to support me and keep me from
writing proprietary software

Thank You!

* * *   D O N A T I O N S   A R E   A C C E P T E D   * * *</tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_paypal.png</logo>
    <query>http://paypal.com</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category -->
  <!-- category>
    <title>BerliOS</title>
    <tooltip></tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>http://developer.berlios.de/bslogo.php?group_id=0</logo>
    <query>http://developer.berlios.de</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category -->
  <!-- category>
    <title>SourceForge</title>
    <tooltip></tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>http://sourceforge.net/sflogo.php?group_id=0</logo>
    <query>http://sourceforge.net</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category -->
  <category>
    <title>Digg</title>
    <tooltip>Add to Digg</tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_digg.png</logo>
    <query>http://digg.com/submit?phase=2&amp;url='.$url.'&amp;bodytext=&amp;tags=&amp;title='.$title.'</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category>
  <category>
    <title>Twitter</title>
    <tooltip>Add to Twitter</tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_twitter.png</logo>
    <query>http://twitter.com/home?status='.$url.'</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category>
  <category>
    <title>Facebook</title>
    <tooltip>Add to Facebook</tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_facebook.png</logo>
    <query>http://www.facebook.com/sharer.php?u='.$url.'</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category>
  <category>
    <title>StumbleUpon</title>
    <tooltip>Add to StumbleUpon</tooltip>
    <separate>0</separate>
    <buttononly>1</buttononly>
    <logo>images/widget/widget_relate_stumbleupon.png</logo>
    <query>http://www.stumbleupon.com/submit?url='.$url.'&amp;title='.$title.'</query>
    <button>1</button>
    <select>1</select>
    <movable>0</movable>
    <voteable>0</voteable>
  </category>
</categories>';

//http://twitter.com/home?status=Hardware+Companies+Team+Up+To+Fight+Mobile+Linux+Fragmentation%3A+http%3A%2F%2Fbit.ly%2Fd9DXNF
//http://www.facebook.com/sharer.php?u=http://linux.slashdot.org/story/10/06/05/1327228/Hardware-Companies-Team-Up-To-Fight-Mobile-Linux-Fragmentation

  // DEBUG
//  echo $relate_config_xml;
//  exit;
  $config_xml = simplexml_load_string ($relate_config_xml);

  $p = '';

//widget_cms ($logo, $config_xml, $link_suffix = NULL, $flags = 4)
  $p = widget_cms (NULL, $config_xml);

  return $p;
*/
  return '';
}


function
widget_test ()
{
//error_reporting (E_ALL|E_STRICT);

$p = "widget_video(): ";

$url = "http://y6.ath.cx/test/media/test.flv";
$p .= widget_video ($url, 400, 300);

$p .= "<hr>widget_upload(): ";

$max_file_size = 1000000;
$mime_type = NULL; // anything
$p .= widget_upload ("tooltip", "/tmp", $max_file_size, $mime_type, NULL);

$p .= "<hr>widget_audio(): ";

$url = "media/audio.mp3";
$start = 0;
$stream = NULL;
$next_stream = NULL; 
$p .= widget_audio ($url, $start, $stream, $next_stream);

$p .= "<hr>widget_map(): ";

$p .= widget_map (0, 0);

$p .= "<hr>widget_index(): ";

$dir = "media/";
$recursive = 0;
$p .= widget_index ($dir, $recursive);

$p .= "<hr>widget_relate(): ";

$relate_site_title_s = "relate_site_title";
$relate_site_url_s = "relate_site_url";
$p .= widget_relate ($relate_site_title_s, $relate_site_url_s, "./", 0,
                            WIDGET_RELATE_TELLAFRIEND|
                            WIDGET_RELATE_SBOOKMARKS|
                            0);
return $p;
}



}

?>