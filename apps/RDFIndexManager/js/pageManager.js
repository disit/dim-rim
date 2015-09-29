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
 * Provides the feature to show the specified step of the index tool 
 * 
 * @param {number} step The step to show into the index tool
 * 
 */
function getStep(step) {
	
	// Change the value of the global currentStep page
	currentStep = step;
	
	// Hide all the step pages
	$(".step").hide();
	
	// Set the header
	setHeader(step);

	// Show the current step page
	$("#page" + currentStep).show();

	
	//TODO Questa sezione di codice � un po' da rivedere...
	switch(currentStep) {
    case 0:
    	break;
	case 1:
		// Load the ontologies
		loadOntologies();
		
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", true);
		// TODO Questo va nel generico, se riesco a generalizzare
		setListeners();
		break;
	case 2:
		
		// Load the static data
		loadStaticData();
		
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", true);
		// TODO Questo va nel generico, se riesco a generalizzare
		setListeners();
		break;
	case 3:
		// Load the real time data
		loadRealTimeData();
		
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", true);
		// TODO Questo va nel generico, se riesco a generalizzare
		setListeners();
		break;
	case 4:
		// Load the reconciliations
		loadReconciliations();
		
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", true);
		// TODO Questo va nel generico, se riesco a generalizzare
		setListeners();
		break;
	case 5:
		// Load the enrichments
		loadEnrichments();
		
		$(currentTable + " th.GenColumn0 button.btnRemove").attr("disabled", true);
		// TODO Questo va nel generico, se riesco a generalizzare
		setListeners();
		break;
	case 6:
		// Load the summary
		loadSummary();
		break;

    default:
    	break;
	} 
}


/**
 * 
 * Load the ontologies information and fulfill the table 
 * 
 */
function loadOntologies(currentItem) {
	
	// Set the current table e data type
	currentTable = "#tableOntologies";
	currentDataType = "Ontologies";
	currentItem = currentItem || 0;
	
	// Useful if the static data table is already set
	if ( $.fn.dataTable.isDataTable('#tableOntologies') ) {
		ontologiesTable.ajax.reload();
		return null;
	}
	
	// Set the ontologies table
	ontologiesTable = $('#tableOntologies').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/Ontologies",
	    	"processing": true,
	        "serverSide": true,
	    	// Information about generations to show are sent to the server, as an object
	    	"data" : function (d) {
	    		d.generations = $.extend({}, generationColumns[currentDataType]);
	    		d.currentSession = currentSessionID;
	    		if ($('input[name="OntRdStatus"]').is(':checked')) {
	    			var val = $('input[name="OntRdStatus"]:checked').val()
	    			if(val!=-1)
	    				d.columns[11].search.value=""+val;
	    		}
	    	}
	    },

		"columns": [

			{ "data": "Name" },
			{ "data": "URIPrefix" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Ontologies/" + row.Name;
				return "Ontologies/" +row.Name;
				}
			},
			{ "data": function (row, type, val, meta) {
				var lastUpdate = row.LastFileDate;
			/*	lastUpdate = lastUpdate.replace("T", " ");
				lastUpdate = lastUpdate.split(".");*/
				return lastUpdate; //[0];
				}
			},
			{
				"class": "GenColumn0",
				"orderable": false,
				"searchable": false,
				"data": function (row, type, val, meta) {
					if (row.Generation0)
						return row.Generation0;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn1",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation1)
						return row.Generation1;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn2",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation2)
						return row.Generation2;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn3",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation3)
						return row.Generation3;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn4",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation4)
						return row.Generation4;
					else 
						return "n.a.";
			    }
			},
			{
				"orderable": false,
				// Create an HTML select with all the versions of the data
				//TODO Correggi +01
			    "data": function (row, type, val, meta) {
			    	if(row.Locked=="1")
			    	{
			    		var lastUpdate = row.SelectedVersion; //row.LastFileDate;
						if(lastUpdate)
						{
							lastUpdate = lastUpdate.replace("T", " ");
							lastUpdate = lastUpdate.split(".");
							lastUpdate=lastUpdate[0];
						}
						return lastUpdate;
						
			    	}
			    	else
			    		return  "<select size='1' class='form-control cboToday'>" +
			    	addToSelect(row.Versions, row.SelectedVersionIndex) + 
			    	"</select>";
			    }
			},
			{ 
				"class":"status",
				"name":"Locked",
				"data":"Locked",
				"render": function(row, type, val, meta) {
				if(val.Locked=="1")
				{
					var _class="locked"
					if(val.Clone=="1")
					{
						_class+=" cloned";
					}
					
					ontologiesTable.row( '#'+val.DT_RowId )
				    .nodes()
				    .to$()      // Convert to a jQuery object
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					if(val.SelectedVersionIndex)
					{
						ontologiesTable.row( '#'+val.DT_RowId )
					
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
						return "<b>NEW</b>";
					}
				}
				return "";
					
			
				}
			},
			
		],
		"displayStart": currentItem,
		"dom": '<l<"#Onto.toolbar">>TCfrtip', //'Clfrtip',
		"colVis" : {
			"exclude": [ 5, 6, 7, 8, 9,10]
		},
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 75, 100 ], [10, 25, 50, 75, 100 ]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "responsive": true,
        "tableTools": {
            "sRowSelect": "multi",
            "sRowSelector":"td:nth-child(n+1):nth-child(-n+5)",
            "aButtons": [ "select_all", "select_none",
                          {
            					"sButtonClass":"DTTT_disabled",
            					"sExtends":    "text",
            					"sButtonText": "Add Selection",
            					
            					"fnSelect":function( nButton, oConfig )
            					{
            						$('#'+nButton.id).removeClass("DTTT_disabled");
            						var indexes = this.fnGetSelectedIndexes();
            						if(indexes.length==0)
                						$('#'+nButton.id).addClass("DTTT_disabled");
            					},
            					"fnClick": function ( nButton, oConfig, oFlash ) {
            						if($('#'+nButton.id).hasClass("DTTT_disabled"))
            							return;
            						var aData = this.fnGetSelectedData();
            						for(var d in aData)
            						{
            							if(aData[d].Versions.length>0)
            							{
            								$("#"+aData[d].DT_RowId+" select.cboToday").val(1).change();
            							}
            						}
            						this.fnSelectNone();
            						//$('#'+nButton.id).addClass("DTTT_disabled");
            					}
               
                          },
                          {
                        	    "sButtonClass":"DTTT_disabled",
				            	"sExtends":    "text",
				                "sButtonText": "Remove Selection",
				               
            					"fnSelect":function( nButton, oConfig )
            					{
            						$('#'+nButton.id).removeClass("DTTT_disabled");
            						var indexes = this.fnGetSelectedIndexes();
            						if(indexes.length==0)
                						$('#'+nButton.id).addClass("DTTT_disabled");
            					},
            					"fnClick": function ( nButton, oConfig, oFlash ) {
            						if($('#'+nButton.id).hasClass("DTTT_disabled"))
            							return;
            						var aData = this.fnGetSelectedData();
            						for(var d in aData)
            						{
            							$("#"+aData[d].DT_RowId+" select.cboToday").val(0).change();
            						}
            						//$('#'+nButton.id).addClass("DTTT_disabled");
            						this.fnSelectNone();
            					}
				           }],
            "fnRowSelected":function(node){
            	for(var n in node)
            	{
            		if($(node[n]).hasClass("locked"))
            			this.fnDeselect( $(node[n]) );
            	}
            }
        }
	} );
	
	$("div#Onto.toolbar").html('for <input type=radio name="OntRdStatus" value="-1" checked/><b>All</b> <input type=radio name="OntRdStatus" value="1"/><b>Committed</b> <input type=radio name="OntRdStatus" value="0"/><b>New</b> items ');

	$('input[name="OntRdStatus"]').on('change',function(){
			ontologiesTable.ajax.reload();
	});
	
}


/**
 * 
 * Load the static data information and fulfill the table 
 * 
 */
function loadStaticData(currentItem) {
	
	// Set the current table e data type
	currentTable = "#tableStaticData";
	currentDataType = "StaticData";
	currentItem = currentItem || 0;

	// Useful if the static data table is already set
	if ( $.fn.dataTable.isDataTable('#tableStaticData') ) {
		staticDataTable.ajax.reload();
		return null;
	}
	
	// Set the static data table
	staticDataTable = $('#tableStaticData').DataTable( {
		"fnServerParams": function (aoData) {
	      //  var includeUsuallyIgnored = $("#include-checkbox").is(":checked");
	        //aoData.columns.push({name: "Clone", value: 0});
	    },
	    "ajax": {
	    	"url": "IndexGenerator/StaticData",
	    	"processing": true,
	        "serverSide": true,
	    	// Information about generations to show are sent to the server, as an object
	    	"data" : function (d) {
	    		d.generations = $.extend({}, generationColumns[currentDataType]);
	    		d.currentSession = currentSessionID;
	    		if ($('input#SDataWithTriples').is(':checked')) {
	    			d.columns[6].search.value="201";
	    		}
	    		if ($('input[name="SDataRdStatus"]').is(':checked')) {
	    			var val = $('input[name="SDataRdStatus"]:checked').val()
	    			if(val!=-1)
	    				d.columns[13].search.value=""+val;
	    		}
	    	}
	    },
		"columns": [
			{
				"class": "btnMoreInfo",
				"orderable": false,
				"data": null,
				"defaultContent": ''
			},
			{ "data": "Name" },
			{ "data": "Resource" },
			{ "data": "Description" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Triples/" + row.Category + "/" + row.Name;
				return row.Category + "/" + row.Name;
				}
			},
			{ /*"data": function (row, type, val, meta) {
				var lastUpdate = row.LastUpdate;
				//TODO Reintegrare???
//				lastUpdate = lastUpdate.replace("T", " ");
//				lastUpdate = lastUpdate.split(".");
				return lastUpdate; //[0];
				},*/
				"data":"LastUpdate",
				"orderable": true,
				"searchable": true,
				"type":"date"
			},
			{
				"class": "GenColumn0",
				"orderable": false,
				"searchable": false,
				"data": function (row, type, val, meta) {
					if (row.Generation0)
						return row.Generation0;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn1",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation1)
						return row.Generation1;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn2",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation2)
						return row.Generation2;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn3",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation3)
						return row.Generation3;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn4",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation4)
						return row.Generation4;
					else 
						return "n.a.";
			    }
			},
			{
				"orderable": false,
				// Create an HTML select with all the versions of the data
				//TODO Correggi +01
			    "data": function (row, type, val, meta) {
			    	if(row.Locked=="1")
			    	{
			    		var lastUpdate = row.SelectedVersion;//row.LastUpdate;
						if(lastUpdate)
						{
							lastUpdate = lastUpdate.replace("T", " ");
							lastUpdate = lastUpdate.split(".");
							lastUpdate=lastUpdate[0];
						}
						
						return lastUpdate; //[0];
						
			    	}
			    	else
			    	return  "<select size='1' class='form-control cboToday'>" +
			    	addToSelect(row.Versions, row.SelectedVersionIndex) + 
			    	"</select>";
			    }
			},
			{ 
			  "class":"status",
			  "searchable": true,
			  "data":"Locked",
			  "render": function(row, type, val, meta) {
				if(val.Locked=="1")
				{
					var _class="locked"
					if(val.Clone=="1")
					{
						_class+=" cloned";
					}
					
					staticDataTable.row( '#'+val.DT_RowId )
				    .nodes()
				    .to$()      // Convert to a jQuery object
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					if(val.SelectedVersionIndex)
					{
						staticDataTable.row( '#'+val.DT_RowId )
					
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
						return "<b>NEW</b>";
					}
				}
				return "";
			} },
		],
		"displayStart": currentItem,
		"dom": '<l<"#SD.toolbar">>TCfrtip',
		"colVis" : {
			"exclude": [0, 7, 8, 9, 10, 11, 12]
		},
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[1, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "responsive": true,
        "tableTools": {
            "sRowSelect": "multi",
            "sRowSelector":"td:nth-child(n+2):nth-child(-n+6)",
            "aButtons": [ "select_all", "select_none",
                          {
            					"sButtonClass":"DTTT_disabled",
            					"sExtends":    "text",
            					"sButtonText": "Add Selection",
            					
            					"fnSelect":function( nButton, oConfig )
            					{
            						$('#'+nButton.id).removeClass("DTTT_disabled");
            						var indexes = this.fnGetSelectedIndexes();
            						if(indexes.length==0)
                						$('#'+nButton.id).addClass("DTTT_disabled");
            					},
            					"fnClick": function ( nButton, oConfig, oFlash ) {
            						if($('#'+nButton.id).hasClass("DTTT_disabled"))
            							return;
            						var aData = this.fnGetSelectedData();
            						for(var d in aData)
            						{
            							if(aData[d].Versions.length>0)
            							{
            								$("#"+aData[d].DT_RowId+" select.cboToday").val(1).change();
            							}
            						}
            						this.fnSelectNone();
            						//$('#'+nButton.id).addClass("DTTT_disabled");
            					}
               
                          },
                          {
                        	    "sButtonClass":"DTTT_disabled",
				            	"sExtends":    "text",
				                "sButtonText": "Remove Selection",
				               
            					"fnSelect":function( nButton, oConfig )
            					{
            						$('#'+nButton.id).removeClass("DTTT_disabled");
            						var indexes = this.fnGetSelectedIndexes();
            						if(indexes.length==0)
                						$('#'+nButton.id).addClass("DTTT_disabled");
            					},
            					"fnClick": function ( nButton, oConfig, oFlash ) {
            						if($('#'+nButton.id).hasClass("DTTT_disabled"))
            							return;
            						var aData = this.fnGetSelectedData();
            						for(var d in aData)
            						{
            							$("#"+aData[d].DT_RowId+" select.cboToday").val(0).change();
            						}
            						//$('#'+nButton.id).addClass("DTTT_disabled");
            						this.fnSelectNone();
            					}
				           }],
            "fnRowSelected":function(node){
            	for(var n in node)
            	{
            		if($(node[n]).hasClass("locked"))
            			this.fnDeselect( $(node[n]) );
            	}
            }
        }
	} );
	//addSelectionToolbar("#toolbar","tableStaticDataToolBar");
	$("div#SD.toolbar").html('for <input type=radio name="SDataRdStatus" value="-1" checked/><b>All</b> <input type=radio name="SDataRdStatus" value="1"/><b>Committed</b> <input type=radio name="SDataRdStatus" value="0"/><b>New</b> items ' +
			'<input type="checkbox" name="ckbFilter" id=SDataWithTriples value=""/> only with Triples');
	$('input#SDataWithTriples').on('change',function(){
		staticDataTable.ajax.reload();
	});
	$('input[name="SDataRdStatus"]').on('change',function(){
		staticDataTable.ajax.reload();
	});
}


/**
 * 
 * Load the real time data information and fulfill the table 
 * 
 */
function loadRealTimeData(currentItem) {
	
	// Set the current table e data type
	currentTable = "#tableRealTimeData";
	currentDataType = "RealTimeData";
	currentItem = currentItem || 0;

	// Useful if the real time data table is already set
	if ( $.fn.dataTable.isDataTable('#tableRealTimeData') ) {
		realTimeDataTable.ajax.reload();
		return null;
	}
		
	// When the table is drawn, it will show the date and time pickers
	$('#tableRealTimeData').on("draw.dt", function (){
			setDateTimePickers();
	});		
	
	// Set the static data table
	realTimeDataTable = $('#tableRealTimeData').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/RealTimeData",
	    	"processing": true,
	        "serverSide": true,
	    	// Information about generations to show are sent to the server, as an object
	    	"data" : function (d) {
	    		d.generations = $.extend({}, generationColumns[currentDataType]);
	    		d.currentSession = currentSessionID;
	    		if ($('input#RTDataWithTriples').is(':checked')) {
	    			d.columns[6].search.value="201";
	    		}
	    		if ($('input[name="SRTDataRdStatus"]').is(':checked')) {
	    			var val = $('input[name="SRTDataRdStatus"]:checked').val()
	    			if(val!=-1)
	    				d.columns[13].search.value=""+val;
	    		}
	    	},
	    },
		"columns": [
			{
				"class": "btnMoreInfo",
				"orderable": false,
				"data": null,
				"defaultContent": ''
			},
			{ "data": "Name" },
			{ "data": "Resource" },
			{ "data": "Description" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Triples/" + row.Category + "/" + row.Name;
				return row.Category + "/" + row.Name;
				}
			},
			{ 
				"data":"LastUpdate",
				"orderable": true,
				"searchable": true,
				"type":"date",
				"render": function (row, type, val, meta) {
				//var lastUpdate = row.LastFileDate;
				var lastUpdate = val.LastUpdate;
				if(lastUpdate)
				{
					lastUpdate = lastUpdate.replace("T", " ");
					lastUpdate = lastUpdate.split(".");
					lastUpdate=lastUpdate[0];
				}
				
				return lastUpdate; //[0];
				}
			},
			{
				"class": "GenColumn0",
				"orderable": false,
				"searchable": false,
				"data": function (row, type, val, meta) {
					if (row.Generation0Start && row.Generation0End)
						return row.Generation0Start + " to <br>" + row.Generation0End;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn1",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation1Start && row.Generation1End)
						return row.Generation1Start + " to <br>" + row.Generation1End;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn2",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation2Start && row.Generation2End)
						return row.Generation2Start + " to <br>" + row.Generation2End;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn3",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation3Start && row.Generation3End)
						return row.Generation3Start + " to <br>" + row.Generation3End;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn4",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation4Start && row.Generation4End)
						return row.Generation4Start + " to <br>" + row.Generation4End;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "TodaySelection",
				"orderable": false,
				"data": null,
			    "render": function (row, type, full, meta) {
			    	
			    	var data = row;
			    	// Get the start and end time and date
			    	if (data.SelectedStartDateTime != null && data.SelectedEndDateTime != null) {
			    		var selectedStartDate = data.SelectedStartDateTime.substring(0, 10);
			    		var selectedStartTime = data.SelectedStartDateTime.substring(11, 19);
			    		var selectedEndDate = data.SelectedEndDateTime.substring(0, 10);
			    		var selectedEndTime = data.SelectedEndDateTime.substring(11, 19);
			    	}
			    	else {
			    		var selectedStartDate = "";
			    		var selectedStartTime = "00:00:00";
			    		var selectedEndDate = "";
			    		var selectedEndTime = "23:59:59";			    		
			    	}
			    	var disabled ="";
			    	// Check if all the triples where selected
			    	if (selectedStartDate == "from first" &&
			    			selectedEndDate == "until last") {
			    		var checked = "checked";
			    		disabled="disabled";
			    	}
			    	else {
			    		var checked = "";
			    	}
			    	if(row.Locked=="1")
			    	{			    	
			    		return selectedStartDate  + " " + selectedStartTime + " - " +
							selectedEndDate  + " " + selectedEndTime;
			    	}
			    	else
			    		return "<div class=datetime-group>" +
							"<div class='input-group date'>" +
								"<span class='input-group-addon' style='width: 40pt;'>from</span>" +
								"<input type='text' class='datepicker input-sm form-control start-date' "+disabled+" value='"+ selectedStartDate + "'/>" +
								"<span class='input-group-addon'>" +
									"<i class='glyphicon glyphicon-th'></i>" +
								"</span>" +
							"</div>" +
							"<div class='input-group time'>" +
								"<input type='text' class='timepicker input-sm form-control start-time' "+disabled+" value='"+ selectedStartTime + "'>" +
								"<span class='input-group-addon'>" +
									"<i class='glyphicon glyphicon-time'></i>" +
								"</span>" +
							"</div>" +
							
						"</div>" + 
						
						"<div class=datetime-group>" +
							"<div class='input-group date'>" +
								"<span class='input-group-addon' style='width: 40pt;'>to</span>" +
								"<input type='text' class='datepicker input-sm form-control end-date' "+disabled+" value='"+ selectedEndDate + "'/>" +
								"<span class='input-group-addon'>" +
									"<i class='glyphicon glyphicon-th'></i>" +
								"</span>" +
							"</div>" +
							"<div class='input-group time'>" +
								"<input type='text' class='timepicker input-sm form-control end-time' "+disabled+" value='"+ selectedEndTime + "'>" +
								"<span class='input-group-addon'>" +
									"<i class='glyphicon glyphicon-time'></i>" +
								"</span>" +
							"</div>" +
						"</div>"+
						
						"<div class='input-group'>" +
							"<span class='input-group-addon'>" +
							"<input type='checkbox' class='chkAllTriples' " + checked + ">" +
							"</span>" +
							"<label class='input-group-addon form-control'>All</label>" +
						"</div>";
			    },
			    "width": "350pt"
			},
			{ 
				"class":"status",
				"name":"Locked",
				"data":"Locked",
				"searchable": true,
				"render": function(row, type, val, meta) {
					if(val.Locked=="1")
					{
						var _class="locked"
						if(val.Clone=="1")
						{
							_class+=" cloned";
						}
						
						realTimeDataTable.row( '#'+val.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( _class );
						return "<i class='glyphicon glyphicon-lock'></i>";
					}
					else
					{
						if (val.SelectedStartDateTime != null && val.SelectedEndDateTime != null)
						{
							realTimeDataTable.row( '#'+val.DT_RowId )
						
						    .nodes()
						    .to$()      // Convert to a jQuery object
						    .addClass( "new" );
							return "<b>NEW</b>";
						}
					}
					return "";
			
			} },
		],
		"displayStart": currentItem,
		"dom": '<l<"#RTD.toolbar">>TCfrtip',
		"colVis" : {
			"exclude": [0, 7, 8, 9, 10, 11, 12]
		},
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[1, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "responsive": true,
        "tableTools": {
            "sRowSelect": "multi",
            "sRowSelector":"td:nth-child(n+2):nth-child(-n+6)",
            "aButtons": [ "select_all", "select_none",
                          {
							"sButtonClass":"DTTT_disabled",
							"sExtends":    "text",
							"sButtonText": "Add Selection",
							
							"fnSelect":function( nButton, oConfig )
							{
								$('#'+nButton.id).removeClass("DTTT_disabled");
								var indexes = this.fnGetSelectedIndexes();
								if(indexes.length==0)
									$('#'+nButton.id).addClass("DTTT_disabled");
							},
							"fnClick": function ( nButton, oConfig, oFlash ) {
								if($('#'+nButton.id).hasClass("DTTT_disabled"))
									return;
								
								var aData = this.fnGetSelectedData();
								addSelectedRTDataDlg(aData);
																
								this.fnSelectNone();
								//$('#'+nButton.id).addClass("DTTT_disabled");
							}
			
			          },
                          {
                        	    "sButtonClass":"DTTT_disabled",
				            	"sExtends":    "text",
				                "sButtonText": "Remove Selection",
				               
            					"fnSelect":function( nButton, oConfig )
            					{
            						$('#'+nButton.id).removeClass("DTTT_disabled");
            						var indexes = this.fnGetSelectedIndexes();
            						if(indexes.length==0)
                						$('#'+nButton.id).addClass("DTTT_disabled");
            					},
            					"fnClick": function ( nButton, oConfig, oFlash ) {
            						if($('#'+nButton.id).hasClass("DTTT_disabled"))
            							return;
            						var aData = this.fnGetSelectedData();
            						for(var d in aData)
            						{
            							
            								var ID = aData[d].DT_RowId.replace("row_","");
            					        	// Change the datepicker and timepicker configuration
            					        	$("#tableRealTimeData tr#row_" + ID + " input.datepicker.start-date").val("").attr("value","");    	
            					        	$("#tableRealTimeData tr#row_" + ID + " input.datepicker.end-date").val("").attr("value","");    	
            					        	$("#tableRealTimeData tr#row_" + ID + " input.timepicker.start-time").timepicker('setTime', '00:00:00'); 	        	
            					        	$("#tableRealTimeData tr#row_" + ID + " input.timepicker.end-time").timepicker('setTime', '23:59:59'); 	        	
            					        	$("#tableRealTimeData tr#row_" + ID + " input.timepicker.end-time")
            					        	$("#tableRealTimeData tr#row_" + ID + " input.chkAllTriples").attr('checked', false);

            					        	// Set the status to send to the server
            					        	sendStatusRealTime(ID);
            					         
            						}
            						//$('#'+nButton.id).addClass("DTTT_disabled");
            						this.fnSelectNone();
            					}
				           }],
            "fnRowSelected":function(node){
            	for(var n in node)
            	{
            		if($(node[n]).hasClass("locked"))
            			this.fnDeselect( $(node[n]) );
            	}
            }
        }
	} );
	$("div#RTD.toolbar").html('for <input type=radio name="SRTDataRdStatus" value="-1" checked/><b>All</b> <input type=radio name="SRTDataRdStatus" value="1"/><b>Committed</b> <input type=radio name="SRTDataRdStatus" value="0"/><b>New</b> items ' +
	'<input type="checkbox" name="ckbFilter" id=RTDataWithTriples value=""/> only with Triples');
		$('input#RTDataWithTriples').on('change',function(){
		realTimeDataTable.ajax.reload();
		});
	$('input[name="SRTDataRdStatus"]').on('change',function(){
		realTimeDataTable.ajax.reload();
		});
}


/**
 * 
 * Load the reconciliations information and fulfill the table 
 * 
 */
function loadReconciliations(currentItem) {
	
	// Set the current table e data type
	currentTable = "#tableReconciliations";
	currentDataType = "Reconciliations";
	currentItem = currentItem || 0;

	// Useful if the static data table is already set
	if ( $.fn.dataTable.isDataTable('#tableReconciliations') ) {
		reconciliationsTable.ajax.reload();
		return null;
	}
	
	// Set the reconciliations table
	reconciliationsTable = $('#tableReconciliations').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/Reconciliations",
	    	"processing": true,
	        "serverSide": true,
	    	// Information about generations to show are sent to the server, as an object
	    	"data" : function (d) {
	    		d.generations = $.extend({}, generationColumns[currentDataType]);
	    		d.currentSession = currentSessionID;
	    /*		if ($('input#RecDataWithTriples').is(':checked')) {
	    			d.columns[5].search.value="201";
	    		}*/
	    		if ($('input[name="RecDataRdStatus"]').is(':checked')) {
	    			var val = $('input[name="RecDataRdStatus"]:checked').val()
	    			if(val!=-1)
	    				d.columns[13].search.value=""+val;
	    		}
	    	}
	    },
		"columns": [
			{ "data": "Name" },
			{ "data": "Macroclasses" },
			{ "data": "Triples" },
			{ "data": "Description" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
	             	
	                 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Triples/Riconciliazioni/" + row.Name;
				return "Riconciliazioni/" + row.Name;
				}
			},
			{ 	
				"data":"LastUpdate",
				"orderable": true,
				"searchable": true,
				"type":"date",
				"render": function (row, type, val, meta) {
				var lastUpdate = val.LastFileDate;
				if(lastUpdate)
				{
					lastUpdate = lastUpdate.replace("T", " ");
					lastUpdate = lastUpdate.split(".");
					lastUpdate=lastUpdate[0];
				}
				
				return lastUpdate; //[0];
				}
			},
			{
				"class": "GenColumn0",
				"orderable": false,
				"searchable": false,
				"data": function (row, type, val, meta) {
					if (row.Generation0)
						return row.Generation0;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn1",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation1)
						return row.Generation1;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn2",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation2)
						return row.Generation2;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn3",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation3)
						return row.Generation3;
					else 
						return "n.a.";
			    }
			},
			{
				"class": "GenColumn4",
				"orderable": false,
				"searchable": false,
				"visible": false,
				"data": function (row, type, val, meta) {
					if (row.Generation4)
						return row.Generation4;
					else 
						return "n.a.";
			    }
			},
			{
				"orderable": false,
				// Create an HTML select with all the versions of the data
				//TODO Correggi +01
			    "data": function (row, type, val, meta) {
			    	if(row.Locked=="1")
			    	{
			    		var lastUpdate = row.SelectedVersion; //row.LastFileDate;
						if(lastUpdate)
						{
							lastUpdate = lastUpdate.replace("T", " ");
							lastUpdate = lastUpdate.split(".");
							lastUpdate=lastUpdate[0];
						}
						
						return lastUpdate; //[0];
						
			    	}
			    	else
			    	return  "<select size='1' class='form-control cboToday'>" +
			    	addToSelect(row.Versions, row.SelectedVersionIndex) + 
			    	"</select>";
			    }
			},
			{ 
				"class":"status",
				"name":"Locked",
				"data": "Locked",
				"searchable": true,
				"render":function(row, type, val, meta) {
				if(val.Locked=="1")
				{
					var _class="locked"
						if(val.Clone=="1")
						{
							_class+=" cloned";
						}
						
					reconciliationsTable.row( '#'+val.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( _class );
						return "<i class='glyphicon glyphicon-lock'></i>";return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					if(val.SelectedVersionIndex)
					{
						reconciliationsTable.row( '#'+val.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
						return "<b>NEW</b>";
					}
				}
				return "";
			} },
			
		],
		"displayStart": currentItem,
		"dom": '<l<"#Reconciliation.toolbar">>TCfrtip',
		"colVis" : {
			"exclude": [7, 8, 9, 10, 11, 12, 13]
		},
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "responsive": true,
        "tableTools": {
            "sRowSelect": "multi",
            "sRowSelector":"td:nth-child(n+1):nth-child(-n+5)",
            "aButtons": [ "select_all", "select_none",
                          {
				"sButtonClass":"DTTT_disabled",
				"sExtends":    "text",
				"sButtonText": "Add Selection",
				
				"fnSelect":function( nButton, oConfig )
				{
					$('#'+nButton.id).removeClass("DTTT_disabled");
					var indexes = this.fnGetSelectedIndexes();
					if(indexes.length==0)
						$('#'+nButton.id).addClass("DTTT_disabled");
				},
				"fnClick": function ( nButton, oConfig, oFlash ) {
					if($('#'+nButton.id).hasClass("DTTT_disabled"))
						return;
					var aData = this.fnGetSelectedData();
					for(var d in aData)
					{
						if(aData[d].Versions.length>0)
						{
							$("#"+aData[d].DT_RowId+" select.cboToday").val(1).change();
						}
					}
					this.fnSelectNone();
					//$('#'+nButton.id).addClass("DTTT_disabled");
				}

          },
          {
        	    "sButtonClass":"DTTT_disabled",
            	"sExtends":    "text",
                "sButtonText": "Remove Selection",
               
				"fnSelect":function( nButton, oConfig )
				{
					$('#'+nButton.id).removeClass("DTTT_disabled");
					var indexes = this.fnGetSelectedIndexes();
					if(indexes.length==0)
						$('#'+nButton.id).addClass("DTTT_disabled");
				},
				"fnClick": function ( nButton, oConfig, oFlash ) {
					if($('#'+nButton.id).hasClass("DTTT_disabled"))
						return;
					var aData = this.fnGetSelectedData();
					for(var d in aData)
					{
						$("#"+aData[d].DT_RowId+" select.cboToday").val(0).change();
					}
					//$('#'+nButton.id).addClass("DTTT_disabled");
					this.fnSelectNone();
				}
           }
                          ],
            "fnRowSelected":function(node){
            	for(var n in node)
            	{
            		if($(node[n]).hasClass("locked"))
            			this.fnDeselect( $(node[n]) );
            	}
            }
        }
	} );
	$("div#Reconciliation.toolbar").html('for <input type=radio name="RecDataRdStatus" value="-1" checked/><b>All</b> <input type=radio name="RecDataRdStatus" value="1"/><b>Committed</b> <input type=radio name="RecDataRdStatus" value="0"/><b>New</b> items ');
	/*'<input type="checkbox" name="ckbFilter" id=RecDataWithTriples value=""/> only with Triples');
	$('input#RecDataWithTriples').on('change',function(){
		reconciliationsTable.ajax.reload();
		});*/
	$('input[name="RecDataRdStatus"]').on('change',function(){
		reconciliationsTable.ajax.reload();
		});

}

/**
 * 
 * Load the enrichments information and fulfill the table 
 * 
 */
function loadEnrichments(currentItem) {
	
	// Set the current table e data type
	currentTable = "#tableEnrichments";
	currentDataType = "Enrichments";
	currentItem = currentItem || 0;

	// Useful if the static data table is already set
	if ( $.fn.dataTable.isDataTable('#tableEnrichments') ) {
		enrichmentsTable.ajax.reload();
		return null;
	}
	
	// Set the reconciliations table
	enrichmentsTable = $('#tableEnrichments').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/Enrichments",
	    	"processing": true,
	        "serverSide": true,
	    	// Information about generations to show are sent to the server, as an object
	    	"data" : function (d) {
	    	
	    		d.currentSession = currentSessionID;
	    	
	    		if ($('input[name="EnrichDataRdStatus"]').is(':checked')) {
	    			var val = $('input[name="EnrichDataRdStatus"]:checked').val()
	    			if(val!=-1)
	    				d.columns[3].search.value=""+val;
	    		}
	    	}
	    },
		"columns": [      
			{ "data": "Name" },
			{ "data": "Description" },
			{ "data": "Query" },
			{ 
				"class":"status",
				"name":"Locked",
				"data": "Locked",
				"searchable": true,
				"render":function(row, type, val, meta) {
				if(!val.Locked && !val.Cloned)
					return "";
				if(val.Locked=="1")
				{
					var _class="locked"
						if(val.Clone=="1")
						{
							_class+=" cloned";
						}
						
					enrichmentsTable.row( '#'+val.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( _class );
						return "<i class='glyphicon glyphicon-lock'></i>";return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					
						enrichmentsTable.row( '#'+val.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
						return "<b>NEW</b>";
					
				}
				return "";
			} 
			
			},
			{
				"render":function(row, type, val, meta)
				{
					if(!val.Locked && !val.Cloned)
						return "<button class='button light-gray btn-xs btn btn-primary' onclick='addEnrichment(\""+val.DT_RowId+"\",true)'>Add</button>";
					else
						return "<button class='button light-gray btn-xs btn btn-primary' onclick='removeEnrichment(\""+val.DT_RowId+"\",true)'>Remove</button>";
				}
			}
			
		],
	//	"displayStart": currentItem,
		"dom": '<l<"#Enrichments.toolbar">>TCfrtip',
	/*	"colVis" : {
			"exclude": []
		},
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },*/
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "responsive": true,
        "tableTools": {
            "sRowSelect": "multi",
        //    "sRowSelector":"td:nth-child(n+1):nth-child(-n+5)",
            "aButtons": [ "select_all", "select_none",
                          {
				"sButtonClass":"DTTT_disabled",
				"sExtends":    "text",
				"sButtonText": "Add Selection",
				
				"fnSelect":function( nButton, oConfig )
				{
					$('#'+nButton.id).removeClass("DTTT_disabled");
					var indexes = this.fnGetSelectedIndexes();
					if(indexes.length==0)
						$('#'+nButton.id).addClass("DTTT_disabled");
				},
				"fnClick": function ( nButton, oConfig, oFlash ) {
					if($('#'+nButton.id).hasClass("DTTT_disabled"))
						return;
					var aData = this.fnGetSelectedData();
					for(var d in aData)
					{
						
							addEnrichment(aData[d].DT_RowId,false);
						
					}
					//this.fnSelectNone();
					enrichmentsTable.ajax.reload();
					//$('#'+nButton.id).addClass("DTTT_disabled");
				}

          },
          {
        	    "sButtonClass":"DTTT_disabled",
            	"sExtends":    "text",
                "sButtonText": "Remove Selection",
               
				"fnSelect":function( nButton, oConfig )
				{
					$('#'+nButton.id).removeClass("DTTT_disabled");
					var indexes = this.fnGetSelectedIndexes();
					if(indexes.length==0)
						$('#'+nButton.id).addClass("DTTT_disabled");
				},
				"fnClick": function ( nButton, oConfig, oFlash ) {
					if($('#'+nButton.id).hasClass("DTTT_disabled"))
						return;
					var aData = this.fnGetSelectedData();
					for(var d in aData)
					{
						removeEnrichment(aData[d].DT_RowId,false);
					}
					//$('#'+nButton.id).addClass("DTTT_disabled");
					//this.fnSelectNone();
					enrichmentsTable.ajax.reload();
				}
           }
                          ],
            "fnRowSelected":function(node){
            	for(var n in node)
            	{
            		if($(node[n]).hasClass("locked"))
            			this.fnDeselect( $(node[n]) );
            	}
            }
        }
	} );
	$("div#Enrichments.toolbar").html('for <input type=radio name="EnrichDataRdStatus" value="-1" checked/><b>All</b> <input type=radio name="EnrichDataRdStatus" value="0"/><b>New</b> items ');

	$('input[name="EnrichDataRdStatus"]').on('change',function(){
		enrichmentsTable.ajax.reload();
		});

}

function addEnrichment(id,reload)
{
	// Gets the row index
    var row = $(currentTable).DataTable().row( '#'+id ).data();
   
    var itemID = row.Name;
  
	
	var status = {
            "dataType": "Enrichments",
            "action":"select",
	    	"currentSession": currentSessionID,
			"select": {
				"id": itemID,
				"version": 0,
			}
	};
	$.ajax({
		type: "GET",
		url: "IndexGenerator/Status",
		data: status,
		async: false,
		beforeSend:function()
        {
        	//showWait();
        },
		success:function(data)
		{
			//bootbox.hideAll();
			if(!data)
			{
				$(currentTable).DataTable().row( '#'+id )
			    .nodes()
			    .to$()      // Convert to a jQuery object
			    .addClass( "new" );
				$(currentTable + " tr#" + row.DT_RowId).find('.status').html("<b>NEW</b>");
			}
		},
		complete:function()
		{
			//hideWait();
			if(reload)
				$(currentTable).DataTable().ajax.reload();
		}
	});
	
	
}

function removeEnrichment(id,reload)
{
	// Gets the row index
    var row = $(currentTable).DataTable().row( '#'+id ).data();
   
    var itemID = row.Name;
	
	var status = {
            "dataType": "Enrichments",
            "action":"select",
	    	"currentSession": currentSessionID,
			"select": {
				"id": itemID,
				"version":""
			}
	};
	
	
	$.ajax({
		type: "GET",
		url: "IndexGenerator/Status",
		data: status,
		async:false,
		beforeSend:function()
        {
        	//showWait();
        },
		success:function(data)
		{
			//bootbox.hideAll();
			if(!data)
			{
				$(currentTable).DataTable().row( '#'+id )
			    .nodes()
			    .to$()      // Convert to a jQuery object
			    .removeClass( "new" );
				$(currentTable + " tr#" + row.DT_RowId).find('.status').html("");
			}
		},
		complete:function()
		{
			//hideWait();
			if(reload)
				$(currentTable).DataTable().ajax.reload();
		}
	});
}



/**
 * 
 * Set the active step tab in the page header
 * 
 * @param step the index of the current step
 * 
 */
function setHeader(step) {
	/*for ( var i = 1; i < 7; i++) {
		$("#header_step" + i).removeClass("active");
	}*/
	$('.header').removeClass("active");
	
	$("#header_step" + step).addClass("active");
}


/**
 * 
 * Shows a popup with information about a static data
 * 
 * @param ID the ID of the static data
 */
function showDataInfo(ID) {
	$('#modMoreInfo').modal();
	$('#modMoreInfoTitle').html(ID);
	$('#modMoreInfoBody').html("Loading.....");
	// Get the information about the static data
	$.ajax(
		{
			type: "GET",
			url: "IndexGenerator/DataInfo/"+ID,
			dataType:"json",
			//data: {"id": ID},
			// Fill the page with the loaded generation data, if success
			success: function(data, status, jqXHR) {
				// Parse the received data
				receivedData = data; //JSON.parse(data);
				
				// Set the html of the page
				var htmlBody = "<table class='table table-striped table-condensed'>";
				for(var i in receivedData) {
						htmlBody += "<tr><td><b>" +
							i + "</b></td><td>" +receivedData[i] +
							"</td></tr>";
				}
				htmlBody += "</table>"
				
				// Show the modal window
				/*$('#modMoreInfo').modal();
				$('#modMoreInfoTitle').html(ID);*/
				$('#modMoreInfoBody').html(htmlBody);
				
			} ,
			error: function(data, status, jqXHR) {
				// Show the modal window
				//$('#modAlert').modal();
				
				// Set the window elements
				//$('#modAlertTitle').html("Error");
				$('#modMoreInfoBody').html("Server not responding!");
			}
		}
	);

}


/**
 * 
 * Set the listeners of the various page elements
 * 
 */
function setListeners() {
	
	
    // Add event listener for btnMoreInfo elements
    $(currentTable + ' tbody').off('click', 'td.btnMoreInfo').on('click', 'td.btnMoreInfo', function () {
        var row = $(currentTable).DataTable().row($(this).closest('tr'));
        showDataInfo(row.data().Name);
    } );
    
    
    // Add event listener for cboToday elements
   $(currentTable + ' tbody').off('change', 'select.cboToday').on('change', 'select.cboToday', function () {
  
    	// Gets the row index
	    var row = $(currentTable).DataTable().row($(this).closest('tr')).data();
	   
        var ID = row.Name;
        var currentRow = $(currentTable + " tr#" + row.DT_RowId);
		var itemVersion = $(this).find(":selected").text();
     	
        var status = {
                "dataType": currentDataType,
                "action":"select",
    	    	"currentSession": currentSessionID,
    			"select": {
    				"id": ID,
    				"version": itemVersion
    			}
        };
        
        // Send the status to the server
        if(itemVersion!="")
        {
        	$(currentTable).DataTable().row( '#'+row.DT_RowId )
		    .nodes()
		    .to$()      // Convert to a jQuery object
		    .addClass( "new" );
        	currentRow.find('.status').html("<b>NEW</b>");
        }
        else
        {
        	$(currentTable).DataTable().row( '#'+row.DT_RowId )
		    .nodes()
		    .to$()      // Convert to a jQuery object
		    .removeClass( "new" );
        	currentRow.find('.status').html("");
        }
        sendStatus(status);
        

    });
    	
    // Add event listener for btnCopyToday elements
    $(currentTable + ' thead').off('click', 'button.btnCopyToday').on('click', 'button.btnCopyToday', function () {
    	
    	// Gets the generation column index
    	var column = $(this).closest('th').index();

    	// Set the status to send
        var status = {
                    "dataType": currentDataType,
        	    	"currentSession":  currentSessionID,
                    "action":"copy",
        			"column": generationColumns[currentDataType][column]
        };
        
        // Send the status to the server
        sendStatus(status);
    });

    // Add event listener for btnCloneToday elements
    $(currentTable + ' thead').off('click', 'button.btnCloneToday').on('click', 'button.btnCloneToday', function () {
    	
    	// Gets the generation column index
    	var column = $(this).closest('th').index();

    	// Set the status to send
        var status = {
                    "dataType": currentDataType,
                    "action": "clone",
        	    	"currentSession":  currentSessionID,
        			"column": generationColumns[currentDataType][column]
        };
        
        // Send the status to the server
        sendStatus(status);
        
    });
    
    // Add event listener for btnRemove elements
    $(currentTable + ' thead').off('click', 'button.btnRemove').on('click', 'button.btnRemove', function () {
    	
    	// Gets the generation column index
    	var column = $(this).closest('th').index();

    	removeGeneration(column);
    	
    });
    
    // Add event listener for cboSelectGenerationDate elements
    $(currentTable + ' thead').off('change', 'select.cboSelectGenerationDate').on('change', 'select.cboSelectGenerationDate', function () {
    	
    	var column = $(this).closest('th').index();
        var generation = $(this).find(":selected").attr("value");
        
        // Show the required generation
        showGeneration(column, generation);
           	
    });
    
   
    // Add event listener for chkAllTriples elements
    $(currentTable + ' tbody').off('click', 'input.chkAllTriples').on('click', 'input.chkAllTriples', function () {
    	
    	// Gets the row index
        var ID = $(currentTable).DataTable().row($(this).closest('tr')).data().Name;
        
        var checked = $(this).is(":checked");
        
        if (checked) {
        	
        	
        	// Change the datepicker and timepicker configuration
        	$(currentTable + " tr#row_" + ID + " input.datepicker.start-date").val("from first").attr("disabled","disabled");  	
        	$(currentTable + " tr#row_" + ID + " input.datepicker.end-date").val("until last").attr("disabled","disabled");
        	$(currentTable + " tr#row_" + ID + " input.datepicker.start-date").attr("value","from first");    	
        	$(currentTable + " tr#row_" + ID + " input.datepicker.end-date").attr("value","until last");    	
        	$(currentTable + " tr#row_" + ID + " input.timepicker.start-time").timepicker('setTime', '00:00:00').attr("disabled","disabled");	        	
        	$(currentTable + " tr#row_" + ID + " input.timepicker.end-time").timepicker('setTime', '23:59:59').attr("disabled","disabled"); 	        	
        	// Set the status to send to the server
  /*          var status = {
                    "dataType": currentDataType,
                    "action":"select",
        	    	"currentSession":  currentSessionID,
        			"select": {
        				"id": ID,
        				"from": "from first",
        				"to": "until last"
        			}
            };*/
            
        }
        else {
        	
        	// Change the datepicker and timepicker configuration
        	$(currentTable + " tr#row_" + ID + " input.datepicker.start-date").attr("value","").removeAttr("disabled");    	
        	$(currentTable + " tr#row_" + ID + " input.datepicker.end-date").attr("value","").removeAttr("disabled");  
        	$(currentTable + " tr#row_" + ID + " input.datepicker.start-date").val("");    	
        	$(currentTable + " tr#row_" + ID + " input.datepicker.end-date").val("");      	
        	$(currentTable + " tr#row_" + ID + " input.timepicker.start-time").timepicker('setTime', '00:00:00').removeAttr("disabled");	        	
        	$(currentTable + " tr#row_" + ID + " input.timepicker.end-time").timepicker('setTime', '23:59:59').removeAttr("disabled"); 	        	

        	// Set the status to send to the server
    /*        var status = {
                    "dataType": currentDataType,
                    "action":"select",
        	    	"currentSession":  currentSessionID,
        			"select": {
        				"id": ID,
        				"from": "",
        				"to": ""
        			}
            };*/
            
        }
        
        // Send the status to the server
        sendStatusRealTime(ID);
           	
    });
		
}


/**
 * 
 * Send the status to the server, about a real time data
 * 
 * @param ID the ID of the real time data
 * 
 */
function sendStatusRealTime(ID){
	
	// Gets the generation column index
    var currentRow = $(currentTable + " tr#row_" + ID);
	var startDate = currentRow.find('.start-date').val();
	var startTime = currentRow.find('.start-time').val();
	var endDate = currentRow.find('.end-date').val();
	var endTime = currentRow.find('.end-time').val();
	var startDateTime="";
	var endDateTime="";
	// Check for non setted dates
	if (currentRow.find('.start-date').attr("value") == "from first")
		startDateTime = "from first";
	else if(startDate!="")
		startDateTime = startDate + " " + startTime;
	if (currentRow.find('.end-date').attr("value") == "until last")
		endDateTime = "until last";
	else if(endDate!="")
		endDateTime = endDate + " " + endTime;
	
	// Set the status to send to the server
    var status = {
            "dataType": currentDataType,
            "action":"select",
	    	"currentSession":  currentSessionID,
			"select": {
				"id": ID,
				"from": startDateTime,
				"to": endDateTime
			}
    };
    
    
    if(startDateTime!="" || endDateTime!="")
    {
    	$(currentTable).DataTable().row( '#row_' + ID )
	    .nodes()
	    .to$()      // Convert to a jQuery object
	    .addClass( "new" );
    	currentRow.find('.status').html("<b>NEW</b>");
    }
    else
    {
    	$(currentTable).DataTable().row( '#row_' + ID )
	    .nodes()
	    .to$()      // Convert to a jQuery object
	    .removeClass( "new" );
    	currentRow.find('.status').html("");
    }
 
    // Send the status to the server
    sendStatus(status);
	
}


/**
 * 
 * Load the summary information and fulfill the tables
 * 
 */
function loadSummary() {
	
	// Useful if the static data table is already set
//	if ( $.fn.dataTable.isDataTable('#tableSelectedOntologies') ) {
//		return null;
//	}
	
	// Set the ontologies table
	selectedOntologiesTable = $('#tableSelectedOntologies').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/Ontologies",
	    	"serverSide": true,
		    "processing": true,
	    	// Requires only user selected data
	    	"data" : function (d) {
	    		d.selected = true;
	    		d.currentSession = currentSessionID;
	    	}
	    },
		"columns": [
			{ "data": "Name" },
			{ "data": "URIPrefix" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Ontologies/" + row.Name;
				return "Ontologies/"+row.Name;
				}
			},
			{ "data": function (row, type, val, meta) {
				var lastUpdate = row.LastFileDate;
				if(lastUpdate)
				{
					lastUpdate = lastUpdate.replace("T", " ");
					lastUpdate = lastUpdate.split(".");
					lastUpdate=lastUpdate[0];
				}
				
				return lastUpdate; //[0];
				}
			},
			{
				"data": function (row, type, val, meta) {
					var lastUpdate = row.SelectedVersion;
					if(lastUpdate)
					{
						lastUpdate = lastUpdate.replace("T", " ");
						lastUpdate = lastUpdate.split(".");
						lastUpdate=lastUpdate[0];
					}
					
					return lastUpdate; //[0];
					},
				"orderable": false
			},
			{ 
				"name":"Status",
				"data": function(row, type, val, meta) {
				if(row.Locked=="1")
				{
					var _class="locked"
						if(row.Clone=="1")
						{
							_class+=" cloned";
						}
					selectedOntologiesTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					selectedOntologiesTable.row( '#'+row.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
					return "<b>NEW</b>";
				}
				
				} 
			},
		],
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[20], [20]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
	    "destroy": true,
	    
	} );

	// Set the static data table
	selectedStaticDataTable = $('#tableSelectedStaticData').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/StaticData",
	    	"serverSide": true,
		    "processing": true,
	    	// Requires only user selected data
	    	"data" : function (d) {
	    		d.selected = true;
	    		d.currentSession = currentSessionID;
	    	}
	    },
		"columns": [
			{ "data": "Name" },
			{ "data": "Resource" },
			{ "data": "Description" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Triples/" + row.Category + "/" + row.Name;
				return row.Category + "/" + row.Name;
				}
			},
			{ "data": function (row, type, val, meta) {
				//var lastUpdate = row.LastFileDate;
				var lastUpdate = row.LastUpdate;
				if(lastUpdate)
				{
					lastUpdate = lastUpdate.replace("T", " ");
					lastUpdate = lastUpdate.split(".");
					lastUpdate=lastUpdate[0];
				}
				
				return lastUpdate; //[0];
				}
			},
			{
				"orderable": false,
				"data": function (row, type, val, meta) {
					var lastUpdate = row.SelectedVersion;
					if(lastUpdate)
					{
						lastUpdate = lastUpdate.replace("T", " ");
						lastUpdate = lastUpdate.split(".");
						lastUpdate=lastUpdate[0];
					}
					
					return lastUpdate; //[0];
					},
			},
			{ 
				"name":"Status",
				"data": function(row, type, val, meta) {
				if(row.Locked=="1")
				{
					var _class="locked"
						if(row.Clone=="1")
						{
							_class+=" cloned";
						}
					selectedStaticDataTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					selectedStaticDataTable.row( '#'+row.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
					return "<b>NEW</b>";
				}
				
				} 
			},
		],
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "destroy": true,
        
	    
	} );

	
	// Set the static data table
	selectedRealTimeDataTable = $('#tableSelectedRealTimeData').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/RealTimeData",
	    	"serverSide": true,
		    "processing": true,
	    	// Requires only user selected data
	    	"data" : function (d) {
	    		d.selected = true;
	    		d.currentSession = currentSessionID;
	    	}
	    },
		"columns": [
			{ "data": "Name" },
			{ "data": "Resource" },
			{ "data": "Description" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Triples/" + row.Category + "/" + row.Name;
				return row.Category + "/" + row.Name;
				}
			},
			{ "data": function (row, type, val, meta) {
				//var lastUpdate = row.LastFileDate;
				var lastUpdate = row.LastUpdate;
				if(lastUpdate)
				{
					lastUpdate = lastUpdate.replace("T", " ");
					lastUpdate = lastUpdate.split(".");
					lastUpdate=lastUpdate[0];
				}
				
				return lastUpdate; //[0];
				
				}
			},
			{
				"orderable": false,
				"data": null,
			    "render": function (data, type, full, meta) {
			    	
			    	// Get the start and end time and date
			    	if (data.SelectedStartDateTime != null && data.SelectedEndDateTime != null) {
			    		var selectedStartDate = data.SelectedStartDateTime.substring(0, 10);
			    		var selectedStartTime = data.SelectedStartDateTime.substring(11, 19);
			    		var selectedEndDate = data.SelectedEndDateTime.substring(0, 10);
			    		var selectedEndTime = data.SelectedEndDateTime.substring(11, 19);
			    	}
			    	
					return selectedStartDate  + " " + selectedStartTime + " - " +
						selectedEndDate  + " " + selectedEndTime;
			    },
			    "width": "280pt"

			},
			{ 
				"name":"Status",
				"data": function(row, type, val, meta) {
				if(row.Locked=="1")
				{
					var _class="locked"
						if(row.Clone=="1")
						{
							_class+=" cloned";
						}
					selectedRealTimeDataTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else
				{
					selectedRealTimeDataTable.row( '#'+row.DT_RowId )
					    .nodes()
					    .to$()      // Convert to a jQuery object
					    .addClass( "new" );
					return "<b>NEW</b>";
				}
				
				} 
			},
		],
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "destroy": true,
	    
	} );

	// Set the reconciliations table
	selectedReconciliationsTable = $('#tableSelectedReconciliations').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/Reconciliations",
	    	"serverSide": true,
		    "processing": true,
	    	// Requires only user selected data
	    	"data" : function (d) {
	    		d.selected = true;
	    		d.currentSession = currentSessionID;
	    	}
	    },
		"columns": [
			{ "data": "Name" },
			{ "data": "Macroclasses" },
			{ "data": "Triples" },
			{ "data": "Description" },
			 { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
				 return renderSecurityLabel(val.SecurityLevel);
	            }},
			{ "data": function (row, type, val, meta) {
				//return "/media/Triple/Riconciliazioni/" + row.Name;
				return  row.Name;
				}
			},
			{ "data": function (row, type, val, meta) {
				var lastUpdate = row.LastFileDate;
				if(lastUpdate)
				{
					lastUpdate = lastUpdate.replace("T", " ");
					lastUpdate = lastUpdate.split(".");
					lastUpdate=lastUpdate[0];
				}
				
				return lastUpdate; //[0];
				}
			},
			{
				"orderable": false,
				"data": function (row, type, val, meta) {
					var lastUpdate = row.SelectedVersion;
					if(lastUpdate)
					{
						lastUpdate = lastUpdate.replace("T", " ");
						lastUpdate = lastUpdate.split(".");
						lastUpdate=lastUpdate[0];
					}
					
					return lastUpdate; //[0];
					},
			},
			{ 
				"name":"Status",
				"data": function(row, type, val, meta) {
				if(row.Locked=="1")
				{
					var _class="locked"
						if(row.Clone=="1")
						{
							_class+=" cloned";
						}
					selectedReconciliationsTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else{
					selectedReconciliationsTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( "new" );
					return "<b>NEW</b>";
				}
				
				} 
			},
		],
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "destroy": true,
	    
	} );
	
	// Set the Enrichments table
	selectedEnrichmentsTable = $('#tableSelectedEnrichments').DataTable( {
	    "ajax": {
	    	"url": "IndexGenerator/Enrichments",
	    	"serverSide": true,
		    "processing": true,
	    	// Requires only user selected data
	    	"data" : function (d) {
	    		d.selected = true;
	    		d.currentSession = currentSessionID;
	    	}
	    },
		"columns": [
			{ "data": "Name" },
			{ "data": "Description" },
			{ "data": "Query" },		
			{ 
				"name":"Status",
				"data": function(row, type, val, meta) {
				if(row.Locked=="1")
				{
					var _class="locked"
						if(row.Clone=="1")
						{
							_class+=" cloned";
						}
					selectedEnrichmentsTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( _class );
					return "<i class='glyphicon glyphicon-lock'></i>";
				}
				else{
					selectedEnrichmentsTable.row( '#'+row.DT_RowId )
				    .nodes()
				    .to$()      
				    .addClass( "new" );
					return "<b>NEW</b>";
				}
				
				} 
			},
		],
	    "language": {
	        "decimal": ",",
	        "thousands": "."
	    },
	    "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
		"order": [[0, 'asc']],
		"pagingType": "full_numbers",
		"processing": true,
        "serverSide": true,
        "destroy": true,
	    
	} );
}


/**
 * Set the date and time pickers, and their relative listeners
 * 
 * 
 */
function setDateTimePickers() {
	
	// Set the real time data datepicker and his listener
    $(".input-group.date").datepicker({
    	format: "yyyy-mm-dd",
    	weekStart: 1,
    	clearBtn: true,
    	todayBtn: "linked",
    	autoclose: true,
    	todayHighlight: true
    })
    .on("changeDate", function(){
    	
    	// Gets the generation column index
        var ID = $(currentTable).DataTable().row($(this).closest('tr')).data().Name;
        
        // Send the status to the server
        sendStatusRealTime(ID);
        
    });
    
	// Set the real time data timepicker and his listener
    $(".timepicker").timepicker({
    	defaultTime: '00:00:00',
    	minuteStep: 1,
    	showSeconds: true,
    	secondStep: 1,
    	showMeridian: false
    })
    .on("hide.timepicker", function(e) {
    	
    	// Gets the generation column index
        var ID = $(currentTable).DataTable().row($(this).closest('tr')).data().Name;
        
        // Send the status to the server
        sendStatusRealTime(ID);
    });
}


// TODO Rivedere, funzione messa per fare funzionare il removeGeneration
function loadData(currentItem) {
	
	if (currentDataType == "Ontologies")
		loadOntologies(currentItem);
	if (currentDataType == "StaticData")
		loadStaticData(currentItem);
	if (currentDataType == "RealTimeData")
		loadRealTimeData(currentItem);
	if (currentDataType == "Reconciliations")
		loadReconciliations(currentItem);
	
}


/**
 * Provides to create the index generation script
 * 
 */
function getScript() {
	
	// Get the repository ID
	var repositoryID = $('#modInsertionInput').val();
	var data = {"status":"sessionEnd","repositoryID": repositoryID};
	// HTTP PUT request to save the repository ID and set session end
	$.ajax(
		{
				type: "PUT",
				url: "IndexGenerator/session/" + currentSessionID,
				//data : '{"status":"sessionEnd","repositoryID":"' + repositoryID + '"}',
				data:data,
				async: false,
				error: function(data, status, jqXHR) {
					// Show the modal window
					$('#modAlert').modal();
				
					// Set the window elements
					$('#modAlertTitle').html("Error");
					$('#modAlertBody').html("The server is not responding");
					
				}
		}
	);
				
	
	// HTTP GET request to get the generation script
	$.ajax(
		{
			type: "GET",
			url: "IndexGenerator/Script/" + currentSessionID,
			async: false,
			dataType:"json",
			// Fill the page with the loaded generation data, if success
			success: function(data, status, jqXHR) {

				// Show the modal window
				$('#modAlert').modal();
				// Set the window elements
				$('#modAlertTitle').html("Success");
				$('#modAlertBody').html("The script was created! You can find it in file '"+data.path+"'");
				
				
				// Show the "prepare generation" button
				$("#btnPrepareGeneration").show();
			} ,
			error: function(data, status, jqXHR) {
				// Show the modal window
				$('#modAlert').modal();
				
				// Set the window elements
				$('#modAlertTitle').html("Error");
				$('#modAlertBody').html("The script was not created!");
			}
		}
	);
	
}


/**
 * 
 * Show the modal to confirm the creation of a script
 * 
 */
function showConfirmScript() {
	
	// Show the modal window
	$('#modInsertion').modal();
	
	// Set the window elements
	$('#modInsertionTitle').html("Insert repository ID");
	$('#modInsertionSpan').html("Please specify the ID of the repository you want to create");
	var val = repositoryID==undefined?"":repositoryID;
	$('#modInsertionInput').val(val);
	$('#modInsertionInput').attr("placeholder", "Repository ID");
	$('#modInsertionConfirm').attr("onclick", "getScript()");

}

function renderSecurityLabel(SecurityLevel)
{
	var label ="N.A";
 	if(SecurityLevel!=undefined)
     {
     	
     	switch(SecurityLevel)
     	{
     		case "1":
     			label = "OPEN";
     		break;
     		case "2":
     			label = "PRIVATE";
     		break;
     		case "3":
     			label = "SENSIBLE";
     		break;
     		case "4":
     			label = "CRITICAL";
     		break;
     	}
     		
     }
     return "<label class=security_status>"+label+"</label>";
}


/**
 * 
 * Show the modal to confirm the creation of a new session
 * 
 */
function showConfirmSession() {
	
	// Show the modal window
	$('#modConfirm').modal();
	
	// Set the window elements
	$('#modConfirmTitle').html("Prepare new generation");
	$('#modConfirmBody').html("Do you really want to prepare a new generation?");
	$('#modConfirmOK').attr("onclick", "newSession()");

}

/**
 * 
 * Add various elements to a select html tags.
 * The elements must be passed as an array of object with the ID and Data key, where:
 * <ul>
 * <li>ID represents the ID of the elements</li>
 * <li>Data is the value to show in the select html tag</li>
 * </ul>
 * 
 * @param data an array with the data to show for each element
 * @param selectedId the id of the element to select
 * @returns {String} an html code with all the elements to add
 * 
 */
function addToSelect(data, selectedId) {
	
	// Insert a null choice
	var returnVal = "<option value='0'></option>";
	
	// For each element in the array of data
	for (var key in data) {
			
		// Open the option element, setting the key
		returnVal += "<option value='" + data[key].ID + "'";
			
		// If the element have to be selected, select it
		if (data[key].ID == selectedId) {
			returnVal += " selected='selected'";
		}
			
		// Close the option element, setting the valur
		returnVal += ">" + data[key].Data + "</option>";
	}
	
	return returnVal;	
}


/**
 * 
 * Send information regarding the status to the server
 * 
 * @param status information regarding the status to the server
 * 
 */
function sendStatus(requestStatus) {
	$.ajax(
		{
			type: "GET",
			url: "IndexGenerator/Status",
			data: requestStatus,
			
			// Fill the page with the loaded data, if success
			success: function(data, status, jqXHR) {
				
				if (requestStatus.action == "clone" || requestStatus.action == "copy") {
				
					// Reload the table, only on clone or copy actions
					$(currentTable).DataTable().ajax.reload();
					// Reload the table, at the current page
//					loadData(currentPage * pageLength);
				}
				IndexManager.modified=true;
			} ,
			error: function(data, status, jqXHR) {

				// Show the modal window
				$('#modAlert').modal();
				
				// Set the window elements
				$('#modAlertTitle').html("Error");
				$('#modAlertBody').html("No response from the server");
			}
		}
	);	
}

function addSelectedRTDataDlg(data)
{
	var html = "<div id=RTDataDlg><div class=datetime-group>" +
	"<div class='input-group date'>" +
	"<span class='input-group-addon' style='width: 40pt;'>from</span>" +
	"<input type='text' class='datepicker input-sm form-control start-date'/>" +
	"<span class='input-group-addon'>" +
		"<i class='glyphicon glyphicon-th'></i>" +
	"</span>" +
"</div>" +
"<div class='input-group time'>" +
	"<input type='text' class='timepicker input-sm form-control start-time'>" +
	"<span class='input-group-addon'>" +
		"<i class='glyphicon glyphicon-time'></i>" +
	"</span>" +
"</div>" +

"</div>" + 

"<div class=datetime-group>" +
"<div class='input-group date'>" +
	"<span class='input-group-addon' style='width: 40pt;'>to</span>" +
	"<input type='text' class='datepicker input-sm form-control end-date' />" +
	"<span class='input-group-addon'>" +
		"<i class='glyphicon glyphicon-th'></i>" +
	"</span>" +
"</div>" +
"<div class='input-group time'>" +
	"<input type='text' class='timepicker input-sm form-control end-time' >" +
	"<span class='input-group-addon'>" +
		"<i class='glyphicon glyphicon-time'></i>" +
	"</span>" +
"</div>" +
"</div>"+

"<div class='input-group'>" +
"<span class='input-group-addon'>" +
"<input type='checkbox' class='chkAllTriples'>" +
"</span>" +
"<label class='input-group-addon form-control'>All</label>" +
"</div></div>";
	
	// Show the modal window
	$('#modConfirm').modal();
	
	// Set the window elements
	$('#modConfirmTitle').html("Time Interval for Selected Data");
	$('#modConfirmBody').html(html);
	$('#modConfirmOK').on("click", function(){
		var aData = data;
		
		var startDate = $("#RTDataDlg input.datepicker.start-date").val();    	
    	var endDate = $("#RTDataDlg input.datepicker.end-date").val();    	
    	var startTime = $("#RTDataDlg input.timepicker.start-time").val(); 	        	
    	var endTime = $("#RTDataDlg input.timepicker.end-time").val(); 	
    	var checked = $("#RTDataDlg input.chkAllTriples").is(":checked");
    	if (checked) {
        	
    		startDate = "from first";
    		endDate="until last"
    		startTime = "00:00:00";
    		endTime="23:59:59";
        }
       
    	
    	for(var d in aData)
		{
			
				var ID = aData[d].DT_RowId.replace("row_","");
	        	// Change the datepicker and timepicker configuration
	        	        	

	        	 var currentRow = $(currentTable + " tr#row_" + ID);
	        	 if(!checked)
	        	 { 
	        		 currentRow.find('.start-date').val(startDate);
	        		 currentRow.find('.start-time').val(startTime);
		        	 currentRow.find('.end-date').val(endDate);
		        	 currentRow.find('.end-time').val(endTime);
		        	 sendStatusRealTime(ID);
	        	 }
	        	 else
	        		 currentRow.find('.chkAllTriples').click();
	        	
		}
		
	});
	$("#RTDataDlg .input-group.date").datepicker({
    	format: "yyyy-mm-dd",
    	weekStart: 1,
    	clearBtn: true,
    	todayBtn: "linked",
    	autoclose: true,
    	todayHighlight: true
    });
	$("#RTDataDlg .timepicker").timepicker({
    	defaultTime: '00:00:00',
    	minuteStep: 1,
    	showSeconds: true,
    	secondStep: 1,
    	showMeridian: false
    })
}

function addSelectionToolbar(selector,id)
{
	$(selector).html("<select id'="+id+"'><option value=add>Add Selection</option><option value=remove>Remove Selection</option></select>");
}

function showWait()
{
	var over = '<div id="smTableDataViewOverlay">' +
    '<div id="loading"><i class="sm-icon smTableDataViewWaitIcon"></i> Processing....' +
    '</div>';
	$(over).appendTo('body');
}

function hideWait()
{
	$("#smTableDataViewOverlay").remove();
}