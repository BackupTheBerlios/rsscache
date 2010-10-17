<?php
//require_once ('widget.php');
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


function
widget_embed_proxy_func ($url, $form_action = '', $form_method = 'GET', $allow = ALLOW_DEF)
{
  $html = '';

  $html = file_get_contents ($url);
  // extract head (JS, CSS, etc.) and body
  $a = split_html_content ($html);
  $html = ''.$a['body'];

  // normalize, remove unwanted tags
  $html = strip_tags ($html, $allow);

  // rewrite form action and method
  // TODO: repeat
  $html_tag = substr ($html, strpos ($html, '<form '));
  $html_tag = substr ($html_tag, 0, strpos ($html_tag, '>') + 1);
  $p = misc_gettag ($html_tag, array ('action' => $form_action, 'method' => $form_method), false);
  $html = str_ireplace ($html_tag, $p, $html);

  if ($form_method == 'POST')
    {
      // replace GET links with POST forms if necessary
      // TODO: repeat
      $html_tag = substr ($html, strpos ($html, '<a '));
      $html_tag = substr ($html_tag, 0, strpos ($html_tag, '</a>') + 1);

      // TODO: transform a link into a button in a post form
//      $p = a_href_to_post_form ($html_tag);
      $html = str_ireplace ($html_tag, $p, $html);
    }

  // HACK: fix absolute links again                     
//  $html = str_ireplace ($url.'http://', 'http://', $html);

  return $html;
}


function
widget_embed_proxy ($src)
{
  $a = parse_url ($src);
  parse_str ($a['query'], $a);
  $url = $src.misc_getlink (array_merge ($a, $_GET), false);
//echo $src;
  $html = file_get_contents ($src.misc_getlink (array_merge ($a, $_GET), false));
  $a = split_html_content ($html);
  $p = $a['body'];
  $p = str_ireplace ('</form>', '<input type="hidden" name="widget_embed_proxy" value="'.$src.'"></form>', $p);
  return $p;
//  return widget_embed_proxy_func ($src);
}


function
widget_embed_iframe ($src)
{
  // automatic scale to the content size requires javascript and misc.js
  $p = '';
  $p .= '<iframe'
//       .' onload="javascript:autoscaleiframe(this);" scrolling="no"' // with js
       .' width="100%" height="90%"' // without js
       .' marginheight="0" marginwidth="0" frameborder="0" src="'
       .$src
       .'"></iframe>';
  return $p;
}


function
widget_embed_local ($src)
{
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
  $p = '';
/*
  $p .= '<script type="text/javascript">
';

//  $p .= 'document.include = function (url) { document.write (\'<script type="text/javascript" src="\'+url+\'"></scr\'+\'ipt>\'); }';

  $p .= '// new prototype defintion
document.include = function (url)
{
  if (typeof (url) == \'undefined\')
    return false;

  var p, rnd;
  if (document.all) // IE: create an ActiveX Object instance
    p = new ActiveXObject(\'Microsoft.XMLHTTP\');
  else // mozilla: create an instance of XMLHttpRequest
    p = new XMLHttpRequest ();

  // prevent browsers from caching the included page by appending a random number
  rnd = Math.random ().toString ().substring (2);
  url = url.indexOf (\'?\') > -1 ? url+\'&rnd=\'+rnd : url+\'?rnd=\'+rnd;

  // open the url and write out the response
  p.open (\'GET\', url, false);
  p.send (null);

  document.write (p.responseText);
}
</script>
<script>
document.include (\''.$url.'\');
</script>';
*/
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



?>