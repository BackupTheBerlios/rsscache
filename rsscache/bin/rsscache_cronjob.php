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
require_once ('../htdocs/rsscache_sql.php');
require_once ('../htdocs/rsscache_misc.php');


function
test_main ($category_name = NULL)
{
$debug = 0;
global $config;
global $rsscache_sql_db;

// TODO: single category using category_name

// config.xml
for ($i = 0; isset ($config->category[$i]); $i++)
  if (isset ($config->category[$i]->name))
    for ($j = 0; isset ($config->category[$i]->feed[$j]); $j++)
      {
        $feed = $config->category[$i]->feed[$j];
        $name = trim ($config->category[$i]->name);
        $link = array ();

        // rsstool options
        $opts = '';
        if (isset ($feed->opts))
          $opts = $feed->opts;

        // old style config.xml: link[]
        for ($k = 0; isset ($feed->link[$k]); $k++)
          if (trim ($feed->link[$k]) != '')
            $link[] = $feed->link[$k];

        // TODO: use new style config.xml
        //   link_prefix, link_search[], link_suffix
        if (isset ($feed->link_prefix))
          for ($k = 0; isset ($feed->link_search[$k]); $k++)
            {
              $p = '';
//              if (isset ($feed->link_prefix))
                $p .= $feed->link_prefix;
//              if (isset ($feed->link_search[$k]))
                $p .= $feed->link_search[$k];
              if (isset ($feed->link_suffix))
                $p .= $feed->link_suffix;
              $link[] = $p;
            }

        for ($k = 0; isset ($link[$k]); $k++)
          {
              echo 'category: '.$name."\n";
              echo 'url: '.$link[$k]."\n";

              // get feed
              $xml = rsscache_feed_get ($feed->client, $opts, $link[$k]);
              // download thumbnails
              $xml = rsscache_download_thumbnails ($xml);
              // xml to sql
              $sql = rsstool_write_ansisql ($xml, $name, $config->category[$i]->table_suffix, $rsscache_sql_db->conn);
              // insert
              rsscache_sql_insert ($sql);
          }
      }

}


// main ()



// unlimited execution time
//ini_set('max_execution_time', '3600');
set_time_limit (0);

rsscache_sql_open ();
 
// config.xml from inside htdocs
$rsscache_config_xml = '../htdocs/'.$rsscache_config_xml;
$config = config_xml ();

// DEBUG
echo 'database: '.$rsscache_dbname.' ('.$rsscache_dbuser.')'."\n";

test_main ();

rsscache_sql_close ();

exit;


?>