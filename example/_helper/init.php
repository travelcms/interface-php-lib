<?php 

set_include_path(get_include_path() . PATH_SEPARATOR . PROJECTDIR.'lib/');

require_once PROJECTDIR."example/config.inc.php";

require_once "TravelCMSInterface/Client.php";
require_once "TravelCMSInterface/Backend/Interface.php";
require_once "TravelCMSInterface/Backend/Curl.php";
require_once "TravelCMSInterface/Backend/FSockOpen.php";

/*
  You could also use the Zend Autoloader:

  require_once "Zend/Loader/Autoloader.php";
  $autoloader = Zend_Loader_Autoloader::getInstance();
  $autoloader->registerNamespace('TravelCMSInterface_');
*/

function htmlHeader()
{
  echo('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>');
}

function htmlFooter()
{
  echo('</body></html>');
}
