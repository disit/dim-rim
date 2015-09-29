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

/**
 * 
 * Get an array with all the generations
 * 
 */
function getGenerations() {	
	
	$.ajax(
		{
			type: "GET",
			url: "IndexGenerator/Generations",
			// Useful for the right fulfill of the generation comboboxes
			async: false,
			dataType:"json",
			// Fill the page with the loaded generation data, if success
			success: function(generations, status, jqXHR) {
				
				// Parse the received data
				//generations = data; //JSON.parse(data);
				
				// Transform the received data
				data = [];
				var j = 0;
				for (var i = 0; i < generations.length; i++ ) {
					//TODO Capire se migliorabile
					if(generations[i].RepositoryID!=repositoryID)
					{
						data[j] = new Object();
						data[j].ID = generations[i].ID;
						data[j].Data = generations[i].RepositoryID;
						j++;
					}
				}				
				
				// Fulfill the generation comboboxes
				$(".cboSelectGenerationDate").append(addToSelect(data, 0));
				
				// Set to use the last generation
//				generationColumns = [generations[0].ID];
				generationColumns.Ontologies[0] = generations[0].ID;
				generationColumns.StaticData[0] = generations[0].ID;
				generationColumns.RealTimeData[0] = generations[0].ID;
				generationColumns.Reconciliations[0] = generations[0].ID;
				
				// The first column show the last generation (the server sent generations in descedant order)
				$("th.GenColumn0 select.cboSelectGenerationDate").val(generations[0].ID);
			} ,
			error: function(data, status, jqXHR) {
				// Show the modal window
				$('#modAlert').modal();
				
				// Set the window elements
				$('#modAlertTitle').html("Error");
				$('#modAlertBody').html("Generations list not loaded!");
			}
		}
	);
	
}


/**
 * 
 * Add a new generation column to the table
 * 
 */
function addGenerationColumn() {
	
	// Inizialites a new element in the generationColumns array
	var column = generationColumns[currentDataType].length;	
//	generationColumns[column] = 0;
	generationColumns[currentDataType][column] = 0;
	
	// Get the generation columns offset
	columnsOffset = generationColumns[currentDataType + "Offset"];
	
	// Show a new generation column 
	$(currentTable).DataTable().column(column + columnsOffset).visible(true);
		
	// Select the null choice for the combo box
	$(currentTable + " th.GenColumn" + column + " select.cboSelectGenerationDate").val(0);
	
	// Disable/enable the column buttons
	$(currentTable + " button.btnNewGenerationColumn").attr("disabled", true);
	$(currentTable + " th.GenColumn" + column + " button.btnCopyToday").attr("disabled", true);
	$(currentTable + " th.GenColumn" + column + " button.btnCloneToday").attr("disabled", true);
	$(currentTable + " th.GenColumn" + column + " button.btnRemove").attr("disabled", false);
	
	// Useful for the minimum of generation columns
	if (generationColumns[currentDataType].length > 1) {
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", false);
	}

	// Inizialites a new element in the generationColumns array
//	var column = generationColumns.length;	
//	generationColumns[column] = 0;
//	
//	// Show a new generation column 
//	//TODO Va corretta la chiamata
//	$("tableOntologies").DataTable().column(column + SDColumnsOffset).visible(true);
//	$("tableStaticData").DataTable().column(column + SDColumnsOffset).visible(true);
//	$("tableRealTimeData").DataTable().column(column + SDColumnsOffset).visible(true);
//	$("tableReconciliations").DataTable().column(column + SDColumnsOffset).visible(true);
//		
//	// Select the null choice for the combo box
//	$("table.showing-generations th.GenColumn" + column + " select.cboSelectGenerationDate").val(0);
//	
//	// Disable/enable the column buttons
//	$("table.showing-generations button.btnNewGenerationColumn").attr("disabled", true);
//	$("table.showing-generations th.GenColumn" + column + " button.btnCopyToday").attr("disabled", true);
//	$("table.showing-generations th.GenColumn" + column + " button.btnCloneToday").attr("disabled", true);
//	$("table.showing-generations th.GenColumn" + column + " button.btnRemove").attr("disabled", false);
//	
//	// Useful for the minimum of generation columns
//	if (generationColumns.length > 1) {
//		$("table.showing-generations th.GenColumn0 button.btnRemove").attr("disabled", false);
//	}
}


/**
 * 
 * Remove the specified column from the table
 * @param column the column to remove
 * 
 */
function removeGeneration(column) {

	// Get the current page number
	var currentPage = $(currentTable).DataTable().page();
	var pageLength = $(currentTable).DataTable().page.len();
	
	// Set the null choice for the combobox
	$(currentTable + " th.GenColumn" + column + " select.cboSelectGenerationDate").val(0);

	// Remove the column from the generation columns array
//	generationColumns.splice(column, 1);
	generationColumns[currentDataType].splice(column, 1);
	
	// Destroy the table
	$(currentTable).DataTable().destroy();
	
	// Reload the table, at the current page
	loadData(currentPage * pageLength);
	
	// Show the right columns
	for (var i=0 ; i < generationColumnMax; i++) {
//		if (generationColumns[i]) {
		if (generationColumns[currentDataType][i]) {
			columnsOffset = generationColumns[currentDataType + "Offset"];
			// Show a new generation column 
			$(currentTable).DataTable().columns(i + columnsOffset).visible(true);
			$(currentTable + " th.GenColumn" + i + " select.cboSelectGenerationDate").val(generationColumns[currentDataType][i]);
		}
	}
	
	// Useful for the maximum of generation columns
	$(currentTable + " button.btnNewGenerationColumn").attr("disabled", false);
	
   	// Useful for the minimum of generation columns
	if (generationColumns[currentDataType].length == 1) {
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", true);
	}	
	
//	$(currentTable).DataTable().page(currentPage).draw();
//	alert("Tabella ricaricata");
}


/**
 * 
 * Show the specified generation in the specified column
 * 
 * @param column the column to use to show the generation
 * @param generation the generation to show
 */
function showGeneration(column, generation) {
	
	// Get the current page of the table
	var currentPage = $(currentTable).DataTable().page();
	
	// Set the generation columns item
//	generationColumns[column] = generation;
	generationColumns[currentDataType][column] = generation;

	// Reload the table, at the current page
	$(currentTable).DataTable().ajax.reload().page(currentPage).draw(false);
	
	// Enable the column buttons, if a generation is selected
	if (generation == 0 ) {
		$(currentTable + " th.GenColumn" + column + " button.btnCopyToday").attr("disabled", true);
		$(currentTable + " th.GenColumn" + column + " button.btnCloneToday").attr("disabled", true);
	}
	else {
		$(currentTable + " th.GenColumn" + column + " button.btnCopyToday").attr("disabled", false);
		$(currentTable + " th.GenColumn" + column + " button.btnCloneToday").attr("disabled", false);
	}
		
	// Useful for the maximum of generation columns
	if (generationColumns[currentDataType].length < generationColumnMax) {
		$(currentTable + " button.btnNewGenerationColumn").attr("disabled", false);
	}
	
	// Set the right column footer
	//TODO Sistemare footer
//	genDate = $("th.GenColumn" + column +" select.cboSelectGenerationDate").val();
	$(currentTable + " tfoot th.GenColumn" + column).html("");
	
}


/**
 * Prepare the UI for a new user session, and write it to the database
 * 
 */
function newSession() {
	
	
	
	
	// Create a new administrator session
	$.ajax(
		{
			type: "POST",
			url: "IndexGenerator/session",
			dataType:"json",
			// Proceeds to next steps
			success: function(data, status, jqXHR) {
				
				initSession();
				
				// Get the current session ID
				currentSessionID = data.id;
				
				
				
				// Open the ontologies page
				getStep(1);
				
			} ,
			
			error: function(data, status, jqXHR) {
				
				// Show the modal window
				$('#modAlert').modal();
				
				// Set the window elements
				$('#modAlertTitle').html("Error");
				$('#modAlertBody').html("An error occured while creating a new administrator session");

			}
		}
	);

	
}

function initSession()
{
	$("li.header").removeClass("disabled");
	for (var i = 1; i <= 6; i++) {
		$("li#header_step" + i).attr("onclick", "getStep(" + i + ")");
	}
	// Disable the "prepare generation" button
	//$("#btnPrepareGeneration").hide();
}