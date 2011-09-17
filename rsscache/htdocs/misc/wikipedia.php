<?php
/*
wikipedia.php - wrapper for wikipedia API 

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
if (!defined ('MISC_WIKIPEDIA_PHP'))
{
//phpinfo ();
define ('MISC_WIKIPEDIA_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('misc.php');
require_once ('embed.php');


function
wikipedia_get_xml ($q)
{
  $q = urlencode ($q);
  $url = ''
        .'http://en.wikipedia.org/w/api.php?'
        .'action=query'
        .'&format=xml'
        .'&titles='.$q
        .'&rvprop=content'
        .'&prop=revisions'
        .'&redirects=redirects'
;
//  $url = 'http://en.wikipedia.org/w/api.php?action=opensearch&search='.$q.'&limit=1&format=xml';
//Category:Films
  $ua = ini_get ('user_agent'); 
  ini_set ('user_agent', random_user_agent ()); // yawn  
  $f = file_get_contents ($url);
  ini_set ('user_agent', $ua);
  $xml = simplexml_load_string ($f, 'SimpleXMLElement', LIBXML_NOCDATA);
// DEBUG
//echo '<pre><tt>';
//print_r ($xml->query->pages->page->revisions->rev);
  return $xml;
}


function
wikipedia_get_html ($q)
{
  $xml = wikipedia_get_xml ($q);
  $ua = ini_get ('user_agent');
  ini_set ('user_agent', random_user_agent ()); // yawn  
  $p = file_post_contents (
         'http://en.wikipedia.org/w/api.php',
         array ('action' => 'parse',
                'format' => 'xml',
                'text' => $xml->query->pages->page->revisions->rev));
  ini_set ('user_agent', $ua);
  $xml = simplexml_load_string ($p, 'SimpleXMLElement', LIBXML_NOCDATA);

// DEBUG  
//echo '<pre><tt>';
//print_r (htmlentities ($xml->parse->text));

  $t = $xml->parse->text;
  $s = '';
  // remove contents
  $s .= substr ($t, 0, strpos ($t, '<table id="toc" class="toc">'));
  $s .= substr ($t, strpos ($t, '</table>') + 8);
  // fix links
  $t = str_replace ('<a href="/wiki/', '<a href="http://en.wikipedia.org/wiki/', $s);

  return $t;
}


} 
?>