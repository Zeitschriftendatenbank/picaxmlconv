# Pica+ XML Converter

Converts PicaXML (namespace info:srw/schema/5/picaXML-v1.0) and ppxml (namespace http://www.oclcpica.org/xmlns/ppxml-1.0) vice versa.

## Installation

    composer require ck/picaxmlconv

## Usage

```php
require vendor/autoload.php;

$pconv = new PicaXMLConv;

$picaXML = $pconv->convert('/path/to/ppxml.xml');

print get_class($picaXML);          # print DOMDocument
print $picaXML->saveXML();          # prints XML

if($pconv->validate($picaXML)) {
    print $pconv->getNamespace();   # prints info:srw/schema/5/picaXML-v1.0
    $pconv->convert();
    $pconv->validate($pconv->getTarget());
    print $pconv->getNamespace();   # prints http://www.oclcpica.org/xmlns/ppxml-1.0
}
```

## API

- CK\PicaXMLConv::convert(string|DOMDocument)
- CK\PicaXMLConv::validate(string|DOMDocument)
- CK\PicaXMLConv::getSource()
- CK\PicaXMLConv::getTarget()
- CK\PicaXMLConv::getNamespace()

## Resources

### XSL
- [ppxml to picaXML](https://raw.githubusercontent.com/Zeitschriftendatenbank/picaxmlconv/master/res/xsl/ppxmlToPicaXML.xsl)
- [picaXML to ppxml](https://raw.githubusercontent.com/Zeitschriftendatenbank/php-marc-spec/master/res/xsl/picaXMLToPpxml.xsl)

### XSD
- [ppxml-1.0.xsd](https://raw.githubusercontent.com/Zeitschriftendatenbank/php-marc-spec/master/res/xsd/ppxml-1.0.xsd)
- [pica-xml-v1-0.xsd](https://raw.githubusercontent.com/Zeitschriftendatenbank/php-marc-spec/master/res/xsd/pica-xml-v1-0.xsd)