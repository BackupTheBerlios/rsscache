/*
misc.js - miscellaneous javascript functions

Copyright (c) 2006-2010 NoisyB


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


function misc_getwh ()
{
  var w = screen.width;
  var h = screen.height;
  if (self.innerWidth != undefined)
    {
      w = self.innerWidth;
      h = self.innerHeight;
    }
  else
    {
      var d = document.documentElement;
      if (d)
        {
          w = d.clientWidth;
          h = d.clientHeight;
        } 
    }
  return [w, h];
}


function
misc_toggle (id)
{
//<a href="javascript:void(0);" onclick="toggle('toggle1');">Toggle</a>
//<div style="display:none;" id="toggle1">
//Hello World!
//<a href="javascript:void(0);" onclick="toggle('toggle1');">Close</a>
//</div>
  e = document.getElementById (id);
  e.style.display = (e.style.display == 'none' ? 'block' : 'none');
}


//function defined (constant_name)
//{
//    return (typeof window[constant_name] !== 'undefined');
//}


//function function_exists (function_name)
//{
//  if (typeof function_name == 'string')
//    return (typeof window[function_name] == 'function');
//  return (function_name instanceof Function);
//}


//function misc_count (mixed_var, mode)
//{
//  var key, cnt = 0;
// 
//  if (mode == 'COUNT_RECURSIVE') mode = 1;
//  if (mode != 1) mode = 0;
// 
//  for (key in mixed_var)
//    {
//      cnt++;
//      if(mode == 1 && mixed_var[key] &&
//         (mixed_var[key].constructor === Array || mixed_var[key].constructor === Object))
//        cnt += misc_count (mixed_var[key], 1);
//    }
//  return cnt;
//}


//function misc_sizeof (mixed_var, mode)
//{
//  return count (mixed_var, mode);
//}


// string functions

function strrep (s, search, rep)
{
  while (s.indexOf (search) > -1)
    s = s.replace (search, rep);
  return s;
}


function strcmp (s1, s2)
{
  if (s1 == s2)
    return 0;
  if (s1 > s2)
    return 1;
  return -1;
}


function strstr (s, search, bool)
{
  var pos = s.indexOf (search);

  if (pos == -1)
    return false;

  if (bool)
    return s.substr (0, pos);

  return s.slice (pos);
}


function get_suffix (filename)
{
  return '.'+filename.split('.').pop();
}


function set_suffix (filename, suffix)
{
  s = get_suffix (filename)
  return filename.replace (s, suffix);
}


//function dynamic_include_js (filename)
//{
//  document.write ('<script type="text/javascript" src="'+filename+'"></scr' + 'ipt>');
//}


function dynamic_include (filename)
{
  var suffix = get_suffix (filename);
  var head = document.getElementsByTagName ('head')[0];
  if (suffix == '.js')
    {
      var e = document.createElement ('script');
      e.setAttribute ('type', 'text/javascript');
      e.setAttribute ('src', filename);
      head.appendChild (e);
    }
  else if (suffix == '.css')
    {
      var e = document.createElement ('link');
      e.setAttribute ('rel', 'stylesheet');
      e.setAttribute ('type', 'text/css');
      e.setAttribute ('href', filename);
      head.appendChild (e);
    }
}


function place_element_xy (id, x, y)
{
  var e = document.getElementById (id);
//  var z = 999;
//  if (document.all)
//    {
//      e.style.pixelLeft = x;
//      e.style.pixelTop = y;
//    }
//  else
//    {
      e.style.position = 'absolute';
      e.style.left = x + 'px';
      e.style.top = y + 'px';
//    }
//  e.style.z-index = z;
}


// event handling

function event_getmouse (e)
{
  // return array with X and Y mouse position
  if (!e)
    e = window.event;
  return document.all ? [e.x, e.y] : [e.pageX /* + document.body.scrollLeft */, e.pageY /* + document.body.scrollTop */]; 
}


function event_debug (e)
{
//<script type="text/javascript" src="misc.js"></script>
//<script type="text/javascript"><!--
//window.onload = function ()
//{
//  place_element_xy ('test1', 100, 400);
//  event_set_callback (event_debug);
//}
//// -->
//</script>
//<textarea id="test" cols=80 rows=20></textarea>
//<div id="test1">test</div>

//  if (document.all)
//    {
//      e = window.event;
//      key = e.keyCode;
//    }
//  else
//    if (document.layers)
//      key = e.which;

  if (!e)
    e = window.event;

  var key = e.keyCode;

  var p = "Event: "
         +e.type
         +"\nkeyCode: "
         +key
         +"\nWhich: "
         +e.which

         +"\nalt: "
         +e.altKey
         +"\nctrl: "
         +e.ctrlKey
         +"\nshift: "
         +e.shiftKey

         +"\nrepeat: "
         +e.repeat

         +"\nMouse"
         +"\nButton: "
         +e.button
         +"\nX: "
         +(document.all ? e.x : e.pageX)
         +"\nY: "
         +(document.all ? e.y : e.pageY)
;

  // DEBUG
  document.getElementById('test').value = p;
  var xy = event_getmouse (e);
  place_element_xy ('test1', xy[0], xy[1]);
}


function event_set_callback (func)
{ 
/*
  events

  onabort      Loading of image is interrupted
  onblur       element loses focus
  onchange     user changes content of field
  onclick      Mouse clicks object
  ondblclick   Mouse double-clicks object
  onerror      error occurs when loading document or image
  onfocus      element gets focus
  onkeydown    key is pressed
  onkeypress   key is pressed or held down
  onkeyup      key is released
  onload       page or image finished loading
  onmousedown  mouse button pressed
  onmousemove  mouse is moved
  onmouseout   mouse is moved off an element
  onmouseover  mouse is moved over element
  onmouseup    mouse button released
  onreset      reset button clicked
  onresize     window or frame is resized
  onselect     text is selected
  onsubmit     submit button clicked
  onunload     user exits page
*/
  document.onkeypress = func; 
  document.onkeydown = func;
  document.onkeyup = func;
  document.onmousedown = func;
  document.onmouseup = func; 
  document.onmousemove = func;
}


// XML functions

function simplexml_load_file (url)
{
//  var req = new XMLHttpRequest();  
//  req.open("GET", "chrome://yourextension/content/peopleDB.xml", false);   
//  req.send(null);  
//  var xmlDoc = req.responseXML;         
//  var nsResolver = xmlDoc.createNSResolver( xmlDoc.ownerDocument == null ? xmlDoc.documentElement : xmlDoc.ownerDocument.documentElement);  
//  var personIterator = xmlDoc.evaluate('//person', xmlDoc, nsResolver, XPathResult.ANY_TYPE, null );  
  try
    {
      if (document.all) // IE
//        var xml = new ActiveXObject('Microsoft.XMLDOM')
        var xml = new ActiveXObject('Microsoft.XMLHTTP')
      else
        var xml = document.implementation.createDocument('', '', null);
//        var xml = new XMLHttpRequest ();
      if (xml)
        {
          xml.async = false;
          xml.load (url);
//          xml.open ('GET', url, false);
          return xml;
        }
      return null;
    }
  catch(e) {}

  return null;
}


function xml_xpath (xml, xpath)
{
  // https://developer.mozilla.org/en/DOM/document.evaluate
  // http://www.w3schools.com/XPath/default.asp

  if (document.all) // IE
    {
      xml.setProperty ('SelectionLanguage', 'XPath'); // fix [0] vs. [1]
      o = xml.selectNodes (xpath);
    }
  else
    o = xml.evaluate (xpath, xml, null, XPathResult.ANY_TYPE, null);
//    o = xml.evaluate (xpath, xml, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);

  var s = '';
  var h;
//  h = o[1];
//  h = o.iterateNext (); // skip [0]
  while ((h = o.iterateNext ()) != null)
//  while ((h = o.snapshotItem ()) != null)
    s += h.textContent;
  return s;
}


/*
function xml_get_node (name, node)
{
  if (node)
    var n = items[node].getElementsByTagName (name);
  else
    var n = xmlDoc.getElementsByTagName (name);
  if (n.length > 0)
    return n[0].firstChild.nodeValue;
  return null;
}


function xml_get_name (node)
{
  if (node)
    if (node.length > 0)
      if (node[0].firstChild)
        if (node[0].firstChild.nodeName)
          return node[0].firstChild.nodeName;
  return null;
}


function xml_get_value (node)
{
  if (node)
    if (node.length > 0) 
      if (node[0].firstChild)
        if (node[0].firstChild.nodeValue)
          return node[0].firstChild.nodeValue;
  return null;
}


function xml_get_string (node)
{
  return xml_get_value (node);
}
*/


// embedding

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
          req = new ActiveXObject ('Msxml2.XMLHTTP');
        }
      catch (e)
        {
          try
            {
              req = new ActiveXObject ('Microsoft.XMLHTTP');
            }
          catch (e)
            {
              req = false;
            }
        }
    }

  if (req)
    {
      // synchronous request, wait till we have it all
      req.open ('GET', url, false);
      req.send (null);
      return req.responseText;
    }
}


function autoscaleiframe (f)
{
// scales iframe to the size of its content
// RESTRICTIONS: iframe content must be from the *same* domain as this file
// tested with FF and IE8
//<iframe onload="javascript:autoscaleiframe(this);" src="test40_.html" frameborder="0" marginwidth="0" marginheight="0 scrolling="no"></iframe>

//var d = f.contentDocument;
var w = f.contentWindow;
var width = 
        w.document.body.scrollLeft ||
        w.document.body.scrollWidth
//        d.body.scrollLeft ||
//        d.body.scrollWidth
;
var height =
        w.document.body.scrollTop ||
        w.document.body.scrollHeight
//        d.body.scrollTop ||
//        d.body.scrollHeight
;
//f.style.width = parseInt (width) + 10 + 'px';
//f.style.height = parseInt (height) + 10 + 'px';
f.style.width = (width * 1) + 10 + 'px';
f.style.height = (height * 1) + 10 + 'px';
}  


function embed_iframe (id, url)
{
  var e = document.getElementById (id);
  e.innerHTML = '<iframe onload="javascript:autoscaleiframe(this);" src="'+url
               +'" frameborder="0" marginwidth="0" marginheight="0 scrolling="no"></iframe>';
}


