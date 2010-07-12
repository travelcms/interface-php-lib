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
class TravelCMSInterface_Backend_FSockOpen implements TravelCMSInterface_Backend_Interface 
{
    
  private $host = 'interface.travelcms.de';
  private $username;
  private $password;  
  
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
    $header =  'GET '.$path.' HTTP/1.0'."\r\n";
    $header .= 'Host: '.$this->host."\r\n";
    $header .= 'Content-type: application/x-www-form-urlencoded'."\r\n";
    $header .= 'Connection: close'."\r\n";
    $header .= 'Authorization: Basic '.base64_encode($this->username.':'.$this->password)."\r\n";
    $header .= "\r\n";    
    
    return $this->request($this->host, 80, $header);
  }  
  
  public function postRequest($path, $body)
  { 
    $header =  'POST '.$path.' HTTP/1.0'."\r\n";
    $header .= 'Host: '.$this->host."\r\n";
    $header .= 'Content-type: application/x-www-form-urlencoded'."\r\n";
    $header .= 'Content-length: '.strlen($body)."\r\n";
    $header .= 'Connection: close'."\r\n";
    $header .= 'Authorization: Basic '.base64_encode($this->username.':'.$this->password)."\r\n";
    $header .= "\r\n";    
    
    return $this->request($this->host, 80, $header, $body);	
  }
  
  public function deleteRequest($path)
  {
    $header =  'DELETE '.$path.' HTTP/1.0'."\r\n";
    $header .= 'Host: '.$this->host."\r\n";
    $header .= 'Content-type: application/x-www-form-urlencoded'."\r\n";
    $header .= 'Connection: close'."\r\n";
    $header .= 'Authorization: Basic '.base64_encode($this->username.':'.$this->password)."\r\n";
    $header .= "\r\n";    
    
    return $this->request($this->host, 80, $header);
  }
  
  private function request($host, $port, $header, $body = '')
  {
    if (!$connect = fsockopen($host, $port))
      return false;

    if (fputs($connect, $header.$body) === FALSE) 
      return false;
    
    $resultBody = '';
    $resultHeader = '';
    
    $header = true; 
    while(!feof($connect))
    {  
      $buffer = fgets($connect, 128);
      
      if ($header)
        $resultHeader .= $buffer;
      else
        $resultBody .= $buffer;
      
      if ($header && $buffer == "\r\n")
        $header = false;
    }
    
    if (fclose($connect) === FALSE)
      return false;          

    return $resultBody;   	
  }
  
}