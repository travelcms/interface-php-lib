<?php 

define('PROJECTDIR', dirname(dirname($_SERVER['PWD'])).'/');
include "../_helper/init.php";

$tcms = new TravelCMSInterface_Client();
$tcms->setBackend(new TravelCMSInterface_Backend_Curl());
$tcms->setUsername(TCMSINTERFACEUSER);
$tcms->setPassword(TCMSINTERFACEPASSWORD);
$tcms->setLanguage(TCMSINTERFACELANGUAGE);
$tcms->setHost(TCMSINTERFACEHOST); /* optional */

if (count($argv) < 3)
  die("Usage:   php search.php [TourOperatorCode] [BookingCode]\n".
      "Example: php search.php TOC PMI3333\n");

$result = $tcms->getList('search', array(
              'PerPage' => 10, 
              'Page' => 1,
              'TourOperatorCode' => $argv[1],
              'BookingCode' => $argv[2]
            ));
            
if ($result === false)
  echo $tcms->getLastError(); /* contains error if result === false */
else {
  
	echo("Result: \n");
	foreach ($result->response->searchlist->catalogobject as $catalogObject)
	{
	  echo($catalogObject->object->name.' ['.$catalogObject['id'].'], '.
	       $catalogObject->catalog->name. "\n");
	}
	
}

