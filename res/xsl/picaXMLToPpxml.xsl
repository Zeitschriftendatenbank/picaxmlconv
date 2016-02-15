<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:ppxml="http://www.oclcpica.org/xmlns/ppxml-1.0" xmlns:picaXML="info:srw/schema/5/picaXML-v1.0" exclude-result-prefixes="picaXML ppxml">
    <xsl:output method="xml" indent="yes" encoding="UTF-8"/>
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="name(/*)='collection'">
                <collection>
                    <xsl:apply-templates select="/picaXML:collection/*" />
                </collection>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="picaXML:record" />
            </xsl:otherwise>
        </xsl:choose> 
    </xsl:template>
    <xsl:template match="picaXML:record">
        <ppxml:record>
            <xsl:choose>
                <xsl:when test="0 &lt; count(picaXML:datafield['0' != substring(@tag,1,1)])">
                    <ppxml:global>
                        <xsl:apply-templates select="picaXML:datafield['0' = substring(@tag,1,1)]" mode="global"/>
                    </ppxml:global>
                    <xsl:for-each select="picaXML:datafield['101@' = @tag]">
                        <xsl:variable name="next" select="position() + 1"/>
                        <xsl:variable name="absolute" select="1 + count(current()/preceding-sibling::*)"/>
                        <xsl:variable name="nextabsolute" select="1 + count(../picaXML:datafield['101@' = @tag][$next]/preceding-sibling::*)"/>
                        <ppxml:owner>
                            <xsl:attribute name="iln">
                                <xsl:value-of select="picaXML:subfield[@code = 'a']"/>
                            </xsl:attribute>
                            <xsl:if test="'101@' = @tag">
                                <ppxml:local>
                                    <xsl:call-template name="tag"/>
                                </ppxml:local>
                            </xsl:if>
                            <ppxml:copy>
                                <xsl:attribute name="epn">
                                    <xsl:value-of select="../picaXML:datafield[position() &lt; $nextabsolute and position() &gt;= $absolute and @tag = '203@']/picaXML:subfield[@code = '0']"/>
                                </xsl:attribute>
                                <xsl:for-each select="../picaXML:datafield[position() &lt; $nextabsolute and position() &gt;= $absolute]">
                                    <xsl:if test="'101@' != @tag">
                                        <xsl:call-template name="tag"/>
                                    </xsl:if>
                                </xsl:for-each>
                            </ppxml:copy>
                        </ppxml:owner>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="0 = count(picaXML:datafield['0' != substring(@tag,1,1)])">
                    <ppxml:global>
                        <xsl:apply-templates select="picaXML:datafield['0' = substring(@tag,1,1)]" mode="global"/>
                    </ppxml:global>
                </xsl:when>
                <xsl:otherwise>
                    <ppxml:global>
                        <xsl:apply-templates select="picaXML:datafield['0' = substring(@tag,1,1)]" mode="global"/>
                    </ppxml:global>
                </xsl:otherwise>
            </xsl:choose>
        </ppxml:record>
    </xsl:template>
    <xsl:template match="picaXML:datafield['0' = substring(@tag,1,1)]" mode="global">
        <xsl:call-template name="tag"/>
    </xsl:template>
    <xsl:template name="tag">
        <ppxml:tag>
            <xsl:attribute name="id">
                <xsl:value-of select="@tag"/>
            </xsl:attribute>
            <xsl:attribute name="occ">
                <xsl:if test="'0' = substring(@occurrence,1,1)">
                    <xsl:value-of select="substring(@occurrence,2,1)"/>
                </xsl:if>
                <xsl:if test="'0' != substring(@occurrence,1,1)">
                    <xsl:value-of select="@occurrence"/>
                </xsl:if>
            </xsl:attribute>
            <xsl:for-each select="picaXML:subfield">
                <ppxml:subf>
                    <xsl:attribute name="id">
                        <xsl:value-of select="@code"/>
                    </xsl:attribute>
                    <xsl:value-of select="."/>
                </ppxml:subf>
            </xsl:for-each>
        </ppxml:tag>
    </xsl:template>
</xsl:stylesheet>