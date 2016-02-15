<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:ppxml="http://www.oclcpica.org/xmlns/ppxml-1.0" xmlns="info:srw/schema/5/picaXML-v1.0" exclude-result-prefixes="ppxml">
    <xsl:output method="xml" indent="yes" encoding="UTF-8"/>
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="local-name(/*)='collection'">
                <collection>
                    <xsl:apply-templates select="/collection/*" />
                </collection>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="ppxml:record" />
            </xsl:otherwise>
        </xsl:choose> 
    </xsl:template>
    <xsl:template match="ppxml:record">
        <record>
            <xsl:apply-templates select="ppxml:global/*" />
            <xsl:apply-templates select="ppxml:owner/*" />
        </record>
    </xsl:template>
    <xsl:template match="ppxml:local">
        <xsl:apply-templates select="ppxml:tag" />
    </xsl:template>
    <xsl:template match="ppxml:copy">
        <xsl:apply-templates select="ppxml:tag" />
    </xsl:template>
    <xsl:template match="ppxml:tag">
        <datafield>
            <xsl:attribute name="tag">
                <xsl:value-of select="@id"/>
            </xsl:attribute>
            <xsl:if test="0 &lt; @occ">
                <xsl:attribute name="occurrence">
                    <xsl:if test="10 &gt; @occ">
                        <xsl:text>0</xsl:text>
                    </xsl:if>
                    <xsl:value-of select="@occ"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:for-each select="ppxml:subf">
                <subfield>
                    <xsl:attribute name="code">
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    <xsl:value-of select="."/>
                </subfield>
            </xsl:for-each>
        </datafield>
    </xsl:template>
</xsl:stylesheet>