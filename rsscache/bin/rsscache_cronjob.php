#!/usr/bin/php -q
<?php
/*
rsscache_cronjob.php - read config.xml, download feeds, turn feeds into SQL and write to db

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
require_once ('../htdocs/rsscache/default.php');
require_once ('../htdocs/config.php');
require_once ('../htdocs/misc/misc.php');
require_once ('../htdocs/misc/sql.php');
require_once ('../htdocs/misc/rss.php');
require_once ('../htdocs/misc/youtube.php');
require_once ('../htdocs/rsscache/rsscache_sql.php');
require_once ('../htdocs/rsscache/rsscache_misc.php');


// main ()


// unlimited execution time
//ini_set('max_execution_time', '3600');
set_time_limit (0);


rsscache_sql_open ();
 

$config = config_xml ();

echo misc_exec ('/etc/init.d/tor restart');

// DEBUG
echo 'database: '.$rsscache_dbname.' ('.$rsscache_dbuser.')'."\n";

for ($i = 0; isset ($config['item'][$i]); $i++)
  if (isset ($config['item'][$i]['category']))
    {
      print_r ($config['item'][$i]);
      rsscache_download_feeds_by_category ($config['item'][$i]['category']);
    }

rsscache_sql_close ();


exit;


?>