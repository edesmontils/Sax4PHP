<?php 
/**
 * This file contains the code for managing SAX via basic PHP XML Parser functions.
 * (see http://www.php.net/manual/en/ref.xml.php)
 *
 * PHP version 5.1
 *
 * @category   XML for PHP
 * @package    Sax4PHP
 * @version	   0.3
 * @author     Emmanuel Desmontils <emmanuel.desmontils@univ-nantes.fr> Original Author
 * @license    GNU GPL 
 * @copyright  (c) 2007 Emmanuel Desmontils
 * @link       http://sax4php.sourceforge.net/
 *
 *   This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

/**
 * DefaultHandler Class
 *
 * This class is the default handlers for majors types of events.
 *
 * basic usage:<code>
 *	class CompteurFormations extends SaxHandler {
 *	  function startElement($name, $att) {echo "<start name='$name'/>\n";}
 *	  function endElement($name) {echo "<end name='$name'/>\n";} 
 *	  function startDocument() {echo '<?xml version="1.0" encoding="ISO-8859-1"?><list>';}
 *	  function endDocument() {echo '</list>';}
 *	}
 * </code>
 *
 * @access   public
 */

class DefaultHandler {
  function __construct() {}
  function __destruct(){}
  
  //Basic PHP/SAX object to handle events
  final function startElementHandler($sax, $name, $att) {$this->startElement($name, $att);}
  final function endElementHandler($sax, $name) { $this->endElement($name);}
  final function piHandler($sax, $target, $content) { $this->processingInstruction($target, $content);}
  final function defaultHandler($sax, $data) { $this->node($data);}
  final function characterHandler($sax, $string) { $this->characters($string);}
  final function startDocumentHandler($sax) { $this->startDocument();}
  final function endDocumentHandler($sax) { $this->endDocument();}
  
  //Java like functions to manage SAX events
  
  //void startElement(String uri,String localName,String qName,Attributes atts) throws SAXException 
  function startElement($name, $att) {}
  
  //void endElement(String uri,String localName,String qName) throws SAXException
  function endElement($name) {}
  
  //void processingInstruction(String target, String data) throws SAXException
  function processingInstruction($target, $content) {}
  
  //Not a Java Method but usefull for SAX 2 :
  // * void comment(char[] ch, int start, int length) throws SAXException
  // * void startEntity(String name) throws SAXException ?
  // * void endEntity(String name) throws SAXException ?
  // * void startDTD(String name, String publicId, String systemId) throws SAXException ?
  // * void endDTD() throws SAXException ?
  function node($data) {}
  
  //void characters(char[] ch,int start,int length) throws SAXException
  //void ignorableWhitespace(char[] ch, int start, int length) throws SAXException
  //CDATA section (like in SAX 2):
  // * void startCDATA() throws SAXException
  // * void endCDATA() throws SAXException
  function characters($string) {}
  
  //void startDocument() throws SAXException
  function startDocument() {}
  
  //void endDocument() throws SAXException
  function endDocument(){}
} 

/**
 * SaxParser Class
 *
 * This class is the main class for parsing an XML string using a SAX Handler.
 *
 * basic usage:<code>
 * $xml = file_get_contents('myFile.xml');
 * $sax = new SaxParser(new mySaxHandler());
 * $sax->parse($xml);
 * </code>
 *
 * @access   public
 */

class SaxParser {
private $sax;
private $saxHandler;

	function __construct(DefaultHandler $saxHandler, $encoding = 'UTF-8') {
		$this->saxHandler = $saxHandler;
		$this->sax = xml_parser_create($encoding);
		xml_set_object($this->sax,$saxHandler);
    	xml_parser_set_option($this->sax,XML_OPTION_CASE_FOLDING, FALSE);
    	$this->setElementHandler('startElementHandler','endElementHandler');
		$this->setDefaultHandler('defaultHandler');
		$this->setCharacterHandler('characterHandler');
		$this->setPIHandler('piHandler');
	}
    function __destruct() {xml_parser_free($this->sax);}

	function setElementHandler($openElementHandler, $closeElementHandler) {
		xml_set_element_handler($this->sax,$openElementHandler,$closeElementHandler);}
	function setDefaultHandler($defaultHandler) {
		xml_set_default_handler($this->sax,$defaultHandler);}
	function setCharacterHandler($characterHandler){
		xml_set_character_data_handler ($this->sax, $characterHandler);}	
	function setPIHandler($piHandler){
	    xml_set_processing_instruction_handler ($this->sax, $piHandler);}
	
	function parse($xml) {
		$this->saxHandler->startDocumentHandler($this->sax);

    $fp = fopen($xml, "r");
    while($fileContent = fread($fp, 1024*1024*5)) {
        $error = !xml_parse($this->sax, $fileContent, feof($fp));
        if ($error) {
            #$xmlErreurString = xml_error_string(xml_get_error_code($this->sax));
            throw new SAXException($this->sax,xml_error_string(xml_get_error_code($this->sax)), 
                                        xml_get_error_code($this->sax));
        }
    }
		$this->saxHandler->endDocumentHandler($this->sax);
	}
}

/**
 * SAXException Class
 *
 * This class defines the SAX parser exception.
 *
 * @access   public
 */
class SAXException extends Exception {
	protected $lineNumber, $columnNumber;
	
  public function __construct($sax,$message = NULL, $code = 0) {
  	if ($code == 0) $code = xml_get_error_code($sax);
  	if ($message == NULL) $message = xml_error_string($code);
    parent::__construct($message, $code);
    $this->lineNumber = xml_get_current_line_number($sax);
    $this->columnNumber = xml_get_current_column_number($sax);
  }

  public function __toString() {
    return __CLASS__ . " >> [{$this->code}]:". 
                       " {$this->message} at ".
                       "{$this->lineNumber},{$this->columnNumber}\n";
  }
}?>