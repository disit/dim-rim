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

class OD_ProcessManagerJSONView extends sm_ViewElement
{
	function __construct($data=NULL)
	{
		parent::__construct($data);
		
	}

	/**
	 * Create the HTML code for the module.
	 * First the createHTMLLabels() will be called to add all labels to the template,
	 * Then the tpl_id set in $this->getTemplateId() will be added to the main template automatically
	 */
	
	public function build() {
		$this->uiView=new sm_JSON();
		$this->uiView->insert($this->model);
	}
	
}