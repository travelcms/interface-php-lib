<?php 
/**
 * 
 * @package TravelCMSInterface
 */

/**
 * 
 * 
 * @author Leander Hanwald
 */
class TravelCMSInterface_Backend_Curl implements TravelCMSInterface_Backend_Interface
{

  private $host = 'interface.travelcms.de';
  private $username;
  private $password;	
	
  function __construct() 
  {
  	if (!function_exists(curl_init))
  	  throw new Exception('"curl" extension is missing, but needed for TravelCMS Interface Curl Backend');
  }  
  
  public function setHost($host)
  {   
    $this->host = $host;
  }
  
  public function setUsername($username)
  {   
    $this->username = $username;
  }
  
  public function setPassword($password)
  {  
    $this->password = $password;
  } 

  public function getRequest($path)
  {  
  	$url = sprintf(
  	    'http://%s:%s@%s%s',
  	    $this->username,
  	    $this->password,
  	    $this->host,
  	    $path
  	  );
  	
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_HEADER, 0);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	
  	return curl_exec($ch);
  }

  public function postRequest($path, $body)
  {    
    $url = sprintf('http://%s%s', $this->host, $path);

    $headers = array(
      'POST '.$path.' HTTP/1.1',
      'Host: '.$this->host,
      'Content-type: application/x-www-form-urlencoded',
      'Accept: text/xml',
      'Cache-Control: no-cache',
      'Pragma: no-cache',      
      'Content-length: '.strlen($body),
      'Authorization: Basic '.base64_encode($this->username.':'.$this->password)
    );       

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    return curl_exec($ch);  	
  }

  public function deleteRequest($path)
  {
    $url = sprintf('http://%s%s', $this->host, $path);

    $headers = array(
      'Accept: text/xml',
      'Cache-Control: no-cache',
      'Pragma: no-cache',      
      'Authorization: Basic '.base64_encode($this->username.':'.$this->password)
    );       

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    
    return curl_exec($ch);    
  }  
  
}