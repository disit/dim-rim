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

class OD_ProcessManager extends PMySqlConfig
{
	function __construct()
	{
		parent::__construct();
	
	}
	
	public function ajaxCommand($command,$request)
	{
		$ret = null;
		$error=null;
		switch ($command)
		{
			case 'scheduleProcess':
			case 'scheduleProcessWithoutTrig':
			case 'scheduleConcatProcessWithoutTrig':
			case 'scheduleConcatProcess';
			case 'deleteProcess':
			case 'launchProcess':
			case 'pauseProcess':
			case 'resumeProcess':
			case 'checkExistJob':
			{
				$sched = new OD_SchedulerManager();
				$ret = $sched->ajaxCommand($command, $request);
			}
			break;
			case 'deleteRow':
				$ret = $this->deleteRow($request,$error);
				break;
			case 'disableRow':
				$ret = $this->disableRow($request,$error);
			break;
			case 'enableRow':
				$ret = $this->enableRow($request,$error);
			break;
			case 'addRow':
				$ret = $this->addRow($request,$error);
			break;
			case 'editCell':
				$ret = $this->editCell($request,$error);
			break;
					
		}
		if($ret)
		{
			$message="Command ".$command." execute successfully!";
		}
		else 
		{
			$message="Error when executing command ".$command."!";
			if($error)
				$message.="<br>".$error;
		}
		return array('result'=>$message);
	}
	
	
	public function getSingleTable($request)
	{
		// DB table to use
		// $table = 'process_manager';
		$table = 'process_manager2';
		
		// Table's primary key
		// $primaryKey = 'Process';
		$primaryKey = 'Process';
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
				array (
						'db' => 'process',
						'dt' => 'Process ID'
				),
				array (
						'db' => 'Resource',
						'dt' => 'Resource'
				),
				array (
						'db' => 'Category',
						'dt' => 'Category'
				),
				array (
						'db' => 'Format',
						'dt' => 'Format'
				),
				array (
						'db' => 'Automaticity',
						'dt' => 'Automaticity'
				),
				array (
						'db' => 'Process_type',
						'dt' => 'Process type'
				),
				array (
						'db' => 'Access',
						'dt' => 'Access'
				),
				array (
						'db' => 'Source',
						'dt' => 'Source'
				),
				array (
						'db' => 'A',
						'dt' => 'A'
				),
				array (
						'db' => 'B',
						'dt' => 'B'
				),
				array (
						'db' => 'status_A',
						'dt' => 'Status A'
				),
				array (
						'db' => 'status_B',
						'dt' => 'Status B'
				),
				array (
						'db' => 'status_C',
						'dt' => 'Status C'
				),
				array (
						'db' => 'time_A',
						'dt' => 'Time A'
				),
				array (
						'db' => 'time_B',
						'dt' => 'Time B'
				),
				array (
						'db' => 'time_C',
						'dt' => 'Time C'
				),
				array (
						'db' => 'exec_A',
						'dt' => 'Exec A'
				),
				array (
						'db' => 'exec_B',
						'dt' => 'Exec B'
				),
				array (
						'db' => 'exec_C',
						'dt' => 'Exec C'
				),
				array (
						'db' => 'period',
						'dt' => 'Period'
				),
				array (
						'db' => 'overtime',
						'dt' => 'Overtime'
				),
				array (
						'db' => 'param',
						'dt' => 'Parameters'
				),
				array (
						'db' => 'last_update',
						'dt' => 'Last Update'
				),
				array (
						'db' => 'last_triples',
						'dt' => 'Last Triples'
				),
				array (
						'db' => 'error',
						'dt' => 'Error'
				),
				array (
						'db' => 'description',
						'dt' => 'Description'
				)
		);
		
		$sqlConfig = $this->sql_details;
		$sqlConfig['db']=$this->sql_details['db2'];
		return SSP::simple ( $request,  $sqlConfig, $table, $primaryKey, $columns ) ;
		
	}
	
	public function getMultiTables($request)
	{
		// DB table to use
		// $table = 'process_manager';
		$table = 'process_manager2';
		
		// Table's primary key
		// $primaryKey = 'Process';
		$primaryKey = 'Process';
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
				array(
						'db' => 'process',
						'dt' => 'DT_RowId',
						'formatter' => function( $d, $row ) {
							// Technically a DOM id cannot start with an integer, so we prefix
							// a string. This can also be useful if you have multiple tables
							// to ensure that the id is unique with a different prefix
							return 'row_'.$d;
						}
				),
				array (
						'db' => 'process',
						'dt' => 'process'
				),
				array (
						'db' => 'Resource',
						'dt' => 'Resource'
				),
				array (
						'db' => 'Resource_Class',
						'dt' => 'Resource_Class'
				),
				array (
						'db' => 'Category',
						'dt' => 'Category'
				),
				array (
						'db' => 'Format',
						'dt' => 'Format'
				),
				array (
						'db' => 'Automaticity',
						'dt' => 'Automaticity'
				),
				array (
						'db' => 'Process_type',
						'dt' => 'Process_type'
				),
				array (
						'db' => 'Access',
						'dt' => 'Access'
				),
				array (
						'db' => 'Real_time',
						'dt' => 'Real_time'
				),
				array (
						'db' => 'Source',
						'dt' => 'Source'
				),
				array (
						'db' => 'SecurityLevel',
						'dt' => 'SecurityLevel'
				),
				array (
						'db' => 'A',
						'dt' => 'A'
				),
				array (
						'db' => 'B',
						'dt' => 'B'
				),
				array (
						'db' => 'C',
						'dt' => 'C'
				),
				array (
						'db' => 'D',
						'dt' => 'D'
				),
				array (
						'db' => 'E',
						'dt' => 'E'
				),
				array (
						'db' => 'status_A',
						'dt' => 'status_A'
				),
				array (
						'db' => 'status_B',
						'dt' => 'status_B'
				),
				array (
						'db' => 'status_C',
						'dt' => 'status_C'
				),
				array (
						'db' => 'status_D',
						'dt' => 'status_D'
				),
				array (
						'db' => 'status_E',
						'dt' => 'status_E'
				),
				array (
						'db' => 'time_A',
						'dt' => 'time_A',
						'table2' => 'QRTZ_JOB_TRIGGERED',
						'field' => 'PREV_FIRE_TIME',
				),
				array (
						'db' => 'time_B',
						'dt' => 'time_B',
						'table2' => 'QRTZ_JOB_TRIGGERED',
						'field' => 'PREV_FIRE_TIME'
				),
				array (
						'db' => 'time_C',
						'dt' => 'time_C',
						'table2' => 'QRTZ_JOB_TRIGGERED',
						'field' => 'PREV_FIRE_TIME'
				),
				array (
						'db' => 'time_D',
						'dt' => 'time_D',
						'table2' => 'QRTZ_JOB_TRIGGERED',
						'field' => 'PREV_FIRE_TIME',
				),
				array (
						'db' => 'time_E',
						'dt' => 'time_E',
						'table2' => 'QRTZ_JOB_TRIGGERED',
						'field' => 'PREV_FIRE_TIME',
				),
				array (
						'db' => 'error_A',
						'dt' => 'error_A'
				),
				array (
						'db' => 'error_B',
						'dt' => 'error_B'
				),
				array (
						'db' => 'error_C',
						'dt' => 'error_C'
				),
				array (
						'db' => 'error_D',
						'dt' => 'error_D'
				),
				array (
						'db' => 'error_E',
						'dt' => 'error_E'
				),
				array (
						'db' => 'period',
						'dt' => 'period'
				),
				array (
						'db' => 'overtime',
						'dt' => 'overtime'
				),
				array (
						'db' => 'param',
						'dt' => 'param'
				),
				array (
						'db' => 'last_update',
						'dt' => 'last_update'
				),
				array (
						'db' => 'last_triples',
						'dt' => 'last_triples'
				),
				array (
						'db' => 'Triples_count',
						'dt' => 'Triples_count'
				),
				array (
						'db' => 'Triples_countRepository',
						'dt' => 'Triples_countRepository'
				),
				array (
						'db' => 'error',
						'dt' => 'error'
				),
				array (
						'db' => 'description',
						'dt' => 'description'
				),
				array (
						'db' => 'exec_A',
						'dt' => 'exec_A'
				)
				,
				array (
						'db' => 'LicenseUrl',
						'dt' => 'LicenseUrl'
				),
				array (
						'db' => 'LicenseText',
						'dt' => 'LicenseText'
				)
		);
		
		
		
		return SSP::nested ( $request, $this->sql_details, $table, $primaryKey, $columns );
	}
	
	
	
	
	
	function deleteRow($request,&$error){
		$processArray = $request['processArrayJson'];
		$this->db->setDB($this->sql_details['db2']);
		
		foreach ($processArray as $key => $value) {
			if ($key == 0) {
				$processesId = "'".$value."'";
			}
			else {
				$processesId = $processesId.", '".$value."'";
			}
		}
		$event = new sm_Event("DeleteProcess",$processArray);
		sm_EventManager::handle($event);
	
		$query="DELETE FROM `process_manager2` WHERE `process` IN (".$processesId.")";
		//sm_Logger::write($query);
		$result = $this->db->query($query);
		$error = $this->db->getError();
		return empty($error);
	}
	
	function disableRow($request,&$error){
		$processArray = $request['processArrayJson'];
		$this->db->setDB($this->sql_details['db2']);
	
		foreach ($processArray as $key => $value) {
			if ($key == 0) {
				$processesId = "'".$value."'";
			}
			else {
				$processesId = $processesId.", '".$value."'";
			}
		}
	
	
		$query="UPDATE `process_manager2` SET `exec_A`= 'no' WHERE `process` IN (".$processesId.")";
		$result = $this->db->query($query);
		$error = $this->db->getError();
		return empty($error);
	}
	
	function enableRow($request,&$error){
		$processArray = $request['processArrayJson'];
		$this->db->setDB($this->sql_details['db2']);
	
		foreach ($processArray as $key => $value) {
			if ($key == 0) {
				$processesId = "'".$value."'";
			}
			else {
				$processesId = $processesId.", '".$value."'";
			}
		}
	
	
		$query="UPDATE `process_manager2` SET `exec_A`= 'yes' WHERE `process` IN (".$processesId.")";
		$result = $this->db->query($query);
		$error = $this->db->getError();
		return empty($error);
	}
	
	function addRow($request,&$error)
	{
		$processArray = $request['processArrayJson'];
		$this->db->setDB($this->sql_details['db2']);
	/*	if ($processArray['process'] != ''){
			$keys = "process";
			$values = "'".$processArray['process']."'";
		}
		else {
			return 'Process (key) missing !!';
		}
		
		
		foreach ($processArray as $key => $value) {
			if ($value != '' and $key != 'process') {
				if ($key == 'period' || $key == 'Triples_count' || $key == 'Triples_countRepository') {
					$keys = $keys.", ".$key;
					$values = $values.", ".$value."";
				}
				else {
					$keys = $keys.", ".$key;
					$values = $values.", '".$value."'";
				}
			}
		
		}
		
		$query="INSERT INTO `process_manager2`(".$keys.") VALUES (".$values.")";*/
		$where = null;
		if(isset($processArray['process']) && $this->db->selectRow("process_manager2",array('process'=>$processArray['process'])))
		{
			$where['process']=$processArray['process'];
			unset($processArray['process']);
		}
		$result = $this->db->save("process_manager2", $processArray,$where);//query($query);
		$error = $this->db->getError();
		return empty($error);
	}
	
	function editCell($request,&$error)
	{
		$processArray = $request['processArrayJson'];
		
		$this->db->setDB($this->sql_details['db2']);
		
		$column = $processArray["column"];
		$process = $processArray["process"];
		$newContent = $processArray["newContent"];
		
		
		$query="UPDATE `process_manager2` SET ".$column." = '".$newContent."' WHERE `process` = '".$process."';";
		$result = $this->db->query($query);
		$error = $this->db->getError();
		return empty($error);
	}
	
	/**
	 * @desc Get the list of triples definition files
	 * @param OpenData $ont
	 * @return multitype:
	 */
	function getTriplesFiles(OpenData $oData=null){
	
		$files = array();
		if(!$oData)
			return $files;
	
		if($oData->getReal_time()=="no")
			$UPLOAD_DIR=sm_Config::get('STATICDATAPATH',"/media/Triples");
		else 
			$UPLOAD_DIR=sm_Config::get('REALTIMEDATAPATH',"/media/Triples");
		$UPLOAD_DIR=str_replace("\\", "/", $UPLOAD_DIR);
		if(substr($UPLOAD_DIR, -1)=="/")
			$UPLOAD_DIR.=$oData->getCategory()."/".$oData->getprocess();
		else
			$UPLOAD_DIR.="/".$oData->getCategory()."/".$oData->getprocess();
	
		if(is_dir($UPLOAD_DIR))
		{
				
			$file_name = glob($UPLOAD_DIR."/*/*/*/*/*.*",GLOB_BRACE); //$this->classPath . $class . $this->suffix;
			//At this point, we are relatively assured that the file name is safe
			// to check for it's existence and require in.
				
			if( FALSE === $file_name || count($file_name)==0) //FALSE === file_exists($file_name) )
				return $files;
			else
			{
				foreach($file_name as $k=>$filename)
				{
					$k=explode("/",$filename);
					$k=$k[count($k)-1];
	
					$pathData = pathinfo($filename);
					$size = $this->byteFormat(filesize($filename),"B");
					$folder=str_replace($UPLOAD_DIR."/", "", $pathData['dirname']);
					$files[$folder][]=array("File Name"=>$k,"Size"=>$size['value']." ".$size['unit'] ,"Creation Time"=>date ("Y/m/d H:i:s", filectime($filename)));
				}
			}
		}
		return $files;
	}
	function byteFormat($bytes, $from="", $unit = "", $decimals = 2) {
		$units = array('B' => 0,
				'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4,
				'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8,
				'K' => 1, 'M' => 2, 'G' => 3, 'T' => 4,
				'P' => 5, 'E' => 6, 'Z' => 7, 'Y' => 8
		);
	
		$value = 0;
	
		if ($bytes > 0) {
			if(array_key_exists($from, $units))
			{
				$bytes *= pow(1024,$units[$from]);
			}
			// Generate automatic prefix by bytes
			// If wrong prefix given
			if (!array_key_exists($unit, $units)) {
				$pow = floor(log($bytes)/log(1024));
				$unit = array_search($pow, $units);
			}
	
			// Calculate byte value by prefix
			$value = (floatval($bytes)/pow(1024,$units[$unit]));
		}
		else
		{
			$value=$bytes;
			$unit=$from;
		}
	
		// If decimals is not numeric or decimals is less than 0
		// then set default value
		if (!is_numeric($decimals) || $decimals < 0) {
			$decimals = 2;
		}
	
		// Format output
		return array("value"=>sprintf('%.' . $decimals . 'f ', $value),"unit"=>$unit);
		//return sprintf('%.' . $decimals . 'f '.$unit, $value);
	}
	static public function install($db)
	{
		include 'php/dbDetails.php';
		sm_Config::set("PROCESSMANAGERDB2",array('value'=>$sql_details['db2'],"description"=>"Set the Data Source Database Name #1"));
		sm_Config::set("PROCESSMANAGERDB1",array('value'=>$sql_details['db1'],"description"=>"Set the Task Scheduler Database Name #2"));
		sm_Config::set("PROCESSMANAGERDBUSER",array('value'=>$sql_details['user'],"description"=>"Set the Data Source Database User"));
		sm_Config::set("PROCESSMANAGERDBPWD",array('value'=>$sql_details['pass'],"description"=>"Set the Data Source Database Password"));
		sm_Config::set("PROCESSMANAGERDBURL",array('value'=>$sql_details['host'],"description"=>"Set the Data Source Database URL"));
		sm_Logger::write("Installed Open Data Manager Source Database Settings");
		
		/****** ACL Section ******************		
		sm_Logger::write("Installing Permissions: ODManager::Edit");
		sm_ACL::installPerm(array('permID'=>null,'permName'=>'OpenData Edit','permKey'=>'ODManager::Edit'));
		sm_Logger::write("Installing Permissions: ODManager::View");
		sm_ACL::installPerm(array('permID'=>null,'permName'=>'OpenData View','permKey'=>'ODManager::View'));
		sm_Logger::write("Permissions Installed");
		*/
	}
	
	static public function uninstall($db)
	{
		sm_Config::delete("PROCESSMANAGERDB1");
		sm_Config::delete("PROCESSMANAGERDB2");
		sm_Config::delete("PROCESSMANAGERDBUSER");
		sm_Config::delete("PROCESSMANAGERDBPWD");
		sm_Config::delete("PROCESSMANAGERDBURL");
	}
	
}
