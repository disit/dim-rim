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

class SiiMobilitySessionManagerController extends sm_ControllerElement
{
		
	/**
	 * @desc Create a new Editing Session
	 *
	 * @url POST IndexGenerator/session
	 *
	 * @callback
	 */
	function new_session()
	{
		$man = new SiiMobilitySessionManager();
		$ret = $man->new_session();
		if($ret)
		{
			$this->view = new SiiMobilityJSONView($ret);
		}
	}
	
	/**
	 * @desc Update a existing Editing Session
	 *
	 * @url PUT IndexGenerator/session/:id
	 *
	 * @callback
	 */
	function update_session($id=null,$data=null)
	{
		$man = new SiiMobilitySessionManager();
		sm_Logger::write($data);
		$ret = $man->update_session($id,$data);
		if($ret)
		{
			$this->view = new SiiMobilityJSONView($ret);
		}
	}
	
	
}