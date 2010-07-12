<?php 

define('PROJECTDIR', dirname(dirname($_SERVER['PWD'])).'/');
include "../_helper/init.php";

$tcms = new TravelCMSInterface_Client();
$tcms->setBackend(new TravelCMSInterface_Backend_Curl());
$tcms->setUsername(TCMSINTERFACEUSER);
$tcms->setPassword(TCMSINTERFACEPASSWORD);
$tcms->setLanguage(TCMSINTERFACELANGUAGE);
$tcms->setHost(TCMSINTERFACEHOST); /* optional */

/* First, we create a new object */

$xml = new XmlWriter();
$xml->openMemory();
$xml->startDocument('1.0', 'UTF-8');
$xml->startElement('travelcms');
  $xml->startElement('object');
    $xml->writeElement('name', 'Example');
  $xml->endElement();
$xml->endElement();

echo "\nSend xml...";
$result = $tcms->postNew('object', $xml);

if ($result === false)
  die($tcms->getLastError()."\n"); /* contains error if result === false */
echo " success, object id: ".$result->result."\n";


/* Next, we update that object with a new code */

$xml = new XmlWriter();
$xml->openMemory();
$xml->startDocument('1.0', 'UTF-8');
$xml->startElement('travelcms');
  $xml->startElement('object');
    $xml->writeElement('code', 'update: '.date('r'));
  $xml->endElement();
$xml->endElement();  

echo "Update object... ";
$result = $tcms->postUpdate('object', $result->result, $xml);

if ($result === false)
  echo $tcms->getLastError()."\n"; /* contains error if result === false */
echo " success\n\n";