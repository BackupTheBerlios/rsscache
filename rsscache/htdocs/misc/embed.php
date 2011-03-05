<?php
/*
embed.php - embedding other pages functions

Copyright (c) 2010 NoisyB


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
if (!defined ('MISC_EMBED_PHP'))
{
define ('MISC_EMBED_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
//require_once ('wikipedia.php'); // embed wikipedia stuff using wikipedia API


define ('ALLOW_DEF', ''
       .'<a></a>'
       .'<br>'

       .'<font></font>'
       .'<form></form>'

       .'<img>'
       .'<input>'

       .'<option></option>'

       .'<pre></pre>'

       .'<select></select>'

       .'<table></table>'
       .'<td></td>'
       .'<textarea></textarea>'
       .'<tr></tr>'
       .'<tt></tt>'
);


define ('WIDGET_EMBED_AUTO', 0); // decide wether to use proxy, iframe or include local file
define ('WIDGET_EMBED_PROXY', 1);
define ('WIDGET_EMBED_IFRAME', 2);  // scales to content size
define ('WIDGET_EMBED_LOCAL', 3); // embed a local file
define ('WIDGET_EMBED_JS', 4);
//define ('WIDGET_EMBED_INDEX', 5); // embed ftp with proper index


function
widget_embed_proxy ($src, $form_action = '', $form_method = 'GET', $allow = ALLOW_DEF)
{
  $a = parse_url ($src);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);
/*
  if (!strncasecmp ($src, 'http://', 7))
    {
      $b = array ();
      parse_str ($a['query'], $b);
      $a = array_merge ($b, $_GET);
      $query = http_build_query2 ($a, false);

      $url = $src.($query != '' ? '?'.$query : '');
*/
  if (isset ($a['query']))
    {
      parse_str ($a['query'], $a);
      $b = array_merge ($a, $_GET);
    }
  else
    $b = $_GET;

  $query = http_build_query2 ($b, false);
/*
    }
  else
    $url = ''.$a['path'];
*/
  $url = $src.($query != '' ? '?'.$query : '');

  // DEBUG
//  echo $url;

  // get source and extract head (JS, CSS, etc.) and body
  $html = file_get_contents ($url);
  $a = split_html_content ($html);
//  $head = ''.$a['head'];
  $body = ''.$a['body'];

/*
  // normalize, remove unwanted tags
  $body = strip_tags ($body, $allow);

  // rewrite form tag (action and method)
  // TODO: repeat
  $t = substr ($body, strpos ($body, '<form '));
  $t = substr ($t, 0, strpos ($t, '>') + 1);
  $s = misc_gettag ($t, array ('action' => $form_action, 'method' => $form_method), false);
  $body = str_ireplace ($t, $s, $body);
*/

/*
  // transform GET links into POST forms if necessary
  if ($form_method == 'POST')
    {
      // TODO: repeat
      $t = substr ($body, strpos ($body, '<a '));
      $t = substr ($t, 0, strpos ($t, '</a>') + 1);

//      $s = a_href_to_post_form ($t);
      $body = str_ireplace ($t, $s, $body);
    }
*/

  // rewrite form with hidden tag containing the complete url
  $p = '';
  $a = array_keys ($b);
  for ($i = 0; isset ($a[$i]); $i++)
    if (in_array ($a[$i], array ('f', 'c'))) // HACK
//      output_add_rewrite_var ($a[$i], $b[$a[$i]]); // not useable
      $p .= '<input type="hidden" name="'.$a[$i].'" value="'.$b[$a[$i]].'">';
//      $p .= '<input type="hidden" name="widget_embed_proxy" value="'.urlencode ($url).'">';
  $body = str_ireplace ('</form>', $p.'</form>', $body);

  // HACK: fix absolute links again                     
//  $body = str_ireplace ($url.'http://', 'http://', $body);

  return $body;
}


function
widget_embed_iframe ($src)
{
  $a = parse_url ($src);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);
  if (isset ($a['query']))
    {
      parse_str ($a['query'], $a);
      $b = array_merge ($a, $_GET);
    }
  else
    $b = $_GET;

  $query = http_build_query2 ($b, false);

// TODO: make pass of queries optional
//  $url = $src.($query != '' ? '?'.$query : '');
  $url = $src;

  // DEBUG
//  echo $url;

  // automatic scale to the content size requires javascript and misc.js
  $p = '';

  $p .= '<a href='.$url.' target="_blank"><img src="images/widget/redirectltr.png" border="0">Open Frame in New Window</a><br>';
  $p .= '<iframe'
//       .' onload="javascript:autoscaleiframe(this);" scrolling="no"' // with js
       .' width="100%"'
       .' height="90%"'
       .' marginheight="0" marginwidth="0" frameborder="0" src="'
       .$url
       .'"></iframe>';
  return $p;
}


function
widget_embed_local ($src)
{
  // use widget_embed_local() for templates
  $p = '';
  if (file_exists ($src))
    {
//    $p .= file_get_contents ($src);
      ob_start ();
      require_once ($src);
      $p .= ob_get_contents ();
      ob_end_clean ();
    } 

  // TODO: strip html, head and body tags
  return $p;
}


function
widget_embed_js ($src)
{
//  $p .= 'document.include = function (url) { document.write (\'<script type="text/javascript" src="\'+url+\'"></scr\'+\'ipt>\'); }';
//$p .= '<script>
//document.include (\''.$src.'\');
//</script>;';

  // prevent browsers from caching the included page by appending a random number
//  url += (url.indexOf (\'?\') > -1 ? \'&\' : \'?\')
//        +'rnd='+Math.random ().toString ().substring (2);

//<script type="text/javascript" src="test.js"></script>
//<div id="test"></div>

  $p = '';

$p .= '
<script type="text/javascript">

//function handler ()
//{
//  if (req.readyState == 4) // complete
//    if (req.status == 200)
//      return req.responseText;
//}


function file_get_contents (url)
{
  var req = false;

  if (window.XMLHttpRequest)
    {
      try
        {
          req = new XMLHttpRequest ();
        }
      catch (e)
        {
          req = false;
        }
    }
  else if (window.ActiveXObject) // IE
    {
      try
        {
          req = new ActiveXObject (\'MSXML2.XMLHTTP\');
//          req = new ActiveXObject (\'MSXML2.XMLHTTP.3.0\');
        }
      catch (e)
        {
          if (document.all) // IE: create an ActiveX Object instance
            {
              try
                {
                  req = new ActiveXObject (\'Microsoft.XMLHTTP\');
                }
              catch (e)
                {
                  req = false;
                }
            }
        }
    }

/*
  // If native XMLHTTP has been disabled, developers can override the
  // XMLHttpRequest property of the window object with the MSXML-XMLHTTP control,
  // unless ActiveX has also been disabled, as in the following example.
  if (!window.XMLHttpRequest)
    {
      window.XMLHttpRequest = function ()
        {
          try
            {
              req = new ActiveXObject (\'MSXML2.XMLHTTP.3.0\');
            }
          catch (e)
            {
              req = false;
            }
        }
    }
*/

  if (req)
    {
      // synchronous request, wait till we have it all
      req.open (\'GET\', url, false);
      // asynchronous request
//      req.open (\'GET\', url, true);
//      req.onreadystatechange = handler;
      req.send ();
      return req.responseText;
    }
}


function test_main ()
{
  var e = document.getElementById (\'test\');
  e.innerHTML = file_get_contents (\''.$src.'\');
}


window.onload = function () {
//  setInterval (\'test_main()\', 2000);
  test_main ();
}
</script>

';

  return $p;
}


function
widget_embed ($src, $flags = 0)
{
  $p = '';
  if ($flags == 0 || $flags == WIDGET_EMBED_AUTO)
    {
      if (!strncasecmp ($src, 'http://', 7))
        {
//          $p .= widget_embed_proxy ($src);
          $p .= widget_embed_iframe ($src);
        }
      else
        $p .= widget_embed_local ($src);

      return $p;
    }
  else if ($flags == WIDGET_EMBED_PROXY)
    $p .= widget_embed_proxy ($src);
  else if ($flags == WIDGET_EMBED_IFRAME)
    $p .= widget_embed_iframe ($src);
  else if ($flags == WIDGET_EMBED_LOCAL)   
    $p .= widget_embed_local ($src);
//  else if ($flags == WIDGET_EMBED_JS)
//    $p .= widget_embed_local_js ($src);

  return $p;
}



}

?>