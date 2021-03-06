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

define("VIRTUOSOPluginVersion","v.1.0");
define("VIRTUOSOPluginDescription","VIRTUOSO Adaptor");
define("VIRTUOSOPluginName","VIRTUOSO");

class VIRTUOSOPlugin extends sm_Plugin
{
	static $instance;
	function __construct()
	{
		parent::__construct();
		$this->pluginFolder=sm_relativeURL(dirname(__FILE__))."/"; //.DIRECTORY_SEPARATOR;
		$this->pluginDescription=VIRTUOSOPluginDescription;
		$this->pluginVersion=VIRTUOSOPluginVersion;
		$this->pluginName=VIRTUOSOPluginName;
		sm_EventManager::addEventHandler($this);
		
	}
		
	static function instance()
	{
		if(self::$instance ==null)
		{
			$c=__CLASS__;
			self::$instance=new self();
			
		}
		return self::$instance;
	}
	
	/**
	 *
	 * @param sm_Event $event
	 */
	public function onFormAlter(sm_Event &$event)
	{
	
		
	}
}