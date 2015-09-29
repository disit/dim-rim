<?php
/* Data Ingestion Manager and RDF Indexing Manager (DIM-RIM).
   Copyright (C) 2015 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. */

include 'system/functions.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');
sm_no_cache();
session_start();
spl_autoload_register('sm_autoloader');
set_error_handler(array("sm_Logger","logErrorHandler"));
sm_Logger::$debug=true;
sm_Logger::$usedb=false;
$maxStep=5;
$page = new sm_Site();
$page->setTemplateId("main","installer.tpl.html");
$page->remove("title");
$page->insert("title","Welcome to smPHP");
$page->addJS("installer.js");
$page->addCSS("installer.css");
$installer=new sm_Installer($page);

if(!isset($_GET['step']) && !isset($_POST['step']))
	$_GET['step']=1;


if(isset($_GET['step']) && $_GET['step']==3)
{
	sm_Logger::$fileLog="SM_installer.log";
	sm_Logger::$showPrompt=false;
	sm_Logger::removeLog();
	sm_Logger::write("Click install to start!");
}


if(isset($_POST['step']))
{
	sm_get_error();
	if($_POST['step']==3)
	{
		sm_Logger::$fileLog="SM_installer.log";
		sm_Logger::$showPrompt=false;
	}
	$next = isset($_POST['nextstep'])?$_POST['nextstep']:null;
	if(!$installer->do_step($_POST['step'],$next))
		$installer->setRedirection("?step=".$_POST['step']);
	
}
else if(isset($_GET['step']) && ($_GET['step']==1 || ($_GET['step']>1 && $installer->termsAccepted())))
{
	$steps[]=array('step'=>1,"label"=>"Terms & Conditions");
	$steps[]=array('step'=>2,"label"=>"Database Settings");
	$steps[]=array('step'=>3,"label"=>"System Install");
	$steps[]=array('step'=>4,"label"=>"Admin Registration");
	$steps[]=array('step'=>5,"label"=>"Finish");
	$steps[$_GET['step']-1]['active']="active";
	$page->addTemplateDataRepeat("main", "breadcrumb", $steps);
	$percent = $_GET['step'] / $maxStep * 100;
	$page->insert("step",$_GET['step']);
	$page->insert("max_step",$maxStep);
	$page->insert("step_perc",$percent);
	$page->insert("step_str","Step ".$_GET['step']." of ".$maxStep);
	$html = new sm_HTML();
	$html->setTemplateId("step_".$_GET['step'],"installer.tpl.html");
	$page->insert('content',$html);
	$page->insert('picture',"pic_step_".$_GET['step']);
	$errors = sm_get_error();
	//$message = sm_get_message();
	if(count($errors) >0)
	{
		$html=new sm_HTML();
		$html->setTemplateId('message', 'ui.tpl.html');
		$html->insertArray(array(
				'type'=>'danger',
				'message'=>implode("<br>",$errors))
		);
		$page->insert('message',$html);
	}
	/*if(count($message) >0)
	{
		$html=new sm_HTML();
		$html->setTemplateId('message', 'ui.tpl.html');
		$html->insertArray(array(
				'type'=>'success',
				'message'=>implode("<br>",$message))
		);
		$page->insert('message',$html);
	}*/
	
}
else 
	$installer->setRedirection("?step=1");
	
$installer->handle();