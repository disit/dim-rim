<?php



/*
 * Helper functions for building a DataTables server-side processing SQL query
 * The static functions in this class are just helper functions to help build
 *  the SQL used in the DataTables demo server-side processing scripts.
 *  These functions obviously do not represent all that can be done with server-side processing,
 *  they are intentionally simple to show how it works.
 *  More complex server-side processing operations will likely require a custom script.
 *  See http://datatables.net/usage/server-side for full details on the server- side processing requirements of DataTables.
 *  @license MIT - http://datatables.net/license_mit
 */

class SSP {
	
	

	/**
	 * 
	 * Create the data output array for the DataTables rows
	 *
	 * @param array $columns Column information array, with the associations between dataTables and the database
	 * @param array $data Data obtained from the SQL SELECT
	 * @return array Formatted data in a row based format
	 *        
	 */
	static function data_output($columns, $data) {
		
		// Output array
		$out = array ();
		
		// For each row in data
		for($i = 0, $ien = count ( $data ); $i < $ien; $i ++) {
			
			// The current row to fulfill (i-th row)
			$row = array ();
			
			// For each column of the array results-dataTables
			for($j = 0, $jen = count ( $columns ); $j < $jen; $j ++) {
				
				// The current column
				$column = $columns [$j];
				
				// If a formatter is set, apply the formatter function
				// The arguments of it are:
				// * id: the column name in database
				// * row: the i-th row of database results
				//
				// The destionation column is identified by 'dt', i.e. the column name in dataTables
				if (isset ( $column['formatter'])) {
					$row [$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
				} 
				
				// If no formatter is set, set the value of a cell of the current row
				// The destionation column is identified by 'dt', i.e. the column name in dataTables
				// The source cell is in the i-th row of $data, and the column is identified by 'db', i.e. the column name in database
				else {
					$row [$column['dt']] = $data [$i][$columns [$j]['db']];
				}
			}
			
			$out [] = $row;
		}
		
		return $out;
	}
	
	
	/**
	 *
	 * Construct the LIMIT clause for the SQL query
	 *
	 * @param array $request Data sent to server by DataTables
	 * @param array $columns Column information array, with the associations between dataTables and the database
	 * @return string SQL limit clause to use
	 *        
	 */
	static function limit($request, $columns) {
		
		// The limit clause to set
		$limit = '';
		
		// Set the limit clause, if find the right parameters
		if (isset ( $request ['start'] ) && $request ['length'] != - 1) {
			$limit = "LIMIT " . intval ( $request ['start'] ) . ", " . intval ( $request ['length'] );
		}
		
		return $limit;
	}
	
	
	/**
	 *
	 * Construct the ORDER BY clause for the SQL query
	 *
	 * @param array $request Data sent to server by DataTables
	 * @param array $columns Column information array, with the associations between dataTables and the database
	 * @return string SQL order clause to use
	 *        
	 */
	static function order($request, $columns) {
		
		// The order clause to set
		$order = '';
		
		if (isset ( $request ['order'] ) && count ( $request ['order'] )) {
			$orderBy = array ();
			$dtColumns = self::pluck ( $columns, 'dt' );
			
			for($i = 0, $ien = count ( $request ['order'] ); $i < $ien; $i ++) {
				// Convert the column index into the column data property
				$columnIdx = intval ( $request ['order'] [$i] ['column'] );
				$requestColumn = $request ['columns'] [$columnIdx];
				
				$columnIdx = array_search ( $requestColumn ['data'], $dtColumns );
				$column = $columns [$columnIdx];
				
				if ($requestColumn ['orderable'] == 'true') {
					$dir = $request ['order'] [$i] ['dir'] === 'asc' ? 'ASC' : 'DESC';
					
					$orderBy [] = '`' . $column ['db'] . '` ' . $dir;
				}
			}
			
			$order = 'ORDER BY ' . implode ( ', ', $orderBy );
		}
		
		return $order;
	}
	
	/**
	 * Searching / Filtering
	 *
	 * Construct the WHERE clause for server-side processing SQL query.
	 *
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here performance on large
	 * databases would be very poor
	 *
	 * @param array $request
	 *        	Data sent to server by DataTables
	 * @param array $columns
	 *        	Column information array
	 * @param array $bindings
	 *        	Array of values for PDO bindings, used in the
	 *        	sql_exec() function
	 * @return string SQL where clause
	 *        
	 */
	static function filter($request, $columns, &$bindings) {
		$globalSearch = array ();
		$columnSearch = array ();
		$dtColumns = self::pluck ( $columns, 'dt' );
		
		if (isset ( $request ['search'] ) && $request ['search'] ['value'] != '') {
			$str = $request ['search'] ['value'];
			
			for($i = 0, $ien = count ( $request ['columns'] ); $i < $ien; $i ++) {
				$requestColumn = $request ['columns'] [$i];
				$columnIdx = array_search ( $requestColumn ['data'], $dtColumns );
				$column = $columns [$columnIdx];
				
				if ($requestColumn ['searchable'] == 'true') {
					$binding = self::bind ( $bindings, '%' . $str . '%', PDO::PARAM_STR );
					$globalSearch [] = "`" . $column ['db'] . "` LIKE " . $binding;
				}
			}
		}
		
		// Individual column filtering
		for($i = 0, $ien = count ( $request ['columns'] ); $i < $ien; $i ++) {
			$requestColumn = $request ['columns'] [$i];
			$columnIdx = array_search ( $requestColumn ['data'], $dtColumns );
			$column = $columns [$columnIdx];
			
			$str = $requestColumn ['search'] ['value'];
			
			if ($requestColumn ['searchable'] == 'true' && $str != '') {
				
				if(!$requestColumn ['search']['regex'])
				{
					$binding = self::bind ( $bindings, '%' . $str . '%', PDO::PARAM_STR );
					$columnSearch [] = "`" . $column ['db'] . "` LIKE " . $binding;
				}
				else {
					$binding = self::bind ( $bindings, $str , PDO::PARAM_STR );
					$columnSearch [] = "`" . $column ['db'] . "` RLIKE " . $binding;
				}
			}
		}
		
		// Combine the filters into a single string
		$where = '';
		
		if (count ( $globalSearch )) {
			$where = '(' . implode ( ' OR ', $globalSearch ) . ')';
		}
		
		if (count ( $columnSearch )) {
			$where = $where === '' ? implode ( ' AND ', $columnSearch ) : $where . ' AND ' . implode ( ' AND ', $columnSearch );
		}
		
		if ($where !== '') {
			$where = 'WHERE ' . $where. " AND `exec_A` <> ''";
		}
		
		return $where;
	}
	
	/**
	 * Perform the SQL queries needed for an server-side processing requested,
	 * utilising the helper functions of this class, limit(), order() and
	 * filter() among others.
	 * The returned array is ready to be encoded as JSON
	 * in response to an SSP request, or can be modified if needed before
	 * sending back to the client.
	 *
	 * @param array $request
	 *        	Data sent to server by DataTables
	 * @param array $sql_details
	 *        	SQL connection details - see sql_connect()
	 * @param string $table
	 *        	SQL table to query
	 * @param string $primaryKey
	 *        	Primary key of the table
	 * @param array $columns
	 *        	Column information array
	 * @return array Server-side processing response array
	 *        
	 */
	static function simple($request, $sql_details, $table, $primaryKey, $columns) {
		
		//TODO Non chiara l'utilit� di bindings
		$bindings = array ();
		
		// Get an handle to the database connection
		$db = self::sql_connect ( $sql_details );
		
		// Build the SQL query string from the request
		$limit = self::limit ( $request, $columns );
		$order = self::order ( $request, $columns );
		$where = self::filter ( $request, $columns, $bindings );
		// $where = "WHERE `exec_A` <> 'no'";
		// print_r("`
		// 	FROM `$table`
		// 	$where 
		// 	$order 
		// 	$limit");
		// The query to get the data
		$data = self::sql_exec ( $db, $bindings, "SELECT SQL_CALC_FOUND_ROWS `" . implode ( "`, `", self::pluck ( $columns, 'db' ) ) . "`
			FROM `$table`
			$where 
			$order 
			$limit" );
		
		// Dataset length after filtering
		$resFilterLength = self::sql_exec ( $db, "SELECT FOUND_ROWS()" );
		$recordsFiltered = $resFilterLength[0][0];
		
		// Total data set length
		$resTotalLength = self::sql_exec ( $db, "SELECT COUNT(`{$primaryKey}`) FROM `$table`" );
		$recordsTotal = $resTotalLength[0][0];

		 $outTemp = array (
		 		"draw" => intval ($request['draw']),
				"recordsTotal" => intval ($recordsTotal),
		 		"recordsFiltered" => intval ($recordsFiltered),
		 		"data" => self::data_output ($columns, $data) 
		 );
		
		// print_r($outTemp);
		// Data output
		/*return array (
				"draw" => intval ($request['draw']),
				"recordsTotal" => intval ($recordsTotal),
				"recordsFiltered" => intval ($recordsFiltered),
				"data" => self::data_output ($columns, $data) 
		);*/
		 return $outTemp;
	}


	//Uguale alla funzione simple(), che restituisce anche i dati provenienti dalla
	//tabella quartz.QUARTZ_TRIGGERS. Nel caso non sia necessario avere i dati
	//aggiuntivi provenienti dalla seconda tabella utilizzare la funzione simple,
	//specificandola nel file getMultiTables.php
	static function nested($request, $sql_details, $table, $primaryKey, $columns) {
		
		
		$sqlConfig = $sql_details;
		$sqlConfig['db']=$sql_details['db2'];
		$firstTable = self::simple($request, $sqlConfig, $table, $primaryKey, $columns);
		// $processesSet = array();
		$bindings = array ();

		//Costruzione dell'insieme di jobs da passare alle clausola IN della query
		//Per ogni job della prima tabella ci sono tre processi sulla seconda, quindi
		//nel ciclo for vengono ricreate le chiavi di quest'ultima.
		
		$processesSetCount = count($firstTable["data"]);
		//No matching data - result = 0
		if($processesSetCount==0)
			return $firstTable;

		$processesSet = "(";
		$i = 0;
		foreach ($firstTable["data"] as &$item){
			$i++;
			$process = $item["process"];
			if ($i < $processesSetCount) {
				$processesSet = $processesSet."'".$process."', ";
			}
			else {
				$processesSet = $processesSet."'".$process."'";
			}
		}

		$processesSet = $processesSet.')';
		
		$sqlConfig['db']=$sql_details['db1'];
		//sm_Logger::write($sqlConfig);
		// Get an handle to the database connection
		$db = self::sql_connect ( $sqlConfig );
		// Query alla tabelle QRTZ_TRIGGERS del database quartz per il recupero dell'ultima data di avvio dei processi
		$query = "SELECT JOB_NAME, PREV_FIRE_TIME FROM `QRTZ_TRIGGERS` WHERE JOB_GROUP IN ".$processesSet; //." OR JOB_GROUP NOT IN ".$processesSet;
	//	sm_Logger::write($query);
		$secondTable = self::sql_exec ($db, $bindings, $query);
	//	sm_Logger::write($secondTable);
		// Query alla tabelle QRTZ_LOGS del database quartz per il recupero dello stato dell'ultimo lancio dei processi
		//$query2 = "select JOB_NAME,STATUS from (select * from QRTZ_LOGS where JOB_GROUP IN ".$processesSet." order by ID desc ) x group by JOB_NAME";
		$query2 = "select  JOB_NAME,STATUS from QRTZ_LOGS where JOB_GROUP IN ".$processesSet." group by JOB_NAME order by ID";
	//	sm_Logger::write($query2);
		$thirdTable = self::sql_exec ($db, $bindings, $query2);
	//	sm_Logger::write($thirdTable);
		// print_r($thirdTable);

		//Per ogni riga recuperata dalla tabella quartz.QUARTZ_TRIGGERS, vengono recuperate le informazioni
		//richieste (es. i PREV_FIRE_TIME) e inserite nella giusta posizione sulla tabella di visualizzazione
		//finale.

		unset($item);
		foreach ($firstTable["data"] as $key=>$item){
			$process = $item["process"];
			$processI = $process."_I";
			$processQI = $process."_QI";
			$processT = $process."_T";
			$processV = $process."_V";
			$processR = $process."_R";

			$jobI = self::search($secondTable, 'JOB_NAME', $processI);
			$jobQI = self::search($secondTable, 'JOB_NAME', $processQI);
			$jobT = self::search($secondTable, 'JOB_NAME', $processT);
			$jobV = self::search($secondTable, 'JOB_NAME', $processV);
			$jobR = self::search($secondTable, 'JOB_NAME', $processR);
			$jobIfromThirdTable = self::search($thirdTable, 'JOB_NAME', $processI);
			$jobQIfromThirdTable = self::search($thirdTable, 'JOB_NAME', $processQI);
			$jobTfromThirdTable = self::search($thirdTable, 'JOB_NAME', $processT);
			$jobVfromThirdTable = self::search($thirdTable, 'JOB_NAME', $processV);
			$jobRfromThirdTable = self::search($thirdTable, 'JOB_NAME', $processR);
			if (!empty($jobI)) {
				$prevFireTimeA = $jobI[0]['PREV_FIRE_TIME'];
				$seconds = $prevFireTimeA / 1000;
				$firstTable["data"][$key]['Time A'] = date("Y-m-d H:i:s", $seconds);
			}
			else {
				$firstTable["data"][$key]['Time A'] = "";
			}
			if (!empty($jobQI)) {
				$prevFireTimeB = $jobQI[0]['PREV_FIRE_TIME'];
				$seconds = $prevFireTimeB / 1000;
				$firstTable["data"][$key]['Time B'] = date("Y-m-d H:i:s", $seconds);				
			}
			else {
				$firstTable["data"][$key]['Time B'] = "";
			}
			if (!empty($jobT)) {
				$prevFireTimeC = $jobT[0]['PREV_FIRE_TIME'];
				$seconds = $prevFireTimeC / 1000;
				$firstTable["data"][$key]['Time C'] = date("Y-m-d H:i:s", $seconds);
			}
			else {
				$firstTable["data"][$key]['Time C'] = "";
			}
			if (!empty($jobV)) {
				$prevFireTimeD = $jobV[0]['PREV_FIRE_TIME'];
				$seconds = $prevFireTimeD / 1000;
				$firstTable["data"][$key]['Time D'] = date("Y-m-d H:i:s", $seconds);
			}
			else {
				$firstTable["data"][$key]['Time D'] = "";
			}
			if (!empty($jobR)) {
				$prevFireTimeE = $jobR[0]['PREV_FIRE_TIME'];
				$seconds = $prevFireTimeE / 1000;
				$firstTable["data"][$key]['Time E'] = date("Y-m-d H:i:s", $seconds);
			}
			else {
				$firstTable["data"][$key]['Time E'] = "";
			}

			if (!empty($jobIfromThirdTable)) {
				$firstTable["data"][$key]['Status A'] = $jobIfromThirdTable[0]['STATUS'];
			}
			else {
				$firstTable["data"][$key]['Status A'] = "";
			}
			if (!empty($jobQIfromThirdTable)) {
				$firstTable["data"][$key]['Status B'] = $jobQIfromThirdTable[0]['STATUS'];
			}
			else {
				$firstTable["data"][$key]['Status B'] = "";
			}
			if (!empty($jobTfromThirdTable)) {
				$firstTable["data"][$key]['Status C'] = $jobTfromThirdTable[0]['STATUS'];
			}
			else {
				$firstTable["data"][$key]['Status C'] = "";
			}
			if (!empty($jobVfromThirdTable)) {
				$firstTable["data"][$key]['Status D'] = $jobVfromThirdTable[0]['STATUS'];
			}
			else {
				$firstTable["data"][$key]['Status D'] = "";
			}
			if (!empty($jobRfromThirdTable)) {
				$firstTable["data"][$key]['Status E'] = $jobRfromThirdTable[0]['STATUS'];
			}
			else {
				$firstTable["data"][$key]['Status E'] = "";
			}
		}
		// $query = "select STATUS from (select * from QRTZ_LOGS where JOB_NAME IN ".$processesSet." order by ID desc) x group by JOB_NAME";
		return $firstTable;
	}
	
		
	/**
	 * 
	 * Connect to the specified database, returning an handle to it.
	 *
	 * @param array $sql_details
	 *        	SQL server connection details array, with the
	 *        	properties:
	 *        	* host - host name
	 *        	* db - database name
	 *        	* user - user name
	 *        	* pass - user password
	 * @return resource Database connection handle
	 *        
	 */
	static function sql_connect($sql_details) {
		
		// Try to connect to the specified database
		try {
			$db = @new PDO (
				"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
				$sql_details ['user'],
				$sql_details ['pass'],
				array (
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", 
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8" 
				)
			);
		} 
		// Catch an exception if it was not possible to connect to database
		catch ( PDOException $e ) {
			self::fatal ("An error occurred while connecting to the database. " . "The error reported by the server was: " . $e->getMessage () );
		}
		
		return $db;
	}
	
	/**
	 * Execute an SQL query on the database
	 *
	 * @param resource $db
	 *        	Database handler
	 * @param array $bindings
	 *        	Array of PDO binding values from bind() to be
	 *        	used for safely escaping strings. Note that this can be given as the
	 *        	SQL query string if no bindings are required.
	 * @param string $sql
	 *        	SQL query to execute.
	 * @return array Result from the query (all rows)
	 *        
	 */
	static function sql_exec($db, $bindings, $sql = null) {
		// Argument shifting
		// NON MI PIACE
		if ($sql === null) {
			$sql = $bindings;
		}
		
		// Prepare the statement for execution
		$stmt = $db->prepare ( $sql );
		
		// Bind parameters
		//TODO Capire questo blocco
		if (is_array ( $bindings )) {
			for($i = 0, $ien = count ( $bindings ); $i < $ien; $i ++) {
				$binding = $bindings [$i];
				$stmt->bindValue ( $binding ['key'], $binding ['val'], $binding ['type'] );
			}
		}
		
		// Try to execute the statement
		try {
			$stmt->execute();
		} 
		// Catch an exception
		catch ( PDOException $e ) {
			self::fatal ( "An SQL error occurred: " . $e->getMessage () );
		}
		// Return all the result sets rows
		return $stmt->fetchAll ();
	}
	
	
	/**
	 * 
	 * Throw a fatal error.
	 *
	 * This writes out an error message in a JSON string which DataTables will
	 * see and show to the user in the browser.
	 *
	 * @param string $msg Message to send to the client
	 *        	
	 */
	static function fatal($msg) {
		echo json_encode (
			array ("error" => $msg)
		);
		
		exit (0);
	}
	
	
	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with sql_exec()
	 *
	 * @param
	 *        	array &$a Array of bindings
	 * @param * $val
	 *        	Value to bind
	 * @param int $type
	 *        	PDO field type
	 * @return string Bound key to be used in the SQL where this parameter
	 *         would be used.
	 *        
	 */
	static function bind(&$a, $val, $type) {
		$key = ':binding_' . count ( $a );
		
		$a [] = array (
				'key' => $key,
				'val' => $val,
				'type' => $type 
		);
		
		return $key;
	}
	
	
	/**
	 * Pull a particular property from each associative
	 * array in a numeric array,
	 * returning an array of the property values from each item.
	 *
	 * @param array $a Array to get data from
	 * @param string $prop Property to read
	 * @return array Array of property values
	 *        
	 */
	static function pluck($a, $prop) {
		$out = array ();
		
		for ($i=0, $len=count($a); $i<$len; $i ++) {
			$out[] = $a[$i][$prop];
		}
		
		return $out;
	}

static function  search($array, $key, $value)
{
    $results = array();
    self::search_r($array, $key, $value, $results);
    return $results;
}

static function search_r($array, $key, $value, &$results)
{
    if (!is_array($array)) {
        return;
    }

    if (isset($array[$key]) && $array[$key] == $value) {
        $results[] = $array;
    }

    foreach ($array as $subarray) {
        self::search_r($subarray, $key, $value, $results);
    }
}
}

