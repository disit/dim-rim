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

class SiiMobilityBuilder
{
	static function getPerformance($command){
		
		$data['cpu']=0.0;
		$data['mem']=0.0;
		$cmd=" ps axo stat,comm,pid,%cpu,%mem | grep 'S' | grep ".$command;
		$output=array();
		exec($cmd,$output);
		if(isset($output[0]))
		{
			$s=preg_replace('/\s+/', ';',$output[0]);
			$s=explode(";",$s);
			$data['cpu']=$s[3];
			$data['mem']=$s[4];
		}
		return $data;
	
	}
	
	static function getCommittedData(SiiMobilityRepository $repos)
	{
		$repositoryID = $repos->getRepositoryID();
		$csvFile  = "/opt/indexgenerator/script/".$repositoryID."/".$repositoryID.".csv";
		$Data=null;
		if(file_exists($csvFile))
		{
			$f=fopen($csvFile, "r");
			$CsvString=fread($f,filesize($csvFile));
			fclose($f);
			$Data = str_getcsv($CsvString, "\n"); //parse the rows
			foreach($Data as &$Row) 
				$Row = str_getcsv($Row, ";");
			
		}
		//var_dump($Data); exit();
		return $Data;
	}
	
	static function countItems2Process(SiiMobilityRepository $repos)
	{
		$repositoryID = $repos->getRepositoryID();
		$lockFile  = "/opt/indexgenerator/script/".$repositoryID."/index.txt";
		$n=null;
		if(file_exists($lockFile))
		{
			$f=fopen($lockFile, "r");
			$n=fread($f,filesize($lockFile));
			fclose($f);
		}
		return $n;
	}
	
	static function command($command,$value)
	{
		switch(strtolower($command))
		{
			case "run_script": // > /dev/null 2>/dev/null &
			{
				$rep = new SiiMobilityRepository();
				$rep->load($value);
				$script = "/".$rep->getScriptPath();
				$path_parts=pathinfo($script);
				$folder=$path_parts['dirname'];
				if(!is_dir($folder))
				{
					return array("message"=>"Script does not exist!","result"=>false);
				}
			//	$lock = $folder."/.lock";
				if(self::scriptIsRunning($folder))
				{
					return array("message"=>"Lock file detected. Script is running or remove it!","result"=>false);
				}
				//sm_Logger::write("sudo ".__DIR__."/test/rimScriptLaunch.sh ".$script."  > /dev/null 2>/dev/null &"); //
				$out=array();
				$out = shell_exec("sudo /opt/indexgenerator/script/rimScriptLaunch.sh ".$script." > /dev/null 2>/dev/null &");
				return array("message"=>"Script was launched successfully!","result"=>true);
				break;
			}
		}
	}
	
	static function scriptIsRunning($folder){
		
		$lock = $folder."/.lock";
		return file_exists($lock);
		
	}
	
	static function getData2Process(SiiMobilityRepository $repos) {
	
		$idGeneration = $repos->getID();
		// Set the select query according to the choosen data type
		$query['Ontologies'] = "SELECT ontologies.Name, 'Ontology' AS `Category`, Ontologies_Generations.TripleDate AS SelectedVersion
				FROM ontologies
				INNER JOIN Ontologies_Generations
				ON ontologies.Name = Ontologies_Generations.ID_Ontology AND Ontologies_Generations.Clone=0 AND Ontologies_Generations.Locked=0 AND Ontologies_Generations.ID_Generation = " . $idGeneration;
	
	
		$query['StaticData'] = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart AS SelectedVersion, OpenData_Generations.TripleEnd 
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.Locked=0 AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'no'";
	
		$query['RealTimeData'] = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart AS SelectedVersion, OpenData_Generations.TripleEnd
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.Locked=0 AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'yes'";
	
		$query['Reconciliations'] = "SELECT Reconciliations.Name, 'Riconciliazioni' AS `Category`, Reconciliations_Generations.TripleDate AS SelectedVersion
				FROM Reconciliations
				INNER JOIN Reconciliations_Generations
				ON Reconciliations.Name = Reconciliations_Generations.ID_Reconciliation AND Reconciliations_Generations.Clone=0 AND Reconciliations_Generations.Locked=0 AND Reconciliations_Generations.ID_Generation = " . $idGeneration;
		
		$query['Enrichments'] = "SELECT enrichments.Name, enrichments.Type AS `Category`, '' AS SelectedVersion
				FROM enrichments
				INNER JOIN enrichments_generations
				ON enrichments.Name = enrichments_generations.ID_Enrichment AND enrichments_generations.Clone=0 AND enrichments_generations.Locked=0 AND enrichments_generations.ID_Generation = " . $idGeneration;
		
		$Path['Ontologies']=sm_Config::get('ONTOLOGIESPATH',"/media/rim/Ontologie");
		$Path['StaticData']=sm_Config::get('STATICDATAPATH',"/media/rim/Triples");
		$Path['RealTimeData']=sm_Config::get('REALTIMEDATAPATH',"/media/rim/Triples");
		$Path['Reconciliations']=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");

		//Create the bindings array
		$bindings = array ();
		$mysqlSettings = new MySqlConfig();
		// Get an handle to the database connection
		$db = MySqlConnector::sql_connect ($mysqlSettings->getSqlDetails());
		$items=array();
		foreach ($query as $k=>$selectQuery)// The query to get the data
		{
			$data = MySqlConnector::sql_select( $db, $bindings, $selectQuery);
			foreach ($data as $v)
			{
				$n="n.a";
				if($k!="RealTimeData")
				{
					if($v["SelectedVersion"]!="")
					{
						$folder = Versioner::getPathFromDateTime($v["SelectedVersion"]);
					
						if($k!="Ontologies"){
							$filesFolder = $Path[$k]."/".$v['Category']."/".$v['Name']."/".$folder;
						}
						else 
							$filesFolder = $Path[$k]."/".$v['Name']."/".$folder;
							
						if(is_dir($filesFolder))
						{
							$fi = new FilesystemIterator($filesFolder, FilesystemIterator::SKIP_DOTS);
							$n = iterator_count($fi);
						}
					}
					$items[]=array("Name"=>$v['Name'],"Triples Date"=>$v["SelectedVersion"],"Type"=>$k,"Category"=>$v['Category'],"#Files"=>$n);
				}
				else 
				{
					$filesFolder = $Path[$k]."/".$v['Category']."/".$v['Name']."/";
					$nFiles=0;
				//	sm_Logger::write($filesFolder." ".$v["SelectedVersion"]." ".$v["TripleEnd"]);
					/*$folders = Versioner::getResourcesByTimeInterval($filesFolder,$v["SelectedVersion"], $v["TripleEnd"]);
					foreach ($folders as $folder)
					{
						//sm_Logger::write($filesFolder.$folder);
						if(is_dir($filesFolder.$folder))
						{
							$fi = new FilesystemIterator($filesFolder.$folder, FilesystemIterator::SKIP_DOTS);
							$nFiles += iterator_count($fi);
						}		
					}
					if($nFiles>0)
						$n=$nFiles;*/
					$items[]=array("Name"=>$v['Name'],"Triples Date"=>$v["SelectedVersion"]." - ".$v["TripleEnd"],"Type"=>$k,"Category"=>$v['Category'],"#Files"=>$n);
				}
			}
			
		}
	
		return $items;
			
	}
	
}