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

function DeleteTBWSelectedGenerations(id,endCallback) { 
	 link = "RepositoryManager/Generations/Archive/Delete"; 
	 RepositoryManagerAjaxCommandDelete(link,id,endCallback);
}  

function DeleteTBWSelectedOntologies(id,endCallback) { 
	 link = "RepositoryManager/Ontologies/Archive/Delete"; 
	 RepositoryManagerAjaxCommandDelete(link,id,endCallback);
}  

function RepositoryManagerAjaxCommandDelete(link,id,callback)
{
	 jQuery.ajax({url:link,  type:"POST", data:id, dataType:"json", success: function(msg) 
		 {  
			 if(callback!=undefined)
			 {
				 callback("Deleted item "+id);
			 }
	    }  
		});  
}

/*function NewTBWOntology()
{
	bootbox.dialog({
		  message: "Hello",
		  title: "New Ontology",
		  buttons: {
		    success: {
		      label: "Save",
		      className:"button light-gray btn-xs btn btn-primary",
		      callback: function() {
		        	bootbox.hideAll();
		        	//TableDataView.doCommand(items,fn);
		      }
		    },
		    main: {
		        label: "Cancel",
		        className:"button light-gray btn-xs btn btn-primary",
		      }
		    }
		  });
	return false;
}*/

$(document).ready(function(){
	$("#OntologyNewDlg .btn").addClass("button light-gray btn-sm");
	var myBackup = $('#OntologyNewDlg').clone();
	
	$('body').on('hidden.bs.modal', '#OntologyNewDlg', function () {
		 $('#OntologyNewDlg').modal('hide').remove();
	        var myClone = myBackup.clone();
	        $('body').append(myClone);
	});
	
	
				
	$('#OntologyNewDlg').on('click','#btnSave',function(event) {
		//$('#OntologyNewDlg').modal('hide');
		$('#OntologyNewDlg form input[type="submit"]').click();
	});
});





