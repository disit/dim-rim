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

class SiiMobilityIndexGeneratorController extends sm_ControllerElement
{
	protected $model;
	protected $view;
	
	function __construct()
	{
		
	}
	
	/**
	 * @desc Gets the Index Generator Editor View
	 *
	 * @url GET /IndexGenerator/Editor
	 * 
	 */
	function editor()
	{
		$data = array("ID"=>null,"title"=>null);
		$this->view = new SiiMobilityIndexGeneratorView($data);
		$this->view->setOp("editor");
	}
	
	/**
	 * @desc Gets the Index Generator Editor Help View
	 *
	 * @url GET /IndexGenerator/Editor/Help
	 * 
	 * @callback
	 */
	function editor_help()
	{
		$data['url']=SiiMobilityApp::getFolderUrl("help")."index.html";
		$this->view = new SiiMobilityIndexGeneratorView($data);
		$this->view->setOp("help");
	}
	
	/**
	 * @desc Gets the Index Generator Editor Welcome View
	 *
	 * @url GET /IndexGenerator/Editor/Welcome
	 *
	 * @callback
	 */
	function editor_welcome()
	{
		
		$this->view = new SiiMobilityIndexGeneratorView();
		$this->view->setOp("welcome");
	}
	
	
	/**
	 * @desc Gets the Index Generator Editor View for a Repository by id
	 *
	 * @url GET /IndexGenerator/Edit/:id
	 *
	 */
	function edit_repository($id=null)
	{
		$data = array("ID"=>$id,"title"=>null);
		if($id){
			$data = array("ID"=>$id,"title"=>null);
			$gen = new Generation();
			$gen->select($id);
			$title  = $gen->getRepositoryID();
			if($title=="")
				$title = "Generation ".$id." (No name)";
			$data['title']=$data['RepositoryID']=$title;
			$data["index"]=$gen;
			$this->editor();
			$this->view->setModel($data);
		}
		else 
			sm_send_error("400");
		
	}
	
	/**
	 * @desc Gets the Index Generator Editor View for a Repository by id
	 *
	 * @url GET /IndexGenerator/Validate/:id
	 *
	 * @callback
	 */
	function edit_validate($id=null)
	{
		if($id){
			
			$validator = new SiiMobilityIndexValidator();
			$index = new SiiMobilityRepository();
			$index->load($id);
			
			if($validator->validate($index,SECURITY)){
				$data["result"] = true;
				$data['items']=array();
			}
			else {
				$data["result"]=false;
				$data['items'] = $validator->getValidationResult();
			
				//$data['RemoveLink']=array('name'=>'RemoveTBWSelectedItem','title'=>'Remove Selection',"icon"=>"glyphicon glyphicon-remove","label"=>" Remove","data-confirm"=>"Are you sure you want to remove the selecteds items?");
				$data['RevalidateLink']="IndexGenerator/Validate/".$id;
					
			}
			$data['index']=$index;
			$this->view = new SiiMobilityIndexGeneratorView($data);
			$this->view->setOp("validation");
		}
		else
			sm_send_error("400");
	
	}
	
	/**
	 * @desc Gets Open Repository Wiew
	 *
	 * @url GET /IndexGenerator/Open
	 *
	 */
	function open_repository()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$_data["generations"] = $RepositoryManager->getGenerations();
		$data=array();
		foreach($_data['generations'] as $i=>$v)
		{
			
			//
			$data[]=array(
						"link"=>'IndexGenerator/Edit/'.$v['ID'],
						"title"=>$v['RepositoryID'],
						"description"=>$v['Description'],
						"type"=>strtolower($v['Type']),
			);
			//
			
		}
		$this->view = new SiiMobilityIndexGeneratorView($data);
		$this->view->setOp("open");
		
		
		
	}
	
	/**
	 * @desc Gets the Generations Archive
	 *
	 * @url GET /IndexGenerator/Editor/Archive
	 *
	 */
	function generations_archive()
	{
		$keywords="";
		if(isset($_SESSION['IndexGenerator/Editor/Archive']['keywords']))
			$keywords=$_SESSION['IndexGenerator/Editor/Archive']['keywords'];
		$where=array();
		if($keywords!="")
		{
			$keys = explode(" ",$keywords);
			foreach($keys as $k){
				if($k!="")
					$where[$k]="(repositoryID like '%".$k."%')";
			}
	
		}
		if(count($where)>0)
			$where="where ".implode(" AND ",$where);
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$_totalRows=$RepositoryManager->getAllCountGenerations($where);
		$pager = new sm_Pager("IndexGenerator/Editor/Archive");
		$pager->set_total($_totalRows);
	
		$data["generations"] = $RepositoryManager->getGenerations($where,$pager->get_limit());
		foreach($data['generations'] as $i=>$v)
		{
			$data['generations'][$i]['SecurityLevel']=IndexSecurityLevels::toString($data['generations'][$i]['SecurityLevel']);
			$data['generations'][$i]['actions']['html']=array("id"=>"repository-html-".$v['ID'],"title"=>"Html Repository Summary","url"=>' IndexGenerator/Html/'.$v['ID'],"data"=>"<i class=html-icon></i>","method"=>"GET");
			$data['generations'][$i]['actions']['export']=array("id"=>"repository-export-".$v['ID'],"title"=>"Export Repository","url"=>' IndexGenerator/Export/'.$v['ID'],"data"=>"<i class=xml-icon></i>","method"=>"GET");
			//if(sm_ACL::checkPermission("IndexEditor::Delete"))
			$data['generations'][$i]['actions']['edit']=array("id"=>"repository-edit-".$v['ID'],"title"=>"Edit Repository","url"=>' IndexGenerator/Edit/'.$v['ID'],"data"=>"<i class=edit-icon></i>","method"=>"GET");
			//if(sm_ACL::checkPermission("IndexEditor::Delete"))
			$data['generations'][$i]['actions']['delete']=array("id"=>"repository-delete-".$v['ID'],"title"=>"Delete Repository","class"=>"confirm","message"=>"Are you sure you want to delete this repository?","url"=>'IndexGenerator/Editor/Archive/Delete/'.$v['ID'],"data"=>"<i class=delete-icon></i>","method"=>"POST");
		}
		//if(sm_ACL::checkPermission("RepositoryManager::Edit"))
		{
			$data['seletectedCmd']=array();
			$data['seletectedCmd']['DeleteTBWSelectedGenerations']=array('name'=>'DeleteTBWSelectedGenerations','title'=>'Delete Selection',"icon"=>"glyphicon glyphicon-trash","data-confirm"=>"Are you sure you want to delete the selecteds items?");
				
		}
	
		$data["total"]=$_totalRows;
		$data['pager'] = $pager;
		$data['keywords']=$keywords;
		$data['title'] = "Archive";
		$this->view = new SiiMobilityIndexGeneratorView($data);
		$this->view->setOp("generations");
	}
	
	/**
	 * @desc Delete a generation from Archive callback
	 *
	 * @url POST /IndexGenerator/Editor/Archive/Delete
	 *
	 * @callback
	 */
	function generations_archive_delete_cbk($id=null)
	{
		$value=false;
		if(isset($id))
		{
			$_id = array_keys($id);
			$RepositoryManager = new SiiMobilityRepositoryManager();
			$value = $RepositoryManager->deleteRepository($_id[0])?true:false;
		}
		else
			$value = false;
		$this->view= new SiiMobilityJSONView($value);
	}
	
	/**
	 * @desc Delete a generation from Archive
	 *
	 * @url POST  /IndexGenerator/Editor/Archive/Delete/:id
	 *
	 */
	function generations_archive_delete($id=null)
	{
		if(isset($id) && is_numeric($id))
		{
			$RepositoryManager = new SiiMobilityRepositoryManager();
			if ($RepositoryManager->deleteRepository($id))
				sm_set_message("Index Generator: Generation #".$id." deleted successfully!");
			else
				sm_set_error(sm_Database::getInstance()->getError());
	
		}
		else
			sm_set_error("Invalid data");
	
		sm_app_redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * @desc Gets Open Repository Properties View
	 *
	 * @url GET /IndexGenerator/Properties/:id
	 * 
	 * @callback
	 */
	function generation_properties($id=null)
	{
		if($id)
		{
			$generation = new Generation();
			$generation->select($id);
			$this->view = new SiiMobilityIndexGeneratorView(array("generation"=>$generation));
			$this->view->setOp("properties");
		}
		else 
			sm_send_error("400");

	}
	
	/**
	 * @desc Post Properties Update
	 *
	 * @url POST /IndexGenerator/Properties/:id
	 * @callback
	 * 
	 */
	function post_generation_properties($id=null,$data)
	{
		if($id)
		{
			$generation = new Generation();
			$generation->select($id);
			$generation->setRepositoryID($data['repositoryID']);
			$generation->setDescription($data['description']);
			$generation->setType($data['type']);
			$generation->setSecurityLevel(end($data['SecurityLevel']));
			$generation->update();
			$response = array(
					"message"=>array("Properties Successfully Updated")
			);
			//$this->view = new SiiMobilityJSONView($response);
			
		}
		else
			$response = array(
					"errors"=>array("404")
			);
		$this->view = new SiiMobilityJSONView($response);
	
	}
	
	/**
	 * @desc Gets New Repository View
	 *
	 * @url GET /IndexGenerator/New
	 *
	 */
	function new_repository($mode=null)
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$data=array();
		$where="where repositoryID!=''";
			$data["generations"] = $RepositoryManager->getGenerations($where);		
		$this->view = new SiiMobilityIndexGeneratorView($data);
		$this->view->setOp("new");
	}
		
	/**
	 * @desc Post New Repository Data
	 *
	 * @url POST /IndexGenerator/New
	 *
	 */
	function post_new_repository($data)
	{
		
			$rep = new SiiMobilityRepository();
			$mode = strtolower($data['mode']);
			switch ($mode)
			{
				case "copy":
					$rep->copyRepository($data['parentID']);
					$rep->setRepositoryID($data['repositoryID']);
					$rep->setDescription($data['description']);
					$rep->setType($data['type']);
					$rep->setSecurityLevel(end($data['SecurityLevel']));
					$rep->_copy();
				break;
				
				case "clone":
					$rep->cloneRepository($data['parentID']);
					$parentID = $rep->getParentID();
					$rep->setRepositoryID($data['repositoryID']);
					if($data['description']!="")
					{
						$description = $data['description']."\n\n ###### ".$parentID." ###### \n".$rep->getDescription();
						$rep->setDescription($description);
					}
					$rep->_clone();
				break;
				
				default:
					$rep->setRepositoryID($data['repositoryID']);
					$rep->setDescription($data['description']);
					$rep->setType($data['type']);
					$rep->setSecurityLevel(end($data['SecurityLevel']));
					$rep->save();
				break;
			}
			$redirection="IndexGenerator/Edit/".$rep->getID();
			sm_app_redirect($redirection);
	}
	
	/**
	 * @desc Import a Repository
	 *
	 * @url POST /IndexGenerator/Import
	 *
	 */
	function post_import_repository($data)
	{
		$repos = new SiiMobilityRepository();
		$xml=null;
		if(isset($data['file']['tmp_name']) && $data['file']['type'] == "text/xml")
		{
			$xml = file_get_contents($data['file']['tmp_name']);
		}
		else if(isset($data['xmltext']))
		{
			$xml = $data['xmltext'];
		}
		if(empty($xml))
		{
			sm_set_error("XML data not valid");
			$redirection = "IndexGenerator/Editor";
			if(isset($_SERVER['HTTP_REFERER']))
				$redirection = $_SERVER['HTTP_REFERER'];
		}
		else 
		{
			$repos->importXML($xml);
			if(isset($data['repositoryID']) && !empty($data['repositoryID']))
				$repos->setRepositoryID($data['repositoryID']);
			if(isset($data['description']) && !empty($data['description']))
			{
				$description = $data['description']." - ".$repos->getDescription();
				$repos->setDescription($description);
			}
			$repos->save();
			if($repos->getID()>0)
				sm_set_message("Repository ".$repos->getRepositoryID()." imported successfully!");
			$redirection="IndexGenerator/Edit/".$repos->getID();
		}
		sm_app_redirect($redirection);
	}
	
	/**
	 * @desc Import a Repository
	 *
	 * @url GET /IndexGenerator/Import
	 *
	 */
	function import_repository()
	{
		$this->view = new SiiMobilityIndexGeneratorView();
		$this->view->setOp("import");
	}
	
	/**
	 * @desc Gets DataInfo
	 *
	 * @url GET /IndexGenerator/Html/:id
	 *
	 * @callback
	 */
	function html($id=null){
		$index = new SiiMobilityRepository();
		$index->load($id);
		$xslt = new XSLT_Processor();
		$xslt->setSchemaDir("/tmp/");
		$xsltFile=SiiMobilityApp::getFolder("xsl")."html.xsl";
		$xml=$xslt->mapString($index->toXMLString(), $xsltFile);
		if($xml)
		{
			header('Content-Description: File Transfer');
			header('Content-Type: text/html');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="'.$index->getRepositoryID().'.html"');
			echo $xml;			
		}
			
		else
			sm_send_error("400");
		exit();
	}
	
	
	
	/**
	 * @desc Gets the Generations
	 *
	 * @url GET /IndexGenerator/Generations
	 *
	 * @callback
	 */
	function generations()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getAllGenerations());
	}
	
	/**
	 * @desc Gets DataInfo
	 *
	 * @url GET /IndexGenerator/DataInfo/:id
	 *
	 * @callback
	 */
	function dataInfo($id=null)
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getDataInfo($id));
	}
	
	/**
	 * @desc Gets Ontologies
	 *
	 * @url GET /IndexGenerator/Ontologies
	 *
	 * @callback
	 */
	function ontologies()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getAllOntologies($_GET));
	}
	
	/**
	 * @desc Gets the RealTimeData
	 *
	 * @url GET /IndexGenerator/RealTimeData
	 *
	 * @callback
	 */
	function realTimeData()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getAllRealTimeData($_GET));
	}
	
	/**
	 * @desc Gets the StaticData
	 *
	 * @url GET /IndexGenerator/StaticData
	 *
	 * @callback
	 */
	function staticData()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getAllStaticData($_GET));
	}
	
	/**
	 * @desc Gets the Reconciliations
	 *
	 * @url GET /IndexGenerator/Reconciliations
	 *
	 * @callback
	 */
	function reconciliationsData()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getAllReconciliationsData($_GET));
	}
	
	/**
	 * @desc Gets the Enrichments
	 *
	 * @url GET /IndexGenerator/Enrichments
	 *
	 * @callback
	 */
	function enrichmentsData()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->getAllEnrichmentsData($_GET));
	}
	
	/**
	 * @desc Sets the Status
	 *
	 * @url GET /IndexGenerator/Status
	 *
	 * @callback
	 */
	function setStatus()
	{
		$RepositoryManager = new SiiMobilityRepositoryManager();
		$this->view = new SiiMobilityJSONView($RepositoryManager->setStatus($_GET));
	}
	
	/**
	 * @desc Generate the SiiMobility Index Generation Script
	 *
	 * @url GET IndexGenerator/Script/:id
	 *
	 * @callback
	 */
	function makeScript($id=null)
	{
		$man = new SiiMobilityRepositoryManager();
		$index = new SiiMobilityRepository();
		$index->load($id);
		$validator = new SiiMobilityIndexValidator();
		if($validator->validate($index,SECURITY)){
			$ret = $man->doScript($index);
			$this->view = new SiiMobilityIndexGeneratorView($ret);
			$this->view->setOp("script");
			
		}
		else {
			$ret = $validator->getValidationResult();
			$data['result']=false;
			$data['items']=$ret;
			$data['index']=$index;
			$this->view = new SiiMobilityIndexGeneratorView($data);
			$this->view->setOp("validationError");
		}
		
	}
	
	
	/**
	 * @desc Get an XML of a repository
	 *
	 * @url GET IndexGenerator/Export/:id
	 *
	 */
	function export($id=null)
	{
		if($id)
		{
			$rep = new SiiMobilityRepository();
			$rep->load($id);
			$xml = $rep->toXMLString();	
			if($xml)
			{
				header('Content-Type: application/xml');
				header('Content-Disposition: attachment; filename="'.$rep->getRepositoryID().'.xml"');
				echo $xml;			
			}
			
		}
		else
			sm_send_error("400");
		exit();
	}
	
	
	
	
	
	/**
	 * @desc Install menu item 
	 * @param sm_MenuManager $menu
	 */
	static function menu(sm_MenuManager $menu)
	{
		$menu->setMainLink("Index Editor",'IndexGenerator/Editor',"pencil");
	}
}