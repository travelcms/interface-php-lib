<?php
require_once 'PHPUnit/Framework.php';
 
class SecurityTest extends PHPUnit_Framework_TestCase
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

    public function testSpecialChars()
    {
      $this->backend->log = array();      
      $this->client->get('catalogobject', 12345, array(
          'p1' => 'to&p2=cat',
          'p3' => 'bc'
        ));
      $path = '/rest/catalogobject/DE/12345.xml?p1=to%26p2%3Dcat&p3=bc';
      $this->assertEquals('getRequest: '.$path, $this->backend->log[0]);    
    }       
    
}

