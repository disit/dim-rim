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

define("CONFIRMATION_DLG","YesNo_Dlg");

class sm_Dialog extends sm_HTML
{
	protected $confirmSettings;
	function __construct($id,$type="")
	{
		parent::__construct($id);
		$this->insert("id", $id);
		$this->confirmSettings=null;
		if($type=="")
			$this->tpl_id=CONFIRMATION_DLG;
		else 
			$this->tpl_id=$type;
		$this->setTemplateId($this->tpl_id,"ui.tpl.html");
		
	}
	
	function render()
	{
		$json='{}';
		if($this->confirmSettings)
		{
			$json = json_encode($this->confirmSettings);
		}
		$js = 'sm_Dialog.configure('.$json.');';
		$this->addJS($js,$this->tpl_id);
		$this->addJS('sm_dialog.js',$this->tpl_id,'js/sm_ui/');
		return parent::render();		
	}
	
	function setConfirmationFormClass($form=null)
	{
		if($form)
		{
			    $this->confirmSettings=array("formClass"=>$form);
				$this->confirmSettings['id']=$this->id;
				if($this->tpl_id==CONFIRMATION_DLG)
					$this->confirmSettings['btnId']="btnYes";
		}
	}
}