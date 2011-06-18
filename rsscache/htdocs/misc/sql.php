<?php
/*
sql.php - simplified wrappers for SQL access

Copyright (c) 2006 NoisyB


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
if (!defined ('MISC_SQL_PHP'))
{
define ('MISC_SQL_PHP', 1);


class misc_sql
{
protected $host = NULL;
protected $user = NULL;
protected $password = NULL;
protected $database = NULL;
public $conn = NULL; // connection
//protected $assoc = 0; // last fetch was assoc
public $res = NULL; // resource
protected $unbuffered = 0; // last query was unbuffered
protected $memcache_expire = 0; // 0 == off
protected $memcache = NULL;


function
sql_stresc ($s)
{
//  return mysql_escape_string ($s); // deprecated
  return mysql_real_escape_string ($s, $this->conn);
}


function
stresc ($s)
{
  return $this->stresc ($s);
}


function
sql_open ($host /* = 'localhost' */ , $user, $password, $database, $memcache_expire = 0)
{
  // DEBUG
//  print_r ($host.$database);
//  exit;

  if ($this->conn)
    {
      mysql_close ($this->conn);
//      $this->conn = NULL;
    }

  $this->host = $host;
  $this->user = $user;
  $this->password = $password;
  $this->database = $database;

  $this->conn = mysql_connect ($host, $user, $password);
  if ($this->conn == FALSE)
    {
      echo mysql_error ();
      exit;
    }

  if (mysql_select_db ($database, $this->conn) == FALSE)
    {
      echo mysql_error ();
      exit;
    }

  // open memcache too
  if ($memcache_expire > 0)
    {
      $this->memcache = new Memcache;
      if ($this->memcache->connect ('localhost', 11211) != TRUE)
        {
          echo 'memcache: could not connect';
          $this->memcache_expire = 0;
          return;
        }

      $this->memcache_expire = $memcache_expire;
    }
}


function
sql_read ($assoc = 0, $debug = 0)
{
  if ($debug == 1)
    if ($this->res == TRUE)
      {
        echo 'result is TRUE';
        if (!is_resource ($this->res))
          echo 'but no resource';
      }

  if (!is_resource ($this->res)) // either FALSE or just TRUE
    return NULL;  

  $a = array ();
  if ($assoc)
    {
      while ($row = mysql_fetch_array ($this->res, MYSQL_ASSOC)) // MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
        $a[] = $row;
//      $this->assoc = 1;
    }
  else
    {
      while ($row = mysql_fetch_array ($this->res)) // MYSQL_BOTH
        $a[] = $row;
//      $this->assoc = 0;
    }

  if ($debug == 1)
    {
      $p = '';
//      echo '<pre><tt>';
      for ($i = 0; isset ($a[$i]); $i++)
        $p .= implode (' ', $a[$i]).'<br>';
      echo $p;
//      echo '</tt></pre>';
    }

  return $a;
}


function
sql_getrow ($row, $assoc = 0, $debug = 0)
{
  if ($debug == 1)
    if ($this->res == TRUE)
      echo 'result is TRUE but no resource';

  if (!is_resource ($this->res)) // either FALSE or just TRUE
    return NULL;

  if ($this->unbuffered)
    {
      // DEBUG
      echo '<tt>ERROR: mysql_num_rows() and mysql_data_seek() after mysql_unbuffered_query()<br>';
    }

  $num_rows = mysql_num_rows ($this->res);
  if ($row >= $num_rows || $num_rows == 0)
    return NULL;

  if (mysql_data_seek ($this->res, $row) == FALSE)
    return NULL;

  if ($assoc)
    {
      $a = mysql_fetch_array ($this->res, MYSQL_ASSOC); // MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
//      $this->assoc = 1;
    }
  else
    {
      $a = mysql_fetch_array ($this->res); // MYSQL_BOTH
//      $this->assoc = 0;
    }

  if ($debug == 1)
    {
      $p = '';
//      echo '<pre><tt>';
      for ($i = 0; isset ($a[$i]); $i++)
        $p .= implode (' ', $a[$i]).'<br>';
      echo $p;
//      echo '</tt></pre>';
    }

  return $a;
}


function
sql_write ($sql_query_s, $unbuffered = 0, $debug = 0)
{
  if ($debug == 1)
    {
//      $sql_query_s = 'explain '.$sql_query_s;
      echo ''
          .'<br><br><tt>'
          .$sql_query_s
          .'</tt><br><br>'
;
    }

  if (is_resource ($this->res))
    {
      mysql_free_result ($this->res);
//      $this->res = NULL;
    }

  if (!is_resource ($this->conn))
    { 
      exit;
    }

  if ($this->memcache_expire > 0)
    {
      // data from the cache
      $p = $this->memcache->get (md5 ($sql_query_s));
      if ($p)
        $this->res = unserialize ($p);
      return 1;
    }

  if ($unbuffered)
    {
      $this->res = mysql_unbuffered_query ($sql_query_s, $this->conn);
      $this->unbuffered = 1;
    }
  else
    {
      $this->res = mysql_query ($sql_query_s, $this->conn);
      $this->unbuffered = 0;
    }

  if (is_resource ($this->res)) // cache resources only, not TRUE's
    if ($this->memcache_expire > 0)
      {
        // store data in the cache
        $this->memcache->set (md5 ($sql_query_s), serialize ($this->res), false, $this->memcache_expire);
      }

  if ($this->res != FALSE) // TRUE or resource (depending on query)
    return 1;
  return 0;
}


function
sql_close ()
{
  if (is_resource ($this->res))          
    {
      mysql_free_result ($this->res);    
//      $this->res = NULL;
    }

  if ($this->conn)
    {
      mysql_close ($this->conn);
//      $this->conn = NULL;
    }
}


function
sql_seek ($row)
{
  if ($this->unbuffered)
    {
      // DEBUG
      echo '<tt>ERROR: mysql_data_seek() after mysql_unbuffered_query()<br>';
    }
  return mysql_data_seek ($this->res, $row);
}


function
sql_get_result ()
{
  // returns FALSE or TRUE or resource
  return $this->res;
}


function
sql_get_rows ()
{
  if ($this->unbuffered)
    {
      // DEBUG
      echo '<tt>ERROR: mysql_num_rows() after mysql_unbuffered_query()<br>';
    }
  return mysql_num_rows ($this->res);
}


function
sql_get_table_rows ($table)
{
  $sql_query_s = 'SELECT COUNT(*) FROM '.$table.' WHERE 1';
  $this->sql_write ($sql_query_s);
  $a = $this->sql_read ();
  return $a[0][0];
}


// UNUSED 
function
sql_query2boolean_escape_func ($s)
{
  if (strlen (trim ($s, ' +-')) < 4)
//  if (strlen (trim ($s)) < 4)
    return false;

  $l = strlen ($s);
  for ($i = 0; $i < $l; $i++)
    if (!isalnum ($s[$i]) && !in_array ($s[$i], array ('-', '+', /* '(', ')', '"' */)))
      return false;
 
  return true;
}
  

function
sql_query2boolean_escape ($s)
{
  $a = explode (' ', strtolower ($s));
  for ($i = 0; isset ($a[$i]); $i++)
    $a[$i] = trim ($a[$i]);
  // TODO: more sensitivity instead of array_filter()
  $a = array_filter ($a, 'sql_query2boolean_escape_func');
  $a = misc_array_unique_merge ($a);
  
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $s = implode (' ', $a);
  $s = trim ($s);

  return $s;
}


function
sql_query2boolean ($q)
{
  /*
    parses google style search query into
      boolean full-text search query

    IMPORTANT: replaces mysql_real_escape_string()
  */

  global $debug_sql;
  $debug = $debug_sql;

  /*
    google style

    ALL of these words: test1 test2
    the exact wording or phrase: "test3  " "test4  "
    ONE OR MORE of these words: test5 OR test6
    ANY of these unwanted words: -test7 -test8

    1) test1 test2 test5 OR test6 "test3  " "test4  " -test7 -test8

    2) http://www.google.com/search?q=test1+test2+test5+OR+test6+%22test3++%22+%22test4++%22+-test7+-test8
  */

  $p = str_ireplace (' OR ', ' ', $q);
  $p = str_ireplace ('\\', '', $p); // unescape query
  $p = sql_query2boolean_escape ($p); 
  $match = $p;

  // DEBUG
  if ($debug)
    echo '<pre><tt>'
        .'query: "'.$q.'"'."\n"
//        .sprint_r ($a)."\n"
        .'match: \''.$match.'\''."\n";

  return $match;
}




}


}

?>