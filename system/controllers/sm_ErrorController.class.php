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

class sm_ErrorController extends sm_ControllerElement
{
	/**
	 * @desc Error page
	 * @url GET error/:code
	 * 
	 */
	
	public function error($code=null){
		if($code){
			$data['code']=$code;
			$data['requested_url']=isset($_GET['url'])?$_GET['url']:"";
			$errorView = new sm_ErrorView($data);
		}
	}
}