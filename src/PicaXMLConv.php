<?php
/**
* PicaXMLConv converts PicaXML (namespace info:srw/schema/5/picaXML-v1.0) and
* ppmxl (namespace http://www.oclcpica.org/xmlns/ppxml-1.0) vice versa.
*
* @author Carsten Klee <mailme.klee@yahoo.de>
*/
namespace CK\PicaXMLConv;

use DOMDocument;
use DOMException;
use RuntimeException;
use InvalidArgumentException;
use XSLTProcessor;

class PicaXMLConv
{

    /**
    * @var DOMDocument  The source
    */
    protected $source;
    
    /**
    * @var DOMDocument  The target
    */
    protected $target;
    
    /**
    * @var string  The default namespace
    */
    protected $defaultNamespace;
    
    /**
    * @var array Associative array of namespaces and files
    */
    protected $picaMap;
    
    /**
    * Constructor
    *
    * @param string|DOMDocument $pathOrDom  Either a path to the XML file, a XML string or a DOMDocument
    *
    * @return DOMDocument                   Converted Pica-XML-DOMDocument
    */
    public function __construct()
    {
        $xsl_dir = realpath(__DIR__ . '/../res/xsl');
        $xsd_dir = realpath(__DIR__ . '/../res/xsd');
        $this->picaMap = [
            'info:srw/schema/5/picaXML-v1.0' => [
                'xsl' => $xsl_dir. DIRECTORY_SEPARATOR .'picaXMLToPpxml.xsl',
                'xsd' => $xsd_dir. DIRECTORY_SEPARATOR .'pica-xml-v1-0.xsd'
            ],
            'http://www.oclcpica.org/xmlns/ppxml-1.0' => [
                'xsl' => $xsl_dir. DIRECTORY_SEPARATOR .'ppxmlToPicaXML.xsl',
                'xsd' => $xsd_dir. DIRECTORY_SEPARATOR .'ppxml-1.0.xsd'
            ]
        ];
    }
    /**
    * Get the source
    * @return DOMDocument|null   The source
    */
    public function getSource()
    {
        return ($this->source) ? $this->source : null;
    }
    
    /**
    * Get the target
    * @return DOMDocument|null   The target
    */
    public function getTarget()
    {
        return ($this->target) ? $this->target : null;
    }
    
    /**
     * Get the default namespace
     *
     * @return string   The default namespace
     */
    public function getNamespace()
    {
        return ($this->defaultNamespace) ? $this->defaultNamespace : null;
    }
    
    /**
    * Converts picaXML and ppxml vice versa.
    * 
    * @param bool|string|DOMDocument $pathOrXMLOrDom   Either a path to the XML file, a XML string or a DOMDocument
    *
    * @return DOMDocument   The converted Pica-XML-DOMDocument
    *
    * @throws RuntimeException              If XML is not a valid Pica-XML.
    */
    public function convert($pathOrXMLOrDom = false)
    {
        if (!$pathOrXMLOrDom and is_null($this->getSource())) {
            throw new InvalidArgumentException("Either ".get_class($this)."::\$source must be set or a valid filepath, xml-string or DOMDocument must be provides as parameter.");
        } elseif (!$pathOrXMLOrDom) {
            $pathOrXMLOrDom = $this->source;
        } else {
            $this->loadPica($pathOrXMLOrDom);
        }
        
        $this->validate();
        
        $xsl = new DOMDocument;
        $xsl->load($this->picaMap[$this->defaultNamespace]['xsl']);
        
        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);
        
        $this->target = $proc->transformToDoc($this->source);
        
        return $this->target->saveXML();
    }
    
    /**
    * Loads Pica (as file, string or DOMDocument)
    *
    * @param string|DOMDocument $pathOrDom  Either a path to the XML file, a XML string or a DOMDocument
    *
    * @throws RuntimeException              If file can't be found
    * @throws DOMException                  If file can't be loaded
    */
    protected function loadPica($pathOrXMLOrDom)
    {
        $this->target = null;
        
        if (!($pathOrXMLOrDom instanceof DOMDocument)) {
            /** handle Errors coming from libxml */
            set_error_handler(['CK\PicaXMLConv\PicaXMLConv','handleXmlError']);

            $this->source = new DOMDocument;

            if (substr($pathOrXMLOrDom, 0, 5) == "<?xml") {
                $this->source->loadXML($pathOrXMLOrDom, LIBXML_PARSEHUGE);
            } else {
                if (!$realPathToXML = realpath($pathOrXMLOrDom)) {
                    throw new RuntimeException('File not found.');
                }
                $this->source->load($realPathToXML);
            }
            restore_error_handler();
        } else {
            $this->source = $pathOrXMLOrDom;
        }
        
        foreach (array_keys($this->picaMap) as $ns) {
            if (count($this->source->getElementsByTagNameNS($ns, 'record')) > 0) {
                $this->defaultNamespace = $ns;
            } else {
                throw new RuntimeException('No known namespace used in XML.');
            }
        }
    }
    
    /**
    * Validate Pica-XML against its schema
    *
    * @param bool|string|DOMDocument $pica   Either a path to the XML file, a XML string or a DOMDocument
    *
    * @return bool True if valid or false if not.
    *
    * @throws RuntimeException  If the file does not validate against one of the schemas
    */
    public function validate($pica = false)
    {
        if (!$pica and is_null($this->getSource())) {
            throw new InvalidArgumentException("Either ".get_class($this)."::\$source must be set or a valid filepath, xml-string or DOMDocument must be provides as parameter.");
        } elseif (!$pica) {
            $pica = $this->source;
        } else {
            $this->loadPica($pica);
        }

        libxml_use_internal_errors(true);
        foreach ($this->picaMap as $namespace => $res) {
            if ($this->source->schemaValidate($res['xsd'])) {
                libxml_clear_errors();
                $this->defaultNamespace = $namespace;
                return true;
            }
            return false;
        }
    }
    
    public static function handleXmlError($errno, $errstr, $errfile, $errline)
    {
        if ($errno) {
            throw new DOMException($errstr);
        } else {
            return false;
        }
    }
}
