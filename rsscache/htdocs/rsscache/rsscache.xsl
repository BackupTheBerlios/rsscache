<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/">
<xsl:template match="/">
<html>
<head>
<title><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></title>
<style type="text/css">
@import url(rsscache/rsscache.css);
</style>
<link rel="alternate" type="application/rss+xml">
<xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/></xsl:attribute>
<xsl:attribute name="title"><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></xsl:attribute>
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
<a href="{link}"><xsl:value-of disable-output-escaping="yes" select="title"/></a><br/>
<xsl:value-of disable-output-escaping="yes" select="pubDate"/>, Category: <xsl:value-of disable-output-escaping="yes" select="category"/><br/>
<!-- xsl:template match="media:group" -->
<!-- xsl:value-of disable-output-escaping="yes" select="media:thumbnail"/ --><br/>
<!-- /xsl:template -->
<xsl:value-of disable-output-escaping="yes" select="description"/><br/><br/>
</xsl:for-each>
<br/>
</body>
</html>
</xsl:template>
</xsl:stylesheet>