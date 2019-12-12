# Sax4PHP

## Description:

Sax4PHP is a PHP5 class to manage XML with a Java like SAX API [1, 2]. 

## Development Status: 

- [ ] Production/Stable
- [X] development/Unstable

## Version

0.6

## Intended Audience:

- Developers, 
- End Users/Desktop

## License: 

GNU General Public License (GNU GPL) v3.0

## Operating System:

All (hopefully!) :
- MS Windows (95/98/NT/2000/XP), 
- All BSD Platforms (FreeBSD/NetBSD/OpenBSD/Apple Mac OS X), 
- All POSIX

## Programming Languages: 

PHP5:
-  Classes
-  Basic PHP4&5 XML Parser functions (see [3])

## Code example in PHP5:


```php
<?php header('Content-type: text/xml');
include_once('Sax4PHP.php');

class MySaxHandler extends DefaultHandler {

  function startElement($name, $att) {
	echo "<start name='$name'/>\n";
  }
  
  function endElement($name) {
	echo "<end name='$name'/>\n";
  } 
  
  function startDocument() {
	echo "<list>\n";
  }
  
  function endDocument() {
	echo "</list>\n";
  }
}

$sax = new SaxParser(new MySaxHandler());
$sax->parse('myFile.xml');
?>
```

## References:

- [1] SAX : http://www.saxproject.org/ 
- [2] Java 1.5 SAX API : http://java.sun.com/j2se/1.5.0/docs/api/org/xml/sax/package-summary.html 
- [3] PHP XML Parser Functions : http://www.php.net/manual/en/ref.xml.php

## Contact


