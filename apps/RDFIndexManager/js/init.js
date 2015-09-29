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

var currentSessionID;
var repositoryID;

//Defines the used tables
var ontologiesTable;
var staticDataTable;
var realTimeDataTable;
var reconciliationsTable;
var enrichmentsTable;



$(document).ready(function() {
	
	// Activate the Bootstap tooltip
	$('button').tooltip();
	
	// Disable the header choices
//	$('li.header a').attr('onclick', '');
	
	// Set to 0 the current step to be shown, in order to show the null step page
	var currentStep = currentSessionID==undefined?0:1;
	
	// Set to 0 the current session ID
	currentSessionID = currentSessionID==undefined?0:currentSessionID;
	
	//We are edit an existing stored session by currentSessionID
	if(currentSessionID)
		initSession();
	// The table that is shown
	var currentTable ="";

	// The data type that is shown
	var currentDataType = "";
	
	// Arrays containing information about the generation to show and the offsets
	generationColumns = {
	        'Ontologies' : [],
	        'StaticData' : [],
	        'RealTimeData' : [],
	        'Reconciliations' : [],
	        'OntologiesOffset': 4,
	        'StaticDataOffset': 6,
	        'RealTimeDataOffset': 6,
	        'ReconciliationsOffset': 6,
	    };	
	
	// The maximum number of generation columns to show
	generationColumnMax = 5;
	
	// Defines the used tables
	/*var ontologiesTable;
	var staticDataTable;
	var realTimeDataTable;
	var reconciliationsTable;
	*/
/*	$( 'input#hideshowstaticdata').on( 'change', function () {
        alert("ciao");
        var filteredData = staticDataTable
        .column( 12 )
        .data()
        .filter( function ( value, index ) {
            return value ? true : false;
        } );
    } );
	*/
	// Get the generations
	getGenerations();
	

	
	// Provides to showing the null step page, hiding the other step pages
	$(".step").hide();
	getStep(currentStep);
	
} );