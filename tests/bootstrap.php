<?php 

$projectBaseDir = dirname($_SERVER['PWD']).'/';
    
set_include_path(get_include_path() . PATH_SEPARATOR . $projectBaseDir.'lib/');

require_once "TravelCMSInterface/Client.php";
require_once "TravelCMSInterface/Backend/Interface.php";