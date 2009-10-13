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
var $host = NULL;
var $user = NULL;
var $password = NULL;
var $database = NULL;
var $conn = NULL;
var $res = NULL;

var $memcache_expire = 0; // 0 == off
var $memcache = NULL;
//var $row_pos = -1;


function
sql_stresc ($s)
{
//  return mysql_escape_string ($s); // deprecated
  return mysql_real_escape_string ($s, $this->conn);
}


function
sql_open ($host, $user, $password, $database, $memcache_expire = 0)
{
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
      return;
    }

  if (mysql_select_db ($database, $this->conn) == FALSE)
    {
      echo mysql_error ();
      return;
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
sql_read ($debug = 0)
{
  if ($debug == 1)
    if ($this->res == TRUE)
      echo 'result is TRUE but no resource';

  if (!is_resource ($this->res)) // either FALSE or just TRUE
    return NULL;  

  $a = array ();
//  while ($row = mysql_fetch_array ($this->res, MYSQL_ASSOC)) // MYSQL_ASSOC, MYSQL_NUM, and the default value of MYSQL_BOTH
  while ($row = mysql_fetch_array ($this->res))
    $a[] = $row;

  if ($debug == 1)
    {
      $p = '<tt>';
      $i_max = sizeof ($a);
      for ($i = 0; $i < $i_max; $i++)
        {
          $j_max = sizeof ($a[$i]);
          for ($j = 0; $j < $j_max; $j++)
            $p .= $a[$i][$j]
                 .' ';

          $p .= '</tt><br>';
        }

      echo $p;
    }

  return $a;
}


function
sql_getrow ($row, $debug = 0)
{
  if ($debug == 1)
    if ($this->res == TRUE)
      echo 'result is TRUE but no resource';

  if (!is_resource ($this->res)) // either FALSE or just TRUE
    return NULL;

  if ($row >= mysql_num_rows ($this->res) || mysql_num_rows ($this->res) == 0)
    return NULL;

  if (mysql_data_seek ($this->res, $row) == FALSE)
    return NULL;

//  $this->row_pos = $row;

  $a = mysql_fetch_row ($this->res);

  if ($debug == 1)
    {
      $p = '<tt>';
      $i_max = sizeof ($a);
      for ($i = 0; $i < $i_max; $i++)
        $p .= $a[$i]
           .' ';

      $p .= '</tt><br>';

      echo $p;
    }

  return $a;
}


function
sql_write ($sql_query_s, $debug = 0)
{
  if ($debug == 1)
    echo '<br><br><tt>'
        .$sql_query_s
        .'</tt><br><br>';

  if (is_resource ($this->res))
    {
      mysql_free_result ($this->res);
//      $this->res = NULL;
    }

  if ($this->memcache_expire > 0)
    {
      // data from the cache
      $p = $this->memcache->get (md5 ($sql_query_s));
      if ($p)
        $this->res = unserialize ($p);
      return 1;
    }

  $this->res = mysql_query ($sql_query_s, $this->conn);

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

//  $this->row_pos = -1;
}


function
sql_seek ($row)
{
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
  return mysql_num_rows ($this->res);
}


}


}

?>