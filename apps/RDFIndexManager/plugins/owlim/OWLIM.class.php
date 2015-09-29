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

class OWLIM implements sm_Module
{
	
	protected $sql_details;
	
	function __construct()
	{
		$db = new MySqlConfig();
		$this->sql_details = $db->getSqlDetails();
	}
	
	function create($name=null){
		if(!$name)
			return false;
		
		$tpl = new sm_Template();
		$filename = __DIR__.DIRECTORY_SEPARATOR."settings".DIRECTORY_SEPARATOR."memory_settings.tpl.txt";
		
		$tpl->newTemplate("memory_owlim", $filename);
		$tpl->addTemplateData("memory_owlim", array("name"=>$name));
		$body = $str = $tpl->display("memory_owlim");
		$urlName = "http://www.disit.org%23".str_replace(" ", "_", $name);
		$url = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$url1 = $url."repositories/SYSTEM/rdf-graphs/service?graph=";
		$url1.= $urlName;
		$triple = "<"."http://www.disit.org#".str_replace(" ", "_", $name)."> a <http://www.openrdf.org/config/repository#RepositoryContext>.";
		$url2 = $url."repositories/SYSTEM/statements";
		//sm_Logger::write($body."\n".$url1."\n".$url2."\n".$triple);
		
		$client = APIRest_Client::call("POST", $url1, $body,null,null,"application/x-turtle");
		//sm_Logger::write($body);
		//sm_Logger::write($client->getResponse());
	
		$client = APIRest_Client::call("POST", $url2, $triple,null,null,"application/x-turtle");
		//sm_Logger::write($client->getResponse());
		
		$ret = $this->exist($name); //$client->getResponse();
	/*	if($ret)
			$this->flush($name);*/
		return $ret;
	}
	
	function delete($name=null){
		//DELETE /openrdf-sesame/repositories/mem-rdf HTTP/1.1
		$url = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$deleteUrl = $url."repositories/".$name;
		$client = APIRest_Client::call("DELETE", $deleteUrl, null);
		$ret = !$this->exist($name); //$client->getResponse();
		/*	if($ret)
		 $this->flush($name);*/
		if(!$ret)
		{
			$ret['url']= $deleteUrl;
			$ret['error'] = $client->getResponseMessage();
		}
		return $ret;
	}
	
	function clear($name=null){
		//DELETE /openrdf-sesame/repositories/mem-rdf HTTP/1.1
		$url = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$clearUrl = $url."repositories/".$name."/statements";
		$client = APIRest_Client::call("DELETE", $clearUrl,null);
		$ret = !$this->exist($name); //$client->getResponse();
		/*	if($ret)
		 $this->flush($name);*/
		if(!$ret)
		{
			$ret['url']= $clearUrl;
			$ret['error'] = $client->getResponseMessage();
		}
		return $ret;
	}
	
	function deleteRepository($name=null)
	{
		if(!$name || !$this->exist($name))
			return false;
		sm_Logger::write("OWLIM delete: ".$name);
	}
	
	function flush($name)
	{
		if(!$name)
			return false;
		$repos=str_replace(" ", "_", $name);
		$url = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$url.= "repositories/".$repos."/statements";
		$triple = '<http://www.example.com> <http://www.ontotext.com/owlim/system#flush> "" .';
		$client = APIRest_Client::call("POST", $url, $triple,null,null,"text/plain");
		//sm_Logger::write($client->getResponse());
		return true;
	}
	
	function creategeospatial($name)
	{
		if(!$name)
			return false;
		$repos=str_replace(" ", "_", $name);
		$url = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$url.= "repositories/".$repos;
		
		$query = 'ASK  { _:b1 <http://www.ontotext.com/owlim/geo#createIndex> _:b2. }';
		
		$data['query']=$query;
		$client = APIRest_Client::call("GET", $url, $data,null,null,"text/plain");
		$response = str_replace("\n","",$client->getResponse());
		//sm_Logger::write($response);
		return $response=="true"?true:false;
	}
	
	function exist($name)
	{
		if(!$name)
			return false;
		$repos=str_replace(" ", "_", $name);
		$url = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$url.= "repositories/SYSTEM";
		$query = 'ASK { ?s <http://www.openrdf.org/config/repository#repositoryID> "%ID" }';
		$query = str_replace("%ID",$name,$query);
		$data['query']=$query;
		$client = APIRest_Client::call("GET", $url, $data,null,null,"text/plain");
		$response = str_replace("\n","",$client->getResponse());
		//sm_Logger::write($response);
		return $response=="true"?true:false;
	}
	
	
	static function install($db)
	{
		sm_Config::set('OWLIMOPENRDFWORKBENCH',array("value"=>"http://localhost:8080/openrdf-workbench/","description"=>"OpenRDF Workbench Address"));
		SiiMobilityRepositoryManager::addType("OWLIM");
		sm_Config::set('OWLIMOPENRDFURL',array("value"=>"http://localhost:8080/openrdf-sesame/","description"=>"OpenRDF Sesame Database Address"));
		
		return true;
	}
	
	static function uninstall($db)
	{
		sm_Config::delete('OWLIMOPENRDFWORKBENCH');
		sm_Config::delete('OWLIMOPENRDFURL');
		SiiMobilityRepositoryManager::removeType("OWLIM");
		return true;
	}
	
	function doScript(SiiMobilityRepository $generation)
	{
		$res = $this->_getScript($generation);	
		$generation->setScriptPath(ltrim($res['path'],"/"));
		$generation->update();
		return $res;
		
	}
	
	
	/**
	 *
	 * Get a script
	 *
	 * @param string $currentSession the id of the session whose the script is related
	 * @param string $repositoryID the id of the repository to create
	 */
	private function _getScript(SiiMobilityRepository $generation){
	
		$currentSession = $generation->getID();
		$repositoryID=$generation->getRepositoryID();
		$parentID = $generation->getParentID();
		$flush=false;
		$user = sm_Config::get('USERCONFIGCTRL',"");
		$passw = sm_Config::get('PWDCONFIGCTRL',"");
		$restcallUrl = "http://localhost".sm_Config::get("BASEURL","")."api/IndexGenerator/session/";
		$restcallUrlCreate = "http://localhost".sm_Config::get("BASEURL","")."api/owlim/create/";
		$restcallUrlFlush = "http://localhost".sm_Config::get("BASEURL","")."api/owlim/flush/";
		$restcallUrlCommit="http://localhost".sm_Config::get("BASEURL","")."api/IndexGenerator/commit/";
		$restcallUrlGeoIndex = "http://localhost".sm_Config::get("BASEURL","")."api/owlim/create/geospatial/";
		$restAuth = "-u ".$user.":".$passw;
	
		$indexFile =  "/opt/indexgenerator/script/".$repositoryID."/index.txt";
		$lockFile  = "/opt/indexgenerator/script/".$repositoryID."/.lock";
		$logFile   = "/opt/indexgenerator/script/".$repositoryID."/".$repositoryID.".csv";
		$logDir   = "/opt/indexgenerator/script/".$repositoryID."/log";
		$progressFile =  "/opt/indexgenerator/script/".$repositoryID."/progress.out";
		$ontologiesPath=sm_Config::get('ONTOLOGIESPATH',"/media/rim/Ontologie");
		$staticDataPath=sm_Config::get('STATICDATAPATH',"/media/rim/Triples");
		$realTimeDataPath=sm_Config::get('REALTIMEDATAPATH',"/media/rim/Triples");
		$reconciliationsPath=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");
		$sesameUrl = sm_Config::get('OWLIMOPENRDFURL',"http://localhost:8080/openrdf-sesame/");
		$scriptPath= '/opt/indexgenerator/script/owlim.sh';//"/opt/owlim/getting-started/example.sh";
		
		// Get the ontologies array
		$ontologies = $this->_getData("ontologies", $currentSession);
		// Get the static data array
		$staticData = $this->_getData("staticData", $currentSession);
		// Get the realtime data array
		$realTimeData = $this->_getData("realTimeData", $currentSession);
		// Get the reconciliations array
		$reconciliations = $this->_getData("reconciliations", $currentSession);
		$N = count($ontologies) + count($staticData) + count($realTimeData) + count($reconciliations);
		
		// The name of the script file
		//TODO Va messo in cartella con data?
		//$fileName = '/opt/lampp/htdocs/indexgenerator/script/generateindex.sh';
		//$fileName = '/home/ubuntu/Desktop/owlim-lite-5.4.6287/getting-started/generateindex.sh';
		$fileDir = '/opt/indexgenerator/script/'.$repositoryID;
		
		if (!file_exists($fileDir)) {
			mkdir($fileDir, 0777, true);
		}
		if (!file_exists($logDir)) {
			mkdir($logDir, 0777, true);
		}
		$fileName = $fileDir.'/owlim_'.$generation->getID().'.sh';
		// Create the script file
		$fp = fopen($fileName, "w");
	
		// First line of the script
		fwrite($fp, "#!/bin/bash\n");
		fwrite($fp, "\n");
		
		// Set configuration parameters
		fwrite($fp, "#Set configuration parameters\n");
		fwrite($fp, "sessionId=\"" . $currentSession . "\" # DO NOT CHANGE THIS PARAMETER!\n");
		fwrite($fp, "scriptPath=\"".$scriptPath."\"\n");
		fwrite($fp, "ontologiesPath=\"".$ontologiesPath."\"\n");
		fwrite($fp, "staticDataPath=\"".$staticDataPath."\"\n");
		fwrite($fp, "realTimeDataPath=\"".$realTimeDataPath."\"\n");
		fwrite($fp, "reconciliationsPath=\"".$reconciliationsPath."\"\n");
		fwrite($fp, "sesameUrl=\"".$sesameUrl."\"\n");
		fwrite($fp, "repositoryId=\"" . $repositoryID . "\"\n");
		fwrite($fp, "baseContext=\"http://www.disit.org/km4city/resource/\"\n");
		fwrite($fp, "indexFile=\"".$indexFile."\"\n");
		fwrite($fp, "lockFile=\"".$lockFile."\"\n");
		fwrite($fp, "logFile=\"".$logFile."\"\n");
		fwrite($fp, "progressFile=\"".$progressFile ."\"\n"); 
		fwrite($fp, "logDir=\"".$logDir ."\"\n");
		fwrite($fp, "	#  Log file copy\n");
		fwrite($fp, "logFileBackup=`date \"+%d_%m_%y_%H_%M_%S\"`.csv\n");
		fwrite($fp, "progressFileBackup=`date \"+%d_%m_%y_%H_%M_%S\"`.out\n");
		fwrite($fp, "\n");
		fwrite($fp, "# Moves to load script directory\n");
		fwrite($fp, "cd ".$fileDir."/\n");
		fwrite($fp, "\n");
	
		# Looking for lock file
		fwrite($fp, "# Looks for the lock file; if found it, the process ends\n");
		fwrite($fp, "if [ -f \$lockFile ] ; then\n");
		fwrite($fp, "	clear\n");
				fwrite($fp, "	echo\n");
						fwrite($fp, "	echo \"---------------------------------------------------------------\"\n");
								fwrite($fp, "	echo\n");
										fwrite($fp, "	echo \"ERROR!!!\"\n");
										fwrite($fp, "	echo \"Lock file found (".$lockFile.")\"\n");
										fwrite($fp, "	echo \"Another index generation is already running.\"\n");
										fwrite($fp, "	echo \"Is not possible to generate a new index!\"\n");
										fwrite($fp, "	echo\n");
				fwrite($fp, "	echo\n");
				fwrite($fp, "	exit\n");
							fwrite($fp, "fi\n");
							fwrite($fp, "\n");
							
				
				fwrite($fp, "	# Start dateTime\n");
				fwrite($fp, "	startTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
				fwrite($fp, "# Remove progress file\n");
				fwrite($fp, "rm progress.out\n");
				fwrite($fp, "# Remove log file\n");
				fwrite($fp, "rm \$logFile\n");
				fwrite($fp, "echo \"*********** START OWLIM SCRIPT *****************\" >> \$progressFile\n");
				fwrite($fp, "echo \"Start Creation/Cloning \$startTime\" >> \$progressFile\n");
				
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				# Create lock file and save generation start date and time
				fwrite($fp, "# Create the lock file\n");
				fwrite($fp, "echo ".$N." > \$lockFile\n");
				fwrite($fp, "echo ".$N." > \$indexFile\n");
				fwrite($fp, "# Save to database the generation start dateTime\n");
				fwrite($fp, "curl -X PUT ".$restAuth." ".$restcallUrl."\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationStart\\\"}\"\n");
				fwrite($fp, "echo\n");
				fwrite($fp, "\n");
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				fwrite($fp, "echo \"*************** START OWLIM COMMIT *************\" >> \$progressFile\n");
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				if(!$this->exist($repositoryID))
				{
					fwrite($fp, "echo \"********** CREATE REPOSITORY **********\" >> \$progressFile\n");
					fwrite($fp, "# Create the new repository in Sesame\n");
					fwrite($fp, "echo \"Create the new repository in Sesame\"\n");
					fwrite($fp, "curl -X GET ".$restAuth." ".$restcallUrlCreate.$repositoryID."\n");
					fwrite($fp, "echo\n");
					fwrite($fp, "\n");
				
				
					if(!empty($parentID))
					{
						fwrite($fp, "echo \"********** Flush the parent repository in Sesame **********\" >> \$progressFile\n");
						fwrite($fp, "# Flush the parent repository in Sesame\n");
						fwrite($fp, "echo \"Flush the parent repository in Sesame\"\n");
						fwrite($fp, "curl -X GET ".$restAuth." ".$restcallUrlFlush.$parentID."\n");
						fwrite($fp, "echo\n");
						fwrite($fp, "\n");
						/*fwrite($fp, "# Save to database the generation Update Step: Cloning Repository\n");
						fwrite($fp, "curl -X PUT ".$restAuth." ".$restcallUrl."\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationStart\\\"}\"\n");
						fwrite($fp, "\n");*/
						$this->_cloneScript($fp,$generation);
					}
				}
				else
				{
					fwrite($fp, "echo \"********** RESTART TOMCAT SERVER **********\" >> \$progressFile\n");
					$this->_restartScript($fp, $generation);
				}
				
				fwrite($fp, "	# Start dateTime\n");
				fwrite($fp, "	startTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
				fwrite($fp, "echo \"END Creation/Cloning \$startTime\" >> \$progressFile\n");			//
							//
							// ONTOLOGIES
							//
							//
	
				
				
	
				if (count($ontologies) > 0) {
					fwrite($fp, "echo \"********** COMMIT ONTOLOGIES **********\" >> \$progressFile\n");
					$flush=true;
					fwrite($fp, "# Declares each ontology to use\n");
	
					// For each ontology, declares it in the script array
					for($i = 0; $i < count($ontologies); $i ++) {
					$folder = Versioner::getPathFromDateTime($ontologies[$i]["SelectedVersion"]);
							fwrite($fp, "declare -A ontology" . $i ."=([name]=" . $ontologies[$i]["Name"] . " [category]=Ontology [SelectedVersion]=\"" . $folder . "\")\n");
					}
					fwrite($fp, "\n");
	
							// Loop over each ontology, to get parameters and load triples
					fwrite($fp, "# Loops over each ontology\n");
						fwrite($fp, "for i in {0.." . (count($ontologies) - 1) . "}\n");
						fwrite($fp, "do\n");
					fwrite($fp, "	name=\"ontology\$i[name]\"\n");
					fwrite($fp, "	category=\"ontology\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"ontology\$i[SelectedVersion]\"\n");
						fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Run the load process\n");
					
					/*fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category} " .
							"preload=\${ontologiesPath}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					*/
					fwrite($fp, "	bash \${scriptPath} \${sesameUrl} \${repositoryId} \${baseContext}\${!category} " .
							"\${ontologiesPath}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> \$logDir/\$logFileBackup\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
					fwrite($fp, "# Lock the Ontology Already Inserted\n");
					fwrite($fp, "echo \"Lock the Ontology Already Inserted\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/ontologies/\${!name}\n");
					fwrite($fp, "echo\n");
					
					fwrite($fp, "done\n");
					
					
					fwrite($fp, "\n");
					fwrite($fp, "\n");
					
	
	}
	
	
	
	
	
								//
								//
								// STATIC DATA
								//
								//
	
							
	
				if (count($staticData) > 0) {
					fwrite($fp, "echo \"********** COMMIT STATIC DATA **********\" >> \$progressFile\n");
					$flush=true;
					fwrite($fp, "# Declares each static data to use\n");
	
					// For each static data, declares it in the script array
						for($i = 0; $i < count($staticData); $i ++) {
						$folder = Versioner::getPathFromDateTime($staticData[$i]["SelectedVersion"]);
						fwrite($fp, "declare -A staticdata" . $i ."=([name]=" . $staticData[$i]["Name"] . " [category]=" . $staticData[$i]["Category"] . " [SelectedVersion]=\"" . $folder . "\")\n");
								}
								fwrite($fp, "\n");
	
								// Loop over each static data, to get parameters and load triples
								fwrite($fp, "# Loops over each static data\n");
								fwrite($fp, "for i in {0.." . (count($staticData) - 1) . "}\n");
					fwrite($fp, "do\n");
								fwrite($fp, "	name=\"staticdata\$i[name]\"\n");
								fwrite($fp, "	category=\"staticdata\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"staticdata\$i[SelectedVersion]\"\n");
					fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Run the load process\n");
					
				/*	fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
							"preload=\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					*/
					
					fwrite($fp, "	bash \${scriptPath} \${sesameUrl} \${repositoryId} \${baseContext}\${!category}/\${!name} " .
							"\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					
					
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> \$logDir/\$logFileBackup\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
					fwrite($fp, "# Lock the Static Data Already Inserted\n");
					fwrite($fp, "echo \"Lock the Static Data Already Inserted\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/staticdata/\${!name}\n");
					fwrite($fp, "echo\n");
					fwrite($fp, "done\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
	}
	
	
	
	
	
						//
						//
						// REAL TIME DATA
						//
						//
	
					
					if (count($realTimeData) > 0) {
						fwrite($fp, "echo \"********** COMMIT REAL TIME DATA **********\" >> \$progressFile\n");
						$flush=true;
					// Declare an associative array
						fwrite($fp, "# Declares each realtime data to use\n");
	
						$Nrt=0;
							// For each realtime data, declares it in the script array
						for($i = 0; $i < count($realTimeData); $i ++) {
								$startDateTime = $realTimeData[$i]["TripleStart"];
								if (strpos($startDateTime,'from first') !== false)
									$startDateTime = "1970-01-01 00:00:00";
								$endDateTime = $realTimeData[$i]["TripleEnd"];
								if (strpos($endDateTime,'until last') !== false)
										$endDateTime = date("Y-m-d H-i-s",time());//"3000-01-01 00:00:00";
								
								//fwrite($fp, "declare -A realtimedata" . $i ."=([name]=" . realtimedata[$i]["Name"] . " [category]=" . $realTimeData[$i]["Category"] . " [TripleStart]=\"" . $startDateTime . "\" [TripleEnd]=\"" . $endDateTime . "\")\n");
								$directory =  $realTimeDataPath."/".$realTimeData[$i]['Category']."/".$realTimeData[$i]['Name']."/";
								$files = Versioner::getResourcesByTimeInterval($directory, $startDateTime, $endDateTime);
								for($j = 0; $j < count($files); $j ++) {
									
									$folder = $files[$j];
									fwrite($fp, "declare -A realtimedata" . $Nrt ."=([name]=" . $realTimeData[$i]["Name"] . " [category]=" . $realTimeData[$i]["Category"] . " [SelectedVersion]=\"" . $folder . "\")\n");
									$Nrt++;
								}
								//$N+=count($files);
						}
							fwrite($fp, "\n");
					fwrite($fp, "current=\"NULL\"\n");
								// Loop over each static data, to get parameters and load triples
					fwrite($fp, "# Loops over each realtime data\n");
					fwrite($fp, "for i in {0.." . ($Nrt-1) . "}\n");
					fwrite($fp, "do\n");
					fwrite($fp, "	name=\"realtimedata\$i[name]\"\n");
					fwrite($fp, "	category=\"realtimedata\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"realtimedata\$i[SelectedVersion]\"\n");
					
					fwrite($fp, "	if [ \$current != \${!name} ]; then\n");
					fwrite($fp,	"		current=\"\${!name}\"\n");
					fwrite($fp,	"   	# Start dateTime\n");
					fwrite($fp, "		startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	fi\n");
					
					
					fwrite($fp, "	# Run the load process\n");
					
				
					fwrite($fp, "	bash \${scriptPath} \${sesameUrl} \${repositoryId} \${baseContext}\${!category}/\${!name} " .
							"\${realTimeDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					
					fwrite($fp, "	if [ \$i -eq ".($Nrt-1)." ]; then\n");
					fwrite($fp, "		# End dateTime\n");
					fwrite($fp, "		endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "		# Save execution time in logs\n");
					fwrite($fp, "		echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "		echo \"\${!name};\$startDateTime;\$endDateTime\" >> \$logDir/\$logFileBackup\n");
					fwrite($fp, "		# Lock the RealTime Data Already Inserted\n");
					fwrite($fp, "		echo \"Lock the RealTime Data Already Inserted\"\n");
					fwrite($fp, "		curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/realtimedata/\${!name}\n");
					fwrite($fp, "	else\n");
					fwrite($fp, "			let \"j= \$i + 1\"\n");
					fwrite($fp, "			next=\"realtimedata\$j[name]\"\n");
					fwrite($fp, "			if [ \${!next} != \${!name} ]; then\n");
					fwrite($fp, "				# End dateTime\n");
					fwrite($fp, "				endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "				# Save execution time in logs\n");
					fwrite($fp, "				echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "				echo \"\${!name};\$startDateTime;\$endDateTime\" >> \$logDir/\$logFileBackup\n");
					fwrite($fp, "				# Lock the RealTime Data Already Inserted\n");
					fwrite($fp, "				echo \"Lock the RealTime Data Already Inserted\"\n");
					fwrite($fp, "				curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/realtimedata/\${!name}\n");
					fwrite($fp, "			fi\n");
					fwrite($fp, "	fi\n");
					
					fwrite($fp, "\n");
					fwrite($fp, "\n");
					fwrite($fp, "echo\n");
					fwrite($fp, "done\n");
					fwrite($fp, "# Lock the RealTime Data Already Inserted\n");
					fwrite($fp, "echo \"Lock the RealTime Data Already Inserted\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/realtimedata/\n");
					
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
	
	}
	
	
	
	
	
						//
						//
						// RECONCILIATIONS
						//
						//
	
					
	
				if (count($reconciliations) > 0) {
					fwrite($fp, "echo \"********** COMMIT RECONCILIATIONS DATA **********\" >> \$progressFile\n");
					$flush=true;
					fwrite($fp, "# Declares each reconciliation procedure to use\n");
	
					// For each reconciliation, declares it in the script array
					for($i = 0; $i < count($reconciliations); $i ++) {
					$folder = Versioner::getPathFromDateTime($reconciliations[$i]["SelectedVersion"]);
					fwrite($fp, "declare -A reconciliation" . $i ."=([name]=" . $reconciliations[$i]["Name"] . " [category]=Riconciliazioni [SelectedVersion]=\"" . $folder . "\")\n");
						}
						fwrite($fp, "\n");
	
						// Loop over each reconciliation, to get parameters and load triples
						fwrite($fp, "# Loops over each reconciliation\n");
						fwrite($fp, "for i in {0.." . (count($reconciliations) - 1) . "}\n");
					fwrite($fp, "do\n");
						fwrite($fp, "	name=\"reconciliation\$i[name]\"\n");
						fwrite($fp, "	category=\"reconciliation\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"reconciliation\$i[SelectedVersion]\"\n");
					fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Run the load process\n");
					
					/*fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
							"preload=\${reconciliationsPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					*/
					
					fwrite($fp, "	bash \${scriptPath} \${sesameUrl} \${repositoryId} \${baseContext}\${!category}/\${!name} " .
							"\${reconciliationsPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
						
					
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> \$logDir/\$logFileBackup\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
					fwrite($fp, "# Lock the Reconciliations Data Already Inserted\n");
					fwrite($fp, "echo \"Lock the Reconciliations Data Already Inserted\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/reconciliations/\${!name}\n");
					fwrite($fp, "echo\n");
					
					fwrite($fp, "done\n");
						fwrite($fp, "\n");
						fwrite($fp, "\n");
	
	}
	
	//Create Context data for static and realtime Dataset
	if(count($staticData) || count($realTimeData))
	{
		$sourcefile=$fileDir;
		$context = "urn:km4city:context:metadata";
		$this->_createContext($generation, $sourcefile);
		fwrite($fp,"echo \"Set Context for Static and Realtime Data Set\" >> \$progressFile\n");
		fwrite($fp,"echo\n");
		fwrite($fp, "	bash \${scriptPath} \${sesameUrl} \${repositoryId} ".$context." ".$sourcefile." username=admin password=ubuntu queryfile=none.\n");
	}
	
	
					if($flush){
						fwrite($fp, "echo \"********** Create Geo Spatial Index in Sesame **********\" >> \$progressFile\n");
						fwrite($fp, "# Create Geo Spatial Index in Sesame\n");
						fwrite($fp, "echo \"Create Geo Spatial Index in Sesame\"\n");
						fwrite($fp, "curl -X GET ".$restAuth." ".$restcallUrlGeoIndex.$repositoryID."  >> \$progressFile\n");
						fwrite($fp, "echo\n");
						fwrite($fp, "\n");
						fwrite($fp, "echo \"********** Flush the repository in Sesame **********\" >> \$progressFile\n");
						fwrite($fp, "# Flush the repository in Sesame\n");
						fwrite($fp, "echo \"Flush the repository in Sesame\"\n");
						fwrite($fp, "curl -X GET ".$restAuth." ".$restcallUrlFlush.$repositoryID."  >> \$progressFile\n");
						fwrite($fp, "echo\n");
						fwrite($fp, "\n");
						
						$this->_restartScript($fp,$generation);
					}
					fwrite($fp, "echo \"********** END COMMIT (Removing files) **********\" >> \$progressFile\n");
	// Close the generation on database and remove lock file
					fwrite($fp, "# Save to database the generation end dateTime\n");
					fwrite($fp, "curl -X PUT ".$restAuth." ".$restcallUrl."\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationEnd\\\"}\"\n");
					
					fwrite($fp, "# Remove the lock file\n");
					fwrite($fp, "rm \$lockFile\n");
					fwrite($fp, "# Remove progress file\n");
					
					fwrite($fp, "	# EndTime dateTime\n");
					fwrite($fp, "	endTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "echo \"\$endTime\" >> \$progressFile\n");
					fwrite($fp, "mv \$progressFile \$logDir/\$progressFileBackup\n");
					fwrite($fp, "rm \$progressFile\n");
					
					// Close the script file
					fclose($fp);
					if (file_exists($fileName)) {
						chmod($fileName, 0777);
					}
					return array('path' => $fileName);
					//return json_encode(array('path' => $fileName));;
						
	}
	
	private function _getData($dataType, $idGeneration) {
	
						// Set the select query according to the choosen data type
						if ($dataType == "ontologies") {
			$selectQuery = "SELECT ontologies.Name, Ontologies_Generations.TripleDate AS SelectedVersion
				FROM ontologies
				INNER JOIN Ontologies_Generations
				ON ontologies.Name = Ontologies_Generations.ID_Ontology AND Ontologies_Generations.Clone=0 AND Ontologies_Generations.Locked=0 AND Ontologies_Generations.ID_Generation = " . $idGeneration;
						}
						else if ($dataType == "staticData") {
			$selectQuery = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart AS SelectedVersion
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.Locked=0 AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'no'";
						}
		else if ($dataType == "realTimeData") {
			$selectQuery = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart, OpenData_Generations.TripleEnd
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.Locked=0 AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'yes'";
						}
						else if ($dataType == "reconciliations") {
			$selectQuery = "SELECT Reconciliations.Name, Reconciliations_Generations.TripleDate AS SelectedVersion
				FROM Reconciliations
				INNER JOIN Reconciliations_Generations
				ON Reconciliations.Name = Reconciliations_Generations.ID_Reconciliation AND Reconciliations_Generations.Clone=0 AND Reconciliations_Generations.Locked=0 AND Reconciliations_Generations.ID_Generation = " . $idGeneration;
					}
	
					//Create the bindings array
					$bindings = array ();
	
					// Get an handle to the database connection
					$db = MySqlConnector::sql_connect ($this->sql_details);
	
					// The query to get the data
			$data = MySqlConnector::sql_select( $db, $bindings, $selectQuery);
	
			return $data;
			
		}
		
		private function _cloneScript($fp,Generation $generation)
		{
			$repositoryFolder="/usr/share/tomcat7/.aduna/openrdf-sesame/repositories/";
			$target = $repositoryFolder.$generation->getRepositoryID();
			$source = $repositoryFolder.$generation->getParentID();
			if(!$fp)
				return;
			$line[]= "echo \"Stopping Tomcat7\"\n";
			$line[]="# Stopping Tomcat7 Server\n";
			$line[]="service tomcat7 stop \n";
			$line[]= "echo \"Copy Repository from ".$source." to ".$target."\"\n";
			$line[]="# Copy Repository from ".$source." to ".$target."\n";
			$line[]="cp -pR ".$source." ".$target."\n";
		/*	$line[]="# Assign mode & permissions to ".$target."\n";
			$line[]="chown -R tomcat7:tomcat7 ".$target."\n";
			$line[]="chmod 755 -R ".$target."\n";*/
			$line[]="echo \"Restarting Tomcat7\"\n";
			$line[]="# Restarting Tomcat7 Server \n";
			$line[]="service tomcat7 start \n";
			foreach ($line as $l){
				fwrite($fp,$l);
			}
			
		}
		
		private function _restartScript($fp,Generation $generation)
		{
			$repositoryFolder="/usr/share/tomcat7/.aduna/openrdf-sesame/repositories/";
			
			$source = $repositoryFolder.$generation->getRepositoryID()."/owlim-storage";
			if(!$fp)
				return;
			$line[]= "echo \"Stopping Tomcat7\"\n";
			$line[]="# Stopping Tomcat7 Server\n";
			$line[]="service tomcat7 stop \n";
			$line[]= "echo \"Remove Repository Lock from ".$source."\"\n";
			$line[]="# Remove Repository Lock from ".$source."\n";
			$line[]="rm  ".$source."/lock \n";
			/*	$line[]="# Assign mode & permissions to ".$target."\n";
			 $line[]="chown -R tomcat7:tomcat7 ".$target."\n";
			 $line[]="chmod 755 -R ".$target."\n";*/
			$line[]="echo \"Restarting Tomcat7\"\n";
			$line[]="# Restarting Tomcat7 Server \n";
			$line[]="service tomcat7 start \n";
			foreach ($line as $l){
				fwrite($fp,$l);
			}
				
		}
		
		private function _createContext(SiiMobilityRepository $index,$folder)
		{
			if(!$folder)
				$folder = __DIR__;
		
			$filename = $folder."/virt_context_".$index->getID().".n3";
			$tpl = new sm_Template();
			$tpl->newTemplate("context", __DIR__."/templates/context.tpl.n3");
			$dc = $index->getDataCollection();
			//Static Data
			foreach($dc[1] as $d){
				$metadata[]=$d->getRawProperties();
		
			}
			//Realtime Data
			foreach($dc[2] as $d){
				$metadata[]=$d->getRawProperties();
			}
			$tpl->addTemplateDataRepeat("context", "context", $metadata);
		
			$context = $tpl->display("context");
			if(file_exists($filename))
				unlink($filename);
			$file=fopen($filename,"w");
			fwrite($file,$context);
			fclose($file);
				
				
				
		}
		
		
	
	
}