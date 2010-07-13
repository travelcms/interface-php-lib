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
class TravelCMSInterface_Backend_FSockOpen 
  implements TravelCMSInterface_Backend_Interface
{
    private $_host = 'interface.travelcms.de';
    private $_username;
    private $_password;  
    
    public function setHost($host)
    {   
        $this->_host = $host;
    }
    
    public function setUsername($username)
    {   
        $this->_username = $username;
    }
    
    public function setPassword($password)
    {  
        $this->_password = $password;
    } 
  
    public function getRequest($path)
    {     
        $auth = base64_encode($this->_username.':'.$this->_password);
  
        $header =  'GET '.$path.' HTTP/1.0'."\r\n";
        $header .= 'Host: '.$this->_host."\r\n";
        $header .= 'Content-type: application/x-www-form-urlencoded'."\r\n";
        $header .= 'Connection: close'."\r\n";
        $header .= 'Authorization: Basic '.$auth."\r\n";
        $header .= "\r\n";    
      
        return $this->request($this->host, 80, $header);
    }  
    
    public function postRequest($path, $body)
    { 
        $auth = base64_encode($this->_username.':'.$this->_password);
  
        $header =  'POST '.$path.' HTTP/1.0'."\r\n";
        $header .= 'Host: '.$this->_host."\r\n";
        $header .= 'Content-type: application/x-www-form-urlencoded'."\r\n";
        $header .= 'Content-length: '.strlen($body)."\r\n";
        $header .= 'Connection: close'."\r\n";
        $header .= 'Authorization: Basic '.$auth."\r\n";
        $header .= "\r\n";    
      
        return $this->request($this->host, 80, $header, $body); 
    }
    
    public function deleteRequest($path)
    {
        $auth = base64_encode($this->_username.':'.$this->_password);
  
        $header =  'DELETE '.$path.' HTTP/1.0'."\r\n";
        $header .= 'Host: '.$this->_host."\r\n";
        $header .= 'Content-type: application/x-www-form-urlencoded'."\r\n";
        $header .= 'Connection: close'."\r\n";
        $header .= 'Authorization: Basic '.$auth."\r\n";
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
        while (!feof($connect)) {  
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