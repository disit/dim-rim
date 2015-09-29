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

class ServerSideProcessor {
	
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
		for($i = 0, $ien = count ($data); $i < $ien; $i ++) {
			
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
		
		// Verify if the 'order' clause is set and if there is at least an element
		if (isset ( $request ['order'] ) && count ( $request ['order'] )) {

			// Create an array for the order clause
			$orderBy = array ();

			// Extract the 'dt' key of the columns array
			// in order to obtain all the DataTables columns name
			$dtColumns = self::getNumericArray ( $columns, 'dt' );
			
			// For each column, create the order clause section
			for($i = 0, $ien = count ( $request ['order'] ); $i < $ien; $i ++) {

				// Index of the order column into the request columns array
				$columnIdx = intval ( $request ['order'] [$i] ['column'] );
				
				// Column of the request columns array
				$requestColumn = $request ['columns'] [$columnIdx];
				
				// Index of the order column into the DataTables columns array
				$columnIdx = array_search ( $requestColumn ['data'], $dtColumns );
				
				// The right column of the column information array (associations between dataTables and the database)
				$column = $columns [$columnIdx];
				
				// If this column is orderable, set the order clause section
				if ($requestColumn ['orderable'] == 'true') {
					
					// Look for the specified order: ascendant or descendant
					$dir = $request ['order'] [$i] ['dir'] === 'asc' ? 'ASC' : 'DESC';
					
					// Create the order clause section
					$orderBy [] = '`' . $column ['db'] . '` ' . $dir;
				}
			}
			
			// Implode the order clause to be used
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
	 * @param array $request Data sent to server by DataTables
	 * @param array $columns Column information array, with the associations between dataTables and the database
	 * @param array $bindings Array of values for PDO bindings, used in the sql_select() function. This array is passed by reference
	 * @param array $filter Global filter to use    
	 * @return string SQL where clause to use
	 *        
	 */
	static function filter($request, $columns, $filter, &$bindings) {
		
		// Global and column search where clause arrays
		$globalSearch = array ();
		$columnSearch = array ();
		
		// Extract the 'dt' key of the columns array
		// in order to obtain all the DataTables columns name
		$dtColumns = self::getNumericArray($columns, 'dt');
		
		//
		// Global search
		//
		
		// Verify if the 'search' clause is set and if there is at least a search value element
		if (isset ($request['search']) && $request ['search']['value'] != '') {
			
			// Get the string value to search
			$globalSearchValue = $request['search']['value'];
			
			// For each column of request
			for($i = 0, $ien = count ($request['columns']); $i < $ien; $i ++) {
				
				// i-th column of the request columns array
				$requestColumn = $request['columns'] [$i];
				
				// Index of the order column into the DataTables columns array
				$columnIdx = array_search ($requestColumn['data'], $dtColumns);
				
				// The right column of the column information array (associations between dataTables and the database)
				$column = $columns [$columnIdx];
				
				// If this column is searchable, set the where clause section
				if ($requestColumn['searchable'] == 'true') {
					
					// Execute the binding of the search parameter
					$binding = self::bind ($bindings, '%' . $globalSearchValue . '%', PDO::PARAM_STR);
					
					// Create the where clause section (with the binding of the search parameter)
					if(isset($column['table']))
						$globalSearch [] = $column['db'] . "_Table.".$column ['db'] . " LIKE " . $binding;
					else
						$globalSearch [] = "`" . $column ['db'] . "` LIKE " . $binding;
				}
			}
		}
		
		//
		// Individual column filtering search
		//
		
		// For each column of request
		for($i = 0, $ien = count ( $request ['columns'] ); $i < $ien; $i ++) {

			// i-th column of the request columns array
			$requestColumn = $request ['columns'] [$i];

			// Index of the order column into the DataTables columns array
			$columnIdx = array_search ( $requestColumn ['data'], $dtColumns );

			// The right column of the column information array (associations between dataTables and the database)
			$column = $columns [$columnIdx];
			
			// The column search value
			$columnSearchValue = $requestColumn ['search'] ['value'];
			
			// If this column is searchable and the column search value is set, set the where clause section
			if ($requestColumn ['searchable'] == 'true' && $columnSearchValue != '') {

				//Execute the binding of the search parameter
				$binding = self::bind ( $bindings, '%' . $columnSearchValue . '%', PDO::PARAM_STR );

				// Create the where clause section (with the binding of the search parameter)
				if(isset($column['table']))
						$columnSearch [] = $column['db'] . "_Table.".$column ['db'] . " LIKE " . $binding;
				else
					$columnSearch [] = "`" . $column ['db'] . "` LIKE " . $binding;
			}
		}
		
		// The where clause to set
		$where = '';
		
		// Implode the where clause for the global search
		if (count ( $globalSearch )) {
			$where = '(' . implode (' OR ', $globalSearch) . ')';
		}
		
		// Implode the where clause for the individual column search,
		// checking if the global clause search is set or not.
		if (count ( $columnSearch )) {
			$where = $where === '' ? implode (' AND ', $columnSearch) : $where . ' AND ' . implode (' AND ', $columnSearch);
		}
		
		// Execute the binding of a global filter
		if (isset ($filter['filterField']) && isset ($filter['filterValue'])) {
			$binding = self::bind ($bindings, $filter['filterValue'], PDO::PARAM_STR);
			// Create the where clause section (with the binding of the search parameter)
			$where = $where === '' ? '`' . $filter['filterField'] . '` LIKE ' . $binding : $where . ' AND `' . $filter['filterField'] . '` LIKE ' . $binding;
		}
 		if (isset ($filter['selectionField'])) {
 			$where = $where === '' ? $filter['selectionTable'] . '.' . $filter['selectionField'] . ' IS NOT NULL' : $where . ' AND ' . $filter['selectionTable'] . '.' . $filter['selectionField'] . ' IS NOT NULL';
		}
		
		// Create the final where clause
		if ($where !== '') {
			$where = 'WHERE ' . $where;
		}
		//sm_Logger::write($where);
		return $where;
	}
	
// 	/**
// 	 * Perform the SQL queries needed for an server-side processing requested,
// 	 * utilising the helper functions of this class, limit(), order() and
// 	 * filter() among others.
// 	 * The returned array is ready to be encoded as JSON
// 	 * in response to an SSP request, or can be modified if needed before
// 	 * sending back to the client.
// 	 *
// 	 * @param array $request
// 	 *        	Data sent to server by DataTables
// 	 * @param array $sql_details
// 	 *        	SQL connection details - see sql_connect()
// 	 * @param string $table
// 	 *        	SQL table to query
// 	 * @param string $primaryKey
// 	 *        	Primary key of the table
// 	 * @param array $columns
// 	 *        	Column information array
// 	 * @return array Server-side processing response array
// 	 *        
// 	 */
// 	static function simple($request, $sql_details, $table, $primaryKey, $columns) {
		
// 		//Create the bindings array, the will be modified by the filter function
// 		$bindings = array ();
		
// 		// Get an handle to the database connection
// 		$db = self::sql_connect ( $sql_details );
		
// 		// Build the SQL query string from the request
// 		$limit = self::limit ( $request, $columns );
// 		$order = self::order ( $request, $columns );
// 		$where = self::filter ( $request, $columns, $bindings );
		
// 		// The query to get the data
// 		$data = self::sql_select ( $db, $bindings, "SELECT SQL_CALC_FOUND_ROWS `" . implode ( "`, `", self::getNumericArray( $columns, 'db' ) ) . "`
// 			FROM `$table`
// 			$where
// 			$order
// 			$limit" );
		
// 		// Dataset length after filtering
// 		$resFilterLength = self::sql_select ( $db, "SELECT FOUND_ROWS()" );
// 		$recordsFiltered = $resFilterLength[0][0];
		
// 		// Total data set length
// 		$resTotalLength = self::sql_select ( $db, "SELECT COUNT(`{$primaryKey}`) FROM `$table`" );
// 		$recordsTotal = $resTotalLength[0][0];
		
// 		// Data output
// 		return array (
// 				"draw" => intval ($request['draw']),
// 				"recordsTotal" => intval ($recordsTotal),
// 				"recordsFiltered" => intval ($recordsFiltered),
// 				"data" => self::data_output ($columns, $data) 
// 		);
// 	}
	
	
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
	 * @param array $filter
	 *        	Global filter to use    
	 * @return array Server-side processing response array
	 *        
	 */
	static function nested($request, $sql_details, $masterTable, $primaryKey, $columns, $filter) {				
		
		//Create the bindings array, the will be modified by the filter function
		$bindings = array ();
		
		// Get an handle to the database connection
		$db = MySqlConnector::sql_connect ($sql_details);
		
		// Build the SQL query string from the request
		$limit = self::limit ($request, $columns);
		$order = self::order ($request, $columns);
		$where = self::filter ($request, $columns, $filter, $bindings);
		
		$leftJoinClause = "";
		
		// For each elements in the column information array, prepare the table.field clause for the select and the leftJoinClause
		foreach ($columns as &$element) {
			// If the table element is set, then this is a nested column
			if (isset($element['table'])) {
				
				// Set a placeholder element for the following implode
				$element['db_table'] = $element['db'] . "_Table." . $element['field'] . " AS " . $element['db'];

				// Execute the binding of the search parameter
				$binding = self::bind ($bindings, $element['clauseValue'], PDO::PARAM_STR);
				
				// Set the left join clause
				$leftJoinClause = $leftJoinClause.
				" LEFT JOIN " . $element['table'] . " AS ". $element['db'] ."_Table" .
				" ON " . $masterTable . "." . $primaryKey. " = " . $element['db'] . "_Table." . $element['primaryKey'].
				" AND " . $element['db'] . "_Table." . $element['clauseField'] . " = " . $binding;
			
			}
			// ... else it is a column of the master table
			else 
				$element['db_table'] = $masterTable . "." . $element['db'];
		}		
						
// 		print_r("SELECT SQL_CALC_FOUND_ROWS " . implode ( ", ", self::getNumericArray( $columns, 'db_table' ) ) . 
// 				" FROM $masterTable 
// 				$leftJoinClause 
// 				$where 
// 				$order 
// 				$limit");
/*		sm_Logger::write("SELECT SQL_CALC_FOUND_ROWS " . implode ( ", ", self::getNumericArray( $columns, 'db_table' ) ) .
		" FROM $masterTable
		$leftJoinClause
		$where
		$order
		$limit");
		sm_Logger::write($bindings);*/
		// The query to get the data
		$data = MySqlConnector::sql_select( $db, $bindings, 
			"SELECT SQL_CALC_FOUND_ROWS " . implode ( ", ", self::getNumericArray( $columns, 'db_table' ) ) . 
				" FROM $masterTable 
				$leftJoinClause 
				$where 
				$order 
				$limit");
		
		// Dataset length after filtering
		$resFilterLength = MySqlConnector::sql_select ( $db, "SELECT FOUND_ROWS()" );
		$recordsFiltered = $resFilterLength[0][0];
		
		// Total data set length
		$totalQuery = "SELECT COUNT(`{$primaryKey}`) FROM `$masterTable`";
		// Consider the global filter
		if (isset ($filter['filterField']) && isset ($filter['filterValue'])) {
			$totalQuery = $totalQuery . ' WHERE ' . $filter['filterField'] . '="' . $filter['filterValue'] . '"';
		}
		$resTotalLength = MySqlConnector::sql_select ( $db, $totalQuery);
		$recordsTotal = $resTotalLength[0][0];		
		
		// Data output
		return array (
				"draw" => intval ($request['draw']),
				"recordsTotal" => intval ($recordsTotal),
				"recordsFiltered" => intval ($recordsFiltered),
				"data" => self::data_output ($columns, $data) 
		);
		
	}
	
			
	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with sql_select()
	 *
	 * @param array &$$bindings Array of bindings
	 * @param $val Value to bind
	 * @param int $type PDO field type
	 * @return string Bound key to be used in the SQL where this parameter would be used.
	 *        
	 */
	static function bind(&$bindings, $val, $type) {
		
		// Create the key for the binding
		$key = ':binding_' . count ($bindings);
		
		// Add the binding to the bindings array
		$bindings [] = array (
				'key' => $key,
				'val' => $val,
				'type' => $type 
		);
		
		// Return the key of the binding
		return $key;
	}
	
	
	/**
	 * Pull a particular property from an associative array into a numeric array,
	 * returning an array of the property values from each item.
	 *
	 * @param array $in Array to get data from
	 * @param string $prop Property to read
	 * @return array Array of property values
	 *        
	 */
	static function getNumericArray($in, $prop) {
		
		// The array to return
		$out = array ();
		
		// Write the i-th value of prop from input array to output array 
		for ($i=0, $len=count($in); $i<$len; $i ++) {
			$out[] = $in[$i][$prop];
		}
		
		return $out;
	}
	
	
}


