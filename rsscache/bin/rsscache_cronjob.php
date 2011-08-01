#!/usr/bin/php -q
<?php
/*
rsscache.php - read config.xml, download feeds, turn feeds into SQL and write to db

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
//phpinfo ();
//error_reporting(E_ALL | E_STRICT);
require_once ('../htdocs/default.php');
require_once ('../htdocs/config.php');
require_once ('../htdocs/rsscache_misc.php');


// main ()

$debug = 0;


// unlimited execution time
//ini_set('max_execution_time', '3600');
set_time_limit (0);


// config.xml from inside htdocs
$config = simplexml_load_file ('../htdocs/'.$rsscache_config_xml);


$db = new misc_sql;
$db->sql_open ($rsscache_dbhost, $rsscache_dbuser, $rsscache_dbpass, $rsscache_dbname);
// DEBUG
echo 'database: '.$rsscache_dbname.' ('.$rsscache_dbuser.')'."\n";

// config.xml
for ($i = 0; isset ($config->category[$i]); $i++)
  if (isset ($config->category[$i]->name))
    for ($j = 0; isset ($config->category[$i]->feed[$j]); $j++)
      {
        $feed = $config->category[$i]->feed[$j];
        $name = trim ($config->category[$i]->name);

        // rsstool options
        $opts = '';
        if (isset ($feed->opts))
          $opts = $feed->opts;

        // old style config.xml: link[]
        for ($k = 0; isset ($feed->link[$k]); $k++)
          if (trim ($feed->link[$k]) != '')
            {
              $link = $feed->link[$k];

              echo 'category: '.$name."\n";
              echo 'url: '.$feed->link[$k]."\n";

              // get feed
              $xml = rsscache_feed_get ($feed->client, $opts, $link);
              // download thumbnails
              $xml = rsscache_download_thumbnails ($xml);
              // xml to sql
              $sql = rsstool_write_ansisql ($xml, $name, $config->category[$i]->table_suffix, $db->conn);
              // insert
              rsscache_sql_insert ($sql);
            }

        // TODO: use new style config.xml
        //   link_prefix, link_search[], link_suffix
        if (isset ($feed->link_prefix))
          for ($k = 0; isset ($feed->link_search[$k]); $k++)
            {
              $link = '';
//              if (isset ($feed->link_prefix))
                $link .= $feed->link_prefix;
//              if (isset ($feed->link_search[$k]))
                $link .= $feed->link_search[$k];
              if (isset ($feed->link_suffix))
                $link .= $feed->link_suffix;

              echo 'category: '.$name."\n";
              echo 'url: '.$link."\n";

              // get feed
              $xml = rsscache_feed_get ($feed->client, $opts, $link);
              // download thumbnails
              $xml = rsscache_download_thumbnails ($xml);
              // xml to sql
              $sql = rsstool_write_ansisql ($xml, $name, $config->category[$i]->table_suffix, $db->conn);
              // insert
              rsscache_sql_insert ($sql);
            }
      }

$db->sql_close ();

exit;


?>