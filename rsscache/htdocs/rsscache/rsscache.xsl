<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/">
<xsl:template match="/">
<html>
<head>
<title><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></title>
<style type="text/css">
@import url(rsscache.css);
</style>
</head>
<body>

<xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/><br/><br/>
<xsl:value-of disable-output-escaping="yes" select="rss/channel/description"/><br/><br/>

<xsl:for-each select="rss/channel/item">
<a href="{link}"><xsl:value-of disable-output-escaping="yes" select="title"/></a><br/>
<xsl:value-of disable-output-escaping="yes" select="pubDate"/>, Category: <xsl:value-of disable-output-escaping="yes" select="category"/><br/>
<xsl:value-of disable-output-escaping="yes" select="./media:group/media:thumbnail"/><br/>
<xsl:value-of disable-output-escaping="yes" select="description"/><br/><br/>
</xsl:for-each>

</body>
</html>
</xsl:template>
</xsl:stylesheet>