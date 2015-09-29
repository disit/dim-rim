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

/*
 * 
 * Helper functions for MySQL connections
 * 
 */
class MySqlConnector {
	
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
	 * 
	 * Prepare the SQL statement for the execution
	 *
	 * @param resource $db Database handler
	 * @param array $bindings
	 *        	Array of PDO binding values from bind() to be
	 *        	used for safely escaping strings. Note that this can be given as the
	 *        	SQL query string if no bindings are required.
	 * @param string $sql
	 *        	SQL query to execute.
	 * @return statement An SQL statement
	 *        
	 */
	static function sql_prepareStatement($db, $bindings, $sql = null) {
		
		// Argument shifting
		if ($sql === null) {
			$sql = $bindings;
		}
		
		// Prepare the statement for execution
		$stmt = $db->prepare ($sql);
		
		// If there is a binding array, bind the parameters
		if (is_array($bindings)) {
			
			// For each element of the binding array
			for($i = 0, $ien = count ($bindings); $i < $ien; $i ++) {
				
				// Bind the parameters to the statetement
				$binding = $bindings [$i];
				$stmt->bindValue ( $binding['key'], $binding['val'], $binding['type']);
			}
		}
		
		// Return the statement
		return $stmt;
				
	}
	
	
	/**
	 * Execute an SQL SELECT on the database
	 *
	 * @param resource $db Database handler
	 * @param array $bindings
	 *        	Array of PDO binding values from bind() to be
	 *        	used for safely escaping strings. Note that this can be given as the
	 *        	SQL query string if no bindings are required.
	 * @param string $sql
	 *        	SQL query to execute.
	 * @return array Result from the query (all rows)
	 *        
	 */
	static function sql_select($db, $bindings, $sql = null) {
				
		// Prepare the statement
		$stmt = self::sql_prepareStatement($db, $bindings, $sql);
		
		// Try to execute the statement
		try {
			$stmt->execute();
		} 
		// Catch an exception
		catch (PDOException $e) {
			self::fatal ("An SQL error occurred: " . $e->getMessage ());
		}
		
		// Return all the result sets rows
		return $stmt->fetchAll();
	}

	
	/**
	 * Execute an SQL query on the database
	 *
	 * @param resource $db Database handler
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
				
		// Prepare the statement
		$stmt = self::sql_prepareStatement($db, $bindings, $sql);
		
		// Try to execute the statement
		try {
			$result = $stmt->execute();
		} 
		// Catch an exception
		catch (PDOException $e) {
			self::fatal ("An SQL error occurred: " . $e->getMessage ());
		}
		
		return $result;
		
	}
	
	
	/**
	 * Execute an INSERT SQL query on the database
	 *
	 * @param resource $db Database handler
	 * @param array $bindings
	 *        	Array of PDO binding values from bind() to be
	 *        	used for safely escaping strings. Note that this can be given as the
	 *        	SQL query string if no bindings are required.
	 * @param string $sql
	 *        	SQL query to execute.
	 * @return array Result from the query (all rows)
	 *        
	 */
	//TODO Dovrebbe essere la generale
	static function sql_insert($db, $sql) {
						
		// Prepare the statement for execution
		$stmt = $db->prepare($sql);
				
		// Try to execute the statement
		try {
			$stmt->execute();
		} 
		// Catch an exception
		catch (PDOException $e) {
			self::fatal ("An SQL error occurred: " . $e->getMessage ());
		}
		
		// Return the ID of the inserted row
		return $db->lastInsertId();
		
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
	
}


