<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:output method="text" omit-xml-declaration="yes"/>
<xsl:template match="/">[playlist]
<xsl:for-each select="rss/channel/item">
File<xsl:value-of select="position()"/>=<xsl:value-of disable-output-escaping="yes" select="link"/>
Title<xsl:value-of select="position()"/>=<xsl:value-of disable-output-escaping="yes" select="title"/>
Length<xsl:value-of select="position()"/>=-1

</xsl:for-each>
NumberOfEntries=<xsl:value-of select="count('rss/channel/item')"/>

Version=2
</xsl:template>
</xsl:stylesheet>