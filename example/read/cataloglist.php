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

$parameter = array('PerPage' => 200, 'Page' => 1);
if (isset($_GET['toid']) && is_numeric($_GET['toid']))
  $parameter['TourOperatorID'] = (int)$_GET['toid'];

$result = $tcms->getList('catalog', $parameter);
if ($result === false)
  echo $tcms->getLastError(); /* contains error if result === false */
else {
  echo("Result: <br>");
  foreach ($result->response->cataloglist->catalog as $catalog)
  {
    echo($catalog->name.'['.$catalog['id'].'] <br>');
  }

}

htmlFooter();