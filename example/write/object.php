<?php 

define('PROJECTDIR', dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))).'/');
include "../_helper/init.php";

htmlHeader();

$tcms = new TravelCMSInterface_Client();
$tcms->setBackend(new TravelCMSInterface_Backend_FSockOpen());
$tcms->setUsername(TCMSINTERFACEUSER);
$tcms->setPassword(TCMSINTERFACEPASSWORD);
$tcms->setLanguage(TCMSINTERFACELANGUAGE);
$tcms->setHost(TCMSINTERFACEHOST); /* optional */

if (isset($_GET['do']) && $_GET['do'] == 'delete')
{ 
  $result = $tcms->delete('object', (int)$_POST['id']);

  if ($result === false)
    echo('<p>An error occured: '.$tcms->getLastError()."</p>");    
}

if (isset($_GET['do']) && $_GET['do'] == 'new')
{
  $xml = new XmlWriter();
  $xml->openMemory();
  $xml->startDocument('1.0', 'UTF-8');
  $xml->startElement('travelcms');
    $xml->startElement('object');
      $xml->writeElement('name', $_POST['name']);
      $xml->writeElement('code', $_POST['code']);
    $xml->endElement();
  $xml->endElement();  
  
  $result = $tcms->postNew('object', $xml);

  if ($result === false)
    echo('<p>An error occured: '.$tcms->getLastError()."</p>");    
}

$result = $tcms->getOwnList('object', array('PerPage' => 200, 'Page' => 1));
if ($result === false)
  echo $tcms->getLastError(); /* contains error if result === false */
else {
  echo("<b>Your objects:</b><br>");
  
  echo('<table border="1px" style="margin-bottom: 20px;">');
  echo('<tr>');
  echo('  <th>Action</th>');
  echo('  <th>Name</th>');
  echo('  <th>Code</th>');
  echo('  <th>Created</th>');
  echo('</tr>');
  foreach ($result->response->objectlist->object as $object)
  {
  	echo('<tr>');
  	echo('<td><form action="?do=delete" method="POST"><input type="hidden" name="id" '.
  	           'value="'.$object['id'].'"><input type="submit" value="Delete"></form></td>');
    echo('<td>'.$object->name.'</td>');
    echo('<td>'.$object->code.'</td>');
    echo('<td>'.date('r', (int)$object->createdat).'</td>');
    echo('</tr>');    
  }
  echo('</table>');

}

echo("<b>Create new object:</b><br>");;
echo('<form action="?do=new" method="POST">');
echo('Name: <input type="text" name="name"><br>');
echo('Code: <input type="text" name="code"><br>');
echo('<input type="submit">');
echo('</form>');

htmlFooter();