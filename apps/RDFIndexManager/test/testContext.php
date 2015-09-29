<?php
chdir("../../../");
include 'system/functions.inc.php';
include 'system/config.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
spl_autoload_register('sm_autoloader');


function create_dataset_context(SiiMobilityRepository $index,$filename=null)
{
	if(!$folder)
		$folder = __DIR__."/";
	$tpl = new sm_Template();
	$tpl->newTemplate("context", __DIR__."/context.tpl.n3");
	$dc = $index->getDataCollection();
	//Static Data
	foreach($dc[1] as $d){
		$metadata[]=$d->getRawProperties();
		
	}
	//Realtime Data
	foreach($dc[2] as $d){
		$metadata[]=$d->getRawProperties();
	}
	$tpl->addTemplateDataRepeat("context", "context", $metadata);
	
	$context = $tpl->display("context");
	$file=fopen($folder."context.n3","w");
	fwrite($file,$context);
	fclose($file);
	return $context;
}

$mID=195;
$index = new SiiMobilityRepository();
$index->load($mID);

echo "<pre>".htmlspecialchars(create_dataset_context($index))."</pre>";