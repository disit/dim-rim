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

class OD_ProcessManagerView extends sm_ViewElement
{
	function __construct($data=NULL)
	{
		parent::__construct($data);
		
	}

	/**
	 * Create the HTML code for the module.
	 */
	public function build() {
		switch(strtolower($this->op))
		{
			case 'index':
				$this->uiView = $this->index_build();
			break;
			case 'explorer':
				$this->uiView = $this->explorer_build();
			break;
			case 'scheduler':
				$this->uiView = $this->scheduler_build();
			break;
			case 'help':
				$this->uiView = $this->help_build();
			break;
			case 'preferences':
				$this->uiView = $this->preferences_build();
			break;
			case 'files':
				$this->uiView = $this->triple_files();
			break;
			case 'file_browser':
				$this->uiView = $this->file_browser();
			break;
			case 'license':
				$this->uiView = $this->license();
			break;
		}
	}
	
	public function index_build()
	{
		$page = new sm_Page("OD_ProcessManager");
		$page->setTitle(null);
		
		$panel = new sm_Panel("OpenDataManager");
		$panel->setTitle("Data Ingestion Manager");
		$panel->icon("<i class=odata-icon></i>");
		$index = new sm_HTML('index');
		$index->setTemplateId("index",OD_ProcessManagerApp::getFolder("templates")."index.tpl.html");
		
		
		$tabs = new sm_Tabs("OpenDataManagerTabs");
		$tabs->insert("explorer", array("title"=>"Explorer","paneldata"=>$this->explorer_build()));
		$tabs->insert("scheduler", array("title"=>"Scheduler","paneldata"=>$this->scheduler_build()));
		$tabs->insert("repository", array("title"=>"Repository","paneldata"=>$this->repository_build()));
		$tabs->insert("preferences", array("title"=>"Preferences","paneldata"=>$this->preferences_build()));
		$tabs->setActive("explorer");
		$tabs->setClass("OD_View");
		$filesDlg=new sm_HTML("TripleFileDlg");
		$filesDlg->setTemplateId("Modal_dlg","ui.tpl.html");
		$filesDlg->insert("title", "Triples Files");
		$filesDlg->insert("id", "TripleFilesDlg");
		$licenseDlg=new sm_HTML("ODLicenseDlg");
		$licenseDlg->setTemplateId("Modal_dlg","ui.tpl.html");
		$licenseDlg->insert("title", "License Text");
		$licenseDlg->insert("id", "ODLicenseDlg");
		$index->insert("panel", $tabs);
		$index->insert("panel", $this->help_build());
		$panel->insert($index);
		$panel->insert($filesDlg);
		$panel->insert($licenseDlg);
		$page->insert($panel);
		$page->addCSS("OD_ProcessManager.css","index",OD_ProcessManagerApp::getFolderUrl("css"));
		
		$page->addJS("jquery.validate.js","index",OD_ProcessManagerApp::getFolderUrl("js")."jquery-validation-1.13.1/dist/");
		$page->addJS("jquery.dataTables.min.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		$page->addJS("dataTables.bootstrap.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		$page->addJS("dataTables.tableTools.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		$page->addJS("dataTables.colVis.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		$page->addJS("jquery.jeditable.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		
		$page->addJS("pageManager.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		$page->addJS("OpenDataManager.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		$page->addJS("jquery.blockUI.js","index",OD_ProcessManagerApp::getFolderUrl("js"));
		
		
		$page->addCSS("dataTables.bootstrap.css","index",OD_ProcessManagerApp::getFolderUrl("css")."dataTables/");
		$page->addCSS("dataTables.tableTools.css","index",OD_ProcessManagerApp::getFolderUrl("css")."dataTables/");
		$page->addCSS("dataTables.colVis.css","index",OD_ProcessManagerApp::getFolderUrl("css")."dataTables/");
		//$page->addCSS("jquery.dataTables.css","index",OD_ProcessManagerApp::getFolderUrl("css")."dataTables/");
		
		return $page;
	}
	
	public function explorer_build()
	{
		$panel = new sm_HTML('explorer');
		$panel->setTemplateId("explorer",OD_ProcessManagerApp::getFolder("templates")."explorer.tpl.html");
		$f = new Form("addRowForm");
		$editor = new Element_CKEditor("License Text Editor", "LicenseText",array("id"=>"LicenseText","type"=>"ckeditor","class"=>"ckeditor processRow"));
		//$f = new Form("addRowForm");
		$editor->_setForm($f);
		ob_start();
		$editor->render();
		$html = ob_get_contents();
		ob_end_clean();
	    $panel->insert("LicenseTextEditor", $html);
		//$panel->addJS($js);
		$panel->addJS("ckeditor.js","explorer","lib/PFBC/Resources/ckeditor/");
		
		return $panel;
	}
	
	public function scheduler_build() {
	
			$data['url']=sm_Config::get('PROCESSMANAGERWEBSCHEDULERURL',null);
			$data['id']="OD_Scheduler_View";
			$panel = new sm_HTML('OD_Panel');
			$panel->setTemplateId("OD_Panel",OD_ProcessManagerApp::getFolder("templates")."panel.tpl.html");
			$panel->addTemplateData("OD_Panel", $data);
		    return $panel;
	}
	
	public function help_build() {
	
		$panel = new sm_Panel("OD_Help_View");
		$panel->setType("default");
		$panel->setTitle("Help");
		$panel->setClass("OD_View");
		$url=OD_ProcessManagerApp::getFolderUrl("help")."index.html";
		$html = new sm_HTML("Help");
		$html->insert("helpframe","<iframe src='".$url."' id='OD_Help_View_Frame' class='OD_panel_view'></iframe>");
		$panel->insert($html);
		return $panel;
	}
	
	public function repository_build() {
	
				
		$data['url']="ProcessManager/Repository/";
		$data['id']="OD_Repository_View";
		$panel = new sm_HTML('OD_Panel');
		$panel->setTemplateId("OD_Panel",OD_ProcessManagerApp::getFolder("templates")."panel.tpl.html");
		$panel->addTemplateData("OD_Panel", $data);
		return $panel;
	}
	
		
	function preferences_build(){
		$conf = sm_Config::instance()->conf;
		$this->model=array();
		foreach($conf as $c=>$p)
		{
			if(strpos($p['module'],"OD_")!==FALSE)
			{
				$p['name']=$c;
				$this->model[$p['module']][]=$p;
			}
		}
		
		$menu = new sm_NavBar("cView_navbar");
		foreach(array_keys($this->model) as $k=>$title)
			$menu->insert($k,array("url"=>"#".$title,"title"=>str_replace("OD_ProcessManager","",$title),"icon"=>"sm-icon icon-".strtolower(str_replace("OD_ProcessManager","",$title))));
		
		$html = new sm_HTML("ProcessManagerSettingsView");
		$html->setTemplateId("OD_Preferences",OD_ProcessManagerApp::getFolder("templates")."preferences.tpl.html");
		
	
		$html->insert("form",sm_Form::buildForm("OD_settings", $this));
		$html->addJS("config.js","OD_Preferences");
		return $html;
	}
	
	public function triple_files(){
		$data['url']=$this->model['url'];
		$data['id']="OD_Repository_View";
		$filesDlg=new sm_HTML("TripleFileDlg");
		$filesDlg->setTemplateId("Modal_remote_Dlg","ui.tpl.html");
		$filesDlg->insert("title", $this->model['title']);
		$filesDlg->insert("id", "TripleFilesDlg");
		$message = "<h4>Repository path: <i>".$this->model['repository']."</i></h4>";
		$filesDlg->insert("body", $message);
		$panel = new sm_HTML('OD_Panel');
		$panel->setTemplateId("OD_Panel",OD_ProcessManagerApp::getFolder("templates")."panel.tpl.html");
		$panel->addTemplateData("OD_Panel", $data);
		$filesDlg->insert("body", $panel);
		return $filesDlg;
	}
	
	public function triple_files_old()
	{
		$filesDlg=new sm_HTML("TripleFileDlg");
		$filesDlg->setTemplateId("Modal_remote_Dlg","ui.tpl.html");
		$filesDlg->insert("title", $this->model['title']);
		$filesDlg->insert("id", "TripleFilesDlg");
		$message = "<h4>Repository path: <i>".$this->model['repository']."</i></h4>";
		$filesDlg->insert("body", $message);
		if(count($this->model['files'])>0)
		{
				
			$table = new sm_TableDataView("TripleFilesTable");
			$table->addHRow();
				
			$table->addHRow();
			$first = reset($this->model['files']);
			$headers=array_keys($first[0]);
			$table->addHeaderCell("Folder");
			foreach($headers as $l)
			{
				$table->addHeaderCell(ucfirst(str_replace("_"," ",$l)));
			}
	
			foreach ($this->model['files'] as $k=>$value)
			{
	
				foreach ($value as $l=>$item)
				{
					$table->addRow();
					if($l==0)
						$table->addCell($k,"",array("rowspan"=>count($value)));
					foreach($item as $v)
						$table->addCell($v);
				}
			}
				
				
		}
		else
			$table='<div class="alert alert-warning">No files detected!!</div>';
	
		$filesDlg->insert("body", $table);
	
		return $filesDlg;
	
	}
	
	
	function file_browser()
	{
		$html=new sm_Site("FileBrowser");
		$html->setTemplateId("file_browser",OD_ProcessManagerApp::getFolder("templates")."filebrowser.tpl.html");
		
			
		$content=array();
		$breadcrumbs = $this->model["breadcrumbs"];
		
		 $content['breadcrumbs']='<p class="navbar-text">';
		 foreach($breadcrumbs as $breadcrumb)
		 {
			 if ($breadcrumb != end($breadcrumbs))
			 {
			 	if(!in_array($breadcrumb['text'],$this->model['home']))
			 	{
			 		$content['breadcrumbs'].='<a href="'.$breadcrumb['link'].'">'.$breadcrumb['text'].'</a>';
			 		$content['breadcrumbs'].='<span class="divider">/</span>';
			 	}
		        	
			 }
			 else if(!in_array($breadcrumb['text'],$this->model['home']))
		        $content['breadcrumbs'].=$breadcrumb['text'];
		                      
		  }
		  $content['breadcrumbs'].="</p>";
		  $content['files']=array();
		  foreach($this->model['dirArray'] as $name => $fileInfo)
		  {
		  	$fileInfo['name']=$name;
		  	$content['files'][]=$fileInfo;
		  }
		  $html->addTemplateDataRepeat("file_browser", "files", $content['files']);
		  $html->insert("breadcrumbs", $content['breadcrumbs']);
		  $messages = $this->model['message'];
		  foreach ($messages as $message){
		  	$msgHtml = new sm_HTML();
		  	$msgHtml->setTemplateId("message","ui.tpl.html");
		  	$msgHtml->insert("type", $message['type']);
		  	$msgHtml->insert("message", $message['text']);
		  	$html->insert("message",$msgHtml);
		  }
		 // $html->addJS("jquery-1.11.0.min.js","file_browser");
		  //$html->addJS("bootstrap3/bootstrap.min.js","file_browser");
		  $html->addJS("directorylister.js","file_browser",OD_ProcessManagerApp::getFolderUrl("lib")."js/");
		  //$html->addCSS("bootstrap3/bootstrap.min.css","file_browser");
		  $html->addCSS("directorylister.css","file_browser",OD_ProcessManagerApp::getFolderUrl("lib")."css/");
		  $html->addCSS("font-awesome.css","file_browser",OD_ProcessManagerApp::getFolderUrl("lib")."css/");
		  
		  return $html;
	}
	
	function license(){
		$licenseDlg=new sm_HTML("ODLicenseDlg");
		$licenseDlg->setTemplateId("Modal_remote_Dlg","ui.tpl.html");
		$licenseDlg->insert("title", "License Text");
		$licenseDlg->insert("id", "ODLicenseDlg");
		$license = $this->model['data']->getLicenseText();
		if(empty($license))
		{
			$msgHtml = new sm_HTML();
			$msgHtml->setTemplateId("message","ui.tpl.html");
			$msgHtml->insert("type", "warning");
			$msgHtml->insert("message", "No license text available!");
			
			$license = $msgHtml;
		}
		$licenseDlg->insert("body", $license);
		return $licenseDlg;
	}
	
	
	
	/**
	 * 
	 * @param sm_Form $form
	 */	
	function OD_settings_form($form){
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				"action"=>"ProcessManager/settings",
				"ajax"=>true,
				"ajaxCallback"=>"OpenDataManager.showSettingsMessage"
		));
		$form->setRedirection("");
	
		$m='OD_ProcessManager';
		if(count($this->model[$m])>0)
		{
			$form->addElement(new Element_HTML('<div class="col-md-6"><div class="panel panel-primary"><div class="panel-heading">MySQL Settings</div></div>'));
			foreach($this->model[$m] as $item=>$i)
			{
				if($i['name']=="PROCESSMANAGERDBPWD")
					$form->addElement(new Element_Password($i['description'],$i['name'],array('value'=>$i['value'],'label'=>$i['description'])));
				else
					$form->addElement(new Element_Textbox($i['description'],$i['name'],array('value'=>$i['value'],'label'=>$i['description'],'required'=>'required')));
			}
			$form->addElement(new Element_HTML('</div>'));
		}
		
		$m='OD_SchedulerManager';
		if(count($this->model[$m])>0)
		{		
			$form->addElement(new Element_HTML('<div class="col-md-6"><div class="panel panel-primary"><div class="panel-heading">Scheduler Settings</div></div>'));
			foreach($this->model[$m] as $item=>$i)
			{
					$form->addElement(new Element_Textbox($i['description'],$i['name'],array('value'=>$i['value'],'label'=>$i['description'],'required'=>'required')));
			}
			$form->addElement(new Element_HTML('</div>'));
		}
		$form->addElement(new Element_HTML('<div class="col-md-12">'));
		$form->addElement(new Element_HTML('<p><small>Fields marked with * are mandatory</small></p>'));
		$form->addElement(new Element_Button("Save","",array("class"=>"button light-gray")));
		$form->addElement(new Element_HTML('</div>'));
	}
	
	
	/**
	 * @desc Install Menu Items & links
	 * @param sm_MenuManager $menu
	 */
	static function menu(sm_MenuManager $menu)
	{
		$menu->setMainLink("Data Ingestion Manager",'ProcessManager',"tasks");
	}
	
	/**
	 * @desc Uninstall Menu Items & links
	 * @param sm_MenuManager $menu
	 */
	static function uninstallMenu(sm_MenuManager $menu)
	{
		$menu->deleteLink('ProcessManager');
	}
};