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

class PMySqlConfig implements sm_Module
{
	protected $sql_details;
	protected $db;
	public function __construct()
	{
		$dbname1 = sm_Config::get("PROCESSMANAGERDB1",null);
		$dbname2 = sm_Config::get("PROCESSMANAGERDB2",null);
		$user = sm_Config::get("PROCESSMANAGERDBUSER",null);
		$pwd = sm_Config::get("PROCESSMANAGERDBPWD",null);
		$host = sm_Config::get("PROCESSMANAGERDBURL",null);
		$schedulerUrl = sm_Config::get("PROCESSMANAGERSCHEDULERURL",null);
		$schedulerWebUrl = sm_Config::get("PROCESSMANAGERWEBSCHEDULERURL",null);
		$this->sql_details = array (
				'user' => $user,
				'pass' => $pwd,
				'db1' => $dbname1,
				'db2' => $dbname2,
				'host' => $host,
				'schedulerUrl'=>$schedulerUrl,
				'schedulerWebUrl'=>$schedulerWebUrl
		);
		$this->db = new sm_Database($this->sql_details['host'],$this->sql_details['user'],$this->sql_details['pass']);
		//$this->db->setDB($this->sql_details['db']);
	} 
	
	function getDatabase(){
		return $this->db;
	}
	
	public function __destruct(){}
	
	function getSqlDetails(){
		return $this->sql_details;
	}
	
	static public function install($db)
	{
		 return true;
	}
	
	static public function uninstall($db)
	{
	 	return true;
	}
}