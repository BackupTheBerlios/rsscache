<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:template match="/">
<html>
<head>
<title><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></title>
<style type="text/css">
@import url('rsscache/rsscache.css');
</style>
<!-- style type="text/css" src="rsscache/rsscache.css"></style -->
<link rel="alternate" type="application/rss+xml">
<xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="rss/channel/link"/>?f=stats</xsl:attribute>
<xsl:attribute name="title">Statistics</xsl:attribute>
</link>
</head>
<body style="background-color:#000;">
<xsl:for-each select="rss/channel/item">
<xsl:if test="media:embed != ''">
<xsl:value-of disable-output-escaping="yes" select="media:embed"/>
<br/>
</xsl:if>
</xsl:for-each>
</body>
</html>
</xsl:template>
</xsl:stylesheet>