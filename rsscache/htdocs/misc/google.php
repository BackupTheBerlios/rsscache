<?php
/*
google.php - google functions

Copyright (c) 2011 NoisyB


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
if (!defined ('MISC_GOOGLE_PHP'))
{
define ('MISC_GOOGLE_PHP', 1);


/*
define:phrase
  Show a list of definitions for phrase

cache:www.example.com
  Google's cache of example.com

link:www.example.com
  List of websites that link to example.com

related:www.example.com
  List webpages similar to www.example.com

info:www.example.com
  Show information that Google has about www.example.com

site:www.example.com
  List all webpages hosted at www.example.com

allintitle:query
  Restrict the results to those with all of the query words in the title

intitle:query
  Restrict the results to documents containing that word in the title

allinurl:query
  Restrict the results to those with all of the query words in the URL

inurl:query
  Restrict the results to documents containing that word in the URL

apple * sauce
  The words apple and sauce separated by exactly one word

Nokia phone $100...300
  Search within a range of numbers for a Nokia phone between $100...300

safesearch: sex education
  Search for sex education material without returning adult sites

300000000000000..399999999999999
5178000000000000..5178999999999999
4356000000000000..4356999999999999
  Credit Card numbers (AmEx, MC, Visa)

intitle:"Index of" passwords modified

allinurl:auth_user_file.txt

"access denied for user" "using password"

"A syntax error has occurred" filetype:ihtml

allinurl: admin mdb

"ORA-00921: unexpected end of SQL command"

inurl:passlist.txt

"Index of /backup"

"Chatologica MetaSearch"

"stack tracking:"

"parent directory " /appz/ -xxx -html -htm -php -shtml -opendivx -md5 -md5sums
"parent directory " DVDRip -xxx -html -htm -php -shtml -opendivx -md5 -md5sums
"parent directory "Xvid -xxx -html -htm -php -shtml -opendivx -md5 -md5sums
"parent directory " Gamez -xxx -html -htm -php -shtml -opendivx -md5 -md5sums
"parent directory " MP3 -xxx -html -htm -php -shtml -opendivx -md5 -md5sums
"parent directory " Name of Singer or album -xxx -html -htm -php -shtml -opendivx -md5 -md5sums
  Notice that I am only changing the word after the parent directory, change
  it to what you want and you will get a lot of stuff.

?intitle:index.of? mp3 SEARCH
inurl:SEARCH filetype:iso

"# -FrontPage-" inurl:service.pwd
  Frontpage passwords.. very nice clean search results listing !!

"AutoCreate=TRUE password=*"
  This searches the password for "Website Access Analyzer", a Japanese
  software that creates webstatistics.  For those who can read Japanese, check
  out the author's site at: http://www.coara.or.jp/~passy/

"http://*:*@www" domainname
  This is a query to get inline passwords from search engines (not just
  Google), you must type in the query followed with the the domain name
  without the .com or .net

"http://*:*@www" bangbus or "http://*:*@www"bangbus
  Another way is by just typing "http://bob:bob@www"

"sets mode: +k"
  This search reveals channel keys (passwords) on IRC as revealed from IRC chat logs.

allinurl: admin mdb
  Not all of these pages are administrator's access databases containing
  usernames, passwords and other sensitive information, but many are!

allinurl:auth_user_file.txt
  DCForum's password file. This file gives a list of (crackable) passwords,
  usernames and email addresses for DCForum and for DCShop (a shopping cart
  program(!!!).  Some lists are bigger than others, all are fun, and all
  belong to googledorks.  =)

intitle:"Index of" config.php
  This search brings up sites with "config.php" files. To skip the technical
  discussion, this configuration file contains both a username and a password
  for an SQL database.  Most sites with forums run a PHP message base.  This
  file gives you the keys to that forum, including FULL ADMIN access to the
  database.

eggdrop filetype:user user
  These are eggdrop config files. Avoiding a full-blown descussion about
  eggdrops and IRC bots, suffice it to say that this file contains usernames
  and passwords for IRC users.

intitle:index.of.etc
  This search gets you access to the etc directory, where many many many types
  of password files can be found.  This link is not as reliable, but crawling
  etc directories can be really fun!

filetype:bak inurl:"htaccess|passwd|shadow|htusers"
  This will search for backup files (*.bak) created by some editors or even by
  the administrator himself (before activating a new version).  Every attacker
  knows that changing the extenstion of a file on a webserver can have ugly
  consequences.

"Windows XP Professional" 94FBR
  the key is the 94FBR code.. it was included with many MS Office registration
  codes so this will help you dramatically reduce the amount of 'fake' porn
  sites that trick you.  or if you want to find the serial for winzip 8.1 -
  "Winzip 8.1" 94FBR


site:www.cwire.org
  This will search only pages which reside on this domain.

related:www.cwire.org
  This will display all pages which Google finds to be related to your URL

link:www.cwire.org
  This will display a list of all pages which Google has found to be linking
  to your site.  Useful to see how popular your site is

spell:word
  Runs a spell check on your word

define:word
  Returns the definition of the word

stocks: [symbol, symbol, etc]
  Returns stock information. eg. stock: msft

maps:
  A shortcut to Google Maps

phone: name_here
  Attempts to lookup the phone number for a given name

cache:
  If you include other words in the query, Google will highlight those words
  within the cached document.  For instance, cache:www.cwire.org web will show
  the cached content with the word web highlighted.

info:
  The query [info:] will present some information that Google has about that
  web page.  For instance, info:www.cwire.org will show information about the
  CyberWyre homepage.  Note there can be no space between the info: and the
  web page url.

weather:
  Used to find the weather in a particular city. eg. weather: new york

filetype:
  Does a search for a specific file type, or, if you put a minus sign (-) in
  front of it, it wont list any results with that filetype.  Try it with .mp3,
  .mpg or .avi if you like.

daterange:
  Is supported in Julian date format only. 2452384 is an example of a Julian date.

allinurl:
  If you start a query with [allinurl:], Google will restrict the results to
  those with all of the query words in the url.  For instance, [allinurl:
  google search] will return only documents that have both google and search
  in the url.

inurl:
  If you include [inurl:] in your query, Google will restrict the results to
  documents containing that word in the url.  For instance, [inurl:google
  search] will return documents that mention the word google in their url, and
  mention the word search anywhere in the document (url or no).  Note there
  can be no space between the inurl: and the following word.

allintitle:
  If you start a query with [allintitle:], Google will restrict the results
  to those with all of the query words in the title.  For instance,
  [allintitle: google search] will return only documents that have both google
  and search in the title.

intitle:
  If you include [intitle:] in your query, Google will restrict the results
  to documents containing that word in the title.  For instance,
  [intitle:google search] will return documents that mention the word google
  in their title, and mention the word search anywhere in the document (title
  or no).  Note there can be no space between the intitle: and the following
  word.

allinlinks:
  Searches only within links, not text or title.

allintext:
  Searches only within text of pages, but not in the links or page title.

bphonebook:
  If you start your query with bphonebook:, Google shows U.S. business white
  page listings for the query terms you specify.  For example, [ bphonebook:
  google mountain view ] will show the phonebook listing for Google in
  Mountain View.

phonebook:
  If you start your query with phonebook:, Google shows all U.S. white page
  listings for the query terms you specify.  For example, [ phonebook: Krispy
  Kreme Mountain View ] will show the phonebook listing of Krispy Kreme donut
  shops in Mountain View.

rphonebook:
  If you start your query with rphonebook:, Google shows U.S. residential
  white page listings for the query terms you specify.  For example, [
  rphonebook: John Doe New York ] will show the phonebook listings for John
  Doe in New York (city or state).  Abbreviations like [ rphonebook: John Doe
  NY ] generally also work.

index of + mp3 + beatles -html -htm -php
index of/mp3 -playlist -html -lyrics beatles
  Right away on the first few results returned by Google you can download MP3s.

sleep recommendations site:edu
  Well mix around a few terms to get more accurate results.  Lets say we
  want to research sleep recommendations.  One assumption could be that
  research papers on this topic would most likely be on an educational website
  perhaps with a .edu domain.

Maybe were in my situation, and am thinking of applying to grad school. Lets
see if we can find the Graduate Studies Admissions Requirements at the
University of Toronto.  We could try this query:

grad school admission requirements site:utoronto.ca 
  Lets see if we can find the Graduate Studies Admissions Requirements at
  the University of Toronto.

allinurl:rss filetype:xml -html -php -htm

SEARCH ext:torrent

http://www.google.com/search?hl=en&client=opera&rls=en&q=intitle%3A%28%22Index.of.*%22%7C%22Index.von.*%22%29+%2B%28Verzeichnis%7Cdirectory%29+-inurl%3A%28htm%7Cphp%7Chtml%7Cpy%7Casp%7Cpdf%29+-inurl%3Ainurl+-inurl%3Aindex+super+nintendo&btnG=Search
intitle:("Index.of.*"|"Index.von.*") +(Verzeichnis|directory) -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index super nintendo

*/


function
misc_google_search ($search)
{
  $a = array ();

  // ftp
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $a[] = 'http://www.google.com/search?q='.urlencode ($query);
  // video
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $a[] = 'http://www.google.com/search?q='.urlencode ($query);
// youtube
//    <feed>http://video.google.com/videosearch?q=quakeworld&amp;so=1&amp;output=rss&amp;num=1000</feed>
//    <feed>http://gdata.youtube.com/feeds/api/videos?vq=quake1&amp;max-results=50</feed>
  $a[] = 'http://en.wikipedia.org/w/index.php?title=Special%3ASearch&redirs=0&search='.$search.'&fulltext=Search&ns0=1'; // wikipedia
  $a[] = 'http://www.google.com/search?ie=UTF-8&oe=utf-8&q='.$search; // lyrics
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q='.$search;             // images
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q=walkthrough+'.$search; // walkthrough
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q=cheat+'.$search; // cheat

  return $a;
}


}

?>