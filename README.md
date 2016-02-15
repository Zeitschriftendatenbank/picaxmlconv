# Pica+ XML Converter

Converts PicaXML (namespace info:srw/schema/5/picaXML-v1.0) and ppxml (namespace http://www.oclcpica.org/xmlns/ppxml-1.0) vice versa.

## Installation

    composer require ck/picaxmlconv

## Usage

```php
require vendor/autoload.php;

use CK\PicaXMLConv\PicaXMLConv;

$pconv = new PicaXMLConv;

$picaXML = $pconv->convert('/path/to/ppxml.xml');

print $picaXML;                                  # XML
print get_class($picaXML->getSource());          # DOMDocument


if($pconv->validate($picaXML)) {
    print $pconv->getNamespace();                # info:srw/schema/5/picaXML-v1.0
    $pconv->convert();
    $pconv->validate($pconv->getTarget());
    print $pconv->getNamespace();                # http://www.oclcpica.org/xmlns/ppxml-1.0
}
```

### Usage with HAB\Pica\Reader\PicaXmlReader

    composer require ck/picaxmlconv hab/picareader

```php
require vendor/autoload.php;

use CK\PicaXMLConv\PicaXMLConv;
use HAB\Pica\Reader\PicaXmlReader;

$pconv = new PicaXMLConv;
$picaXML = $pconv->convert('/path/to/ppxml.xml');

$reader = new PicaXmlReader;
$reader->open($picaXML);
while($record = $reader->read()) {
    ...
}
$reader->close();
```

## API

For ```CK\PicaXMLConv::convert``` and ```CK\PicaXMLConv::validate``` string is either XML or a valid file path.

- CK\PicaXMLConv::convert(string|DOMDocument)
- CK\PicaXMLConv::validate(string|DOMDocument)
- CK\PicaXMLConv::getSource()
- CK\PicaXMLConv::getTarget()
- CK\PicaXMLConv::getNamespace()

## Resources

### XSL
- [ppxml to picaXML](https://raw.githubusercontent.com/Zeitschriftendatenbank/picaxmlconv/master/res/xsl/ppxmlToPicaXML.xsl)
- [picaXML to ppxml](https://raw.githubusercontent.com/Zeitschriftendatenbank/picaxmlconv/master/res/xsl/picaXMLToPpxml.xsl)

### XSD
- [ppxml-1.0.xsd](https://raw.githubusercontent.com/Zeitschriftendatenbank/picaxmlconv/master/res/xsd/ppxml-1.0.xsd)
- [pica-xml-v1-0.xsd](https://raw.githubusercontent.com/Zeitschriftendatenbank/picaxmlconv/master/res/xsd/pica-xml-v1-0.xsd)