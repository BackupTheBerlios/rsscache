<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<!-- xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns="http://www.w3.org/1999/xhtml"
 xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms" -->
<xsl:output method="html" media-type="text/xhtml" omit-xml-declaration="yes" indent="yes"/>


<xsl:include href="rsscache/xsl/rsscache_html_include.xsl"/>


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