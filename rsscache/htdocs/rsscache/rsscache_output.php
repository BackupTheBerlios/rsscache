<?php
/*
rsscache_output.php - rsscache engine output functions

Copyright (c) 2009 - 2011 NoisyB 


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
if (!defined ('RSSCACHE_OUTPUT_PHP'))
{
define ('RSSCACHE_OUTPUT_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
//require_once ('misc/misc.php');
//require_once ('misc/rss.php');
//require_once ('misc/sql.php');
//require_once ('misc/youtube.php');
require_once ('rsscache_sql.php');


function
rsscache_write_robots ()
{
  $p .= '';
  $p .= 'Sitemap: http://'.$_SERVER['SERVER_NAME'].'/sitemap.xml'."\n"
       .'User-agent: *'."\n"
       .'Allow: /'."\n";

  return $p;
}


function
rsscache_write_xsl ($p, $s)
{
  global $rsscache_xsl_trans;

  // XSL transformation
  if ($rsscache_xsl_trans == 2) // check user-agent and decide
    {
      // TODO
      $rsscache_xsl_trans = 0;
    }

  if ($rsscache_xsl_trans == 0) // transform on server
    {
      $xsl = file_get_contents ($s);
      $xslt = new XSLTProcessor (); 
      $xslt->importStylesheet (new SimpleXMLElement ($xsl));
      $p = $xslt->transformToXml (new SimpleXMLElement ($p));
    }
  else if ($rsscache_xsl_trans == 1) // transform on client
    {
return $p;

$p = '<html>
<head>
<script>
function loadXMLDoc(dname)
{
if (window.XMLHttpRequest)
  {
  xhttp=new XMLHttpRequest();
  }
else
  {
  xhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xhttp.open("GET",dname,false);
xhttp.send("");
return xhttp.responseXML;
}

function displayResult()
{
xml=loadXMLDoc("?f=stats");
xsl=loadXMLDoc("'.$s.'");
// code for IE
//if (window.ActiveXObject)
//  {
//  ex=xml.transformNode(xsl);
//  document.getElementById("example").innerHTML=ex;
//  }
// code for Mozilla, Firefox, Opera, etc.
//else if (document.implementation && document.implementation.createDocument)
  {
  xsltProcessor=new XSLTProcessor();
  xsltProcessor.importStylesheet(xsl);
  resultDocument = xsltProcessor.transformToFragment(xml,document);
  document.getElementById("example").appendChild(resultDocument);
  }
}
</script>
</head>
<body onload="displayResult()">
<div id="example" />
</body>
</html>';
    }

  return $p;
}


function
rsstool_write_ansisql ($a, $rsscache_category, $table_suffix = NULL, $db_conn = NULL)
{
  $sql_update = 0;
  $rsscache_engine = 1;
  $p = '';

  $rsstool_table = rsscache_tablename ('rsstool', $table_suffix);
  $keyword_table = rsscache_tablename ('keyword', $table_suffix);

  $p .= '-- -----------------------------------------------------------'."\n"
       .'-- RSStool - read, parse, merge and write RSS and Atom feeds'."\n"
       .'-- -----------------------------------------------------------'."\n"
       ."\n"
       .'-- DROP TABLE IF EXISTS '.$rsstool_table.';'."\n"
       .'-- CREATE TABLE '.$rsstool_table.' ('."\n"
//       .'--   rsstool_url_md5 varchar(32) NOT NULL default \'\','."\n"
       .'--   rsstool_url_crc32 int(10) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_site text NOT NULL,'."\n"
       .'--   rsstool_dl_url text NOT NULL,'."\n"
//       .'--   rsstool_dl_url_md5 varchar(32) NOT NULL default \'\','."\n"
       .'--   rsstool_dl_url_crc32 int(10) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_title text NOT NULL,'."\n"
//       .'--   rsstool_title_md5 varchar(32) NOT NULL default \'\','."\n"
       .'--   rsstool_title_crc32 int(10) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_desc text NOT NULL,'."\n"
       .'--   rsstool_date bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_dl_date bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_keywords text NOT NULL,'."\n"
       .'--   rsstool_media_duration bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_image text NOT NULL,'."\n"
       .'--   rsstool_event_start bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_event_end bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   UNIQUE KEY rsstool_url_crc32 (rsstool_url_crc32),'."\n"
//       .'--   UNIQUE KEY rsstool_url_md5 (rsstool_url_md5),'."\n"
//       .'--   UNIQUE KEY rsstool_title_crc32 (rsstool_title_crc32),'."\n"
//       .'--   UNIQUE KEY rsstool_title_md5 (rsstool_title_md5),'."\n"
//       .'--   FULLTEXT KEY rsstool_title (rsstool_title),'."\n"
//       .'--   FULLTEXT KEY rsstool_desc (rsstool_desc)'."\n"
       .'-- ) TYPE=MyISAM;'."\n"
       ."\n";

  $p .= ''
       .'-- DROP TABLE IF EXISTS '.$rsstool_table.';'."\n"
       .'-- CREATE TABLE IF NOT EXISTS '.$keyword_table.' ('."\n"
//       .'--   rsstool_url_md5 varchar(32) NOT NULL,'."\n"
       .'--   rsstool_url_crc32 int(10) unsigned NOT NULL,'."\n"
//       .'--   rsstool_keyword_crc32 int(10) unsigned NOT NULL,'."\n"
//       .'--   rsstool_keyword_crc24 int(10) unsigned NOT NULL,'."\n"
       .'--   rsstool_keyword_crc16 smallint(5) unsigned NOT NULL,'."\n"
       .'--   PRIMARY KEY (rsstool_url_crc32,rsstool_keyword_crc16),'."\n"
//       .'--   KEY rsstool_keyword_24bit (rsstool_keyword_crc24),'."\n"
       .'--   KEY rsstool_keyword_16bit (rsstool_keyword_crc16)'."\n"
       .'-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'."\n"
       ."\n";

  $items = count ($a['item']);
  for ($i = 0; $i < $items; $i++)
    if ($a['item'][$i]['link'] != '')
    {
      // rsstool_table
      $p .= 'INSERT IGNORE INTO '.$rsstool_table.' ('
           .' rsstool_dl_url,'
//           .' rsstool_dl_url_md5,'
           .' rsstool_dl_url_crc32,'
           .' rsstool_dl_date,'
           .' rsstool_site,'
           .' rsstool_url,'
//           .' rsstool_url_md5,'
           .' rsstool_url_crc32,'
           .' rsstool_date,'
           .' rsstool_title,'
//           .' rsstool_title_md5,'
           .' rsstool_title_crc32,'
           .' rsstool_desc,'
           .' rsstool_keywords,'
           .' rsstool_related_id,'
           .' rsstool_media_duration,'
           .' rsstool_image,'
           .' rsstool_user,'
           .' rsstool_event_start,'
           .' rsstool_event_end';

      // HACK: rsscache category
      if ($rsscache_engine == 1)
        $p .= ', tv2_category, tv2_moved';

      $p .= ' ) VALUES ('
           .' \''.misc_sql_stresc ($a['item'][$i]['rsscache:dl_url'], $db_conn).'\','
//           .' \''.$a['item'][$i]['rsscache:dl_url_md5'].'\','
           .' \''.$a['item'][$i]['rsscache:dl_url_crc32'].'\','
           .' \''.$a['item'][$i]['rsscache:dl_date'].'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['rsscache:site'], $db_conn).'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['link'], $db_conn).'\','
//           .' \''.$a['item'][$i]['rsscache:url_md5'].'\','
           .' \''.$a['item'][$i]['rsscache:url_crc32'].'\','
           .' \''.$a['item'][$i]['pubDate'].'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['title'], $db_conn).'\','
//           .' \''.$a['item'][$i]['rsscache:title_md5'].'\','
           .' \''.$a['item'][$i]['rsscache:title_crc32'].'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['description'], $db_conn).'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['media_keywords'], $db_conn).'\','
           .' '.sprintf ("%u", misc_related_string_id ($a['item'][$i]['title'])).','
           .' \''.($a['item'][$i]['media_duration'] * 1).'\','
           .' \''.$a['item'][$i]['image'].'\','  
           .' \''.$a['item'][$i]['user'].'\','  
           .' \''.($a['item'][$i]['event_start'] * 1).'\','
           .' \''.($a['item'][$i]['event_end'] * 1).'\'';

      // HACK: rsscache category
      if ($rsscache_engine == 1)
        $p .= ', \''.$rsscache_category.'\', \''.$rsscache_category.'\'';

      $p .= ' );'."\n";

      // UPDATE rsstool_table
      $p .= '-- just update if row exists'."\n";
      if ($sql_update == 0)
        $p .= '-- ';
      $p .= 'UPDATE '.$rsstool_table.' SET '
           .' rsstool_title = \''.misc_sql_stresc ($a['item'][$i]['title'], $db_conn).'\','
//           .' rsstool_title_md5 = \''.$a['item'][$i]['title_md5'].'\','
           .' rsstool_title_crc32 = \''.$a['item'][$i]['title_crc32'].'\','
           .' rsstool_desc = \''.misc_sql_stresc ($a['item'][$i]['description'], $db_conn).'\''
           .' WHERE rsstool_url_crc32 = '.$a['item'][$i]['rsscache:url_crc32']
           .';'
           ."\n";

      // keyword_table
      $a = explode (' ', $a['item'][$i]['media_keywords']);
      for ($j = 0; isset ($a[$j]); $j++)
        if (trim ($a[$j]) != '')
          $p .= 'INSERT IGNORE INTO '.$keyword_table.' ('
//               .' rsstool_url_md5,'
               .' rsstool_url_crc32,'
//               .' rsstool_keyword_crc32,'
//               .' rsstool_keyword_crc24,'
               .' rsstool_keyword_crc16'
               .' ) VALUES ('
//               .' \''.$a['item'][$i]['url_md5'].'\','
               .' '.$a['item'][$i]['url_crc32'].','
//               .' '.sprintf ("%u", crc32 ($a[$j])).','
//               .' '.sprintf ("%u", misc_crc24 ($a[$j])).','
               .' '.misc_crc16 ($a[$j])
               .' );'
               ."\n";
    }

  return $p;
}


function
rsscache_write_ansisql ($a, $rsscache_category, $table_suffix = NULL, $db_conn = NULL)
{
  return rsstool_write_ansisql ($a, $rsscache_category, $table_suffix, $db_conn);
}


}


?>