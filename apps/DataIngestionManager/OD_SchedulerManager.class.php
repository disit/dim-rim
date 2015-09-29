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

class OD_SchedulerManager extends PMySqlConfig
{
	/**
	 * @desc Ajax Command Manager for sending command to scheduler. Available commands:
	 * 'scheduleProcess', 'scheduleProcessWithoutTrig', 'scheduleConcatProcessWithoutTrig', 'scheduleConcatProcess', 
	 * 'deleteProcess', 'launchProcess', 'pauseProcess', 'resumeProcess', 'checkExistJob',
	 * @param string $command
	 * @param array $request
	 * @return Ambigous <NULL, mixed>
	 */
	public function ajaxCommand($command,$request)
	{
		$ret = null;
		$error=null;
		switch ($command)
		{
			case 'scheduleProcess':
				$ret = $this->scheduleProcess($request);
				break;
			case 'scheduleProcessWithoutTrig':
				$ret =$this->scheduleProcessWithoutTrig($request);
				break;
			case 'scheduleConcatProcessWithoutTrig':
				$ret =$this->scheduleConcatProcessWithoutTrig($request);
				break;
			case 'scheduleConcatProcess':
				$ret =$this->scheduleConcatProcess($request);
				break;
			case 'deleteProcess':
			case 'launchProcess':
			case 'pauseProcess':
			case 'resumeProcess':
			case 'checkExistJob':
				$ret = $this->postCommand2Scheduler($request);
				break;					
		}
		return $ret;
	}
	
	/**
	 * @desc Schedule a process
	 * @param unknown $request
	 * @return mixed
	 */
	function scheduleProcess($request)
	{
		$processArray = $request['processArrayJson'];
		//$postData = array('json' => $processArrayJson);
	
	
		$actualTime = (string)round(microtime(true) * 1000);
		$postData["startAt"] = $actualTime;
		$postData["withIdentityNameGroup"] = $processArray["withIdentityNameGroup"];
		$postData["withPriority"] = $processArray["withPriority"];
		$postData["repeatForever"] = $processArray["repeatForever"];
		$postData["withIntervalInSeconds"] = $processArray["withIntervalInSeconds"];
		$postData["storeDurably"] = $processArray["storeDurably"];
		$postData["requestRecovery"] = $processArray["requestRecovery"];
		$postData["withJobIdentityNameGroup"] = $processArray["withJobIdentityNameGroup"];
		$postData["id"] = $processArray["id"];
		$postData["jobClass"] = $processArray["jobClass"];
		$jobDataMapTemp = $processArray["jobDataMap"];
	
		//Parameters array
		$processParametersPostArray = $jobDataMapTemp["#processParameters"];
		foreach($processParametersPostArray as $key => $value) {
			$processParametersArr[] = array($key => $value);
		}
		$jobDataMap["#processParameters"] = json_encode($processParametersArr);
	
		//Constraints array
		$jobConstraints = $jobDataMapTemp["#jobConstraints"];
		$jobConstraintsArr[] = $jobConstraints;
		$jobDataMap["#jobConstraints"] = json_encode($jobConstraintsArr);
	
		$jobDataMap["#isNonConcurrent"] = $jobDataMapTemp["#isNonConcurrent"];
		$jobDataMap["#jobTimeout"] = $jobDataMapTemp["#jobTimeout"];
		$postData["jobDataMap"] = $jobDataMap;
		$jsonData["json"] = json_encode($postData);
	
	
		$result = json_decode($this->postData($jsonData), true);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $request
	 * @return mixed
	 */
	function scheduleProcessWithoutTrig($request)
	{
		$processArray = $request['processArrayJson'];
	
		$postData["storeDurably"] = $processArray["storeDurably"];
		$postData["requestRecovery"] = $processArray["requestRecovery"];
		$postData["withJobIdentityNameGroup"] = $processArray["withJobIdentityNameGroup"];
		$postData["id"] = $processArray["id"];
		$postData["jobClass"] = $processArray["jobClass"];
	
		//Estazione dei parametri dall'array
		$jobDataMapTemp = $processArray["jobDataMap"];
		$processParametersPostArray = $jobDataMapTemp["#processParameters"];
		foreach($processParametersPostArray as $key => $value) {
			$processParametersArr[] = array($key => $value);
		}
	
		//Estrazione dei job da concatenare a quello che sta per essere avviato
		$nextJobsArr = array();
		if(isset($jobDataMapTemp["#nextJobs"])) 
			$nextJobsArr[] = $jobDataMapTemp["#nextJobs"];
		
	
	
		$jobDataMap["#isNonConcurrent"] = $jobDataMapTemp["#isNonConcurrent"];
		$jobDataMap["#jobTimeout"] = $jobDataMapTemp["#jobTimeout"];
		$jobDataMap["#processParameters"] = json_encode($processParametersArr);
		$jobDataMap["#nextJobs"] = json_encode($nextJobsArr);
		$postData["jobDataMap"] = $jobDataMap;
		//$processParametersArr[] = array($processParametersPostArray[$i] => $processParametersPostArray[$i + 1]);
		$jsonData["json"] = json_encode($postData);
		$result = json_decode($this->postData($jsonData), true);
		return $result;
	
	}
	
	/**
	 * 
	 * @param unknown $request
	 * @return mixed
	 */
	function scheduleConcatProcess($request)
	{
		$processArray = $request['processArrayJson'];
		//$postData = array('json' => $processArrayJson);
	
	
		$actualTime = (string)round(microtime(true) * 1000);
		$postData["startAt"] = $actualTime;
		$postData["withIdentityNameGroup"] = $processArray["withIdentityNameGroup"];
		$postData["withPriority"] = $processArray["withPriority"];
		$postData["repeatForever"] = $processArray["repeatForever"];
		$postData["withIntervalInSeconds"] = $processArray["withIntervalInSeconds"];
		$postData["storeDurably"] = $processArray["storeDurably"];
		$postData["requestRecovery"] = $processArray["requestRecovery"];
		$postData["withJobIdentityNameGroup"] = $processArray["withJobIdentityNameGroup"];
		$postData["id"] = $processArray["id"];
		$postData["jobClass"] = $processArray["jobClass"];
	
		//Estazione dei parametri dall'array
		$jobDataMapTemp = $processArray["jobDataMap"];
		$processParametersPostArray = $jobDataMapTemp["#processParameters"];
		foreach($processParametersPostArray as $key => $value) {
			$processParametersArr[] = array($key => $value);
		}
	
		//Estrazione dei job da concatenare a quello che sta per essere avviato
		$nextJobsArr = array();
		if(isset($jobDataMapTemp["#nextJobs"])) 
			$nextJobsArr[] = $jobDataMapTemp["#nextJobs"];
	
	
		$jobDataMap["#isNonConcurrent"] = $jobDataMapTemp["#isNonConcurrent"];
		$jobDataMap["#jobTimeout"] = $jobDataMapTemp["#jobTimeout"];
		$jobDataMap["#processParameters"] = json_encode($processParametersArr);
		$jobDataMap["#nextJobs"] = json_encode($nextJobsArr);
		$postData["jobDataMap"] = $jobDataMap;
		//$processParametersArr[] = array($processParametersPostArray[$i] => $processParametersPostArray[$i + 1]);
		$jsonData["json"] = json_encode($postData);
		$result = json_decode($this->postData($jsonData), true);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $request
	 * @return mixed
	 */
	function scheduleConcatProcessWithoutTrig($request)
	{
		$processArray = $request['processArrayJson'];
		//$postData = array('json' => $processArrayJson);
	
	
		// $actualTime = (string)round(microtime(true) * 1000);;
		// $postData["startAt"] = $actualTime;
		// $postData["withPriority"] = $processArray["withPriority"];
		// $postData["repeatForever"] = $processArray["repeatForever"];
		// $postData["withIntervalInSeconds"] = $processArray["withIntervalInSeconds"];
		$postData["storeDurably"] = $processArray["storeDurably"];
		$postData["requestRecovery"] = $processArray["requestRecovery"];
		$postData["withJobIdentityNameGroup"] = $processArray["withJobIdentityNameGroup"];
		$postData["id"] = $processArray["id"];
		$postData["jobClass"] = $processArray["jobClass"];
	
		//Estazione dei parametri dall'array
		$jobDataMapTemp = $processArray["jobDataMap"];
		$processParametersPostArray = $jobDataMapTemp["#processParameters"];
		foreach($processParametersPostArray as $key => $value) {
			$processParametersArr[] = array($key => $value);
		}
	
		//Estrazione dei job da concatenare a quello che sta per essere avviato
		$nextJobsArr = array();
		if(isset($jobDataMapTemp["#nextJobs"])) 
			$nextJobsArr[] = $jobDataMapTemp["#nextJobs"];
	
	
		$jobDataMap["#isNonConcurrent"] = $jobDataMapTemp["#isNonConcurrent"];
		$jobDataMap["#jobTimeout"] = $jobDataMapTemp["#jobTimeout"];
		$jobDataMap["#processParameters"] = json_encode($processParametersArr);
		$jobDataMap["#nextJobs"] = json_encode($nextJobsArr);
		$postData["jobDataMap"] = $jobDataMap;
		//$processParametersArr[] = array($processParametersPostArray[$i] => $processParametersPostArray[$i + 1]);
		$jsonData["json"] = json_encode($postData);
		$result = json_decode($this->postData($jsonData), true);
		return $result;
	}
	
	/**
	 * @desc Create a command specified in the request for a job and post it to the scheduler 
	 * @param array $request
	 * @return mixed
	 */
	function postCommand2Scheduler($request){
		$processArray = $request['processArrayJson'];
		$postData["id"] = $processArray["id"];
		$postData["jobName"] = $processArray["jobName"];
		$postData["jobGroup"] = $processArray["jobGroup"];
	
		$jsonData["json"] = json_encode($postData);
		$result = json_decode($this->postData($jsonData), true);
		return $result;
	}
	
	/**
	 * @desc Post Data to the scheduler API interface
	 * @param array $data
	 * @param string $url
	 * @return string
	 */
	function postData($data, $url=null) {
	
		//$url = 'URL';
		//$data = array('field1' => 'value', 'field2' => 'value');
		if(!$url)
			$url = $this->sql_details['schedulerUrl'];
		$options = array(
				'http' => array(
						'header' => "Content-type: application/x-www-form-urlencoded\r\n",
						'method' => 'POST',
						'content' => http_build_query($data),
				)
		);
		$context = stream_context_create($options);
		return file_get_contents($url, false, $context);
	}
	
	/**
	 * @desc Get allocated tasks of a process.  
	 * @param <String> $process
	 * @return Ambigous <multitype:, $result>
	 */
	function getTasksProcess($process)
	{
		$db = new sm_Database($this->sql_details['host'],$this->sql_details['user'],$this->sql_details['pass']);
		$db->setDB($this->sql_details['db1']);
		$tasks = $db->select("QRTZ_JOB_DETAILS",array("JOB_GROUP"=>$process),array("JOB_NAME"));
		return $tasks;
	}
	
	/**
	 * @desc Delete from scheduler all allocated tasks of a list of processes or single one. 
	 * @param <String, Array> $processes
	 * @return boolean|Ambigous <boolean, NULL, mixed>
	 */	
	function deleteProcess($processes)
	{
		$ret=true;
		if(!isset($processes))
			return false;	
		$processList= array();
		if(is_string($process))
			$processList[]=$processes;
		else if(is_array($process));
			$processList=$processes;
		foreach ($processList as $process)
		{
			
			$tasks = $this->getTasksProcess($process);
			$processArray = array();
			$processArray['processArrayJson']["id"] = "deleteJob";
			foreach ($tasks as $t)
			{
				if ($t['JOB_NAME']!="") {
					$processArray['processArrayJson']["jobName"] = $t['JOB_NAME'];
					$processArray['processArrayJson']["jobGroup"] =$process;
					$ret |= $this->ajaxCommand("deleteProcess",$processArray);
				}
			}	
		}
		return $ret;
	}
	
	static public function install($db)
	{
		include 'php/dbDetails.php';
		
		sm_Config::set('PROCESSMANAGERSCHEDULERURL',array('value'=>$sql_details['schedulerUrl'],"description"=>"Set the Open Data Manager Scheduler URL to use"));
		sm_Config::set('PROCESSMANAGERWEBSCHEDULERURL',array('value'=>$sql_details['schedulerWebUrl'],"description"=>"Set the Open Data Manager Scheduler Web Page URL"));
		sm_Logger::write("Installed Open Data Manager Scheduler URL");
	
	}
	
	static public function uninstall($db)
	{
		sm_Config::delete('PROCESSMANAGERSCHEDULERURL');
		sm_Config::delete('PROCESSMANAGERWEBSCHEDULERURL');
	}
	
}