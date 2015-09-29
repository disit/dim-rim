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

class SiiMobilityRepositoryManagerView extends sm_ViewElement
{
	
	function __construct($data=NULL)
	{
		parent::__construct($data);
	
	}
	
	
	
	/**
	 * Create the HTML code for the module.
	 */
	public function build() {
	
		switch ($this->op)
		{		
			case 'Generations::list':
				$this->GenerationsArchive_build();
			break;
		
		}
	}
	
	public function GenerationsArchive_build()
	{
		$page = $this->uiView=new sm_Page("SiiMobilityGenerationsArchive");
		$page->setTitle(null);
		$confirmDlg=false;
		$id="generations_table";
	
		$table = new sm_TableDataView($id,$this->model);
		
		$header=0;
		$table->addHRow();
		if(count($this->model['generations'])>0)
		{
			$headers=array_keys($this->model['generations'][0]);
				
			foreach($headers as $l)
			{
				$header++;
				if($l=='actions')
					$table->addHeaderCell(ucfirst(str_replace("_"," ",$l)),"sorter-false");
				else
					$table->addHeaderCell(ucfirst(str_replace("_"," ",$l)));
			}
	
		}
			
		foreach ($this->model['generations'] as $k=>$value)
		{
			$table->addRow("",array("id"=>$value['ID']));
			foreach ($value as $l=>$v)
			{
				if($l=='actions' && is_array($v))
				{
					foreach($v as $i=>$action)
					{
						if(isset($action['class']) && preg_match('/confirm/', $action['class']))
						{
							$v[$i]['target']="#confirmArchiveCommand";
							$confirmDlg=true;
						}
					}
					$this->setTemplateId("actions_forms","ui.tpl.html");
					$this->tpl->addTemplateDataRepeat("actions_forms", 'action_form', $v);
					$v=$this->tpl->getTemplate("actions_forms");
	
				}
	
				$table->addCell($v);
			}
		}
		$table->setSortable();
		
		$filterElement['generations_search']=array("Search","", "generations_search", array('placeholder'=>"Search",'value'=>$this->model['keywords'],'class'=>'input-sm form-control'));
		$table->addFilter($filterElement);
	
		$panel = new sm_Panel();
		$dbUsed = sm_Config::get("SIIMOBILITYDBURL",null);
		$panel->setTitle($this->model['title']." (".$dbUsed.")");
		$panel->insert($table);
		$page->insert($panel);
		if($confirmDlg)
		{
			$dlg = new sm_Dialog("confirmArchiveCommand",CONFIRMATION_DLG);
			$dlg->setConfirmationFormClass("confirm");
			$panel->insert($dlg);
		}
	
		$page->addCss("siimobility.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
		$page->addJS("SiiMobilityRepositoryManager.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
	}
	
	
	
	/**
	 *
	 * @param sm_Event $event
	 */
	public function onFormAlter(sm_Event &$event)
	{
	
		$form = $event->getData();
		if(is_object($form) && is_a($form,"sm_Form") && $form->getName()=="generations_table")
		{
			$form->setSubmitMethod("generationsTableFormSubmit");
			return;
		}
		
	}
	
	/**
	 *
	 * @param array $data
	 */
	
	public function generationsTableFormSubmit($data)
	{
		$value=array();
	
		if(isset($data['generations_search']))
		{
			$value['keywords']=$data['generations_search'];
		}
		$_SESSION['RepositoryManager/Generations/Archive']=$value;
	}
	
	
	
}