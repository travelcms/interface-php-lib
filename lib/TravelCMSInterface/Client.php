<?php 
/**
 * 
 * @package TravelCMSInterface
 */

/**
 * 
 * 
 * Possible errors (returned by getLastError): 
 * 0001: No backend defined
 * 0002: No login defined
 * 0003: The document you requested did not exists
 * 0004: Access error, permissions missing
 * 0005: Result is not a valid xml document
 * 0006: XML to send is invalid
 * 0007: Authentication failed
 * 
 * @author Leander Hanwald
 */
class TravelCMSInterface_Client
{
    /** 
     * 
     * @var string
     */
    private $_language = 'DE';

    /**
     * 
     * @var TravelCMSInterface_Backend_Interface
     */
    private $_backend = false;

    /**
     * 
     * @var array
     */
    private $_errorLog;

    /**
     * Contains the last got result of the interface, used for debugging
     * 
     * @var string
     */
    private $_lastResult = '';

    /**
     * 
     * @var boolean
     */
    private $_usernameGiven = false;

    /**
     * 
     * @var boolean
     */
    private $_passwordGiven = false;    

    function __construct() 
    {
        $this->_errorLog = array();
    }

    /**
     * 
     * @param TravelCMSInterface_Backend_Interface $backend
     * @return void
     */
    public function setBackend(TravelCMSInterface_Backend_Interface $backend)
    {
        $this->_backend = $backend;
    }
  
    /**
     * 
     * 
     * @param string $url
     * @return void
     */
    public function setHost($host)
    {
        $this->_backend->setHost($host);        
    }
  
    /**
     * 
     * @param string $username
     * @return void 
     */
    public function setUsername($username)
    {
        $this->_usernameGiven = true;           
        $this->_backend->setUsername($username);
    }
  
    /**
     * 
     * 
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->_passwordGiven = true;           
        $this->_backend->setPassword($password);
    }
  
    /**
     * 
     * 
     * @param string $language
     * @return void
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
    }
  
    /**
     * 
     * 
     * @param string $module
     * @param array $parameter
     * @return mixed false on error or simplexml object
     */
    public function getList($module, $parameter = array())
    {  
        $path = $this->getPath($module, 'clist', $parameter);
        return $this->getRequest($path);
    }
  
    /**
     * 
     * 
     * @param string $module
     * @param array $parameter
     * @return mixed false on error or simplexml object
     */
    public function getOwnList($module, $parameter = array())
    {  
        $path = $this->getPath($module, 'list', $parameter);
        return $this->getRequest($path);
    }  
  
    /**
     * 
     * 
     * @param string $module
     * @param integer $id
     * @return mixed false on error or simplexml object
     */
    public function get($module, $id, $parameter = array())
    {
        $path = $this->getPath($module, $id, $parameter);
        return $this->getRequest($path);
    }
  
    /**
     * 
     *     
     * @param string $module
     * @param mixerd $body
     * @return mixed false on error or simplexml object
     */
    public function postNew($module, $body)
    {
        $path = $this->getPath($module, false, array());
        return $this->postRequest($path, $body);
    }
    
    /**
     * 
     * @param string $module
     * @param integer $id
     * @param mixed $body
     * @return mixed false on error or simplexml object
     */
    public function postUpdate($module, $id, $body)
    {
        $path = $this->getPath($module, $id, array());
        return $this->postRequest($path, $body);
    }  
    
    /**
     * 
     * 
     * @param string $module
     * @param integer $id
     * @return mixed false on error or simplexml object
     */
    public function delete($module, $id)
    {
        $path = $this->getPath($module, $id, array());
        return $this->deleteRequest($path);
    }
    
    /**
     * 
     */
    public function getLastError()
    {
        return end($this->_errorLog);
    }
  
    public function getLastResult()
    {
        return $this->_lastResult;
    }
  
    /* ----------------------------------------------------------- */
  
    /**
     * Add a new error message to the errorlog array. 
     * Returns always false, to be given as return off the called function. 
     * 
     * Example: 
           * if ($this->_backend == false)
     *   return $this->addError('0001: No backend defined');
     *  
     * Would'nt it return false, the code would be much longer, like this:
     * if ($this->_backend == false)
     * {
     *   $this->addError('0001: No backend defined');
     *   return false;
     * }       
     * 
     * @param string $errorStr
     * @return boolean  always false
     */
    private function addError($errorStr)
    {
        $this->_errorLog[] = $errorStr;
        return false;
    }
  
    /**
     * 
     * @param string $path
     */
    function deleteRequest($path)
    {
        if ($this->_backend == false)
            return $this->addError('0001: No backend defined');
  
        if (!$this->_usernameGiven || !$this->_passwordGiven)
            return $this->addError('0002: No login defined');
  
        $result = $this->_backend->deleteRequest($path);
  
        return $this->parseResult($result);
    }
  
    /**
     * 
     * @param string $path
     * @param string $body
     */
    private function postRequest($path, $body)
    {
        if ($this->_backend == false)
            return $this->addError('0001: No backend defined');
  
        if (!$this->_usernameGiven || !$this->_passwordGiven)
            return $this->addError('0002: No login defined');
  
        if ($body instanceof XMLWriter)
            $body = $body->outputMemory(false);
  
        /* we only allow to send valid xml documents to the interface */
        $oldUseInternalErrors = libxml_use_internal_errors(true);
        $parsedBody = simplexml_load_string($body);   
        libxml_use_internal_errors($oldUseInternalErrors);     
        if ($parsedBody === false)
            return $this->addError('0006: XML to send is invalid');  
  
        $result = $this->_backend->postRequest($path, $body);
  
        return $this->parseResult($result);       
    }
  
    /**
     * 
     * @param string $path
     */
    private function getRequest($path)
    {
        if ($this->_backend == false)
            return $this->addError('0001: No backend defined');         
  
        if (!$this->_usernameGiven || !$this->_passwordGiven)
            return $this->addError('0002: No login defined');      
  
        $result = $this->_backend->getRequest($path);
  
        return $this->parseResult($result);
    }
  
    /**
     * 
     * 
     * @param string $result
     */
    private function parseResult($result)
    {
        $this->_lastResult = $result;     
  
        if ($result == 'The document you requested did not exists.')
            return $this->addError(
                '0003: The document you requested did not '.'exists'
            );  
  
        if ($result == 'access error' || 
            $result == '<error>Missing rights!</error>' ||
            $result == 'You not allowed to write data')
            return $this->addError('0004: Access error, permissions missing');
  
        $authError = array(
            'Please enter login data', 
            'Password is wrong',
            'User is wrong',
            'Not an InterfaceUser',
            'Your account is not activated',
            'User not found',        
        );
  
        if (in_array(trim($result), $authError))
            return $this->addError('0007: Authentication failed');
  
        $oldUseInternalErrors = libxml_use_internal_errors(true);            
        $result = simplexml_load_string($result);      
        libxml_use_internal_errors($oldUseInternalErrors);
  
        if ($result === false)
            return $this->addError('0005: Result is not a valid xml document');
  
        return $result; 
    }
  
    /**
     * 
     * 
     * @param string $module
     * @param string $page
     * @param array $parameter
     */
    private function getPath($module, $page, $parameter = array())
    {
        $parameterStr = '?';
        foreach ($parameter as $key=>$value)
            $parameterStr .= $key.'='.urlencode($value).'&';
        $parameterStr = substr($parameterStr, 0, -1);                    
  
        /* If page is false, no page should be given in the url, as it is a 
         * page-less request like a new-post. Also no language is to use. 
         * If a page is given, we need to attach the suffix .xml */
        $structure = '/rest/%s/%s/%s%s';
        if ($page !== false)
            $page .= '.xml'; 
        else
            $structure = '/rest/%s/';
  
        $path = sprintf(
            $structure,
            $module,
            $this->_language,
            $page,
            $parameterStr
        );
  
        return $path; 
    }
    
}