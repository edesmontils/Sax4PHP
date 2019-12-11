<?php header('Content-type: text/xml');
include_once('Sax4PHP.php');

class MySaxHandler extends DefaultHandler {

  function startElement($name, $att) {echo "<start name='$name'/>\n";}
  function endElement($name) {echo "<end name='$name'/>\n";} 
  
  function startDocument() {echo "<list>\n";} 
  function endDocument() {echo "</list>\n";}
}

$xml = file_get_contents('test.xml');
$sax = new SaxParser(new MySaxHandler());
try {
	$sax->parse($xml);
}catch(SAXException $e){  
	echo "\n",$e;}catch(Exception $e) {
	echo "Default exception >>", $e;}?>