<?php 

define('PROJECTDIR', dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))).'/');
include "../_helper/init.php";

htmlHeader();

$tcms = new TravelCMSInterface_Client();
$tcms->setBackend(new TravelCMSInterface_Backend_Curl());
$tcms->setUsername(TCMSINTERFACEUSER);
$tcms->setPassword(TCMSINTERFACEPASSWORD);
$tcms->setLanguage(TCMSINTERFACELANGUAGE);
$tcms->setHost(TCMSINTERFACEHOST); /* optional */

$result = $tcms->getList('touroperator', array('PerPage' => 200, 'Page' => 1));
if ($result === false)
  echo $tcms->getLastError(); /* contains error if result === false */
else {
  echo("Result: <br>");
  foreach ($result->response->touroperatorlist->touroperator as $touroperator)
  {
    echo('<a href="cataloglist.php?toid='.$touroperator['id'].'">'.$touroperator->name.
         '</a> ['.$touroperator['id'].'] <br>');
  }

}

htmlFooter();