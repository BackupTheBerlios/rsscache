<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="http://www.example.com/rsscache/">
<xsl:template match="/">
<html>
<head>
<title><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></title>
<style type="text/css">
@import url('rsscache/rsscache.css');
</style>
<!-- style type="text/css" src="rsscache/rsscache.css"></style -->
<link rel="alternate" type="application/rss+xml">
<xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/></xsl:attribute>
<xsl:attribute name="title"><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></xsl:attribute>
</link>
<link rel="alternate" type="application/rss+xml">
<xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/>?f=stats</xsl:attribute>
<xsl:attribute name="title">Statistics</xsl:attribute>
</link>
</head>
<body>
<img src="images/rsscache_logo.png"/><br/>
<br/>
<xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/><br/>
<br/>
<xsl:value-of disable-output-escaping="yes" select="rss/channel/description"/><br/>
<br/>
<br/>
<xsl:for-each select="rss/channel/item">
<a href="{link}"><xsl:value-of disable-output-escaping="yes" select="title"/></a>
&#160;&#160;<xsl:value-of disable-output-escaping="yes" select="media:duration"/> seconds<br/>
<xsl:value-of disable-output-escaping="yes" select="pubDate"/><br/>
<img>
<xsl:attribute name="src"><xsl:value-of disable-output-escaping="yes" select="media:thumbnail/@url"/></xsl:attribute>
</img><br/>
<xsl:value-of disable-output-escaping="yes" select="description"/><br/><br/>
Category: <a><xsl:attribute name="href">?c=<xsl:value-of disable-output-escaping="yes" select="category"/></xsl:attribute><xsl:value-of disable-output-escaping="yes" select="category"/></a><br/><br/>
Tags: <xsl:value-of disable-output-escaping="yes" select="media:keywords"/><br/>
<br/>
</xsl:for-each>
</body>
</html>
</xsl:template>
</xsl:stylesheet>