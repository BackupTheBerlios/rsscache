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


function is_iframe ()
{
  if (top === self)
    return 0;
  return 1;
}


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


function detect_flash ()
{
//  var browser = navigator.userAgent.toLowerCase();
  v = 0;	

  if (navigator.plugins != null &&
      navigator.plugins.length > 0)
    {
      var p = navigator.plugins['Shockwave Flash'];
      if ( typeof p == 'object' )
        {
          if ( p.description.indexOf('11.') != -1 ) v = 11;
          else if ( p.description.indexOf('10.') != -1 ) v = 10;
          else if ( p.description.indexOf('9.') != -1 ) v = 9;
          else if ( p.description.indexOf('8.') != -1 ) v = 8;
          else if ( p.description.indexOf('7.') != -1 ) v = 7;
          else if ( p.description.indexOf('6.') != -1 ) v = 6;
          else if ( p.description.indexOf('5.') != -1 ) v = 5;
          else if ( p.description.indexOf('4.') != -1 ) v = 4;
          else if ( p.description.indexOf('3.') != -1 ) v = 3;
        }
    }
/*
  else if (browser.indexOf ("msie") != -1 &&
           parseInt (navigator.appVersion) >= 4 &&
           browser.indexOf("win")!= -1 &&
           browser.indexOf("16bit")== -1)
    {
      document.write('<scr' + 'ipt language="VBScript"\> \n');
      document.write('on error resume next \n');
      document.write('DIM obFlash \n');
      document.write('SET obFlash = CreateObject("ShockwaveFlash.ShockwaveFlash.7") \n');
      document.write('IF IsObject(obFlash) THEN \n');
      document.write('v = 7 \n');

      document.write('ELSE SET obFlash = CreateObject("ShockwaveFlash.ShockwaveFlash.6") END IF \n');
      document.write('IF v < 7 and IsObject(obFlash) THEN \n');
      document.write('v = 6 \n');
      document.write('ELSE SET obFlash = CreateObject("ShockwaveFlash.ShockwaveFlash.5") END IF \n');
      document.write('IF v < 6 and IsObject(obFlash) THEN \n');
      document.write('v = 5 \n');
      document.write('ELSE SET obFlash = CreateObject("ShockwaveFlash.ShockwaveFlash.4") END IF \n');
      document.write('IF v < 5 and IsObject(obFlash) THEN \n');
      document.write('v = 4 \n');
      document.write('ELSE SET obFlash = CreateObject("ShockwaveFlash.ShockwaveFlash.3") END IF \n');
      document.write('IF v < 4 and IsObject(obFlash) THEN \n');
      document.write('v = 3 \n');

      document.write('END IF');
      document.write('</scr' + 'ipt\> \n');
    } // no Flash
*/
  else
    {
      v = -1;
    }

  return v;
}

/*
function misc_window_open (url, fullscreen, window_name)
{
// https://developer.mozilla.org/en/Gecko_DOM_Reference
// https://developer.mozilla.org/en/DOM/window.open
  var w = screen.width;
  var h = screen.height;
//  var win=
  if (fullscreen)
    window.open (url, window_name,
      'top=0'
     +',left=0'
//     +',width='+w
//     +',height='+h
     +',fullscreen'
//     +',menubars'
     +',status=0'
//     +',toolbar'
     +',location=0'
//     +',menubar=no'
//     +',directories=no'
//     +',resizable=no'
//     +',scrollbars=no'
//     +',copyhistory'
).focus ();
  else
    window.open (url, window_name,
      'width=400'
     +',height=300'
     +',status=no'
     +',toolbar=no'
     +',location=no'
     +',menubar=no'
     +',directories=no'
     +',resizable=yes'
     +',scrollbars=yes'
     +',copyhistory=yes'
).focus();

//window.opener = top; // this will close opener in ie only (not Firefox)

if (fullscreen)
  window.moveTo (0, 0);
  
// changing bar states on the existing window
//netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserWrite");
window.locationbar.visible = 0;
window.statusbar.visible = 0;

if (document.all)
  window.resizeTo (screen.width, screen.height);
else if (document.layers || document.getElementById)
  if (window.outerHeight < screen.height || window.outerWidth < screen.width)
    {
      window.outerHeight = screen.height;
      window.outerWidth = screen.width;
    }
}
*/


/*
  hr line modification
    var Dicke = 3;
    function dicker ()
      {
        Dicke += Dicke;
        document.getElementById("Linie").size = Dicke;
      }
    <hr id="Linie" noshade="noshade" size="3" onclick="dicker()">

  browser detection
    if (document.layers) alert("Netscape 4");
    if (document.all) alert("IE 4/5");
    if (document.styleSheets) alert("Mozilla 1, Netscape 6, IE4, IE5, DOM 1.0");
    if (document.getElementById) alert("Mozilla/NC 6, IE5 ,DOM 1.0");

  set focus on a form tag
    document.<formname>.<widgetname>.focus();

  close active window
    window.close();

  y/n question
    if (confirm (question))
      ...;

  status line
    window.status = status;

  window title
    document.title = title;

  open url
    location.href = url;
    window.location = url;

  open url in frame
    top[<framename>].location.href = url;

  window.open (url, windowname, arg, ...)
    can be used in onclick="new_window ()" or onload="new_window ()" or as url "javascript:new_window ()"

  args
    screenX=pixels      position of the window in pixels from the left of the screen in Netscape 4+
    screenY=pixels      position of the window in pixels from the top of the screen in Netscape 4+
    left=pixels         position of the window in pixels from the left of the screen in IE 4+
    top=pixels          position of the window in pixels from the top of the screen in IE 4+
    width=pixels        defines the width of the new window.
    height=pixels       defines the height of the new window.
    fullscreen=yes/no   whether or not the window should have fullscreen size
    resizable=yes/no    whether or not you want the user to be able to resize the window.
    scrollbars=yes/no   whether or not to have scrollbars on the window
    toolbar=yes/no      whether or not the new window should have the browser navigation bar at the top
    location=yes/no     whether or not you wish to show the location box with the current url
    directories=yes/no  whether or not the window should show the extra buttons
    status=yes/no       whether or not to show the window status bar at the bottom of the window
    menubar=yes/no      whether or not to show the menus at the top of the window
    copyhistory=yes/no  whether or not to copy the old browser window's history list to the new window

  Width of the document
    document.width

  Height of the document
    document.height

  Width of window
    self.innerWidth;  // ns4
    window.innerWidth - 5;  // ns6
    document.body.clientWidth; // ie

  Height of window
    self.innerHeight;  // ns4
    window.innerHeight - 5;  // ns6
    document.body.clientHeight; // ie

  Popup text at fixed pos
    <div id="text" name="text" style="position:absolute; left:166px; top:527px; width:665px; height:94px; z-index:1"></div>
    function output (s)
      {
        obj = eval("text");
        obj.innerHTML = s;
      }
    <... onMouseOver="output('hello')">

  disables right click menu
    oncontextmenu="return false;"

  resize images
    <img src=... name="image_xyz">
    image_xyz.width = w;
    image_xyz.height = h;

  play sound (onMouseOver)
    <a href="#" onMouseOver="document.all.msound.src='sound.aif'">...</a>

  delay
    function doSomething() {}
    [window.]setTimeout("doSomething();", delay);
    doSomething(); // leave here to run right away

  encode text for use in a url
    encodeURIComponent(text)

  bookmark
    function bookmark ()
      {
        if (document.all)
          window.external.AddFavorite (url, title);
      }
    <a href="javascript:bookmark()">...</a>

  screen width and height
    screen.width
    screen.height

  changes the image size and source on your thumbnail picture
    <img src="yourimage.jpg" width="150" height="200"
     onclick="this.src='yourimage.jpg';this.height=400;this.width=300"
     ondblclick="this.src=yourimage.jpg';this.height=200;this.width=150">

  use this to change the background color when user places mouse over the link.
    <a href="link.htm" onMouseOver="document.body.background='red.gif'">Link Text</a>

  use this to change the background image when user removes mouse from over the link (onMouseOut).
    <a href="link.htm" onMouseOut="document.body.background='green.gif'">Link Text</a>

  use this to change the background image when user clicks the link (onClick).
    <a href="link.htm" onClick="document.body.background='blue.gif'">Link Text</a>

  use this to change the background image twice, once when the user places the
  mouse over the link then again when the user takes the mouse off the link
  (onMouseOver and onMouseOut).
    <a href="link.htm" onMouseOver="document.body.background='red.gif'" onMouseOut="document.body.background='green.gif'">Link Text</a>

  use this to change the background image three times, once when the user
  places the mouse over the link, again when the user takes the mouse off the
  link, and again when the user clicks on the link (onMouseOver, onMouseOut,
  and onClick).
    <a href="link.htm" onMouseOver="document.body.background='red.gif'" onMouseOut="document.body.background='green.gif'" onClick="document.body.background='blue.gif'">Link Text</a>

function include (jsFile) {document.write('<script type="text/javascript" src="'+ jsFile + '"></scr' + 'ipt>');} 


Accessing Values

Having read the Objects and Properties page, you should now know how to find
out the values of form elements through the DOM. We're going to be using the
name notation instead of using numbered indexes to access the elements, so
that you are free to move around the fields on your page without having to
rewrite parts of your script every time. A sample, and simple, form may look
like this:

<form name="feedback" action="script.cgi" method="post" onSubmit="return checkform()">
<input type="text" name="name">
<input type="text" name="email">
<textarea name="comments"></textarea>
</form>

Validating this form would be considerably simpler than one containing radio
buttons or select boxes, but any form element can be accessed. Below are the
ways to get the value from all types of form elements. In all cases, the
form is called feedback and the element is called field. Text Boxes,
<textarea>s and hiddens

These are the easiest elements to access. The code is simply

document.feedback.field.value

You'll usually be checking if this value is empty, i.e.

if (document.feedback.field.value == '') {
	return false;
}

That's checking the value's equality with a null String (two single quotes
with nothing between them). When you are asking a reader for their email
address, you can use a simple » address validation function to make sure the
address has a valid structure. Select Boxes

Select boxes are a little trickier. Each option in a drop-down box is
indexed in the array options[], starting as always with 0. You then get the
value of the element at this index. It's like this:

document.feedback.field.options
[document.feedback.field.selectedIndex].value

You can also change the selected index through JavaScript. To set it to the first option, execute this:

document.feedback.field.selectedIndex = 0;

Check Boxes

Checkboxes behave differently to other elements  their value is always on.
Instead, you have to check if their Boolean checked value is true or, in
this case, false.

if (!document.feedback.field.checked) {
	// box is not checked
	return false;
}

Naturally, to check a box, do this

document.feedback.field.checked = true;

Radio Buttons

Annoyingly, there is no simple way to check which radio button out of a
group is selected you have to check through each element, linked with
Boolean AND operators . Usually you'll just want to check if none of them
have been selected, as in this example:

if (!document.feedback.field[0].checked &&
!document.feedback.field[1].checked &&
!document.feedback.field[2].checked) {
	// no radio button is selected
	return false;
}

You can check a radio button in the same way as a checkbox.
*/
