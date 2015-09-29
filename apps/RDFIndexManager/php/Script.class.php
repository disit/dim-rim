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

require_once 'API.class.php'; 
require_once 'MySqlConnector.class.php';
require_once 'dbDetails.php';
require_once 'Versioner.class.php';

/**
 * API manager class for the script
*
* @author Riccardo Billero
*
*/
class Script extends API {

	public function __construct($request, $sql_details, $remoteIP){
		parent::__construct($request, $sql_details, $remoteIP);
	}
	

	/**
	 * 
	 * Manages the HTTP /session/{id}/script requests
	 * 
	 * @return string the response body
	 */
	public function processAPI() {
				
		// Get the first argument
		$id = $this->args[0];
		
		// Get the repository ID
		$repositoryID = $this->request['repositoryID'];
		
		// Switch according to the required method
		switch ($this->method) {
			case 'GET' :
				//TODO: Per fare controllo errori, prima devo vedere se la sessione richiesta esiste
				return $this->_getScript($id, $repositoryID);
				break;
			default :
				return $this->_response("", 405);
		}
		
	}
	
	
	/**
	 * 
	 * Get a script
	 * 
	 * @param string $currentSession the id of the session whose the script is related
	 * @param string $repositoryID the id of the repository to create
	 */
	private function _getScript($currentSession, $repositoryID){
		
		// The name of the script file
		//TODO Va messo in cartella con data?
		$fileName = '/opt/lampp/htdocs/indexgenerator/script/generateindex.sh';
		//$fileName = '/home/ubuntu/Desktop/owlim-lite-5.4.6287/getting-started/generateindex.sh';
		
		// Create the script file
		$fp = fopen($fileName, "w");
		
		// First line of the script
		fwrite($fp, "#!/bin/bash\n");
		fwrite($fp, "\n");
		
		// Set configuration parameters
		fwrite($fp, "#Set configuration parameters\n");
		fwrite($fp, "sessionId=\"" . $currentSession . "\" # DO NOT CHANGE THIS PARAMETER!\n");
		fwrite($fp, "scriptPath=\"/opt/owlim/getting-started/example.sh\"\n");
		fwrite($fp, "ontologiesPath=\"/media/Ontologie\"\n");
		fwrite($fp, "staticDataPath=\"/media/Triples\"\n");
		fwrite($fp, "realTimeDataPath=\"/media/Triples\"\n");
		fwrite($fp, "reconciliationsPath=\"/media/Riconciliazioni\"\n");
		fwrite($fp, "sesameUrl=\"http://localhost:8080/openrdf-sesame/\"\n");
		fwrite($fp, "repositoryId=\"" . $repositoryID . "\"\n");
		fwrite($fp, "baseContext=\"http://www.disit.org/km4city/resource/\"\n");
		fwrite($fp, "indexFile=\"/opt/owlim/getting-started/index.txt\"\n");
		fwrite($fp, "lockFile=\"/opt/lampp/htdocs/indexgenerator/script/.lock\"\n");
		fwrite($fp, "\n");
		fwrite($fp, "# Moves to load script directory\n");
		fwrite($fp, "cd /opt/owlim/getting-started/\n");
		fwrite($fp, "\n");
		
		# Looking for lock file
		fwrite($fp, "# Looks for the lock file; if found it, the process ends\n");
		fwrite($fp, "if [ -f \$lockFile ] ; then\n");
		fwrite($fp, "	clear\n");
		fwrite($fp, "	echo\n");
		fwrite($fp, "	echo \"---------------------------------------------------------------\"\n");
		fwrite($fp, "	echo\n");
		fwrite($fp, "	echo \"ERROR!!!\"\n");
		fwrite($fp, "	echo \"Lock file found (/opt/lampp/htdocs/indexgenerator/script/.lock)\"\n");
		fwrite($fp, "	echo \"Another index generation is already running.\"\n");
		fwrite($fp, "	echo \"Is not possible to generate a new index!\"\n");
		fwrite($fp, "	echo\n");
		fwrite($fp, "	echo\n");
		fwrite($fp, "	exit\n");
		fwrite($fp, "fi\n");
		fwrite($fp, "\n");
		
		# Create lock file and save generation start date and time
		fwrite($fp, "# Create the lock file\n");
		fwrite($fp, "echo \"\" > \$lockFile\n");
		fwrite($fp, "# Save to database the generation start dateTime\n");
		fwrite($fp, "curl -X PUT http://localhost/indexgenerator/idxgen/session/\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationStart\\\"}\"\n");
		fwrite($fp, "\n");
		
		
		
		//
		//
		// ONTOLOGIES
		//
		//
		
		// Get the ontologies array
		$ontologies = $this->_getData("ontologies", $currentSession);
		
		if (count($ontologies) > 0) {
		
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
			fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category} " .
					"preload=\${ontologiesPath}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
			fwrite($fp, "	# End dateTime\n");
			fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
			fwrite($fp, "	# Save execution time in logs\n");
			fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> /opt/lampp/htdocs/indexgenerator/script/\$repositoryId.csv\n");
			fwrite($fp, "done\n");
			fwrite($fp, "\n");
			fwrite($fp, "\n");
		
		}
		
		
		
		
		
		//
		//
		// STATIC DATA
		//
		//
		
		// Get the static data array
		$staticData = $this->_getData("staticData", $currentSession);
		
		if (count($staticData) > 0) {
		
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
			fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
					"preload=\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
			fwrite($fp, "	# End dateTime\n");
			fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
			fwrite($fp, "	# Save execution time in logs\n");
			fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> /opt/lampp/htdocs/indexgenerator/script/\$repositoryId.csv\n");
			fwrite($fp, "done\n");
			fwrite($fp, "\n");
			fwrite($fp, "\n");
		
		}
		
		
		
		
		
		//
		//
		// REAL TIME DATA
		//
		//
		
		// Get the realtime data array
		$realTimeData = $this->_getData("realTimeData", $currentSession);
		
		if (count($realTimeData) > 0) {
		
			// Declare an associative array
			fwrite($fp, "# Declares each realtime data to use\n");
		
		
			// For each realtime data, declares it in the script array
			for($i = 0; $i < count($realTimeData); $i ++) {
				$startDateTime = $realTimeData[$i]["TripleStart"];
				if (strpos($startDateTime,'from first') !== false)
					$startDateTime = "1970-01-01 00:00:00";
				$endDateTime = $realTimeData[$i]["TripleEnd"];
				if (strpos($endDateTime,'until last') !== false)
					$endDateTime = "3000-01-01 00:00:00";
				fwrite($fp, "declare -A realtimedata" . $i ."=([name]=" . $realTimeData[$i]["Name"] . " [category]=" . $realTimeData[$i]["Category"] . " [TripleStart]=\"" . $startDateTime . "\" [TripleEnd]=\"" . $endDateTime . "\")\n");
			}
			fwrite($fp, "\n");
		
			// Loop over each realtime data, to get parameters and load triples
			fwrite($fp, "# Loops over each realtime data\n");
			fwrite($fp, "for i in {0.." . (count($realTimeData) - 1) . "}\n");
			fwrite($fp, "do\n");
			fwrite($fp, "	name=\"realtimedata\$i[name]\"\n");
			fwrite($fp, "	category=\"realtimedata\$i[category]\"\n");
			fwrite($fp, "	tripleStart=realtimedata\${i}[TripleStart]\n");
			fwrite($fp, "	tripleEnd=realtimedata\${i}[TripleEnd]\n");
			fwrite($fp, "	startDate=$(date -d \"\${!tripleStart}\" +\"%Y%m%d%H%M%S\")\n");
			fwrite($fp, "	endDate=$(date -d \"\${!tripleEnd}\" +\"%Y%m%d%H%M%S\")\n");
			fwrite($fp, "\n");
			fwrite($fp, "	# Start dateTime\n");
			fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
			fwrite($fp, "	# Change directory in order to optimize the result file\n");
			fwrite($fp, "	cd \$realTimeDataPath/\${!category}/\${!name}/\n");
			fwrite($fp, "	# Gets the list of the subdirectories for this real time data\n");
			fwrite($fp, "	find . -type d -fprint \${indexFile}\n");
			fwrite($fp, "\n");
			fwrite($fp, "	# Moves to load script directory\n");
			fwrite($fp, "	cd /home/ubuntu/Desktop/owlim-lite-5.4.6287/getting-started/\n");
			fwrite($fp, "	\n");
			fwrite($fp, "	# Explore the directories tree of the considered real time data\n");
			fwrite($fp, "	for line in $(cat \${indexFile})\n");
			fwrite($fp, "	do\n");
			fwrite($fp, "\n");
			fwrite($fp, "		# Checks that the considered directory is really a tree leaf and not a tree node\n");
			fwrite($fp, "		# In this case the lenght of the directory name should be at least 20, i.e. the length of ./YYYY_mm/dd/HH/MMSS\n");
			fwrite($fp, "		if [ \${#line} -ge 20 ];\n");
			fwrite($fp, "		then");
			fwrite($fp, "\n");
			fwrite($fp, "			# Get the datetime of the considered real time data\n");
			fwrite($fp, "			currentDateTime=$(date -d \"\${line:2:4}-\${line:7:2}-\${line:10:2} \${line:13:2}:\${line:16:2}:\${line:18:2}\" +\"%Y%m%d%H%M%S\")\n");
			fwrite($fp, "\n");
			fwrite($fp, "			# If the datetime of the considered real time date is in the choosen datetime range, load its data\n");
			fwrite($fp, "			if [ \$startDate -le \$currentDateTime ] && [ \$currentDateTime -le \$endDate ];\n");
			fwrite($fp, "			then\n");
			fwrite($fp, "				bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} preload=\${realTimeDataPath}/\${!category}/\${!name}/\${line:2} username=admin password=ubuntu queryfile=none.\n");
			fwrite($fp, "			fi # [ \$startDate -le \$currentDateTime ] && [ \$currentDateTime -le \$endDate ];\n");
			fwrite($fp, "\n");
			fwrite($fp, "		fi #if [ \${#string} -ge 20 ];\n");
			fwrite($fp, "	done\n");
			fwrite($fp, "	# End dateTime\n");
			fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
			fwrite($fp, "	# Save execution time in logs\n");
			fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> /opt/lampp/htdocs/indexgenerator/script/\$repositoryId.csv\n");
			fwrite($fp, "done\n");
			fwrite($fp, "\n");
			fwrite($fp, "\n");
			fwrite($fp, "# Remove the temporary file\n");
			fwrite($fp, "rm \${indexFile}\n");
			fwrite($fp, "\n");
			fwrite($fp, "\n");
				
		}
		
		
		
		
		
		//
		//
		// RECONCILIATIONS
		//
		//
		
		// Get the reconciliations array
		$reconciliations = $this->_getData("reconciliations", $currentSession);
		
		if (count($reconciliations) > 0) {
		
			fwrite($fp, "# Declares each reconciliation procedure to use\n");
		
			// For each reconciliation, declares it in the script array
			for($i = 0; $i < count($reconciliations); $i ++) {
				$folder = Versioner::getPathFromDateTime($reconciliations[$i]["SelectedVersion"]);
				fwrite($fp, "declare -A reconciliation" . $i ."=([name]=" . $reconciliations[$i]["Name"] . " [category]=Reconciliation [SelectedVersion]=\"" . $folder . "\")\n");
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
			fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
					"preload=\${reconciliationsPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
			fwrite($fp, "	# End dateTime\n");
			fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
			fwrite($fp, "	# Save execution time in logs\n");
			fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> /opt/lampp/htdocs/indexgenerator/script/\$repositoryId.csv\n");
			fwrite($fp, "done\n");
			fwrite($fp, "\n");
			fwrite($fp, "\n");
		
		}

		
		// Close the generation on database and remove lock file
		fwrite($fp, "# Save to database the generation end dateTime\n");
		fwrite($fp, "curl -X PUT http://localhost/indexgenerator/idxgen/session/\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationEnd\\\"}\"\n");
		fwrite($fp, "# Remove the lock file\n");
		fwrite($fp, "rm \$lockFile\n");

		
		// Close the script file
		fclose($fp);
		
		return json_encode(array('path' => $fileName));;
			
	}
	
	
	private function _getData($dataType, $idGeneration) {
	
		// Set the select query according to the choosen data type
		if ($dataType == "ontologies") {
			$selectQuery = "SELECT ontologies.Name, Ontologies_Generations.TripleDate AS SelectedVersion
				FROM ontologies
				INNER JOIN Ontologies_Generations
				ON ontologies.Name = Ontologies_Generations.ID_Ontology AND Ontologies_Generations.ID_Generation = " . $idGeneration;
		}
		else if ($dataType == "staticData") {
			$selectQuery = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart AS SelectedVersion
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'no'";
		}
		else if ($dataType == "realTimeData") {
			$selectQuery = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart, OpenData_Generations.TripleEnd
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'yes'";
		}
		else if ($dataType == "reconciliations") {
			$selectQuery = "SELECT Reconciliations.Name, Reconciliations_Generations.TripleDate AS SelectedVersion
				FROM Reconciliations
				INNER JOIN Reconciliations_Generations
				ON Reconciliations.Name = Reconciliations_Generations.ID_Reconciliation AND Reconciliations_Generations.ID_Generation = " . $idGeneration;
		}
	
		//Create the bindings array
		$bindings = array ();
	
		// Get an handle to the database connection
		$db = MySqlConnector::sql_connect ($this->sql_details);
	
		// The query to get the data
		$data = MySqlConnector::sql_select( $db, $bindings, $selectQuery);
	
		return $data;
			
	}
	
	
}