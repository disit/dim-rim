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
 * $virtuosoDb = new VIRTUOSODB(); 
 * $dbtype="odbc";
 * $dsn="My Virtuoso ODBC";
 * $usr="admin";
 * $pwd="";
 * $cursr="SQL_CUR_DEFAULT";
 * $virtuosoDb->opendb($dbtype,$dsn,$usr,$pwd,$cursr); 
 */

/* Create an ODBC Wrapper class for Virtuoso database connectivity */
class VIRTUOSODB {
	/* Set connection variables */
	var $record_set,$connect,$cur_sur,$dbtype,$num_rows,$current_rec,$cur_field,
	$current_row;

	/* Create an object to set the cursor of the database */
	function cursorname($cursor){
		$this->cur=$cursor;
	}

	/*-----------------------------------*/
	/* Connects to the remote database */
	/* $domain = data source name */
	/* $userid = user id */
	/* $passwd = password */
	/* $cursr = type of cursor */
	/*-----------------------------------*/

	/* Create an object to connect to the database/ODBC */
	function opendb($domain,$userid,$passwd,$cursr){
		$this->connect = odbc_connect($domain,$userid,$passwd,$cursr); /* means
		odbc_connect($dsn,$uid,$pwd,$cur) */
		if (!$this->connect){
			return $this->connect;
			break;
		}
		else{
			return $this->connect;
		}
	}

	/*----------------------------------------- */
	/* execute an sql query for the recordset */
	/* $SqlQuery = the sql statement */
	/*----------------------------------------- */

	function openrs($sqlstatement){

		$this->record_set = odbc_exec($this->connect,$sqlstatement); /* get the result */
		while (odbc_fetch_row($this->record_set)){
			$this->num_rows += 1; /* count the number of rows */
		}
		$this->current_rec = odbc_fetch_row($this->record_set,1); /* gets the current row */
		$this->current_row = 1; /* reset row (first row) */
		return $this->record_set;
	}

	/*-----------------------------------*/
	/* display the value of the field */
	/* $dbtype = sql server type */
	/* $kword = column name */
	/*-----------------------------------*/

	function getfield($kword){
		$this->field = odbc_result($this->record_set,$kword); /* get the value of the column
		*/
		return $this->field; /* return the value */
	}

	// move to the first record
	function movetofirst(){
		$this->current_row=1;
		$this->current_rec = odbc_fetch_row($this->record_set,1);
	}

	// move to the next record
	function movetonext(){
		$this->current_row+=1; /* move to the next row */
		if ($this->current_row <= $this->num_rows){ /* see if current row is less than the
			number of rows returned */
			$this->current_rec = odbc_fetch_row($this->record_set,$this->current_row); /* set
			current row */
		}
	}


	// move to the previous record
	function movetoprev(){
		$this->current_row-=1; /* move to the previous row */
		if (!$this->current_row < 1){ /* see if current row is greater than 0 */
			$this->current_rec = odbc_fetch_row($this->record_set,$this->current_row); /* set
			current row */
		}
	}


	// move to the last record
	function movetolast(){
		$this->current_row = $this->num_rows; /* move to the last row */
		$this->current_rec = odbc_fetch_row($this->record_set,$this->current_row); /* set
		current row */
	}

	function eof(){
		if ($this->current_row > $this->num_rows) /* see if last row is reached */
		{
			$this->current_row = $this->num_rows; /* if current row passes the number of rows,
			set it back to the last row */
			return true; /* returns true if current row is the last row */
		}
		else
		{
			return false; /* returns false if current row is less than the number of rows */
		}
	}

	function bof(){
		if ($this->current_row < 1) /* see if first row is reached */
		{
			$this->current_row = 1; /* if current row passes the first row, set it back to the first row
			*/
			return true; /* returns true if current row is the first row */
		}
		else
		{
			return false;/* returns false if current row is not yet the first row */
		}
	}

	function close(){
		odbc_close($this->connect);
	}
	function free(){
		odbc_free_result($this->record_set);
	}

}

?>