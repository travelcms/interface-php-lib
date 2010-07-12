<?php 

define('PROJECTDIR', dirname(dirname($_SERVER['PWD'])).'/');
include "../_helper/init.php";

$tcms = new TravelCMSInterface_Client();
$tcms->setBackend(new TravelCMSInterface_Backend_FSockOpen());
$tcms->setUsername(TCMSINTERFACEUSER);
$tcms->setPassword(TCMSINTERFACEPASSWORD);
$tcms->setLanguage(TCMSINTERFACELANGUAGE);
$tcms->setHost(TCMSINTERFACEHOST); /* optional */

$result = $tcms->getList('catalog');
            
if ($result === false)
  echo $tcms->getLastError(); /* contains error if result === false */
else {
  
	echo("Result: \n");
	foreach ($result->response->cataloglist->catalog as $catalog)
	  echo($catalog->name.' ['.$catalog['id'].']'."\n");
	
}

