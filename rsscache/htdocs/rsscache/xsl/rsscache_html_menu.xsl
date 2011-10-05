<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<!-- xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns="http://www.w3.org/1999/xhtml"
 xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms" -->


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
  <img onerror="this.parentNode.removeChild(this);" src="images/rsscache_logo.png"/>
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
    <img onerror="this.parentNode.removeChild(this);">
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
        <img onerror="this.parentNode.removeChild(this);" border="0" width="25%">
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
    <img onerror="this.parentNode.removeChild(this);">
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

    <img onerror="this.parentNode.removeChild(this);">
    <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>

    <a href="{link}"><xsl:value-of disable-output-escaping="yes" select="title"/></a>&#160;&#160;<br/>
    <xsl:value-of disable-output-escaping="yes" select="description"/><br/>

    <img onerror="this.parentNode.removeChild(this);" border="0" width="25%">
    <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>

    <br/>Category: <img onerror="this.parentNode.removeChild(this);">
    <xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></xsl:attribute>
    </img>
    <a><xsl:attribute name="href">?c=<xsl:value-of disable-output-escaping="yes" select="category"/>&amp;output=html</xsl:attribute><xsl:value-of disable-output-escaping="yes" select="title"/></a><br/>

    <xsl:call-template name="body_item_stats"/>

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

  <!-- RSS link -->
  <link rel="alternate" type="application/rss+xml">
  <xsl:attribute name="href"><!-- xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/ -->?output=rss</xsl:attribute>
  <xsl:attribute name="title"><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></xsl:attribute>
  </link>

  <!-- statistics RSS -->
  <link rel="alternate" type="application/rss+xml" title="Statistics">
  <xsl:attribute name="href"><!-- xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/ -->?f=stats&amp;output=rss</xsl:attribute>
  </link>

  </head>
  <body>
    <xsl:if test="count(rss/channel/item) = 1">
      <xsl:call-template name="body_popout"/>
    </xsl:if>

    <xsl:if test="count(rss/channel/item) &gt; 1">

  <xsl:call-template name="body_header"/>       

      <xsl:choose>

        <xsl:when test="rss/channel/rsscache:stats_items">
          <xsl:call-template name="body_stats"/>
        </xsl:when>

        <xsl:otherwise>
          <xsl:call-template name="body"/>
        </xsl:otherwise>

      </xsl:choose>

  <br/><div style="width:100%;text-align:right;"><xsl:value-of select="rss/channel/title"/></div>

    </xsl:if>

  </body>
  </html>
</xsl:template>


</xsl:stylesheet>