<?php
/*
misc.php - miscellaneous functions

Copyright (c) 2006 - 2009 NoisyB


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
if (!defined ('MISC_MISC_PHP'))
{
define ('MISC_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);


$ctype__ = array(32,32,32,32,32,32,32,32,32,40,40,40,40,40,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,
  -120,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,4,4,4,4,4,4,4,4,4,4,16,16,16,16,16,16,
  16,65,65,65,65,65,65,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,16,16,16,16,16,
  16,66,66,66,66,66,66,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,16,16,16,16,32,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
function isalnum ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(01 | 02 | 04 )) != 0)?1:0);}
function isalpha ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(01 | 02 )) != 0)?1:0);}
function isascii ($c){ global $ctype__; return (((( ord($c) )<=0177) != 0)?1:0);}
function iscntrl ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 040 ) != 0)?1:0);}
function isdigit ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 04 ) != 0)?1:0);}
function isgraph ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(020 | 01 | 02 | 04 )) != 0)?1:0);}
function islower ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 02 ) != 0)?1:0);}
function isprint ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(020 | 01 | 02 | 04 | 0200 )) != 0)?1:0);}
function ispunct ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 020 ) != 0)?1:0);}
function isspace ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 010 ) != 0)?1:0);}
function isupper ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 01 ) != 0)?1:0);}
function isxdigit ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(0100 | 04 )) != 0)?1:0);}


function
get_ip ($address)
{
  // if it isn't a valid IP assume it is a hostname
  $preg = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}'
         .'(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#';
  if (!preg_match($preg, $address))
    {
      $result = gethostbyname ($address);

      // not a valid host nor IP
      if ($result === $address)
        $result = false;
    }
  else
    $result = $address;
        
  return $result;
}


function
check_udp ($address, $port)
{
  $fp = fsockopen('udp://'.$address, $port, $errno, $errstr, 1); 
  if (!$fp)
    return 0;

  socket_set_timeout ($fp, 2);
  if (!fwrite($fp,'\x00'))
    return 0;

  $t1 = time();
  $res = fread($fp, 1);
  $t2 = time();
  fclose ($fp);

  if ($t2 - $t1 > 1)
    return 0; 

  if (!($res))
    return 0;

  return 1;
}


function
scandir4 ($path, $sort)
{
  $i = 0;
  $a = array ();

  $dir = opendir ($path);
  while (($a[$i] = readdir ($dir)) != false)
    $i++;
  closedir ($dir);

  if ($sort)
    array_multisort ($a, SORT_DESC);
  else
    sort ($a);

  return $a;
}


function
time_ms ()
// returns milliseconds since midnight
{
  $tv = gettimeofday ();

  if ($tv)
    {
      $t = $tv['usec'] / 1000;
      $t += ($tv['sec'] % 86400) * 1000;
    }

  return $t;
}


function
time_count ($t_date)
{
  static $t_now = 0;
  static $t_count = 0;

  if ($t_now == 0)
    $t_now = $t_count = time ();

  $p = NULL;

  while ($t_count > $t_date)
    {
      $t_calc = $t_now - $t_count;

      if ($t_calc < 3600)
        {
          $t_count -= 300;
          if ($t_calc)
            if ($t_count < $t_date)
            $p .= sprintf ('%s', $t_calc / 60 .' minutes');
        }
      else if ($t_calc < 86400)
        {
          $t_count -= 3600;
          if ($t_count < $t_date)
            $p .= sprintf ('%s', $t_calc / 3600 .' hours');
        }
      else
        {
          $t_count -= 86400;
          if ($t_count < $t_date)
            $p .= sprintf ('%s', $t_calc / 86400 .' days');
        }
    }

  return $p;
}


function
islocalhost ()
{
  return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'];
}


function
misc_get_keywords_alnum ($s)
{
  if (strlen (trim ($s)) < 4)
    return false;

  for ($i = 0; $s[$i]; $i++)
    if (!isalnum ($s[$i]))
      return false;

  return true;
}


function
misc_get_keywords_alpha ($s)
{
  if (strlen (trim ($s)) < 4)
    return false;

  for ($i = 0; $s[$i]; $i++)
    if (!isalpha ($s[$i]))
      return false;

  return true;
}


function
misc_get_keywords ($s, $flag = 0) // default = isalnum
{
  for ($i = 0; $s[$i]; $i++)
    if (ispunct ($s[$i]))
      $s[$i] = ' ';

  $a = explode (' ', strtolower ($s));
  for ($i = 0; isset ($a[$i]); $i++)
    $a[$i] = trim ($a[$i]);
  // TODO: more sensitivity instead of array_filter()
  $a = array_filter ($a, (!$flag ? 'misc_get_keywords_alnum' : 'misc_get_keywords_alpha'));
  $a = array_merge (array_unique ($a));

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $s = implode (' ', $a);
  $s = trim ($s);

  return $s;
}


function
echo_gzip ($p)
{
  ob_start ('ob_gzhandler');
  echo $p;
  ob_end_flush ();
}


function
get_suffix ($filename)
// get_suffix() never returns NULL
{
  $p = basename ($filename);
  if (!$p)
    $p = $filename;

  $s = strrchr ($p, '.');
  if (!$s)
    $s = strchr ($p, 0);
  if ($s == $p)
    $s = strchr ($p, 0);

  return $s;
}


function
set_suffix ($filename, $suffix)
{
  // always use set_suffix() and NEVER the code below
  return str_replace (get_suffix ($filename), $suffix, $filename);
}


function
short_name ($str, $limit)
{
  // Make sure a small or negative limit doesn't cause a negative length for substr().
  if ($limit < 3)
    $limit = 3;

  // Now truncate the string if it is over the limit.
  if (strlen ($str) > $limit)
    return substr($str, 0, $limit - 3) . '..';
  else
    return $str;
}


function
in_tag ($s)
{
  // are we inside a tag?
  return strpos ($s, '>') < strpos ($s, '<');
}


function
is_url ($s)
{
  // checks if string is a url
  $is_url = 0;
  if (strlen ($s) > 4 &&
      isalpha ($s[0]) &&
      !strstr ($s, '..') &&
      substr_count ($s, '.') == 2)
    $is_url = 1;
  if (stristr ($s, '.net') ||
      stristr ($s, '.org') ||
      stristr ($s, '.com'))
    $is_url = 1;

  return $is_url;
}


function
parse_links ($s)
{
  // turn plain text urls into links
//  return preg_replace ('/\\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', "<a href=\"#\" onclick=\"open_url('\\0')\";return false;>\\0</a>", $s);
//  return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\">\\0</a>", $s);
//  $s = eregi_replace("((([ftp://])|(http(s?)://))((:alnum:|[-\%\.\?\=\#\_\:\&\/\~\+\@\,\;])*))","<a href = '\\0' target='_blank'>\\0</a>", $s);
//  $s = eregi_replace("(([^/])www\.|(^www\.))((:alnum:|[-\%\.\?\=\#\_\:\&\/\~\+\@\,\;])*)", "\\2<a href = 'http://www.\\4'>www.\\4</a>", $s);

  $a = explode (' ', strip_tags ($s));
  $a = array_merge (array_unique ($a)); // remove dupes

  // find eventual urls
  $a_size = sizeof ($a);
  for ($i = 0; $i < $a_size; $i++)
    if (is_url ($a[$i]))
      {
        if (stristr ($a[$i], 'http://'))
          $s = str_replace ($a[$i], '<a href="'.$a[$i].'">'.$a[$i].'</a>', $s);
        else
          $s = str_replace ($a[$i], '<a href="http://'.$a[$i].'">'.$a[$i].'</a>', $s);
      }

  return $s;
}


if (!function_exists ('sprint_r'))
{
function 
sprint_r ($var)
{
  ob_start ();

  print_r ($var);

  $ret = ob_get_contents ();

  ob_end_clean ();

  return $ret;
}
}


function
ftp_search ($search)
{
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $url = 'http://www.google.com/search?q='.urlencode ($query);

  return $url;
}


function
video_search ($search)
{
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $url = 'http://www.google.com/search?q='.urlencode ($query);

  return $url;
// youtube
//    <feed>http://video.google.com/videosearch?q=quakeworld&amp;so=1&amp;output=rss&amp;num=1000</feed>
//    <feed>http://gdata.youtube.com/feeds/api/videos?vq=quake1&amp;max-results=50</feed>
}


function
vname (&$var, $scope=false, $prefix='unique', $suffix='value')
{
  if ($scope)
    $vals = $scope;
  else
    $vals = $GLOBALS;

  $old = $var;
  $var = $new = $prefix.rand().$suffix;
  $vname = FALSE;

  foreach($vals as $key => $val)
    if($val === $new)
      $vname = $key;

  $var = $old;

  return $vname;
}


function
misc_exec ($cmdline, $debug)
{
  if ($debug)
    echo $cmdline.'\n';

  if ($debug < 2)
    {
      $a = array();

      exec (escapeshellcmd ($cmdline), $a, $res);

      $p = '';
      if ($debug)
        $p = $res.'\n';

      $i_max = sizeof ($a);
      for ($i = 0; $i < $i_max; $i++)
        $p .= $a[$i].'\n';

      return $p;
    }

  return '';
}


function
get_request_value ($name)
{
  if (isset ($_POST[$name]))
    return $_POST[$name];

  if (isset ($_GET[$name]))
    return $_GET[$name];

  if (isset ($_REQUEST[$name]))
    return $_REQUEST[$name];

  if (isset ($_COOKIE[$name]))
    return $_COOKIE[$name];

  return NULL;
}


function
html_head_tags_meta ($name, $content)
{
  if ($name && $content)
    return '<meta name="'
          .$name
          .'" content="'
          .$content
          .'">'
          ."\n";

  return '';
}


function
html_head_tags_http_equiv ($http_equiv, $content)
{
  if ($http_equiv && $content)
    return '<meta http-equiv="'
          .$http_equiv
          .'" content="'
          .$content
          .'">'
          ."\n";

  return '';
}


function
html_head_tags ($icon, $title, $refresh, $charset,
                $use_dc, $desc, $keywords, $identifier, $lang, $author)
{
  $p = '';

  $p .= html_head_tags_http_equiv ('Content-Type', 'text/html; charset='.($charset ? $charset : 'UTF-8'));

  if ($refresh > 0)
    $p .= html_head_tags_http_equiv ('refresh', $refresh.'; URL='.$_SERVER['REQUEST_URI']);

/*
    <meta http-equiv="imagetoolbar" content="no">
    <meta http-equiv="reply-to" content="editor@NOSPAM.sniptools.com">
    <meta http-equiv="MSThemeCompatible" content="Yes">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Expires" content="Mon, 24 Sep 1976 12:43:30 IST">
*/

  if ($icon)
    $p .= '<link rel="icon" href="'
         .$icon
         .'" type="image/png">'
         ."\n";

  if ($title)
    $p .= '<title>'
          .$title
          .'</title>'
          ."\n";

  if ($use_dc)
    $p .= html_head_tags_meta ('description', $desc ? $desc : $title)
         .html_head_tags_meta ('author', $author ? $author : 'Admin')
         .html_head_tags_meta ('keywords', $keywords ? $keywords : 'html, php')
         .html_head_tags_meta ('robots', 'follow')
         .'<!-- Dublin Core -->\n'
         .html_head_tags_meta ('DC.Title', $desc ? $desc : $title)
         .html_head_tags_meta ('DC.Creator', $author ? $author : 'Admin')
         .html_head_tags_meta ('DC.Subject', $desc ? $desc : $title)
         .html_head_tags_meta ('DC.Description', $desc ? $desc : $title)
         .html_head_tags_meta ('DC.Publisher', $author ? $author : 'Admin')
//         .html_head_tags_meta ('DC.Contributor', '')
//         .html_head_tags_meta ('DC.Date', '')
         .html_head_tags_meta ('DC.Type', 'Software')
         .html_head_tags_meta ('DC.Format', 'text/html')
         .html_head_tags_meta ('DC.Identifier', $identifier ? $identifier : 'localhost')
//         .html_head_tags_meta ('DC.Source', '')
         .html_head_tags_meta ('DC.Language', $lang ? $lang : 'en')
//         .html_head_tags_meta ('DC.Relation', '')
//         .html_head_tags_meta ('DC.Coverage', '')
//         .html_head_tags_meta ('DC.Rights', 'GPL')

//       .html_head_tags_meta ('description', 'Trapping keyboard events with Javascript -- in a cross-browser way [Sniptools]')
//       .html_head_tags_meta ('keywords', 'Javascript keyboard events, keypress, javascript, keyCode, which, repeat, keydown event, Sniptools')
//       .html_head_tags_meta ('author', 'Shashank Tripathi')
//       .html_head_tags_meta ('revisit-after', '1 week')
//       .html_head_tags_meta ('robots', 'index,all')
//       .html_head_tags_meta ('revisit-after', '7 days')
//       .html_head_tags_meta ('author', 'Shashank Tripathi')
//       .html_head_tags_meta ('generator', 'Homesite 5.0&nbsp; | &nbsp;  Dreamweaver 6 beta&nbsp; | &nbsp; TopStyle 3&nbsp; | &nbsp; Notepad&nbsp; | &nbsp; Adobe PS 7.0')
//       .html_head_tags_meta ('resource-type', 'Public')
//       .html_head_tags_meta ('classification', 'Internet Services')
//       .html_head_tags_meta ('MSSmartTagsPreventParsing', 'TRUE')
//       .html_head_tags_meta ('robots', 'ALL')
//       .html_head_tags_meta ('distribution', 'Global')
//       .html_head_tags_meta ('rating', 'Safe For Kids')
//       .html_head_tags_meta ('language', 'English')
//       .html_head_tags_meta ('doc-type', 'Public')
//       .html_head_tags_meta ('doc-class', 'Living Document')
//       .html_head_tags_meta ('doc-rights', 'Copywritten Work')
//       .html_head_tags_meta ('distribution', 'Global')
;

  return $p;
}


/*
  Create a link by joining the given URL and the parameters given as the second argument.
  Arguments :  $url - The base url.
                 $params - An array containing all the parameters and their values.
                 $use_existing_arguments - Use the parameters that are present in the current page
  Return : The new url.
  Example : 
             misc_getlink('http://www.google.com/search',array('q'=>'binny','hello'=>'world','results'=>10));
                     will return
             http://www.google.com/search?q=binny&amp;hello=world&amp;results=10
*/
function
misc_getlink ($url, $params = array(), $use_existing_arguments = false)
{
  if ($use_existing_arguments)
    $params += $_GET;

  if (!$params)
    return $url;

  $link = $url;
  if (strpos ($link, '?') === false)
    $link .= '?'; // if there is no '?' add one at the end
  else if (!preg_match ('/(\?|\&(amp;)?)$/', $link))
    $link .= '&amp;'; //If there is no '&' at the END, add one.
    
  $params_array = array();
  foreach ($params as $key=>$value)
    if (gettype ($value) == 'array')
      {
        // handle array data properly
        foreach($value as $val)
          $params_array[] = $key
                           .'[]='
                           .urlencode($val);
      }
    else
      $params_array[] = $key
                       .'='
                       .urlencode ($value);

  $link .= implode ('&amp;',$params_array);
    
  return $link;
} 


}

?>