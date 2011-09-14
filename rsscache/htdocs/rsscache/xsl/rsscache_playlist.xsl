<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:output method="text" omit-xml-declaration="yes"/>
<xsl:template match="/">
<xsl:for-each select="rss/channel/item"># <xsl:value-of disable-output-escaping="yes" select="title"/>
# <xsl:value-of disable-output-escaping="yes" select="link"/><xsl:text>
</xsl:text><xsl:value-of disable-output-escaping="yes" select="rsscache:download"/><xsl:text>
</xsl:text>
</xsl:for-each>
</xsl:template>
</xsl:stylesheet>