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
class TravelCMSInterface_Backend_Curl 
  implements TravelCMSInterface_Backend_Interface
{
    private $_host = 'interface.travelcms.de';
    private $_username;
    private $_password; 
  
    function __construct() 
    {
        if (!function_exists(curl_init))
            throw new Exception('"curl" extension is missing, but needed for '.
                                'TravelCMS Interface Curl Backend');
    }  
  
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
        $url = sprintf(
            'http://%s:%s@%s%s',
            $this->_username,
            $this->_password,
            $this->_host,
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
        $url = sprintf('http://%s%s', $this->_host, $path);
  
        $auth = base64_encode($this->_username.':'.$this->_password);
        
        $headers = array(
            'POST '.$path.' HTTP/1.1',
            'Host: '.$this->_host,
            'Content-type: application/x-www-form-urlencoded',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',      
            'Content-length: '.strlen($body),
            'Authorization: Basic '.$auth
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
        $url = sprintf('http://%s%s', $this->_host, $path);
        
        $auth = base64_encode($this->_username.':'.$this->_password);
        
        $headers = array(
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',      
            'Authorization: Basic '.$auth
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