<?php
/*
misc.php - miscellaneous functions

Copyright (c) 2006 - 2010 NoisyB


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


function encodemail ($my_mail)
{
  // light email protection
  $p = '';
  for ($i = 0; $i < strlen ($my_mail); $i++)
    $p .= "%".dechex (ord ($my_mail[$i])); 
  return $p;
}


function get_domain ($url)
{
  if (filter_var ($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE)
    return false;
  $parts = parse_url ($url);
  return $parts['scheme'].'://'.$parts['host'];
}


function
file_post_contents ($url, $vars, $timeout = 300)
{
  $sock = curl_init ();
  if ($sock == FALSE)
    {
      echo 'CURL support missing';
      return NULL;
    }

  curl_setopt ($sock, CURLOPT_URL, $url);
  $agent = random_user_agent ();
  curl_setopt ($sock, CURLOPT_USERAGENT, $agent);
  curl_setopt ($sock, CURLOPT_RETURNTRANSFER, 1); // return to string instead of spewing to output
  curl_setopt ($sock, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt ($sock, CURLOPT_FOLLOWLOCATION, 1);  // follow location header, not sure if this is needed.

  // allow ssl always
  curl_setopt ($sock, CURLOPT_SSL_VERIFYPEER, false);

  // cookies
  $parts = parse_url ($url); 
  curl_setopt ($sock, CURLOPT_COOKIEJAR, $parts['host'].'.txt');
  curl_setopt ($sock, CURLOPT_COOKIEFILE, $parts['host'].'.txt');

  // Expect: 100-continue doesn't work properly with lightTPD
  // This fix by zorro http://groups.google.com/group/php.general/msg/aaea439233ac709b
  curl_setopt ($sock, CURLOPT_HTTPHEADER, array ('Expect:')); 

  // DEBUG
//  curl_setopt ($sock, CURLOPT_VERBOSE, 1);

  // set method POST
  curl_setopt ($sock, CURLOPT_POST, 1);
  curl_setopt ($sock, CURLOPT_POSTFIELDS, $vars);

  $response = curl_exec ($sock);
  if ($response == FALSE)
    {
      echo 'file_post_contents() failed';
      return NULL;
    }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($response);
 
  // DEBUG
//  echo '<pre><tt>';
//  print_r (curl_getinfo ($sock));
//  print_r (curl_getinfo ($sock, CURLINFO_HTTP_CODE));

  curl_close ($sock);

  return $response;
}


function
tor_get_contents ($url, $tor_proxy_host = '127.0.0.1', $tor_proxy_port = 9050, $timeout = 300)
{
  $sock = curl_init ();
  if ($sock == FALSE)
    {
      echo 'CURL support missing';
      return NULL;
    }

  curl_setopt ($sock, CURLOPT_PROXY, $tor_proxy_host.':'.$tor_proxy_port);
  curl_setopt ($sock, CURLOPT_URL, $url);
//  curl_setopt ($sock, CURLOPT_HEADER, 1);
  $agent = random_user_agent ();
  curl_setopt ($sock, CURLOPT_USERAGENT, $agent);
  curl_setopt ($sock, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($sock, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt ($sock, CURLOPT_TIMEOUT, $timeout);
  curl_setopt ($sock, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

  // DEBUG
//  curl_setopt ($sock, CURLOPT_VERBOSE, 1);

  $response = curl_exec ($sock);
  if ($response == FALSE)
    {
      echo 'TOR not running';
      return NULL;
    }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($response);

  // DEBUG
//  echo '<pre><tt>';
//  print_r (curl_getinfo ($sock));

  curl_close ($sock);

  return $response;
}


function
misc_read_cache ($cachefile, $cache_time)
{
  if (file_exists($cachefile))
    if (time() - $cache_time < filemtime ($cachefile))
      {
        $data = file_get_contents ($cachefile);
        return unserialize ($data);
      }
  return NULL;
}


function
misc_write_cache ($cachefile, $data)
{
  $cache = serialize ($data);
  $fh = fopen ($cachefile, 'wb');
  if ($fh)
    {
      fwrite (fh, $cache, sizeof ($cache));
      fclose (fh);
      touch ($cachefile, time()); // make sure it has time()
    }
}


function
misc_url_exists ($url)
{
  if (!strncasecmp ($url, 'udp://'))  // check UDP
    {
      $a = explode (':', $url);

      if (!($fp = fsockopen("udp://".$a[0], $a[1], $errno, $errstr, 1)))
        return false;

      socket_set_timeout ($fp, 2);
      if (!fwrite ($fp,"\x00"))
        return false;

      $t1 = time();
      $res = fread($fp, 1);
      $t2 = time();
      fclose ($fp);

      if ($t2 - $t1 > 1)
        return false;

      if (!($res))
        return false;

      return true;
    }

  if (file_get_contents ($url, FALSE, NULL, 0, 0) === false)
    return false;
  return true;
}


function
check_udp ($address, $port)
{
  return misc_url_exists ('udp://'.$address.':'.$port);
}


function
misc_explode_tag ($html_tag)
{
  // returns 2d array with tag name and attributes and their values
  $s = strpos ($html_tag, '<') + 1;
  $l = strrpos ($html_tag, '>') - $s;
  $p = substr ($html_tag, $s, $l);
  $p = trim ($p);
  // '=      "' to '="'
  $p = str_replace (array ('= "','=  "'), '="', $p);
  $p = str_replace (array ('= "','=  "'), '="', $p);
  $p = str_replace (array ('= "','=  "'), '="', $p);
  // '      ="' to '="'
  $p = str_replace (array (' ="','  ="'), '="', $p);
  $p = str_replace (array (' ="','  ="'), '="', $p);
  $p = str_replace (array (' ="','  ="'), '="', $p);

  // DEBUG
//  echo $p;

  $a = explode (' ', $p);
  $a = array_merge (array_unique ($a));

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $tag = array ();
  $count = 0;
  for ($i = 0; isset ($a[$i]); $i++)
    if (strpos ($a[$i], '=')) // is attribute
      {
        $aa = explode ('=', $a[$i], 2);

        $tag[strtolower (trim ($aa[0]))] = '"'.trim (trim ($aa[1]), '"').'"'; // trim first spaces then quotes
      }
    else // attribute without value (e.g. tag name)
      $tag[strtolower (trim ($a[$i]))] = NULL;

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($tag);

  return $tag;
}


function
misc_implode_tag ($tag)
{
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($tag);

  $p = '';

  $p .= '<';

  $a = array_keys ($tag);
  for ($i = 0; $a[$i]; $i++)
    {
      if ($i > 0)
        $p .= ' ';

      $p .= $a[$i];

      if ($tag[$a[$i]])
        $p .= '='.$tag[$a[$i]];
    }

  $p .= '>';

  return $p;
}


function
misc_gettag ($html_tag, $attr = array(), $use_existing_attr = false)
{
  $tag = misc_explode_tag ($html_tag);

  if (!$use_existing_attr)
    {
      // BUT keep the attributes without value (e.g. tag name)
      $attr_keys = array_keys ($tag);
      for ($i = 0; $attr_keys[$i]; $i++)
        if ($tag[$attr_keys[$i]] == NULL)
          $a[$attr_keys[$i]] = $tag[$attr_keys[$i]];
      $tag = $a;
    }

  if (!$attr)
    return misc_implode_tag ($tag);

//  $tag = array_replace ($tag, $attr);
  $attr_keys = array_keys ($attr);
  for ($i = 0; $attr_keys[$i]; $i++)
    if ($attr[$attr_keys[$i]] != NULL)
      $tag[$attr_keys[$i]] = '"'.trim ($attr[$attr_keys[$i]], '"').'"';

  return misc_implode_tag ($tag);
}


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
scandir4 ($path, $sort)
{
  $i = 0;
  $a = array ();

  $dir = opendir ($path);
  while (($a[$i] = readdir ($dir)) !== false)
    $i++;
  closedir ($dir);

  if ($sort)
    array_multisort ($a, SORT_DESC);
  else
    sort ($a);

  return $a;
}


function
misc_download ($url, $path)
{
  if (!($img = file_get_contents ($url)))
    return;

  if (!($out = fopen ($path, 'wb')))
    return;
 
  fwrite ($out, $img);
  fclose ($out);
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
misc_get_keywords_alnum ($s, $keyword_size = 3)
{
  if (strlen (trim ($s)) < $keyword_size)
    return false;

  for ($i = 0; $i < strlen ($s); $i++)
    if (!isalnum ($s[$i]) && $s[$i] != '_' && $s[$i] != '.')
      return false;

  return true;
}


function
misc_get_keywords_alpha ($s, $keyword_size = 3)
{
  if (strlen (trim ($s)) < $keyword_size)
    return false;

  for ($i = 0; $i < strlen ($s); $i++)
    if (!isalpha ($s[$i]) && $s[$i] != '_' && $s[$i] != '.')
      return false;

  return true;
}


function
misc_get_keywords ($s, $flag = 0) // default = isalnum
{
  $s = str_replace (array ('. ', ',', ';', '!', '?', '"'), ' ', $s);
  $s = str_replace (array ('  ', '  ', '  ', '  ', '  '), ' ', $s);

  for ($i = 0; $i < strlen ($s); $i++)
    if (ispunct ($s[$i]) && $s[$i] != '_' && $s[$i] != '.')
      $s[$i] = ' ';

  $a = explode (' ', strtolower ($s));
  for ($i = 0; isset ($a[$i]); $i++)
    $a[$i] = trim ($a[$i], ' .');
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
misc_get_keywords_html ($s, $flag = 0) // default = isalnum
{
  // so one keyword does not get glued to another because of strip_tags()
//  $s = str_replace ('>', '> ', $s);
//  $s = str_replace ('<', '< ', $s);
  $s = str_replace (array ('>',   '<'),
                    array ('> ', ' <'), $s);
  $s = str_replace (array ('  ', '  ', '  ', '  ', '  '), ' ', $s);
  $s = strip_tags ($s);

  return misc_get_keywords ($s, $flag);
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
str_shorten ($s, $limit)
{
  // Make sure a small or negative limit doesn't cause a negative length for substr().
  if ($limit < 3)
    $limit = 3;

  // Now truncate the string if it is over the limit.
  if (strlen ($s) > $limit)
    return substr($s, 0, $limit - 3).'..';
  else
    return $s;
}


function
str_similar ($str1, $str2)
{
/*
    $count = 0;
   
    $str1 = ereg_replace("[^a-z]", ' ', strtolower($str1));
    while(strstr($str1, '  ')) {
        $str1 = str_replace('  ', ' ', $str1);
    }
    $str1 = explode(' ', $str1);
   
    $str2 = ereg_replace("[^a-z]", ' ', strtolower($str2));
    while(strstr($str2, '  ')) {
        $str2 = str_replace('  ', ' ', $str2);
    }
    $str2 = explode(' ', $str2);
   
    if(count($str1)<count($str2)) {
        $tmp = $str1;
        $str1 = $str2;
        $str2 = $tmp;
        unset($tmp);
    }
   
    for($i=0; $i<count($str1); $i++) {
        if(in_array($str1[$i], $str2)) {
            $count++;
        }
    }
   
    return $count/count($str2)*100;
*/
//      if (similar_text ($last, $category->title, $match) < 50)
//      if (levenshtein ($last, $category->title) > 100)
//      if (str_compare ($last, $category->title) < 50)
  $t = array ();
  $t[] = explode (' ', $str1);
  $t[] = explode (' ', $str2);
  return !strncmp (soundex ($t[0][0]), soundex ($t[1][0]), 3);
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
/*
  // checks if string is a url
  $is_url = 0;

  if (strlen ($s) > 4 &&
      isalpha ($s[0]) &&
      !strstr ($s, '..') &&
      substr_count ($s, '.') == 2 &&
      (substr ($s, -4, 1) == '.' || substr ($s, -3, 1) == '.'))
    $is_url = 1;

  return $is_url;
*/
  if (filter_var ($s, FILTER_VALIDATE_URL) == FALSE)
    return 0;
  return 1;
}


function
parse_links ($s, $cached = 1)
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
        $url = $a[$i];
        if (!stristr ($url, 'http://'))
          $url = 'http://'.$url;

// cached: http://web.archive.org/web/*/http://ucon64.sourceforge.net
        $link = '<a href="'.$url.'">'.$url.'</a>';
        if ($cached == 1)
          $link = $link.'[<a href="http://web.archive.org/web/*/'.$url.'">Cached</a>]';

        $s = str_replace ($a[$i], $link, $s);
      }

  return $s;
}


function
detect_links ($s, $cached = 1)
{
  // find urls in text and turn them into links
  return parse_links ($s, $cached);
}


if (!function_exists('sys_get_temp_dir'))
{
function
sys_get_temp_dir ()
{
  if ($temp = getenv ('TMP'))
    return $temp;
  if ($temp = getenv ('TEMP'))
    return $temp;
  if ($temp = getenv ('TMPDIR'))
    return $temp;
  $temp = tempnam (__FILE__, '');
  if (file_exists ($temp))
    {
      unlink ($temp);
      return dirname ($temp);
    }
  return null;
}
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
misc_search ($search)
{
  $a = array ();

  // ftp
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $a[] = 'http://www.google.com/search?q='.urlencode ($query);
  // video
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $a[] = 'http://www.google.com/search?q='.urlencode ($query);
// youtube
//    <feed>http://video.google.com/videosearch?q=quakeworld&amp;so=1&amp;output=rss&amp;num=1000</feed>
//    <feed>http://gdata.youtube.com/feeds/api/videos?vq=quake1&amp;max-results=50</feed>
  $a[] = 'http://en.wikipedia.org/w/index.php?title=Special%3ASearch&redirs=0&search='.$search.'&fulltext=Search&ns0=1'; // wikipedia
  $a[] = 'http://www.google.com/search?ie=UTF-8&oe=utf-8&q='.$search; // lyrics
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q='.$search;             // images
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q=walkthrough+'.$search; // walkthrough
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q=cheat+'.$search; // cheat

  return $a;
}


/*
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

  foreach ($vals as $key => $val)
    if($val === $new)
      $vname = $key;

  $var = $old;

  return $vname;
}
*/


function
misc_exec ($cmdline, $debug = 0)
{
  if ($debug)
    echo 'cmdline: '.$cmdline."\n"
        .'escaped: '.escapeshellcmd ($cmdline).' (not used)'."\n"
;
  if ($debug == 2)
    return '';

  $a = array();

  exec ($cmdline, $a, $res);

  $p = '';
  if ($debug)
    $p = $res."\n";

  $i_max = count ($a);
  for ($i = 0; $i < $i_max; $i++)
    $p .= $a[$i]."\n";

  return $p;
}


function
get_cookie ($name)
{
  if (isset ($_COOKIE[$name]))
    return $_COOKIE[$name];

  return NULL;
}


function
get_request_value ($name)
{
//  if (isset ($_POST[$name]))
//    return $_POST[$name];

//  if (isset ($_GET[$name]))
//    return $_GET[$name];

  if (isset ($_REQUEST[$name])) // and cookies
    return $_REQUEST[$name];

  return NULL;
}


function
http_build_query2 ($args = array(), $use_existing_arguments = false)
{
  if ($use_existing_arguments)
    $a = array_merge ($_GET, $args); // $args overwrites $_GET
  else
    $a = $args;

  if (!sizeof ($a))
    return '';

  return http_build_query ($a);
} 


function
misc_seo_description ($html_body)
{
  // generate meta tag from the body
  $p = strip_tags ($html_body);
  $p = str_replace (array ('&nbsp;', '&gt;', '&lt;', "\n"), ' ', $p);
  $p = misc_get_keywords ($p, 1);
  return '<meta name="Description" content="'.$p.'">'
        .'<meta name="keywords" content="'.$p.'">';
}


function
misc_head_tags ($icon, $refresh = 0, $charset = 'UTF-8')
{
  $p = '';

  $p .= '<meta http-equiv="Content-Type" content="text/html;charset='.$charset.'">';
//  $p .= '<meta name="Content-Type" content="text/html; charset='.$charset.'">';

  if ($refresh > 0)
//    header ('location:'.$_SERVER['REQUEST_URI']);
    header ('refresh: '.$refresh.'; url='.$_SERVER['REQUEST_URI']);
//    $p .= '<meta http-equiv="refresh" content="'.$refresh.'; URL='.$_SERVER['REQUEST_URI'].'">';

/*
    <meta http-equiv="imagetoolbar" content="no">
    <meta http-equiv="reply-to" content="editor@NOSPAM.sniptools.com">
    <meta http-equiv="MSThemeCompatible" content="Yes">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Expires" content="Mon, 24 Sep 1976 12:43:30 IST">
*/

  if ($icon)
    $p .= '<link rel="icon" href="'.$icon.'" type="image/png">';

  return $p;
}


/*
  split html into an array with content separated by tags
    returns an array

  $a['start']      // [...<head>]
  $a['head']       // <head>[...]</head>
  $a['between']    // [</head>...<body>]
  $a['body']       // <body>[...]</body>
  $a['end']        // [</body>...]EOF
*/
function
split_html_content ($html)
{
  $p = strtolower ($html);

  // split in head, body (and the rest)
  if (strpos ($p, '<head'))
    {
      $head_start = strpos ($p, '<head');
      $head_start += strpos (substr ($p, $head_start), '>') + 1;
      $head_len = strpos ($p, '</head>') - $head_start;
    }
  else
    {
      $head_start = 0;
      $head_len = 0;
    }

  if (strpos ($p, '<body'))
    {
      $body_start = strpos ($p, '<body');
      $body_start += strpos (substr ($p, $body_start), '>') + 1;
      $body_len = strpos ($p, '</body>') - $body_start;
    }
  else
    {
      $body_start = 0;
      $body_len = strlen ($html);
    }

  $start = substr ($html, 0, $head_start);
  $head = substr ($html, $head_start, $head_len);
  $between = substr ($html, $head_start + $head_len, $body_start - ($head_start + $head_len));

  $body = substr ($html, $body_start, $body_len);

  $end = substr ($html, $body_start + $body_len);

  $a = array ();

  $a['start'] = $start;
  $a['head'] = $head;
  $a['between'] = $between;
  $a['body'] = $body;
  $a['end'] = $end;

  // DEBUG
//print_r ($a);

  return $a;
}


function
random_user_agent ()
{
  $ua = array('Mozilla','Opera','Microsoft Internet Explorer','ia_archiver');   
  $op = array('Windows','Windows XP','Linux','Windows NT','Windows 2000','OSX');
  $agent  = $ua[rand(0,3)].'/'.rand(1,8).'.'.rand(0,9).' ('.$op[rand(0,5)].' '.rand(1,7).'.'.rand(0,9).'; en-US;)';
  return $agent;
}


/*
function
var_xml ($v)
{
  // TODO: turn any variable into XML string
  return $v->asXML();
}
*/

// XML serializer
function
array2xml_func (&$xml, $a)
{
  foreach ($a as $name=>$value)
    {
//      $name = preg_replace ("^[0-9]{1,}^", 'data', $name);
      $name = str_replace ('.', '_', $name);
 
      $xml .= '<'.$name.'>';

      if (is_array ($value))
        array2xml_func ($xml, $value);
      else
        {
//          if ($name == 'gq_name' || $name == 'nick' || $name == 'NGU')
//            $xml .= base64_encode ($value);
//          else
            $xml .= htmlspecialchars ($value, ENT_NOQUOTES, 'UTF-8');
        }

      $xml .= '</'.$name.'>'."\n";
    }
}


function 
array2xml ($a, $root_name = 'root')
{
  $xml = '';

//  if (is_array ($a))
//    if (count ($a) > 0)
      {
        array2xml_func ($xml, $a);
 
        return '<?xml version="1.0" encoding="utf-8"?>'."\n"
              .'<'.$root_name.'>'."\n"
              .$xml
              .'</'.$root_name.'>'."\n"
;
      }

  return '';
}
 
 
/*
// XML unserializer
function 
xml2array_func (&$a, $xml)
{
  // EVIL HACK: flattening out the redundant ['data'] tags from multidimensional PHP arrays
  for ($i = 0; $xml->data[$i]; $i++)
    {
      array_push ($a, (array) $xml->data[$i]);

      $b = array ();
      for ($j = 0; $j < sizeof ($a[$i]['players']->data); $j++)
        {
//          print_r ((array) $a[$i]['players']->data[$j]);
          $b[$j] = (array) $a[$i]['players']->data[$j];   
        }
      $a[$i]['players'] = $b;
      $a[$i]['players']['nick'] = base64_decode ($a[$i]['players']['nick']);
      $a[$i]['players']['gq_name'] = base64_decode ($a[$i]['players']['gq_name']);
    }
// base64_decode ()
}
 
 
function
xml2array ($xml)
{
  $a = array ();
  xml2array_func ($a, $xml);

  //DEBUG
//  echo '<pre><tt>';
//  print_r ((array) $a);

  return (array) $a;
}


function
array2xml_test ()
{
  $a = array ("test" => array ("a" => "1", "b" => "2"));
  print_r ($a);
  echo "array2xml(): ";
  $xml = array2xml ($a);
  echo $xml;
  echo "<hr>xml2array(): ";
  $a = xml2array ($xml);
  print_r ($a);
}

*/


}


?>