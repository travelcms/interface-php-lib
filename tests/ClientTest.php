<?php
require_once 'PHPUnit/Framework.php';
 
class ClientTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $backend;
 
    protected function setUp()
    {
    	$this->backend = new TestBackend();
    	
      $this->client = new TravelCMSInterface_Client();
      $this->client->setBackend($this->backend);
      $this->client->setUsername('testuser');
      $this->client->setPassword('testpwd');
      $this->client->setLanguage('DE');        
    }	
	 
    public function testBackendSet()
    {
      $this->backend->log = array();   
      $this->client->setUsername('x1');
      $this->client->setPassword('x2');
      $this->client->setHost('x3');
      
      $this->assertEquals('setUsername: x1', $this->backend->log[0]);
      $this->assertEquals('setPassword: x2', $this->backend->log[1]);
      $this->assertEquals('setHost: x3', $this->backend->log[2]);
    }
    
    public function testUrl()
    {
    	$this->backend->log = array();    	
      $result = $this->client->get('catalogobject', 'lookup', array(
          'TourOperatorCode' => 'to',
          'BookingCode' => 'bc'
        ));
      $path = '/rest/catalogobject/DE/lookup.xml?TourOperatorCode=to&BookingCode=bc';
      $this->assertEquals('getRequest: '.$path, $this->backend->log[0]);
      $this->assertNotEquals(false, $result);      
      
      $this->backend->log = array();      
      $this->client->getList('catalogobject', array(
          'TourOperatorCode' => 'to',
          'BookingCode' => 'bc'
        ));
      $path = '/rest/catalogobject/DE/clist.xml?TourOperatorCode=to&BookingCode=bc';
      $this->assertEquals('getRequest: '.$path, $this->backend->log[0]);
      $this->assertNotEquals(false, $result);      

      $this->backend->log = array();      
      $this->client->getOwnList('catalogobject', array(
          'TourOperatorCode' => 'to',
          'BookingCode' => 'bc'
        ));
      $path = '/rest/catalogobject/DE/list.xml?TourOperatorCode=to&BookingCode=bc';
      $this->assertEquals('getRequest: '.$path, $this->backend->log[0]);
      $this->assertNotEquals(false, $result);
      
      $this->backend->log = array();      
      $this->client->postNew('catalogobject', '<xml></xml>');
      $this->assertEquals('postRequest: /rest/catalogobject/', $this->backend->log[0][0]);      
    }       
    
    public function testDelete()
    {
      $this->backend->log = array();  
      $this->backend->defaultResult = '<xml><result>12345</result></xml>';
      $result = $this->client->delete('object', 111);                 
      $this->assertNotEquals(false, $result);
      $this->assertEquals(12345, (int)$result->result);      
    }    
    
    public function testPostNew()
    {
    	$this->backend->log = array();
      
    	$xml = new XmlWriter();
      $xml->openMemory();
      $xml->startDocument('1.0', 'UTF-8');
      $xml->startElement('travelcms');
      $xml->startElement('object');
      $xml->writeElement('name', 'Example');
      $xml->endElement();
      $xml->endElement();
      $xmlStr = $xml->outputMemory(true);
      
      $result = $this->client->postNew('object', $xmlStr);   
      
      $this->assertNotEquals(false, $result);   
      $this->assertEquals('postRequest: /rest/object/', $this->backend->log[0][0]);
      $this->assertEquals($xmlStr, $this->backend->log[0][1]);
    }    
    
    public function testPostUpdate()
    {
      $this->backend->log = array();
      
      $xml = new XmlWriter();
      $xml->openMemory();
      $xml->startDocument('1.0', 'UTF-8');
      $xml->startElement('travelcms');
      $xml->startElement('object');
      $xml->writeElement('name', 'Example');
      $xml->endElement();
      $xml->endElement();
      $xmlStr = $xml->outputMemory(true);
      
      $result = $this->client->postUpdate('object', 111, $xmlStr);   
      
      $this->assertNotEquals(false, $result);  
      $this->assertEquals('postRequest: /rest/object/DE/111.xml', $this->backend->log[0][0]);
      $this->assertEquals($xmlStr, $this->backend->log[0][1]);    	
    }   	

    /**
     * Like testPostUpdate, but with a native XMLWriter object passed to 
     * postUpdate instead of a xml string. 
     */
    public function testPostUpdateXmlWriterNative()
    {
      $this->backend->log = array();
      
      $xml = new XmlWriter();
      $xml->openMemory();
      $xml->startDocument('1.0', 'UTF-8');
      $xml->startElement('travelcms');
      $xml->startElement('object');
      $xml->writeElement('name', 'Example');
      $xml->endElement();
      $xml->endElement();
      
      $result = $this->client->postUpdate('object', 111, $xml);   
      
      $this->assertNotEquals(false, $result);  
      $this->assertEquals('postRequest: /rest/object/DE/111.xml', $this->backend->log[0][0]);
      $this->assertEquals($xml->outputMemory(false), $this->backend->log[0][1]);      
    }         
    
    public function testErrorHandlingBasic()
    {
    	/* try do a request without a backend */
      $client = new TravelCMSInterface_Client();
      $result = $client->get('catalogobject', 111);           
      $this->assertEquals('0001: No backend defined', $client->getLastError());
      $this->assertFalse($result);
      
      /* try request without username/password */
      $client = new TravelCMSInterface_Client();
      $client->setBackend($this->backend);
      $result = $client->get('catalogobject', 111);
      $this->assertEquals('0002: No login defined', $client->getLastError());
      $this->assertFalse($result);
    }
    
    public function testErrorHandlingSimulatedResults()
    {      
      /* simmulate an non existing page error */
      $this->backend->log = array();  
      $this->backend->defaultResult = 'The document you requested did not exists.';
      $result = $this->client->get('catalogobject', 111);
      $this->assertEquals('0003: The document you requested did not exists', $this->client->getLastError());            
      $this->assertFalse($result);

      /* simulate an access error for delete */
      $this->backend->log = array();  
      $this->backend->defaultResult = 'access error';
      $result = $this->client->delete('catalogobject', 111);
      $this->assertEquals('0004: Access error, permissions missing', $this->client->getLastError());            
      $this->assertFalse($result);
      
      /* simmulate an non existing page error for delete */
      $this->backend->log = array();  
      $this->backend->defaultResult = 'The document you requested did not exists.';
      $result = $this->client->delete('object', 111);
      $this->assertEquals('0003: The document you requested did not exists', $this->client->getLastError());            
      $this->assertFalse($result);            
      
      /* simulate an access error for get* */
      $this->backend->log = array();  
      $this->backend->defaultResult = 'access error';
      $result = $this->client->get('catalogobject', 111);
      $this->assertEquals('0004: Access error, permissions missing', $this->client->getLastError());            
      $this->assertFalse($result);

      /* simulate an access error for post* */
      $this->backend->log = array();  
      $this->backend->defaultResult = 'access error';
      $result = $this->client->postUpdate('catalogobject', 111, '<xml></xml>');
      $this->assertEquals('0004: Access error, permissions missing', $this->client->getLastError());            
      $this->assertFalse($result);
                  
      /* simulate a broken result (xml) string returned from Backend for get*/
      $this->backend->log = array();  
      $this->backend->defaultResult = '<xml><error></tag></xml>';
      $result = $this->client->get('catalogobject', 111);
      $this->assertEquals('0005: Result is not a valid xml document', $this->client->getLastError());            
      $this->assertEquals('<xml><error></tag></xml>', $this->client->getLastResult());
      $this->assertFalse($result);

      /* simulate a broken result (xml) string returned from Backend for delete */
      $this->backend->log = array();  
      $this->backend->defaultResult = '<xml><error></tag></xml>';
      $result = $this->client->delete('catalogobject', 111);
      $this->assertEquals('0005: Result is not a valid xml document', $this->client->getLastError());            
      $this->assertEquals('<xml><error></tag></xml>', $this->client->getLastResult());
      $this->assertFalse($result);      

      /* simulate an empty string returned from Backend for delete */
      $this->backend->log = array();  
      $this->backend->defaultResult = '';
      $result = $this->client->delete('catalogobject', 111);
      $this->assertEquals('0005: Result is not a valid xml document', $this->client->getLastError());            
      $this->assertEquals('', $this->client->getLastResult());
      $this->assertFalse($result);            
      
      /* simulate a broken result (xml) string returned from Backend for post */
      $this->backend->log = array();  
      $this->backend->defaultResult = '<xml><error></tag></xml>';
      $result = $this->client->postNew('catalogobject', '<xml></xml>');
      $this->assertEquals('0005: Result is not a valid xml document', $this->client->getLastError());            
      $this->assertEquals('<xml><error></tag></xml>', $this->client->getLastResult());
      $this->assertFalse($result);

      /* body to send on write is not a valid xml string (if was string, not xml writer) */
      $this->backend->log = array();  
      $this->backend->defaultResult = '<xml><error></tag></xml>';
      $result = $this->client->postNew('catalogobject', '<xml></broken>');
      $this->assertEquals('0006: XML to send is invalid', $this->client->getLastError());            
      $this->assertFalse($result);
    }
    
    public function testErrorHandlingAuth()
    {   	      
      $this->backend->log = array();  
      $this->backend->defaultResult = 'Please enter login data';
      $result = $this->client->get('catalogobject', 111);
      $this->assertEquals('0007: Authentication failed', $this->client->getLastError());            
      $this->assertFalse($result);

      $this->backend->log = array();  
      $this->backend->defaultResult = 'Your account is not activated';
      $result = $this->client->get('catalogobject', 111);
      $this->assertEquals('0007: Authentication failed', $this->client->getLastError());            
      $this->assertFalse($result);
            
    	/* todo test everything with the highest warning level */
    }
}

/**
 * Testbackend. Doesn't send any data to the travelcms, but logs every
 * method call. Returns, if needed, fake results. 
 */
class TestBackend implements TravelCMSInterface_Backend_Interface
{
	/**
	 * Logs the method calls
	 * 
	 * @var array
	 */
	public $log;
	
	/**
	 * A valid xml document, to prevent a false from the simplexml loader every
   * time. We need a valid result, if the error wasn't proposed
   * 
   * @var string
   */
	public $defaultResult = '<xml><result></result></xml>';
	
	function __construct()
	{
	  $this->log = array();
	}
	
  public function setHost($host)
  {  
  	$this->log[] = 'setHost: '.$host;
  }
  
  public function setUsername($username)
  {  
  	$this->log[] = 'setUsername: '.$username;
  }
  
  public function setPassword($password)
  { 
  	$this->log[] = 'setPassword: '.$password; 
  }
    
  public function getRequest($path)
  {  
  	$this->log[] = 'getRequest: '.$path;
  	  	
  	return $this->defaultResult;
  }
  
  public function postRequest($path, $body)
  {
  	$this->log[] = array('postRequest: '.$path, $body);
  	
  	return $this->defaultResult;
  }
  
  public function deleteRequest($path)
  {
    $this->log[] = array('deleteRequest: '.$path, $body);
    
    return $this->defaultResult;  	 
  }
  
}