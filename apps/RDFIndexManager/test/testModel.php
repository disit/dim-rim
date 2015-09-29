<?php
chdir("../../../");
include 'system/functions.inc.php';
include 'system/config.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
spl_autoload_register('sm_autoloader');


$format = 'y/m/d H:i:s';
$date = DateTime::createFromFormat($format, '13/04/15 14:27:24');
echo "Format: $format; " . $date->format('Y-m-d H:i:s') . "\n";

exit();

$id = isset($_GET['id'])?$_GET['id']:147;
$rep = new SiiMobilityRepository();
$rep->cloneRepository($id);
exit();

//echo (__DIR__."\\test.xml");


$xml = file_get_contents(__DIR__."\\test.xml");
$rep = new SiiMobilityRepository();
//$rep->save();
$rep->importXML($xml);
$rep->save();
echo($rep->toXMLString());
