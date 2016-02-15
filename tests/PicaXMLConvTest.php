<?php
/**
 * PicaXMLConv converts PicaXML (namespace info:srw/schema/5/picaXML-v1.0) and
 * ppmxl (namespace http://www.oclcpica.org/xmlns/ppxml-1.0) vice versa.
 *
 * @author Carsten Klee <mailme.klee@yahoo.de>
 */
namespace CK\PicaXMLConv\Test;

use CK\PicaXMLConv\PicaXMLConv;
use DOMDocument;

/**
 * @covers CK\PicaXMLConv\PicaXMLConv
 */
class PicaXMLConvTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    protected $conv;
    
    public static function setUpBeforeClass()
    {
        self::$fixtures = realpath(__DIR__ . '/fixtures');
    }
    
    protected function setUp()
    {
        $this->conv = new PicaXMLConv;
    }
    
 
    public function testGetNullSource()
    {
        $this->assertNull($this->conv->getSource());
    }
    

    public function testGetNullNamespace()
    {
        $this->assertNull($this->conv->getNamespace());
    }
    /**
     * @expectedException RuntimeException
     */
    public function testFileNotFound()
    {
        $this->conv->convert('notExistingFile.xml');
    }
    
    /**
     * @expectedException DOMException
     */
    public function testFileNotReadable()
    {
        $fixture = $this->getFixture('notReadable');
        $this->conv->convert($fixture);
    }
    

    public function testGetSourcePicaXML()
    {
        $this->conv->convert($this->getFixture('picaXML'));
        $this->assertInstanceOf('DOMDocument', $this->conv->getSource());
    }
    
    public function testGetSourcePPXML()
    {
        $this->conv->convert($this->getFixture('ppxml'));
        $this->assertInstanceOf('DOMDocument', $this->conv->getSource());
    }
    

    public function testHasNamespace()
    {
        $this->conv->convert($this->getFixture('ppxml'));
        $this->assertEquals('http://www.oclcpica.org/xmlns/ppxml-1.0', $this->conv->getNamespace());
        
        $this->conv->convert($this->getFixture('picaXML'));
        $this->assertEquals('info:srw/schema/5/picaXML-v1.0', $this->conv->getNamespace());
    }
    
    public function testDom()
    {
        $dom = new DOMDocument;
        $dom->loadXML(file_get_contents($this->getFixture('ppxml')), LIBXML_PARSEHUGE);
        $this->conv->convert($dom);
        $this->assertInstanceOf('DOMDocument', $this->conv->getSource());
    }
    
    public function testString()
    {
        $xml = file_get_contents($this->getFixture('ppxml'));
        $this->conv->convert($xml);
        $this->assertInstanceOf('DOMDocument', $this->conv->getSource());
    }
    
    public function testValidate()
    {
        $valid = $this->conv->validate($this->getFixture('picaXML'));
        $this->assertTrue($valid);
    }
    
    public function testValidateHasSource()
    {
        $xml = file_get_contents($this->getFixture('ppxml'));
        $this->conv->convert($xml);
        $valid = $this->conv->validate($this->getFixture('picaXML'));
        $this->assertTrue($valid);
    }
    
    /**
    * @expectedException InvalidArgumentException
    */
    public function testValidateException()
    {
        $valid = $this->conv->validate(false);
    }
    
    public function testOutput()
    {
        $picaXML = $this->conv->convert($this->getFixture('ppxml'));
        $test = new PicaXMLConv;
        $valid = $test->validate($picaXML);
        $this->assertTrue($valid);
        $this->assertEquals('info:srw/schema/5/picaXML-v1.0', $test->getNamespace());
    }
    
    public function testReadmeExample()
    {
        $picaXML = $this->conv->convert($this->getFixture('ppxml_single_withcopies'));
        
        $this->assertSame('DOMDocument',get_class($picaXML));
        $picaXMLDom = new DOMDocument;
        $picaXMLDom->load($this->getFixture('picaXML_single_withcopies'));
        $this->assertSame(count($picaXML->getElementsByTagName('datafield')),count($picaXMLDom->getElementsByTagName('datafield')));
        
        $this->assertTrue($this->conv->validate($picaXMLDom));
        $this->assertSame('info:srw/schema/5/picaXML-v1.0',$this->conv->getNamespace());
        $this->conv->convert();
        $this->conv->validate($this->conv->getTarget());
        $this->assertSame('http://www.oclcpica.org/xmlns/ppxml-1.0',$this->conv->getNamespace());
    }
    
    protected function getFixture($fixture)
    {
        return self::$fixtures  . DIRECTORY_SEPARATOR .  "{$fixture}.xml";
    }
}
