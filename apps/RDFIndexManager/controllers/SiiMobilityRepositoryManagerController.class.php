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

class SiiMobilityRepositoryManagerController extends sm_ControllerElement
{
	/**
	 * @desc Gets the Generations Archive
	 *
	 * @url GET /RepositoryManager/Generations/Archive
	 *
	 */
	function generations_archive()
	{
		$keywords="";
		if(isset($_SESSION['RepositoryManager/Generations/Archive']['keywords']))
			$keywords=$_SESSION['RepositoryManager/Generations/Archive']['keywords'];
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
		$pager = new sm_Pager("RepositoryManager/Generations/Archive");
		$pager->set_total($_totalRows);
	
		$data["generations"] = $RepositoryManager->getGenerations($where,$pager->get_limit());
		foreach($data['generations'] as $i=>$v)
		{
			$data['generations'][$i]['SecurityLevel']=IndexSecurityLevels::toString($data['generations'][$i]['SecurityLevel']);
			$data['generations'][$i]['actions']['export']=array("id"=>"repository-export-".$v['ID'],"title"=>"Export Repository","url"=>' IndexGenerator/Export/'.$v['ID'],"data"=>"<i class=xml-icon></i>","method"=>"GET");
			//if(sm_ACL::checkPermission("IndexEditor::Delete"))
			$data['generations'][$i]['actions']['edit']=array("id"=>"repository-edit-".$v['ID'],"title"=>"Edit Repository","url"=>' IndexGenerator/Edit/'.$v['ID'],"data"=>"<i class=edit-icon></i>","method"=>"GET");
			//if(sm_ACL::checkPermission("IndexEditor::Delete"))
			$data['generations'][$i]['actions']['delete']=array("id"=>"repository-delete-".$v['ID'],"title"=>"Delete Repository","class"=>"confirm","message"=>"Are you sure you want to delete this repository?","url"=>'RepositoryManager/Generations/Archive/Delete/'.$v['ID'],"data"=>"<i class=delete-icon></i>","method"=>"POST");
		}
		//if(sm_ACL::checkPermission("RepositoryManager::Edit"))
		{
			$data['seletectedCmd']=array();
			$data['seletectedCmd']['DeleteTBWSelectedGenerations']=array('name'=>'DeleteTBWSelectedGenerations','title'=>'Delete Selection',"icon"=>"glyphicon glyphicon-trash","data-confirm"=>"Are you sure you want to delete the selecteds items?");
			
		}
	
		$data["total"]=$_totalRows;
		$data['pager'] = $pager;
		$data['keywords']=$keywords;
		$data['title'] = "Generations Archive";
		$this->view = new SiiMobilityRepositoryManagerView($data);
		$this->view->setOp("Generations::list");
	}
	
	/**
	 * @desc Delete a generation from Archive callback
	 *
	 * @url POST /RepositoryManager/Generations/Archive/Delete
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
	 * @url POST  /RepositoryManager/Generations/Archive/Delete/:id
	 *
	 */
	function generations_archive_delete($id=null)
	{
		if(isset($id) && is_numeric($id))
		{
			$RepositoryManager = new SiiMobilityRepositoryManager();
			if ($RepositoryManager->deleteRepository($id))
				sm_set_message("RepositoryManager: Generation #".$id." deleted successfully!");
			else
				sm_set_error(sm_Database::getInstance()->getError());
	
		}
		else
			sm_set_error("Invalid data");
	
		sm_app_redirect($_SERVER['HTTP_REFERER']);
	}	
}