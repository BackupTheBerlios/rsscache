<?xml version="1.0" encoding="iso-8859-1"?>
<!-- xmlns="http://www.w3.org/1999/xhtml" -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:output method="html" indent="no"/>


<xsl:template name="body_popout">
  <xsl:attribute name="style">background-color:#000;</xsl:attribute>
  <xsl:for-each select="rss/channel/item">
    <xsl:if test="media:embed != ''">
      <xsl:value-of disable-output-escaping="yes" select="media:embed"/>
    </xsl:if>
  </xsl:for-each>
</xsl:template>


<xsl:template name="body_channel_stats">
  <div style="background-color:#999;">
    <xsl:value-of disable-output-escaping="yes" select="rss/channel/rsscache:stats_items"/> items<br/>
    <b><xsl:value-of disable-output-escaping="yes" select="rss/channel/rsscache:stats_items_today"/></b> items today<br/>
    <xsl:value-of disable-output-escaping="yes" select="rss/channel/rsscache:stats_items_7_days"/> items last 7 days<br/>
    <xsl:value-of disable-output-escaping="yes" select="rss/channel/rsscache:stats_items_30_days"/> items last 30 days<br/>
  </div>
</xsl:template>


<xsl:template name="body_item_stats">
  <div style="background-color:#999;">category: <b><xsl:value-of disable-output-escaping="yes" select="category"/></b><br/>
    <xsl:value-of disable-output-escaping="yes" select="rsscache:stats_items"/> items<br/>
    <b><xsl:value-of disable-output-escaping="yes" select="rsscache:stats_items_today"/></b> items today<br/>
    <xsl:value-of disable-output-escaping="yes" select="rsscache:stats_items_7_days"/> items last 7 days<br/>
    <xsl:value-of disable-output-escaping="yes" select="rsscache:stats_items_30_days"/> items last 30 days (<xsl:value-of disable-output-escaping="yes" select="rsscache:stats_items_30_days / 30"/> items per day)<br/>
    <xsl:value-of disable-output-escaping="yes" select="rsscache:stats_days"/> days since creation of category<br/>table_suffix: <b><xsl:value-of disable-output-escaping="yes" select="rsscache:table_suffix"/></b><br/>
  </div>
</xsl:template>


<xsl:template name="body_header">
  <a>
  <xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/></xsl:attribute>
  <img src="images/rsscache_logo.png"/>
  </a>
  <br/>
  <xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/>
  <br/>
  <xsl:value-of disable-output-escaping="yes" select="rss/channel/description"/>
  <br/>
</xsl:template>


<xsl:template name="body">
  <xsl:for-each select="rss/channel/item">
    <hr/>

    <a>
    <xsl:attribute name="href">?c=<xsl:value-of disable-output-escaping="yes" select="category"/>&amp;output=html</xsl:attribute>
    <img>
    <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>
    <!-- xsl:value-of disable-output-escaping="yes" select="rsscache:category_title"/ -->
    </a>

    <xsl:choose>
      <xsl:when test="media:embed != ''">
        <a>
        <xsl:attribute name="href">?item=<xsl:value-of disable-output-escaping="yes" select="rsscache:url_crc32"/>&amp;output=html</xsl:attribute>
        <xsl:value-of disable-output-escaping="yes" select="title"/>
        </a>
      </xsl:when>
      <xsl:otherwise>
        <a href="{link}">
        <xsl:value-of disable-output-escaping="yes" select="title"/>
        </a>
      </xsl:otherwise>
    </xsl:choose>
    <br/>

    <xsl:text>&#160;&#160;</xsl:text>
    <xsl:if test="media:duration &gt; 0">
      <xsl:value-of disable-output-escaping="yes" select="media:duration"/> seconds<br/>
    </xsl:if>

    <xsl:value-of disable-output-escaping="yes" select="pubDate"/>

    <xsl:if test="rsscache:related_id &gt; 0">; Related: <a>
      <xsl:attribute name="href">?f=related&amp;q=<xsl:value-of disable-output-escaping="yes" select="rsscache:related_id"/>&amp;output=html</xsl:attribute><xsl:value-of disable-output-escaping="yes" select="rsscache:related_id"/>
      </a>
      <br/>
    </xsl:if>

    <xsl:if test="media:thumbnail/@url != ''">
      <br/>

    <xsl:choose>

      <xsl:when test="media:embed != ''">
        <a>
        <xsl:attribute name="href">?item=<xsl:value-of disable-output-escaping="yes" select="rsscache:url_crc32"/>&amp;output=html</xsl:attribute>
        <img border="0" width="25%">
        <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="media:thumbnail/@url"/></xsl:attribute>
        </img>
        </a>
      </xsl:when>

      <xsl:otherwise>
        <a href="{link}">
        <img border="0" width="25%">
        <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="media:thumbnail/@url"/></xsl:attribute>
        </img>
        </a>
      </xsl:otherwise>

    </xsl:choose>

      <br/>
      <br/>
    </xsl:if>

    <xsl:if test="media:embed != ''">
      <a href="{link}">Original</a>
    </xsl:if>

    <br/>
    <xsl:value-of disable-output-escaping="yes" select="description"/>
    <br/>
    <br/>Category: <a>
    <xsl:attribute name="href">?c=<xsl:value-of disable-output-escaping="yes" select="category"/>&amp;output=html</xsl:attribute>
    <img>
    <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>
    <xsl:value-of disable-output-escaping="yes" select="rsscache:category_title"/>
    </a>
    <br/>

    <xsl:if test="media:keywords != ''">Tags: <xsl:value-of disable-output-escaping="yes" select="media:keywords"/>
    <br/>
    </xsl:if>

  </xsl:for-each>
</xsl:template>


<xsl:template name="body_stats">
  <xsl:call-template name="body_channel_stats"/>

  <xsl:for-each select="rss/channel/item">
    <hr/>

    <img>
      <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>

    <a href="{link}"><xsl:value-of disable-output-escaping="yes" select="title"/></a>&#160;&#160;<br/>
    <xsl:value-of disable-output-escaping="yes" select="description"/><br/>

    <img border="0" width="25%">
      <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>

    <br/>Category: <img>
      <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>
    <a><xsl:attribute name="href">?c=<xsl:value-of disable-output-escaping="yes" select="category"/>&amp;output=html</xsl:attribute><xsl:value-of disable-output-escaping="yes" select="title"/></a><br/>

    <xsl:call-template name="body_item_stats"/>

  </xsl:for-each>
</xsl:template>


<xsl:template name="body_menu">
  <xsl:for-each select="rss/channel/item">
    <img>
    <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>
    <a href="{link}"><xsl:value-of disable-output-escaping="yes" select="title"/></a>
  </xsl:for-each>
</xsl:template>


<xsl:template match="/">
  <html>
  <head>
  <title><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"></meta>

  <!-- style type="text/css">
@import url('rsscache/rsscache.css');
</style -->
  <style type="text/css" src="rsscache/rsscache.css"></style>

  <link rel="icon" type="image/png" href="images/rsscache_icon.png"></link>

  <!-- link rel="alternate" type="application/rss+xml">
  <xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/></xsl:attribute>
  <xsl:attribute name="title"><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></xsl:attribute>
  </link>
  <link rel="alternate" type="application/rss+xml" title="Statistics">
  <xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/>?f=stats</xsl:attribute>
  </link -->
  <link rel="alternate" type="application/rss+xml" href="?output=rss">
  <xsl:attribute name="title"><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></xsl:attribute>
  </link>
  <link rel="alternate" type="application/rss+xml" title="Statistics" href="?f=stats&amp;output=rss"></link>

<!-- TODO: head seo -->
  <!-- meta name="Description" content="support battlefield beta the deadliest bush gameplay min hours ago well actually just look pdw and potential uses outdoor section operation metro tags totalbiscuit totalhalibut cynicalbrit fps game commentary live feat woodysgamertag ibashtv part leveling prestiging http www.youtube.com cached thanks for watching guys should later today have some great black ops videos planned coming weeks video bash follow www.twitter.com like www.facebook.com tmartn tmart tmartin rush sniping ump xbox playstation callouts sniper mvp using scope nitroy playing show how looks hope you enjoy youtube.com welcome open five kills one grenade watch quot herbbzy get with only this incredible special user funny lol let play together terraria deutsch zwischendurch glitch was uploaded from android phone lets airwolf german diesem zeige ich euch den mod way fight war that gshd twitter.com"><meta name="keywords" content="support battlefield beta the deadliest bush gameplay min hours ago well actually just look pdw and potential uses outdoor section operation metro tags totalbiscuit totalhalibut cynicalbrit fps game commentary live feat woodysgamertag ibashtv part leveling prestiging http www.youtube.com cached thanks for watching guys should later today have some great black ops videos planned coming weeks video bash follow www.twitter.com like www.facebook.com tmartn tmart tmartin rush sniping ump xbox playstation callouts sniper mvp using scope nitroy playing show how looks hope you enjoy youtube.com welcome open five kills one grenade watch quot herbbzy get with only this incredible special user funny lol let play together terraria deutsch zwischendurch glitch was uploaded from android phone lets airwolf german diesem zeige ich euch den mod way fight war that gshd twitter.com"></meta -->

  <!-- meta name="google-site-verification" content=""></meta -->

  <link rel="stylesheet" type="text/css" media="screen" href="tv2/tv2.css"></link>
  <link rel="stylesheet" type="text/css" media="screen" href="pwnoogle.css"></link>

  <script type="text/javascript" src="misc/jquery.js"></script>
  <script type="text/javascript" src="misc/jquery_ui.js"></script>
  <!-- script type="text/javascript" src="misc/jquery_easing.js"></script -->
  <script type="text/javascript" src="misc/jquery_lavalamp.js"></script>
  <script type="text/javascript" src="misc/misc.js"></script>
  <script type="text/javascript" src="tv2/tv2.js"></script>

<!-- parse:head_tag -->

  </head>
  <body>

<!-- parse:body_header -->
<!-- parse:body -->

    <xsl:if test="count(rss/channel/item) = 1">
      <xsl:call-template name="body_popout"/>
    </xsl:if>

    <xsl:if test="count(rss/channel/item) &gt; 1">

  <xsl:call-template name="body_header"/>       
  <xsl:call-template name="body_menu"/>
   <!-- tv2_page ($start, $num, sizeof ($d_array['item']) -->

      <xsl:choose>

        <xsl:when test="rss/channel/rsscache:stats_items">
          <xsl:call-template name="body_stats"/>
        </xsl:when>

        <xsl:otherwise>
          <xsl:call-template name="body"/>
        </xsl:otherwise>

      </xsl:choose>
   <!-- tv2_page ($start, $num, sizeof ($d_array['item']) -->

  <br/><div style="width:100%;text-align:right;"><xsl:value-of select="rss/channel/title"/></div>

    </xsl:if>

  </body>
  </html>
</xsl:template>


</xsl:stylesheet>