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

var OpenDataManager={
		
		modified:false,
		$this:null,
		TripleFilesDlg:null,
		ODLicenseDlg:null,
		init:function(){
			
			//$('#OpenDataManagerTabs_tabs a.button').toggleClass("light-gray");
			$(".modal-dialog").draggable({
			      handle: ".modal-header"
			  });
			$('#container .OD_View').hide();
			 $('#OpenDataEdit').on('show.bs.modal', function (event) {
	        	$("#validationResult").hide();
	        	document.getElementById("addRowForm").reset();
	        	$("form#addRowForm input:text").removeAttr("disabled").attr("value","");
	        	$("form#addRowForm div.form-group").removeClass("has-error");
	        	CKEDITOR.replace["LicenseText"];
	        });	
			 $('#OpenDataEdit').on('hidden.bs.modal', function (event) {
				 CKEDITOR.instances["LicenseText"].setData("");
			 });
			 
			 this.ODLicenseDlg = $('#ODLicenseDlg').clone();
			 $('body').on('hidden.bs.modal', '#ODLicenseDlg', function () {
				 $('#ODLicenseDlg').modal('hide').remove();
			        var myClone = OpenDataManager.ODLicenseDlg.clone();
			        $('body').append(myClone);
			});
			 
			 $('body').on('show.bs.modal', '#ODLicenseDlg', function () {
				    $("#ODLicenseDlg .modal-body").html("<div><i class='sm-icon sm-loader'></i> Loading....</div>");
				});
			 
			 this.TripleFilesDlg = $('#TripleFilesDlg').clone();
			 $('body').on('hidden.bs.modal', '#TripleFilesDlg', function () {
				 $('#TripleFilesDlg').modal('hide').remove();
			        var myClone = OpenDataManager.TripleFilesDlg.clone();
			        $('body').append(myClone);
			}); 
			
			$('body').on('show.bs.modal', '#TripleFilesDlg', function () {
			    $("#TripleFilesDlg .modal-body").html("<div><i class='sm-icon sm-loader'></i> Loading....</div>");
			});
			
				
			$('body').on('loaded.bs.modal', '#TripleFilesDlg', function () {
				 $("#TripleFilesDlg .btn").addClass("button light-gray btn-sm");
			});
	        
	        $('#enableConfirm').on('show.bs.modal', function (event) {
	        	return checkEnableRow();
	        	
	        });
	        
	        $('#disableConfirm').on('show.bs.modal', function (event) {
	        	return checkDisableRow();
	        	
	        });
	        $('#pauseJob').on('show.bs.modal', function (event) {
	        	return checkPauseProcess();
	        	
	        });
	        $('#resumeJob').on('show.bs.modal', function (event) {
	        	return checkResumeProcess();
	        	
	        });
	        $('#deleteFromScheduler').on('show.bs.modal', function (event) {
	        	return checkDeleteProcess();
	        	
	        });
	        $('#deleteConfirm').on('show.bs.modal', function (event) {
	        	return checkDeleteOpenData();
	        	
	        });
			this.ShowPanel("explorer");
			loadData();
		},
		New:function(){		
			addRow();
		},
		ShowPanel:function(id)
		{
			$(".header").removeClass("active");
			$('#container .OD_View').hide();
			$('#OpenDataManagerTabs_tabs').show();
			$("a[name='"+id+"']").parent().toggleClass("active");
			$("a[href='#"+id+"']").click();
			
		},
		
		ShowHelp:function()
		{
			$(".header").removeClass("active");
			$('#container .OD_View').hide();
			$('#OD_Help_View').show();
			$("a[name='help']").parent().toggleClass("active");
		},
		Edit:function(id)
		{
			var rowId="#row_"+id;
			var table = $('#processManager').DataTable();
			var data = table.row(rowId).data();
			$('#OpenDataEdit').modal('show');
			$.each(data, function(key, value){  
			    var $ctrl = $('#OpenDataEdit [name="'+key+'"]');  
			    switch($ctrl.attr("type"))  
			    {  
			        case "text" :   
			        case "hidden":  
			        $ctrl.attr("value",value);   
			        break;   
			        case "radio" : case "checkbox":   
			        $ctrl.each(function(){
			           if($(this).attr('value') == value) {  $(this).attr("checked",value); } });   
			        break;  
			        case "select":
			        	 $("option",$ctrl).each(function(){
			                 if (this.value==value) { this.selected=true; }
			             });
			        	 break;
			        case "ckeditor":
			        	CKEDITOR.instances[key].setData(value);
			        	break;
			        default:
			        	$ctrl.val(value); 
			        break;
			    }  
			    if(key=='process')
			    	$ctrl.attr("disabled","disabled");
			 });  
		},
		CreateActions:function(val)
		{
			var id = val.DT_RowId.replace("row_","");
			var controls = '<button class="button action_form_cmd OD-edit"onclick=OpenDataManager.Edit("'+id+'") title="Edit"><i class="edit-icon"></i></button>';
    		//if(val.Real_time=="no" && val.last_triples!="NULL")
			controls += '<button class="button action_form_cmd OD-License" data-toggle="modal" data-target="#ODLicenseDlg" href="ProcessManager/License/'+id+'" title="License Text"><i class="license-icon"></i></button>';
    		controls += '<button class="button action_form_cmd OD-files" data-toggle="modal" data-target="#TripleFilesDlg" href="ProcessManager/Files/'+id+'" title="Files"><i class="files-icon"></i></button>';
    		return controls;
		},
		showSettingsMessage:function(response)
		{
			var msg ='<div class="alert alert-success"><a class="close" data-dismiss="alert" href="#">×</a><strong class="alert-heading">' + response.message + '</strong></div>';
			jQuery("form#OD_settings").prepend(msg);							
			var animation= new Object();
			animation.scrollTop= jQuery("form#OD_settings").offset().top;
			jQuery("html, body").animate(animation, 500 ); 
			jQuery("form#OD_settings").find("input[type=submit]").removeAttr("disabled");
		},
		loadWelcomePanel:function(link)
		{
			
		},
		
		confirmExit:function()
		{
		/*  if(IndexManager.modified)
			  return "You have attempted to leave this page.  If you have made any changes to the fields without clicking the Save button, your changes will be lost.  Are you sure you want to exit this page?";
		  */
			return null;
		}
}

$(document).ready(function(){
	//window.onbeforeunload = IndexManager.confirmExit;
	OpenDataManager.init();
});