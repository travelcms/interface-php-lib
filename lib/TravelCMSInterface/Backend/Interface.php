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
interface TravelCMSInterface_Backend_Interface
{
    /**
     * 
     */
    public function setHost($host);

    /**
     * 
     */
    public function setUsername($username);

    /**
     * 
     */
    public function setPassword($password);

    /**
     * 
     */
    public function getRequest($path);

    /**
     *
     */
    public function postRequest($path, $body);

    /**
     * 
     */
    public function deleteRequest($path);
    
}