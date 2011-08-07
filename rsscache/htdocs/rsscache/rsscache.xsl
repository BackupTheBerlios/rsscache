<?xml version="1.0" encoding="iso-8859-1"?>
<!-- Time-stamp: "2006-12-28 01:11:57 AST"

 This stylesheet is for rendering RSS as HTML.
 By sburke@cpan.org - I hereby release this XSL code into the public domain.

Resources used:
  http://interglacial.com/rss/smb_xslrss.css
  http://interglacial.com/rss/xsl_mop-up.js
  http://interglacial.com/rss/lastmod_ago.js
  http://interglacial.com/rss/lj_syndicated.gif
  http://interglacial.com/rss/dl_icon.gif
If you want to use this XSL for yours own RSS feeds, you'll need
to make copies of all of those items on your own server.

Overview:
 - namespaces (including Interglacial, for my own extras)
 - HTML preamble, using xsl:element to avoid having browsers
    go crazy and thinking this template IS actually HTML
 - page furniture, nav stuff, etc
 - with the h1, actual stuff.

rsscache urls have a similar syntax like google urls<br>
<br>
<br>
q=SEARCH  SEARCH query<br>
start=N   start from result N<br>
num=N     show N results<br>
c=NAME    category (leave empty for all categories)<br>
<br>
<br>
*** functions ***<br>
f=0_5min      videos with duration 0-5 minutes<br>
f=5_10min     videos with duration 5-10 minutes<br>
f=10_min      videos with duration 10+ minutes<br>
f=stats       statistics<br>
f=new         show only new items<br>
f=related     find related items (requires &q=SEARCH)<br>
<br>
<br>
*** install ***<br>
see apache2/sites-enabled/rsscache<br>


-->
<Q:stylesheet version="1.0"
  xmlns:Q = "http://www.w3.org/1999/XSL/Transform"
  xmlns:sy = "http://purl.org/rss/1.0/modules/syndication/"
  xmlns:rss = "http://purl.org/rss/1.0/"
  xmlns:Interglacial = "http://interglacial.com/rss/#Misc1"
  xmlns = "http://www.w3.org/1999/xhtml"
>

<Q:output method="html" />
<Q:template match="/">

<Q:element name="html"><Q:attribute name="class">RssToHtmlByXsl</Q:attribute>
<head>
  <Q:element name="meta">
   <Q:attribute name="content-type">text/html; charset=iso-8859-1</Q:attribute>
  </Q:element>
  <Q:element name="link">
   <Q:attribute name="rel">stylesheet</Q:attribute>
   <Q:attribute name="href">rsscache.css</Q:attribute>
   <Q:attribute name="type">text/css</Q:attribute>
   <Q:attribute name="title">mainlook</Q:attribute>
  </Q:element>

<Q:for-each select="/rss/channel/title">
  <title>RSS: <Q:value-of select="."/></title>
</Q:for-each>


<!--
 Make a nice link-alternate thing so that when viewed in Firefox et al,
 the little "RSS" subscribey-icon appears.
-->
<Q:for-each select="/rss/channel/Interglacial:self_url">
  <link rel="alternate" type="application/rss+xml">
    <Q:attribute name="href"><Q:value-of select="."/></Q:attribute>
    <Q:choose>
      <Q:when test="/rss/channel/title">
        <Q:attribute name="title"><Q:value-of select="/rss/channel/title"/></Q:attribute>
      </Q:when>
      <Q:otherwise>
        <Q:attribute name="title">This RSS feed</Q:attribute>
      </Q:otherwise>
    </Q:choose>
  </link>

</Q:for-each>

</head>

<body		onload="go_decoding();"		>

<div id="cometestme" style="display:none;"
 ><Q:text disable-output-escaping="yes" >&amp;amp;</Q:text></div>

<script type="text/javascript"
 src="http://interglacial.com/rss/xsl_mop-up.js"  ></script>
<script type="text/javascript"
 src="http://interglacial.com/rss/lastmod_ago.js" ></script>

<p class='meantForReader'>Don't panic.  This web page is
actually a data file that is meant to be read by RSS reader programs.
<br/>See <a href="http://interglacial.com/rss/about.html">here</a> to learn
more about RSS.
</p>
<p class='back'><a href="./" accesskey="U" title="Back to list of RSS feeds">[Back]</a></p>



<blockquote class='aboutThisFeed'>
<Q:for-each select="/rss/channel/lastBuildDate"><p><em>
 Last feed update:</em>
 <span id="lastBuildDate"><Q:value-of select="."/></span></p></Q:for-each>

<Q:for-each select="/rss/channel/Interglacial:livejournal"
  ><p><em>LiveJournal:</em>

  <a href="http://syndicated.livejournal.com/{.}/profile"
    title="about the Livejournal syndication of this feed"
  ><img
   src="http://interglacial.com/rss/lj_syndicated.gif"
   width="16" height="16" class="lj"
  /></a
  ><a href="http://syndicated.livejournal.com/{.}/?style=mine"
     accesskey="l" title="the LiveJournal view of this feed"
  ><Q:value-of select="."/></a></p></Q:for-each>

<Q:for-each select="/rss/channel/webMaster"><p><em>
 Feed admin:</em> <Q:value-of select="." /></p></Q:for-each>
<Q:for-each select="/rss/channel/language"><p><em>
 Language:</em>
 <Q:value-of select="." /></p></Q:for-each>
<Q:for-each select="/rss/channel/Interglacial:generator_url"
  ><p><em>Feed generator:</em>
  <a accesskey="p" href="{.}">source here</a></p></Q:for-each>

</blockquote>


<h1 class="feedtitle"><a accesskey="0" href="{/rss/channel/link}">
  <Q:value-of select="/rss/channel/title"/>
</a></h1>

<Q:for-each select="/rss/channel/description">
  <Q:if test=". != /rss/channel/title" >
  <!-- no point in printing them both if they're the same -->
    <p class='desc'><Q:value-of select="."/></p>
  </Q:if>
</Q:for-each>


<Q:if test="/rss/channel/sy:updatePeriod" >
  <p class='updatefreq'>This feed updates

    <Q:variable name="F" select="/rss/channel/sy:updateFrequency" />
    <Q:choose>
      <Q:when test="$F = '' or $F = 1" > once </Q:when>
      <Q:otherwise> <Q:value-of select="$F"/> times </Q:otherwise>
    </Q:choose>

    <Q:value-of select="/rss/channel/sy:updatePeriod"/>.
    Don't poll it any more often than that! 
  </p>
</Q:if>

<Q:if test="/rss/channel/item/enclosure" >
  <p class="notes">This RSS feed is also a
   <a href='http://en.wikipedia.org/wiki/Podcasting'
   >Podcast</a>, which you can read in iTunes, WinAmp, etc.</p>
</Q:if>

<Q:variable name="C" select="count(/rss/channel/item)" />
<p class='leadIn'>
  <Q:choose>
    <Q:when test="$C = 0" >No items </Q:when>
    <Q:when test="$C = 1" >The only item </Q:when>
    <Q:otherwise>The <Q:value-of select="$C" /> items </Q:otherwise>
  </Q:choose>
  currently in this feed:
</p>



<dl class='Items'>

<Q:if test='$C = 0'>  <dt>(Empty)</dt> </Q:if>


<Q:for-each select="/rss/channel/item">

<dt>

  <Q:for-each select="enclosure">
     <!-- There can be 0, 1, or many enclosures for each item. -->
     <span class="enclosure"><a href="{@url}" type="{@type}"
       title="Click to download a '{@type}' file of about {@length} bytes"
     ><img
       alt  ="Click to download a '{@type}' file of about {@length} bytes"
       src="http://interglacial.com/rss/dl_icon.gif"
       width="35" height="36" border="0"
     /></a></span>
  </Q:for-each>

  <a href="{link}">
    <Q:if test="position() &lt; 10">
      <Q:attribute name='accesskey'><Q:value-of select="position()" /></Q:attribute>
    </Q:if>

    <Q:choose>
      <Q:when test="not(title) or title = ''" ><em>(No title)</em></Q:when>
      <Q:otherwise		><Q:value-of select="title"/></Q:otherwise>
    </Q:choose>
  </a>
</dt>

<Q:if test="description" >
  <dd name="decodeme"
><Q:value-of  disable-output-escaping="yes" select="description" /></dd>
  <!--
   Alas, many implementations can't, and never will, directly
   support disable-output-escaping.  We try to work around that
   with our JavaScript thing.
  -->
</Q:if>
</Q:for-each>
</dl>



<!-- The bottom-of-page options: -->
<p class='end'>
[<a href="./">Back to list of RSS feeds</a>]

<Q:for-each select="/rss/channel/Interglacial:self_url">
&#160;&#160;&#160;&#160;
 [<a href="http://feedvalidator.org/check?url={.}" accesskey="v">Validate
 this feed</a>]
</Q:for-each>

</p>


<p class="badcss">
Hm, you're apparently using a browser that doesn't
support stylesheets properly<em style="display: none">, or at all</em>.
You should really think about using
<a href="http://www.mozilla.org/products/firefox/">Firefox</a>
instead.
</p>


</body								>
	</Q:element					>
		</Q:template			>
			</Q:stylesheet>
