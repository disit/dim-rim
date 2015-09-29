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

class SiiMobilityIndexGeneratorView extends sm_ViewElement
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
			case 'editor':
				$this->IndexEditor_build();
				break;
			case 'list':
				$this->IndexEditorArchive_build();
				break;
			case 'new':
				$this->IndexEditorNew_build();
				break;
			case 'import':
				$this->IndexEditorImport_build();
				break;
			case 'open':
				$this->IndexEditorArchive_build();
				break;
			case 'properties':
				$this->IndexEditorProperties_build();
				break;
			case 'help':
				$this->IndexEditorHelp_build();
				break;
			case 'welcome':
				$this->IndexEditorWelcome_build();
				break;
			case 'generations':
				$this->IndexEditorGenerations_build();
				break;
			case 'script':
				$this->IndexEditorScriptDone_build();
			break;
			case 'validationResult':
			case 'validationError':
				$this->IndexEditorValidationError_build();
			break;
			case 'validation':
				$this->IndexEditorValidation_build();
				break;
				
			
		}
	}
	
	public function IndexEditorWelcome_build()
	{
		$this->uiView = new sm_HTML(); 
		$welcome = new sm_HTML("WelcomePanel");
		$welcome->setTemplateId("welcome",SiiMobilityApp::getFolder("templates")."welcome.tpl.html");
		$this->uiView->insert("panel",$welcome);
	}
	
	public function IndexEditorHelp_build(){
		$url=$this->model['url'];
		$this->uiView = $panel = new sm_Panel("Help");
		$panel->setType("default");
		$panel->setTitle("Help");
		$html = new sm_HTML("Help");
		$html->insert("helpframe","<iframe src='".$url."' id='helpview' ></iframe>");
		$panel->insert($html);
	}
	
	public function IndexEditorArchive_build()
	{
		$this->IndexEditor_build();
		$ele = $this->uiView->getUIElement("index");
		$panel = new sm_Panel("OpenRepository");
		$panel->setType("default");
		$panel->setTitle("Open Index Descriptor");
		$grid = new sm_Grid("RepositoryExplorer");
		$icons=array();
		foreach($this->model as $m)
		{
			$icon = new sm_HTML();
			$icon->setTemplateId("iconizedlink",SiiMobilityApp::getFolder("templates")."iconizedlink.tpl.html");
			$icon->insertArray($m);
			$icons[]=$icon;
			if(count($icons)==12)
			{
				$grid->addRow($icons);
				$icons=array();
			}
			
		}
		if(count($icons)<12)
		{
		//	$w=array();
		//	$w=array_fill(0, count($icons), 1);
			$grid->addRow($icons); //,$w);
		}
		$layout=array(
				"xg"=>2,
				"lg"=>4,
				"md"=>4,
				"xs"=>6,
				);
		$grid->setResponsiveLayout($layout);
		/*
		$panel->setType("default");
		$panel->setTitle("Repository Archive");
		$dlg = new sm_HTML();
		$dlg->setTemplateId("newrepository",SiiMobilityApp::getFolder("templates")."newrepository.tpl.html");
		$dlg->insert("title", "Open Repository");
		$dlg->insert("body","");*/
		$panel->insert($grid);
		$ele->remove("introduction");
		$ele->insert("introduction",$panel);
		
	}
	public function IndexEditor_build()
	{
		$page = $this->uiView=new sm_Page("SiiMobilityIndexGenerator");
		$page->setTitle(null);
		
		$panel = new sm_Panel("SiiMobilityIndexEditor");
		if(isset($this->model['title']))		
			$panel->setTitle("Index Editor - ".$this->model['title']);
		else
			$panel->setTitle("Index Editor");
		$panel->icon("<i class=edit-icon></i>");
		$html = new sm_HTML('index');
		if(isset($this->model['ID']))
		{
			$html->setTemplateId("indexgenerator",SiiMobilityApp::getFolder("templates")."index.tpl.html");
			$html->insert("ID",$this->model['ID']);
			$html->insert("SecurityLevel",IndexSecurityLevels::toString($this->model['index']->getSecurityLevel()));
		}
		
		else 
		{
			$html->setTemplateId("indexgenerator_welcome",SiiMobilityApp::getFolder("templates")."index.tpl.html");
			
			$welcome = new sm_HTML("WelcomePanel");
			$welcome->setTemplateId("welcome",SiiMobilityApp::getFolder("templates")."welcome.tpl.html");
			$html->insert("introduction",$welcome);
		}
		
		$panel->insert($html);
		$page->insert($panel);
		//$page->insert($html);
		if(isset($this->model['ID']))
		{
			/* Javascript dependencies */
			$page->addJS("jquery-1.10.1.dataTables.min.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("dataTables.bootstrap.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("dataTables.colVis.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("dataTables.tableTools.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
		//	$page->addJS("dataTables.responsive.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("datepicker.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("timepicker.min.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			
			
			$page->addJS("init.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("pageManager.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			$page->addJS("generationsManager.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
		//	$page->addJS("jquery.ui-contextmenu.min.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
			/* CSS dependencies */
			//$page->addCss("jquery.dataTables.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
			$page->addCss("dataTables.bootstrap.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
			$page->addCss("dataTables.colVis.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
			$page->addCss("dataTables.tableTools.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
	 //		$page->addCss("dataTables.responsive.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
			$page->addCss("datepicker.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
			$page->addCss("timepicker.min.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
			
			/* Javascript session to open if it set in the model */
			
			$script="currentSessionID = ".$this->model['ID']."; repositoryID='".$this->model['RepositoryID']."';";
			$page->addJS($script,"indexgenerator");
			
		}
		$page->addJS("SiiMobilityIndexManager.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
		$page->addCss("siimobility.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
	}
	
	
	
	function IndexEditorNew_build(){
		$this->IndexEditor_build();
		$ele = $this->uiView->getUIElement("index");
		$panel = new sm_Panel();
		$panel->setType("default");
		$panel->setTitle("New Index Descriptor");
		$dlg = new sm_HTML();
		$dlg->setTemplateId("newrepository",SiiMobilityApp::getFolder("templates")."newrepository.tpl.html");
		$dlg->insert("title", "Create Index Descriptor");
		$dlg->insert("body",sm_Form::buildForm("NewRepository", $this));
		$panel->insert($dlg);
		$ele->remove("introduction");
		$ele->insert("introduction",$panel);
		
	}
	
	function IndexEditorImport_build()
	{
		$this->IndexEditor_build();
		$ele = $this->uiView->getUIElement("index");
		$panel = new sm_Panel();
		$panel->setType("default");
		$panel->setTitle("New Index Descriptor");
		$dlg = new sm_HTML();
		$dlg->setTemplateId("newrepository",SiiMobilityApp::getFolder("templates")."newrepository.tpl.html");
		$dlg->insert("title", "Import Index Descriptor");
		$dlg->insert("body",sm_Form::buildForm("ImportRepository", $this));
		$panel->insert($dlg);
		$ele->remove("introduction");
		$ele->insert("introduction",$panel);
		
	}
	
	function IndexEditorProperties_build()
	{
		//$this->IndexEditor_build();
		//$ele = $this->uiView->getUIElement("index");
		$this->uiView = $panel = new sm_Panel();
		$panel->setType("default");
		$panel->setTitle("Edit Properties");
		$html = new sm_HTML();
		$html->setTemplateId("newrepository",SiiMobilityApp::getFolder("templates")."newrepository.tpl.html");
		//$dlg->insert("title", "Edit Properties");
		$html->insert("body",sm_Form::buildForm("Properties", $this));
		$panel->insert($html);
		//$ele->remove("introduction");
		//$ele->insert("introduction",$panel);
	
	}

	function IndexEditorGenerations_build(){
		$this->IndexEditor_build();
		$ele = $this->uiView->getUIElement("index");
		
		$confirmDlg=false;
		$id="generations_table";
		
		$table = new sm_TableDataView($id,$this->model);
		$table->setAjax();
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
		/*$options=array("-1"=>"All","0"=>"Down","1"=>"UP",);
		 $filterElement['monitor_search']=array("Search","", "monitor_search", array('placeholder'=>"Search",'value'=>$this->model['keywords'],'class'=>'input-sm form-control'));
		 $filterElement['monitor_status']=array("Select","Filter for", "monitor_status", $options,array('value'=>$this->model['state_selector'],'class'=>'input-sm'));
		
		 $table->addFilter($filterElement);
		*/
		$filterElement['generations_search']=array("Search","", "generations_search", array('placeholder'=>"Search",'value'=>$this->model['keywords'],'class'=>'input-sm form-control'));
		$table->addFilter($filterElement);
		
		$panel = new sm_Panel();
		$panel->setType("default");
		$dbUsed = sm_Config::get("SIIMOBILITYDBURL",null);
		$panel->setTitle($this->model['title']." (".$dbUsed.")");
		$panel->insert($table);
	//	$page->insert($panel);
		if($confirmDlg)
		{
			$dlg = new sm_Dialog("confirmArchiveCommand",CONFIRMATION_DLG);
			$dlg->setConfirmationFormClass("confirm");
			$panel->insert($dlg);
		}
		
		$this->uiView->addCss("siimobility.css","indexgenerator",SiiMobilityApp::getFolderUrl("css"));
		$this->uiView->addJS("SiiMobilityRepositoryManager.js","indexgenerator",SiiMobilityApp::getFolderUrl("js"));
		$ele->remove("introduction");
		$ele->insert("introduction",$panel);
	}
	
	function IndexEditorScriptDone_build(){
		$json['title']="Sucess";
		$json['html']="The script was created! You can find it in file '".$this->model['path']."'";
		$this->uiView = new sm_JSON();
		$this->uiView->insert($json);
	}
	
	function IndexEditorValidationError_build(){
		$json['title']="Validation Error";
		//$result = $this->_validationResult();
		
		$level = $this->model['index']->getSecurityLevel();
		$count = count($this->model['items']);
		$html="<p>".$count." data set were found not matching the required Security Level: <b>".IndexSecurityLevels::toString($level)."</b>.<br>Open the Validate Panel for more details!</p>";
		
		$json['html']=$html;
		$this->uiView = new sm_JSON();
		$this->uiView->insert($json);
	}
	
	function IndexEditorValidation_build(){
		//$json['title']="Validation Error";
		$this->uiView=new sm_Panel("ValidationView");
		$this->uiView->setType("default");
		$this->uiView->setTitle("Validate");
		$html = new sm_HTML();
		$html->setTemplateId("validation",SiiMobilityApp::getFolder("templates")."validation.tpl.html");
		$html->insert("SecurityLevel",IndexSecurityLevels::toString($this->model['index']->getSecurityLevel()));
		if(!$this->model['result'])
		{
			$msg="<p>The following data do not match the required Security Level</p>";
			$html->insert("result", $msg);
		
			$bar = new sm_NavBar("header-cmd");
			$bar->setTemplateId("menu_pills");
			$bar->insert("revalidate", array("id"=>"validate-refresh-cmd","link_class"=>"button","href"=>"javascript:void(0)","link_attr"=>'onclick="IndexManager.validateIndex(\''.$this->model['RevalidateLink'].'\')"','label'=>'<i class="glyphicon glyphicon-refresh"></i> Revalidate'));
			$bar->insert("remove", array("id"=>"validate-remove-cmd","link_class"=>"button","href"=>"javascript:void(0)","link_attr"=>'onclick="IndexManager.removeAllFromIndex(\''.$this->model['index']->getID().'\')"','label'=>'<i class="glyphicon glyphicon-remove"></i> Remove All'));
			$html->insert("result",$bar);
			$html->insert("result",$this->_validationResult());
			$progress = new sm_HTML();
			$progress->setTemplateId("progress_bar_dlg","ui.tpl.html");
			$progress->insert("id","ValidateProgressModal");
			$html->insert("result",$progress);
		}
		else
		{
			$msg="<div class='alert alert-success'><b>Index is valid</b></div>";
			$html->insert("result", $msg);
		}
		$this->uiView->insert($html);
	}
	
	protected function _validationResult()
	{
		$id="validation_table";
		$table = new sm_TableDataView($id);
		//$table->setAjax();
		$table->addHRow();
		if(count($this->model['items'])>0)
		{
			$headers=array_keys($this->model['items'][0]);
			$headers[]="actions";
			foreach($headers as $l)
			{
				if($l=='actions')
					$table->addHeaderCell(ucfirst(str_replace("_"," ",$l)),"sorter-false");
				else
					$table->addHeaderCell(ucfirst(str_replace("_"," ",$l)));
			}
			foreach ($this->model['items'] as $k=>$value)
			{
				$table->addRow("",array("id"=>$value['item']));
				foreach ($value as $l=>$v)
				{
					$table->addCell($v,$l);
				}
				$command="<a class=button href=javascript:void(0) onclick='IndexManager.removeItemFromIndex(\"".$this->model['index']->getID()."\",\"".$value['item']."\")'><i class=delete-icon></i></a>";
				$table->addCell($command);
			}
			$table->setSortable();
		}	
		return $table;
	}
	
	/**** FORMS Events and methods**********/
	
	function Properties_form(sm_Form $form)
	{
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				//	"view" => new View_Vertical,
				//"labelToPlaceholder" => 0,
				"action"=>"IndexGenerator/Properties/".$this->model['generation']->getID(),
				"ajax"=>true,
				"ajaxCallback"=>"IndexManager.showPropertiesMessage"
		));
		$form->setRedirection("");
		
		$types = sm_Config::get('RDFREPOSITORYTYPE',null);
		if($types)
		{
			$types = unserialize($types);
			sort($types);
		}
		
		/*$options=array();
		$options[]="Select a repository";
		foreach ($this->model['generations'] as $repo)
		{
			$options[$repo['ID']]=$repo['RepositoryID'];
	
		}
		$mode=array("empty"=>"Empty","clone"=>"Clone","copy"=>"Copy");*/
		$form->addElement(new Element_HTML("<div style='border-bottom:1px solid black; margin:30px 0px;'><h4>Properties</h4></div>"));
		$form->addElement(new Element_Textbox("Index Descriptor Name", "repositoryID",array("value"=>$this->model['generation']->getRepositoryID(),"shortDesc"=>"<small>Insert a name or identifier for the target Index Descriptor</small>","required"=>true)));
		$form->addElement(new Element_Textarea("Description", "description",array("value"=>$this->model['generation']->getDescription(),"shortDesc"=>"<small>Insert a textual description</small>")));
		$form->addElement(new Element_Checkbox("Security Level", "SecurityLevel",array("1"=>"Open","2"=>"Private","3"=>"Sensible","4"=>"Critical"),array("id"=>"SLcheck","value"=>$this->model['generation']->getSecurityLevel(),"shortDesc"=>"<small>Choose the Security Level for the Index</small>","class"=>"SecurityLevelChb","required"=>true)));
		$form->addElement(new Element_Textbox("ParentID", "parentID",array("value"=>$this->model['generation']->getParentID(),"shortDesc"=>"<small>Parent Index Descriptor (cloned)</small>","readonly"=>"readonly")));
		if($types)
		{
			$ele = new Element_Select("Select RDF Database Type", "type",$types,array("value"=>$this->model['generation']->getType(),"shortDesc"=>"<small>Choose the target RDF Database. If it is a clone, this can not be changed</small>","required"=>true));
			if($this->model['generation']->getParentID()!="")
				$ele = new Element_Select("Select RDF Database Type", "type",$types,array("value"=>$this->model['generation']->getType(),"shortDesc"=>"<small>Choose the target RDF Database. If it is a clone, this can not be changed</small>","readonly"=>"readonly"));
			$form->addElement($ele);
		}
		
		$form->addElement(new Element_HTML("<div class=help-inline><p><small>Fields marked with * are mandatory</small></p></div>"));
		$form->addElement(new Element_Button("Save","submit",array('name'=>"send","class"=>"button light-gray btn-xs","shortDesc"=>"<p><small>Fields marked with * are mandatory</small></p>")));
	
	
	}
	function NewRepository_form(sm_Form $form)
	{
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				//	"view" => new View_Vertical,
				//"labelToPlaceholder" => 0,
				"action"=>""
		));
		
		$options=array();
		$options[]="Select an Index Descriptor";
		foreach ($this->model['generations'] as $repo)
		{
			$options[$repo['ID']]=$repo['RepositoryID'];
		
		}
		$optionsType=null;
		$types = sm_Config::get('RDFREPOSITORYTYPE',null);
		if($types)
		{
			$optionsType=array(""=>"Select type ...");
			$types = unserialize($types);
			sort($types);
			foreach ($types as $t){
				$optionsType[$t]=$t;
			}
		}
		$mode=array("empty"=>"Empty (Index Descriptor without items)","clone"=>"Clone (Physical copy of an existing Index Descriptor)","copy"=>"Copy (Use an existing Index Descriptor as model)");
		$form->addElement(new Element_HTML("<div style='border-bottom:1px solid black; margin-top:20px;'><b>Descriptor Properties</b></div>"));
		$form->addElement(new Element_HTML("<div class=help-inline><p><small>Fields marked with * are mandatory</small></p></div>"));
		$form->addElement(new Element_Textbox("Index Descriptor Name", "repositoryID",array("shortDesc"=>"<small>Insert a name or identifier for the target Index Descriptor</small>","required"=>true)));
		$form->addElement(new Element_Textarea("Description", "description",array("shortDesc"=>"<small>Insert a textual description. <b>In clone mode the description will be merged with the parent one</b></small>")));
		
		$form->addElement(new Element_Checkbox("Security Level", "SecurityLevel",array("1"=>"Open","2"=>"Private","3"=>"Sensible","4"=>"Critical"),array("id"=>"SLcheck","value"=>"3","shortDesc"=>"<small>Choose the Security Level for the Index</small>","class"=>"SecurityLevelChb","required"=>true)));
		
		$form->addElement(new Element_Select("Select Index Descriptor Creation Mode", "mode",$mode,array("shortDesc"=>"<small>Choose the Index Descriptor mode generation</small>","required"=>true)));
		$form->addElement(new Element_Select("Select Index Descriptor", "parentID",$options,array("value"=>null,"shortDesc"=>"<small>Choose the Index Descriptor to start</small>","required"=>true)));
		
		if($types)
			$form->addElement(new Element_Select("Select RDF Database Type", "type",$optionsType,array("shortDesc"=>"<small>Choose the target RDF Database. <b>In clone mode the Index Descriptor inherits the parent RDF type and it can not be changed.</b></small>","required"=>true)));
		else
			$form->addElement(new Element_Hidden("type",array("value"=>"")));
		
		$form->addElement(new Element_Button("Create","submit",array('id'=>"sumbitRepos",'name'=>"send","class"=>"button light-gray btn-xs","shortDesc"=>"<p><small>Fields marked with * are mandatory</small></p>")));
	
	
	}
	
	function ImportRepository_form(sm_Form $form)
	{
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				//	"view" => new View_Vertical,
				//"labelToPlaceholder" => 0,
				"action"=>""
		));
		$form->addElement(new Element_HTML("<div style='border-bottom:1px solid black; margin:30px 0px;'><h4>Properties</h4></div>"));
		$form->addElement(new Element_Textbox("Index Descriptor Name", "repositoryID",array("shortDesc"=>"<small>Insert a new name or identifier for the target Index Descriptor or leave empty to maintain the orignal name.</small>")));
		$form->addElement(new Element_Textarea("Description", "description",array("shortDesc"=>"<small>Insert a textual description to be merged with the current description or leave empty to maintain the original.</small>")));
		//$form->addElement(new Element_Radio("Import Mode", "mode", array("Text","File"),array("value"=>'Text')));
		$form->addElement(new Element_HTML("<div style='border-bottom:1px solid black; margin:30px 0px;'><h4>Source Data</h4></div>"));
		$form->addElement(new Element_Radio("Choose Import Mode", "mode", array("Text","File"),array("value"=>'Text')));
		
		$form->addElement(new Element_Textarea("XML data", "xmltext",array("shortDesc"=>"<small>Paste an XML description</small>")));
		$form->addElement(new Element_File("Select XML File", "file",array("shortDesc"=>"<small>Choose the Index Descriptor XML File </small>")));
		
		$form->addElement(new Element_Button("Import","submit",array('name'=>"send","class"=>"button light-gray btn-xs")));
	
	
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
		$_SESSION['IndexGenerator/Editor/Archive']=$value;
	}
	
	
	/**** Extend View ****/
	
	function onExtendView(sm_Event &$event)
	{
		$obj = $event->getData();
		if(get_class($obj)=="SiiMobilitySettingsView")
		{
			$this->extendSettingsView($obj);
		}
	
	}
	
	public function extendSettingsView($obj)
	{
	
		/*$panel=$obj->getUIView()->getUIElement("SiiMobilitySettingsPanel");
			//$this->data=$obj->getData();
			$panel->insert(sm_Form::buildForm("index_manager_config", $this));*/
	}
	
}