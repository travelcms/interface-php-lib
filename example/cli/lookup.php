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

$result = $tcms->get('catalogobject', 'lookup', array(
              'TourOperatorCode' => $argv[1],
              'BookingCode' => $argv[2]
            ));
            
if ($result === false)
  echo $tcms->getLastError(); /* contains error if result === false */
else {
  
	echo("Object information:\n\n");
	
	echo('Catalog: '.$result->response->catalog->name."\n");
	echo('Name: '.$result->response->object->name."\n");
	
	foreach ($result->response->object->bookingcodes->bookingcode as $bc)
	  echo('BC: '.$bc."\n");
	
	$text = '';
  foreach ($result->response->object->texts->textsegment as $text)
  {
  	if (trim($text['title']) != '')
  	  $text .= $text['title'].":\n";
  	$text .= $text."\n\n";  
  }
  $text = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), "\n", $text));
  
  echo("Text: \n".$text);	
}

