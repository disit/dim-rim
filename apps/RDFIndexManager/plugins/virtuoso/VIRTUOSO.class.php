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

define("VIRTUOSODBPATH","/var/lib/virtuoso-opensource-7.2/");
define("VIRTUOSODBSERVICE","virtuoso-opensource-7.2");

class VIRTUOSO implements sm_Module
{
	
	protected $sql_details;
	
	function __construct()
	{
		$db = new MySqlConfig();
		$this->sql_details = $db->getSqlDetails();
	}
	
	
	static function install($db)
	{
		sm_Config::set('VIRTUOSOWORKBENCH',array("value"=>"","description"=>"VIRTUOSO Workbench Address"));
		sm_Config::set('VIRTUOSODBSERVICE',array("value"=>VIRTUOSODBSERVICE,"description"=>"VIRTUOSO Databse Service Name"));
		sm_Config::set('VIRTUOSODBPATH',array("value"=>VIRTUOSODBPATH,"description"=>"VIRTUOSO Databse Folder"));
		SiiMobilityRepositoryManager::addType("VIRTUOSO");
		return true;
	}
	
	static function uninstall($db)
	{
		sm_Config::delete('VIRTUOSOWORKBENCH');
		sm_Config::delete('VIRTUOSODBSERVICE');
		sm_Config::delete('VIRTUOSODBPATH');
		SiiMobilityRepositoryManager::removeType("VIRTUOSO");
		return true;
	}
	
	function db_query(){
		
	/*	if(!$conn   = odbc_connect('VOS', 'dba', 'dba'))
			return array("result"=>false,"error"=>odbc_errormsg());
		$query  = 'SELECT DISTINCT ?g WHERE {GRAPH ?g {?s ?p ?o.}}';
		$result = odbc_exec($conn, $query);
		?>
		<ul>
		<?php while (odbc_fetch_row($result)): ?>
		    <li><?php echo odbc_result($result, 1) ?></li>*/
	}
	
	function deleteRepository($name=null)
	{
		if(!$name || !$this->exist($name))
			return false;
		sm_Logger::write("OWLIM delete: ".$name);
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
		$repositoryDir=str_replace(" ","_",$repositoryID);
		$parentID = $generation->getParentID();
		$user = sm_Config::get('USERCONFIGCTRL',"");
		$passw = sm_Config::get('PWDCONFIGCTRL',"");
		$restcallUrl = "http://localhost".sm_Config::get("BASEURL","")."api/IndexGenerator/session/";
		$restcallUrlCommit="http://localhost".sm_Config::get("BASEURL","")."api/IndexGenerator/commit/";
		$restAuth = "-u ".$user.":".$passw;
	
		$indexFile = "/opt/indexgenerator/script/".$repositoryDir."/index.txt";
		$lockFile  = "/opt/indexgenerator/script/".$repositoryDir."/.lock";
		$logFile   = "/opt/indexgenerator/script/".$repositoryDir."/".$repositoryDir.".csv";
		$progressFile =  "/opt/indexgenerator/script/".$repositoryDir."/progress.out";
		$logDir   = "/opt/indexgenerator/script/".$repositoryDir."/log";
		$ontologiesPath=sm_Config::get('ONTOLOGIESPATH',"/media/rim/Ontologie");
		$staticDataPath=sm_Config::get('STATICDATAPATH',"/media/rim/Triples");
		$realTimeDataPath=sm_Config::get('REALTIMEDATAPATH',"/media/rim/Triples");
		$reconciliationsPath=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");
	
	
		$fileDir = '/opt/indexgenerator/script/'.$repositoryDir;
		if (!file_exists($fileDir)) {
			mkdir($fileDir, 0777, true);
		}
		if (!file_exists($logDir)) {
			mkdir($logDir, 0777, true);
		}
		$fileName = $fileDir.'/virt_'.$generation->getID().'.sh';
		// Create the script file
		$fp = fopen($fileName, "w");
	
		// First line of the script
		fwrite($fp, "#!/bin/bash\n");
		fwrite($fp, "\n");
	
		// Set configuration parameters
		fwrite($fp, "#Set configuration parameters\n");
		fwrite($fp, "sessionId=\"" . $currentSession . "\" # DO NOT CHANGE THIS PARAMETER!\n");
	//	fwrite($fp, "scriptPath=\"".$scriptPath."\"\n");
		fwrite($fp, "ontologiesPath=\"".$ontologiesPath."\"\n");
		fwrite($fp, "staticDataPath=\"".$staticDataPath."\"\n");
		fwrite($fp, "realTimeDataPath=\"".$realTimeDataPath."\"\n");
		fwrite($fp, "reconciliationsPath=\"".$reconciliationsPath."\"\n");
	
		fwrite($fp, "repositoryId=\"" . $repositoryID . "\"\n");
		fwrite($fp, "baseContext=\"http://www.disit.org/km4city/resource/\"\n");
		fwrite($fp, "indexFile=\"".$indexFile."\"\n");
		fwrite($fp, "lockFile=\"".$lockFile."\"\n");
		fwrite($fp, "logFile=\"".$logFile."\"\n");
		fwrite($fp, "logDir=\"".$logDir ."\"\n");
		fwrite($fp, "logFileBackup=`date \"+%d_%m_%y_%H_%M_%S\"`.csv\n");
		fwrite($fp, "progressFileBackup=`date \"+%d_%m_%y_%H_%M_%S\"`.out\n");
		fwrite($fp, "progressFile=\"".$progressFile ."\"\n");
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
							
				// Get the ontologies array
				$ontologies = $this->_getData("ontologies", $currentSession);
				// Get the static data array
				$staticData = $this->_getData("staticData", $currentSession);
				// Get the realtime data array
				$realTimeData = $this->_getData("realTimeData", $currentSession);
				// Get the reconciliations array
				$reconciliations = $this->_getData("reconciliations", $currentSession);
				// Get the enrichments array
				$enrichments = $generation->getDataCollection(SiiMobilityRepository::ENRICHMENTSDATA);
				$N = count($ontologies) + count($staticData) + count($realTimeData) + count($reconciliations)+count($enrichments);
				fwrite($fp, "# Remove progress file\n");
				fwrite($fp, "rm \$progressFile\n");
				fwrite($fp, "# Remove log file\n");
				fwrite($fp, "rm \$logFile\n");
				
							# Create lock file and save generation start date and time
				fwrite($fp, "# Create the lock file\n");
				fwrite($fp, "echo ".$N." > \$lockFile\n");
				fwrite($fp, "echo ".$N." > \$indexFile\n");
				fwrite($fp, "# Save to database the generation start dateTime\n");
				fwrite($fp, "curl -X PUT ".$restAuth." ".$restcallUrl."\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationStart\\\"}\"\n");
							fwrite($fp, "\n");
				fwrite($fp,"echo\n");

				
				//Prepare the virtuoso database
				//fwrite($fp,"isql-vt 1111 dba dba exec=\"DELETE FROM DB.DBA.LOAD_LIST;\"\n");
				//fwrite($fp,"echo\n");
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				fwrite($fp, "echo \"*********** START VIRTUOSO SCRIPT *****************\" >> \$progressFile\n");
				fwrite($fp, "	# Start dateTime\n");
				fwrite($fp, "	startTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
				fwrite($fp, "echo \"Start Creation/Cloning \$startTime\" >> \$progressFile\n");
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				$clone = false;
				/*if($this->exist($repositoryID))
					$this->_removeRepository($fp, $generation);*/
				if(!empty($parentID)) //Clone
				{
					fwrite($fp, "echo \"\" >> \$progressFile\n");
					fwrite($fp, "echo \"********** CLONE REPOSITORY **********\" >> \$progressFile\n");
					fwrite($fp, "echo \"\" >> \$progressFile\n");
					if($this->exist($repositoryID))
						$this->_removeRepository($fp, $generation);
					$this->_cloneScript($fp,$generation);
					$clone=true;
				}
				$this->_startScript($fp, $generation);

				fwrite($fp, "	# Start dateTime\n");
				fwrite($fp, "	startTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
				fwrite($fp, "echo \"END Creation/Cloning \$startTime\" >> \$progressFile\n");
				
				$this->_setHeader($fp, $generation);
							//
							//
							// ONTOLOGIES
							//
							//
	
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				fwrite($fp, "echo \"*************** START VIRTUOSO COMMIT *************\" >> \$progressFile\n");
				fwrite($fp, "echo \"\" >> \$progressFile\n");

				if (count($ontologies) > 0) {
					fwrite($fp, "echo \"\" >> \$progressFile\n");
					fwrite($fp, "echo \"*************** START ONTOLOGIES COMMIT *************\" >> \$progressFile\n");
					fwrite($fp, "echo \"\" >> \$progressFile\n");
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
								
					$context = "\${baseContext}\${!category}";
					$preload = "\${ontologiesPath}/\${!name}/\${!SelectedVersion}";
					
					fwrite($fp, "	# Run the load process\n");
					$this->_virtusoCMD($fp, $context, $preload);
					/*fwrite($fp, "	# Run the load process\n");
					fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category} " .
							"preload=\${ontologiesPath}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					*/
					
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
					
					fwrite($fp, "done\n\n");
					fwrite($fp, "# Lock the Ontology Inserted\n");
					fwrite($fp, "echo \"Lock the Ontology Already\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/ontologies\n");
					fwrite($fp, "echo\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
	}
	
	
	
	
	
								//
								//
								// STATIC DATA
								//
								//
	
								
	
				if (count($staticData) > 0) {
					fwrite($fp, "echo \"\" >> \$progressFile\n");
					fwrite($fp, "echo \"*************** START STATIC DATA COMMIT *************\" >> \$progressFile\n");
					fwrite($fp, "echo \"\" >> \$progressFile\n");
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
					
					$context = "\${baseContext}\${!category}/\${!name}";
					$preload = "\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion}";
						
					fwrite($fp, "	# Run the load process\n");
					$this->_virtusoCMD($fp, $context, $preload);
					/*fwrite($fp, "	# Run the load process\n");
					fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
							"preload=\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					*/
					
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
					fwrite($fp, "done\n\n");
					fwrite($fp, "# Lock the Static Data Inserted\n");
					fwrite($fp, "echo \"Lock the Static Data Inserted\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/staticdata\n");
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
										$endDateTime = "3000-01-01 00:00:00";
								
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
					$context = "\${baseContext}\${!category}/\${!name}";
					$preload = "\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion}";
					
					fwrite($fp, "	# Run the load process\n");
					$this->_virtusoCMD($fp, $context, $preload,"*.n3");
				
		/*			fwrite($fp, "	bash \${scriptPath} \${sesameUrl} \${repositoryId} \${baseContext}\${!category}/\${!name} " .
							"\${realTimeDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
		*/			
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
	
					fwrite($fp, "echo \"\" >> \$progressFile\n");
					fwrite($fp, "echo \"*************** START RECONCILIATIONS COMMIT *************\" >> \$progressFile\n");
					fwrite($fp, "echo \"\" >> \$progressFile\n");
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
					
					$context = "\${baseContext}\${!category}/\${!name}";
					$preload = "\${reconciliationsPath}/\${!category}/\${!name}/\${!SelectedVersion}";
					
					fwrite($fp, "	# Run the load process\n");
					$this->_virtusoCMD($fp, $context, $preload);
					//fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
					//		"preload=\${reconciliationsPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					
					
					
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
					
					fwrite($fp, "done\n\n");
					fwrite($fp, "# Lock the Reconciliations Data Inserted\n");
					fwrite($fp, "echo \"Lock the Reconciliations Data Inserted\"\n");
					fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."/reconciliations\n");
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
		$this->_virtusoCMD($fp,$context,$sourcefile,"*.n3");
		
	}
	
	/*
	 * isql-vt 1111 dba dba exec="DB.DBA.rdf_loader_run();"
	 * 
	 * isql-vt 1111 dba dba exec="DB.DBA.rdf_geo_fill();"
	 * 
	 * isql-vt 1111 dba dba exec="rdfs_rule_set ('urn:ontology', 'http://www.disit.org/km4city/resource/Ontology'); "
	 * 
	 * isql-vt 1111 dba dba exec="cl_exec ('checkpoint'); "
	 */
	if(count($ontologies) || count($staticData) || count($realTimeData) || count($reconciliations) || count($enrichments) )
	{
		fwrite($fp, "echo \"\" >> \$progressFile\n");
		fwrite($fp, "echo \"*********** VIRTUOSO LOAD *****************\" >> \$progressFile\n");
		fwrite($fp, "echo \"\" >> \$progressFile\n");
		fwrite($fp,"isql-vt 1111 dba dba exec=\"DB.DBA.rdf_loader_run();\"  >> \$progressFile 2>&1\n");
		fwrite($fp,"echo\n");
		fwrite($fp, "echo \"\" >> \$progressFile\n");
		fwrite($fp, "echo \"*********** VIRTUOSO GEO FILL *****************\" >> \$progressFile\n");
		fwrite($fp, "echo \"\" >> \$progressFile\n");
		fwrite($fp,"isql-vt 1111 dba dba exec=\"DB.DBA.rdf_geo_fill();\"  >> \$progressFile 2>&1\n");
		fwrite($fp,"echo\n");
		fwrite($fp, "echo \"*********** VIRTUOSO RULE SET *****************\" >> \$progressFile\n");
		fwrite($fp, "echo \"\" >> \$progressFile\n");
		fwrite($fp,"isql-vt 1111 dba dba exec=\"rdfs_rule_set ('urn:ontology', 'http://www.disit.org/km4city/resource/Ontology');\"  >> \$progressFile 2>&1\n");
		fwrite($fp,"echo\n");
		fwrite($fp, "echo \"*********** VIRTUOSO CHECKPOINT *****************\" >> \$progressFile\n");
		fwrite($fp, "echo \"\" >> \$progressFile\n");
		fwrite($fp,"isql-vt 1111 dba dba exec=\"cl_exec ('checkpoint');\"  >> \$progressFile 2>&1\n");
		fwrite($fp,"echo\n");
		if(count($enrichments))
			$this->_postBuilding($fp, $generation);
		fwrite($fp, "# Commit ALL\n");
		fwrite($fp, "echo \"Commit Index\" >> \$progressFile\n");
		fwrite($fp, "curl -X POST ".$restAuth." ".$restcallUrlCommit.$currentSession."\n");
		//$this->_endScript($fp, $generation);
	}
	$this->_endScript($fp, $generation);
		
	// Close the generation on database and remove lock file
	fwrite($fp, "\n");
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
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.Locked=0  AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'yes'";
						}
						else if ($dataType == "reconciliations") {
			$selectQuery = "SELECT Reconciliations.Name, Reconciliations_Generations.TripleDate AS SelectedVersion
				FROM Reconciliations
				INNER JOIN Reconciliations_Generations
				ON Reconciliations.Name = Reconciliations_Generations.ID_Reconciliation AND Reconciliations_Generations.Clone=0 AND Reconciliations_Generations.Locked=0  AND Reconciliations_Generations.ID_Generation = " . $idGeneration;
					}
	
					//Create the bindings array
					$bindings = array ();
	
					// Get an handle to the database connection
					$db = MySqlConnector::sql_connect ($this->sql_details);
	
					// The query to get the data
			$data = MySqlConnector::sql_select( $db, $bindings, $selectQuery);
	
			return $data;
			
		}
		
		private function _virtusoCMD($fp,$context,$sourcefile,$mask="*"){
			/*
			 * echo INDEX ".$context." ".$sourcefile."\n"; 
			 * isql-vt 1111 dba dba exec="ld_dir_all('$sourcefile', '*', '$context');"

			 */
			fwrite($fp, "echo \"\" >> \$progressFile\n");
			fwrite($fp,"echo INDEX ".$context." ".$sourcefile." >> \$progressFile\n");
			fwrite($fp,"echo\n");
			fwrite($fp,"isql-vt 1111 dba dba exec=\"ld_dir_all('$sourcefile', '$mask', '$context');\"  >> \$progressFile 2>&1\n");
		}
		
		private function _endScript($fp,Generation $generation)
		{
			$repositoryFolder=VIRTUOSODBPATH;
				
			$source = $repositoryFolder.str_replace(" ","_",$generation->getRepositoryID());
			if(!$fp)
				return;
			$line[]="echo \"Stopping Virtuoso\" >> \$progressFile\n";
			$line[]= "echo \"Stopping Virtuoso\"\n";
			$line[]="# Stopping Virtuoso Server\n";
			$line[]="service ".VIRTUOSODBSERVICE." stop \n";
			
			$line[]="echo \"Remove Previous Repository\" >> \$progressFile\n";
			$line[]= "echo \"Remove Previous Repository\"\n";
			$line[]="# Remove Previous Repository\n";
			$line[]="mv ".$source." ".$source."_last\n";
			$line[]="rm -R ".$source."\n";
			
			$line[]="echo \"Save New Repository\" >> \$progressFile\n";
			$line[]= "echo \"Save New Repository\"\n";
			$line[]="# Save New Repository\n";
			$line[]="cp -R ".$repositoryFolder."db ".$source."\n";
			
			/*	$line[]="# Assign mode & permissions to ".$target."\n";
			 $line[]="chown -R tomcat7:tomcat7 ".$target."\n";
			 $line[]="chmod 755 -R ".$target."\n";*/
			$line[]="echo \"Restarting Virtuoso\" >> \$progressFile\n";
			$line[]="echo \"Restarting Virtuoso\"\n";
			$line[]="# Restarting Virtuoso Server \n";
			$line[]="service ".VIRTUOSODBSERVICE." restart \n";
			$line[]= "\n";
			foreach ($line as $l){
				fwrite($fp,$l);
			}
		
		}
		
		private function _setHeader($fp,SiiMobilityRepository $generation)
		{
			$sparqlDelete='SPARQL WITH <urn:disit:db:header> DELETE { <http://www.disit.org/repository> ?p ?o } WHERE { <http://www.disit.org/repository> ?p ?o }';
			$sparqlInsert='SPARQL WITH <urn:disit:db:header> INSERT {<http://www.disit.org/repository> dc:title \"'.$generation->getRepositoryID().'\"; dc:date \"'.date("Y-m-d H:i:s").'\"; dc:description \"'.$generation->getDescription().'\".}';
			
			fwrite($fp, "echo \"\" >> \$progressFile\n");
			fwrite($fp,"echo \"Set Database Header Info\" >> \$progressFile\n");
			fwrite($fp,"echo\n");
			fwrite($fp,"isql-vt 1111 dba dba exec=\"$sparqlDelete\"  >> \$progressFile 2>&1\n");
			fwrite($fp,"echo\n");
			fwrite($fp,"isql-vt 1111 dba dba exec=\"$sparqlInsert\"  >> \$progressFile 2>&1\n");
		}
		
		private function exist($name)
		{
			$repositoryFolder=VIRTUOSODBPATH;
			$source = $repositoryFolder.str_replace(" ","_",$name);
			return is_dir($source);
		}
		
		private function _removeRepository($fp, Generation $generation)
		{
			$repositoryFolder=VIRTUOSODBPATH;
			$target = $repositoryFolder.str_replace(" ","_",$generation->getRepositoryID());
			$line[]="echo \"Removing Existing Repository ".$target."\" >> \$progressFile\n";
			$line[]="rm -R ".$target."\n";
			foreach ($line as $l){
				fwrite($fp,$l);
			}
		}
		
		private function _cloneScript($fp, Generation $generation)
		{
			$repositoryFolder=VIRTUOSODBPATH;
			$target = $repositoryFolder.str_replace(" ","_",$generation->getRepositoryID());
			$source = $repositoryFolder.str_replace(" ","_",$generation->getParentID());
			if(!$fp)
				return;
			
			$line[]="echo \"Copy Repository from ".$source." to ".$target."\" >> \$progressFile\n";
			$line[]= "echo \"Copy Repository from ".$source." to ".$target."\"\n";
			$line[]="# Copy Repository from ".$source." to ".$target."\n";
			$line[]="cp -pR ".$source." ".$target."\n";
			
			
			foreach ($line as $l){
				fwrite($fp,$l);
			}
		}
		
		private function _startScript($fp,Generation $generation)
		{
			$repositoryFolder=VIRTUOSODBPATH;
		
			$source = $repositoryFolder.str_replace(" ","_",$generation->getRepositoryID());
			if(!$fp)
				return;
			$line[]="echo \"Stopping Virtuoso\" >> \$progressFile\n";
			$line[]= "echo \"Stopping Virtuoso\"\n";
			$line[]="# Stopping Virtuoso Server\n";
			$line[]="service ".VIRTUOSODBSERVICE." stop \n";
			
			$line[]="echo \"Remove Previous Repository\" >> \$progressFile\n";
			$line[]= "echo \"Remove Previous Repository\"\n";
			$line[]="# Remove Previous Repository\n";
			$line[]="rm -R ".$repositoryFolder."db_last\n";
			$line[]="cp -R ".$repositoryFolder."db ".$repositoryFolder."db_last\n";
			$line[]= "echo \"".$source."\">> \$progressFile\n";
			
				$line[]="if [ -d \"".$source."\" ]; then\n";
				$line[]="	echo \"Resume Repository\" >> \$progressFile\n";
				$line[]= "	echo \"Resume Repository\"\n";
				$line[]="	#Resume Repository\n";
				$line[]="	rm -R ".$repositoryFolder."db\n";
				$line[]="	cp -R ".$source." ".$repositoryFolder."db \n";
				$line[]="else\n";
			
				$line[]="	echo \"Make New DB Repository\" >> \$progressFile\n";
				$line[]="	echo \"Make New DB Repository\"\n";
				$line[]="	# New DB Repository\n";
				$line[]="	rm -R ".$repositoryFolder."db\n";
				$line[]="	mkdir ".$repositoryFolder."db \n";
				$line[]="fi\n";
			
			
				
			/*	$line[]="# Assign mode & permissions to ".$target."\n";
			 $line[]="chown -R tomcat7:tomcat7 ".$target."\n";
			 $line[]="chmod 755 -R ".$target."\n";*/
			$line[]="echo \"Restarting Virtuoso\" >> \$progressFile\n";
			$line[]="echo \"Restarting Virtuoso\"\n";
			$line[]="# Restarting Virtuoso Server \n";
			$line[]="service ".VIRTUOSODBSERVICE." restart \n";
			
			$line[]="echo \"RESET Virtuoso LOAD LIST\" >> \$progressFile\n";
			$line[]="isql-vt 1111 dba dba exec=\"DELETE FROM DB.DBA.LOAD_LIST;\"  >> \$progressFile 2>&1\n";
			
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
		
		private function _postBuilding($fp,SiiMobilityRepository $generation)
		{
			$enrichments = $generation->getDataCollection(SiiMobilityRepository::ENRICHMENTSDATA);
		//	include(__DIR__."/templates/sparqlQuery.php");
			foreach ($enrichments as $what)
			{
				$enrichment = new Enrichment();
				$enrichment->select($what->getID_Enrichment());
				$query = $enrichment->getQuery();
				$sparql=preg_replace ('/\r\n|\r|\n/', ' ',$query);
				fwrite($fp, "	# Start dateTime\n");
				fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
				fwrite($fp, "echo \"\" >> \$progressFile\n");
				fwrite($fp,"echo \"".ucfirst($enrichment->getName())."\" >> \$progressFile\n");
				fwrite($fp,"echo\n");
				fwrite($fp,"isql-vt 1111 dba dba exec=\"$sparql\"  >> \$progressFile 2>&1\n");
				fwrite($fp, "	# End dateTime\n");
				fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
				fwrite($fp, "	# Save execution time in logs\n");
				fwrite($fp, "	echo \"".ucfirst($enrichment->getName()).";\$startDateTime;\$endDateTime\" >> \$logFile\n");
				fwrite($fp, "	echo \"".ucfirst($enrichment->getName()).";\$endDateTime\" >> \$logDir/\$logFileBackup\n");
				fwrite($fp, "\n");
				
			}
			
		}
	
}