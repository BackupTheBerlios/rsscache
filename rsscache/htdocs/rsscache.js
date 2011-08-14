//include ("misc/misc.js");


var server_xml = null;
var map_xml = null;
var online = 0;
var gs_type="3"
var gs_quality="0";


function get_server_xml ()
{
  xml = "gsembed.php?server="
       +gs_server
       +"&game="
       +gs_game
       +"&type=6";

  root = xml_parse (xml);
  if (!root)
    return;

  var n = root.getElementsByTagName ('root');
  if (n.length > 0)
    server_xml = n[0];

  p = xml_get_string (server_xml.getElementsByTagName ("online"));
  if (!strcmp (p, "0"))
    online = 1;
}


function get_map_xml ()
{
  game = xml_get_string (server_xml.getElementsByTagName ("prot"));
  mapname = xml_get_string (server_xml.getElementsByTagName ("mapname"));

  xml = "xml_"
       +game
       +"/"
       +mapname
       +".xml";

  root = xml_parse (xml);
  if (!root)
    return;
 
  var n = root.getElementsByTagName ('map');
  if (n.length > 0)
    map_xml = n[0];
}


function gsembed_server (id)
{
  p = xml_get_string (server_xml.getElementsByTagName ("hostname")).substr (0, 50)
     +"<br>Address: <span style=\"color:#ff0;\">"
     +xml_get_string (server_xml.getElementsByTagName ("address"))
     +":"
     +xml_get_string (server_xml.getElementsByTagName ("port"))
     +"</span>"
//     +"<button onClick=\"ClipBoard();\">Copy to Clipboard</button>"

     +"<br>Game: "
     +xml_get_string (server_xml.getElementsByTagName ("game"))
     +"<br>Mod: "
     +xml_get_string (server_xml.getElementsByTagName ("mod"))

     +"<br>Players: <span style=\"color:#ff0;\">"
     +xml_get_string (server_xml.getElementsByTagName ("numplayers"))
     +"</span>/"
     +xml_get_string (server_xml.getElementsByTagName ("maxplayers"))
;

  if (xml_get_string (server_xml.getElementsByTagName ("numplayers")) > 0)
    {
      n = server_xml.getElementsByTagName ("players");
      n = n[0].getElementsByTagName ("data");
      p += "<table><tr><td class=\"border\"></td><td class=\"border\">Score</td><td class=\"border\">Ping</td></tr>";
      for (i = 0; n[i]; i++)
        {
          ping = xml_get_string (n[i].getElementsByTagName ("ping"));
          p += "<tr><td class=\"border\">"
              +xml_get_string (n[i].getElementsByTagName ("name"))
              +"</td><td class=\"border\">"
              +xml_get_string (n[i].getElementsByTagName ("score"))
              +"</td><td class=\"border\" style=\"color:"
              +(ping > 200 ? "#f0f" : (ping > 100 ? "#f00" : (ping > 50 ? "#ff0" : (ping > 35 ? "#0f0" : "#fff"))))
              +";\">"
              +ping
              +"</td></tr>";
        }
      p += "</table>";
    }
  p += "<br>";

  p += "Misc:<br><textarea cols=40 rows=5 readonly>"
      +strrep (xml_get_string (server_xml.getElementsByTagName ("misc")), ",", "\n")
      +"</textarea>"
;

  return p;
}


function gsembed_map (id)
{

  p = "";

if (map_xml != null)
  p += "<table><tr><td>";

  thumbnail = xml_get_string (server_xml.getElementsByTagName ("thumbnail"));
  if (thumbnail)
    p += "<img src=\""
        +thumbnail
        +"\" border=\"0\">";

if (map_xml != null)
  {
  p += ""
      +"</td><td class=\"border\">"
      +"<br>"
      +"PK3 file: "
      +xml_get_string (map_xml.getElementsByTagName ("pk3_file"))
      +"<br>BSP name: "
      +xml_get_string (map_xml.getElementsByTagName ("bsp_file"))
      +"<br>BSP size: "
      +xml_get_string (map_xml.getElementsByTagName ("bsp_size"))
      +"<br>BSP CRC32: "
      +xml_get_string (map_xml.getElementsByTagName ("bsp_crc32"))
      +"<br>BSP Date: ";

  bsp_date = xml_get_string (map_xml.getElementsByTagName ("bsp_date"));
  if (bsp_date)
    {
      t = new Date();
      t.setTime (bsp_date * 1000);
      p += t.toDateString();
    }
  else
    p += "?";

  p += "<br>Entities:<br>";
  n = map_xml.getElementsByTagName ("entities");
  n = n[0].getElementsByTagName ("entity");
  for (i = 0; n[i]; i++)
    {
      n2 = n[i].getElementsByTagName ("name");
      p += "<img src=\"gsdata/icons_"
          +xml_get_string (server_xml.getElementsByTagName ("game"))
          +"/"
          +n2[0].firstChild.nodeValue
          +".png\" border=\"0\" alt=\""
          +n2[0].firstChild.nodeValue
          +"\" title=\""
          +n2[0].firstChild.nodeValue
          +"\">"
;
      n2 = n[i].getElementsByTagName ("value");  
      p += "x" + n2[0].firstChild.nodeValue;

      if (!((i + 1) % 4))
        p += "<br>";
    }
  p += "</td></tr></table>";


  p += "<br>Textures and Shaders:<br><textarea cols=\"40\" rows=\"5\" readonly>"
  n = map_xml.getElementsByTagName ("textures");
  n = n[0].getElementsByTagName ("texture");
  for (i = 0; n[i]; i++)
    {
      p += n[i].firstChild.nodeValue + "\n";
    }
  p += "</textarea>";
}
  return p;
}


/*
function gsembed_player (id)
{
  p = "player";

  return p;
}


function gsembed_search (id)
{
  p = ""
     +"<form>"
     +"Search: <input type=\"text\" checked=\"checked\"></input>"
     +"<button>Search</button><br>"
     +"Games <input type=\"checkbox\" checked=\"checked\"></input>"
     +"Servers <input type=\"checkbox\" checked=\"checked\"></input>"
     +"Players <input type=\"checkbox\" checked=\"checked\"></input>"
     +"Maps <input type=\"checkbox\" checked=\"checked\"></input>"
     +"</form>";

  return p;
}
*/


function toggle (id)
{
  element = document.getElementById (id);

  element.className = (element.className == "hide" ? "show" : "hide");
}


    get_server_xml ();
    get_map_xml ();

    p = ""

//       +"<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gsembed.css\">"
       +"<style type=\"text/css\">"
       +"div.hide { display: none; }\n"
       +"div.show { display: block; }"
       +"</style>"

       +"<table><tr><td>"

       +"<a href=\"#\" onclick=\"toggle('"
       +gs_id
       +"_map')\">Map</a> "

       +"</td></tr>"

       +"<tr><td valign=\"top\" width=\"150\">"

       +"<div class=\"show\" id=\""
       +gs_id
       +"_server\">"
       +gsembed_server (gs_id)
       +"</div>"

       +"</td><td valign=\"top\" width=\"150\">"

       +"<div class=\"show\" id=\""
       +gs_id
       +"_map\">"
       +gsembed_map (gs_id)
       +"</div>"

       +"</td></tr></table>"
; 

    document.write (p);
<?php

function
gsembed_js ($server, $game)
{
header ('Content-type: text/javascript');

echo 'gs_id="0";'."\n"
    .'gs_server="'.$server.'";'."\n"
    .'gs_game="'.$game.'";'."\n";

ob_start();

include ('misc/misc.js');
include ('gsembed.js');

$p = ob_get_contents();
ob_end_clean();


echo $p;
}


?>
