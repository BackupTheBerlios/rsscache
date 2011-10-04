<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml" omit-xml-declaration="no"/>

  <!-- xml xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms" -->

<xsl:template match="/rss">
  <xml>
    <xsl:apply-templates/>
  </xml>
</xsl:template>


<xsl:template match="@*|node()">
  <xsl:copy>
    <xsl:apply-templates select="@*|node()"/>
  </xsl:copy>
</xsl:template>


</xsl:stylesheet>
