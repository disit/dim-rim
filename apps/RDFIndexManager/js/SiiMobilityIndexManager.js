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

var IndexManager={
		
		modified:false,
		$this:null,
		init:function(){
			
			$("select[name='parentID']").prop('disabled', true);
			$("select[name='mode']").on("change",function(){
				if($(this).val()=="empty")
					$("select[name='parentID']").prop('disabled', true);
				else
					$("select[name='parentID']").prop('disabled', false);
				if($(this).val()=="empty" || $(this).val()=="copy")
					$("select[name='type']").prop('disabled', false);
				else
					$("select[name='type']").prop('disabled', true);
			});
			$("textarea[name='xmltext']").prop('disabled', false);
			$("input[name='file']").prop('disabled', true);
			$("input[name='mode']").on("change",function(){
				if($(this).val()=="Text")
				{
					$("textarea[name='xmltext']").prop('disabled', false);
					$("input[name='file']").prop('disabled', true);
				}
				else
				{
					$("textarea[name='xmltext']").prop('disabled', true);
					$("input[name='file']").prop('disabled', false);
				}
					
			});	
			
			
			
			$('input.SecurityLevelChb').click( function(){
				IndexManager.initSecLevel(this);
			});

			IndexManager.initSecLevel($('input.SecurityLevelChb:checked')[0]);
			
			
		},
		initSecLevel:function (obj)
		{
			 var value = $(obj).attr('value');
		    
		    // iterate through the checkboxes and check those with values lower than or equal to the one you selected. Uncheck any other.
		    for(i=0; i<=3; i++){
		        if ($("#SLcheck-" + i).val() <= value){
		            $("#SLcheck-" + i).prop('checked', true);
		        } else {
		            $("#SLcheck-" + i).prop('checked', false);
		        }
		    }
		},
		runScript:function(link)
		{
			$.getJSON(link,function(data)			
					{
						if(data.result)
						{
							$("#system-message").hide();
							$("#system-message").html(data.result);
							$("#system-message").show().animate({opacity: 1.0}, 3000).fadeOut(1000);
						}
					})
					
		}, 
		unlockIndex:function(link)
		{
			this.showWait();
			$.getJSON(link,function(data)			
					{
						IndexManager.hideWait();
						if(data.result)
						{
							$("#system-message").hide();
							$("#system-message").html(data.result);
							$("#system-message").show().animate({opacity: 1.0}, 3000).fadeOut(1000);
						}
					})
					
		},
		lockIndex:function(link)
		{
			this.showWait();
			$.getJSON(link,function(data)			
					{
						IndexManager.hideWait();
						if(data.result)
						{
							$("#system-message").hide();
							$("#system-message").html(data.result);
							$("#system-message").show().animate({opacity: 1.0}, 3000).fadeOut(1000);
						}
					})
					
		},
		validateIndex:function(link)
		{
			this.loadPanel(link,'#header_validate_index');
			

		},
		removeItemFromIndex:function(ID,itemID)
		{
			var itemID = itemID;
			var currentDataType = $("#validation_table tbody tr#"+itemID).find(".type").text();
			var status = {
	                "dataType": currentDataType,
	                "action":"select",
	    	    	"currentSession": ID,
	    			"select": {
	    				"id": itemID,
	    			}
			};
				    
			if(currentDataType=="RealTimeData")
			{
				status.select['from']="";
				status.select['to']="";
			}
			else
				status.select['version']="";
			
			
			bootbox.dialog({
				  message: "Do you want to remove <b>"+itemID+"</b> from index?",
				  title: "Remove item from Index",
				  buttons: {
				    success: {
				      label: "Ok",
				      className:"button light-gray btn-xs btn btn-primary",
				      callback: function() {
				        	
				        	$.ajax({
								type: "GET",
								url: "IndexGenerator/Status",
								data: status,
								beforeSend:function(){
									 var over = '<div id="smTableDataViewOverlay">' +
						                '<div id="loading"><i class="sm-icon smTableDataViewWaitIcon"></i> Processing....' +
						                '</div>';
						            $(over).appendTo('body');
								},
								success:function(data)
								{
									//bootbox.hideAll();
									$('#header_validate_index a').click();
								},
								complete:function()
								{
									$('#smTableDataViewOverlay').remove();
								}
							});
				      }
				    },
				    main: {
				        label: "Cancel",
				        className:"button light-gray btn-xs btn btn-primary",
				      }
				    }
				  });
		},		
		removeAllFromIndex:function(ID)
		{
			$('#ValidateProgressModal').on('hidden.bs.modal', function () {
				$('#header_validate_index a').click();
			});
			$('#ValidateProgressModal').modal({ backdrop: 'static',
			  keyboard: false});
			$('#ValidateProgressModal').modal('show');
			$('.bar').css("width","0%");
			$('.bar').text('0%');
			$('#mText').css("color","#777");
			var items=new Array();
			//this.loadPanel(link,'#header_validate_index');
			var N = $("#validation_table tbody tr").length;
			$("#validation_table tbody tr").each(function()
			{
				var itemID = $(this).attr("id");
				var currentDataType = $(this).find(".type").text();
				var status = {
			                "dataType": currentDataType,
			                "action":"select",
			    	    	"currentSession": ID,
			    			"select": {
			    				"id": itemID,
			    			}
				};
			    
				if(currentDataType=="RealTimeData")
				{
					status.select['from']="";
					status.select['to']="";
				}
				else
					status.select['version']="";
				items.push(status);
				
			});
			
			
			var updateProgress=function(percentage,message)
        	{
	    		if(percentage > 100) 
	    			percentage = 100;
	    		$("#ValidateProgressModal .progress").hide();
	    		$("#ValidateProgressModal #progressbar-container #mText").html(message);
	    			var w = $('#ValidateProgressModal .bar').width();
	    		$("#ValidateProgressModal .progress").show();
	    		if(w<=percentage)
	    				$('#ValidateProgressModal .bar').css('width', Math.floor(percentage)+'%');
	    		$('#ValidateProgressModal .bar').text(Math.floor(percentage)+'%');
	    	
	    		if(percentage==100)
	    			 setTimeout(function(){$('#ValidateProgressModal .progress').removeClass('active');
	    			 $('#ValidateProgressModal').modal('hide');
	    			 $('#ValidateProgressModal .bar').css('width', '0%');
	    			 },500);
	    		     
	        };
			var done = 0;
			$(items).each(function()
			{	
				var request=this;
				$.ajax({
							type: "GET",
							url: "IndexGenerator/Status",
							data: request,
							success:function(data)
							{
								done++;
								updateProgress((done/items.length)*100,"Removed: <b>"+request.select.id+"</b>");
							}
						});
			});
    	
	        

		},
	
		buildScript:function()
		{
			// Show the modal window
			$('#modInsertion').modal();
			
			// Set the window elements
			$('#modInsertionTitle').html("Insert repository ID");
			$('#modInsertionSpan').html("Please specify the ID of the repository you want to create");
			var val = repositoryID==undefined?"":repositoryID;
			$('#modInsertionInput').val(val);
			$('#modInsertionInput').attr("placeholder", "Repository ID");
			$('#modInsertionConfirm').attr("onclick", "IndexManager.doScript()");
			

		},
		doScript:function() {
			
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
						$('#modAlertTitle').html(data.title);
						$('#modAlertBody').html(data.html);
						
						//"The script was created! You can find it in file '"+data.path+"'"
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
			
		},
		loadBuildPanel:function(link)
		{
			this.loadPanel(link,'#header_builderPanel');
		},
		loadHelpPanel:function(link)
		{
			this.loadPanel(link,'#header_helpPanel');
		},
/*		loadSettings:function(link)
		{
			this.loadPanel(link,'#header_settings');
		},*/
		loadPanel:function(link,id,callback)
		{
			$('.header').removeClass("active");
			$(id).addClass("active");
			$(".step").hide();
			$("#panels").show();
			$("#panels #rotor").show();
			$("#panels #content").load(link, function() {
				$("#panels #rotor").hide();		
				});
		},
		showPropertiesMessage:function(response)
		{
			var msg ='<div class="alert alert-success"><a class="close" data-dismiss="alert" href="#">×</a><strong class="alert-heading">' + response.message + '</strong></div>';
			jQuery("#Properties").prepend(msg);							
			var animation= new Object();
			animation.scrollTop= jQuery("#Properties").offset().top;
			jQuery("html, body").animate(animation, 500 ); 
			jQuery("#Properties").find("input[type=submit]").removeAttr("disabled");
		},
		loadWelcomePanel:function(link)
		{
			this.loadPanel(link,'#header_helpPanel');
		},
		loadPropertiesPanel:function (link)
		{
			$('.header').removeClass("active");
			$("#header_properties").addClass("active");
			$(".step").hide();
			$("#panels").show();
			$("#panels #rotor").show();
			$("#panels #content").load(link, function() {
				$("#panels #rotor").hide();		
				$('input.SecurityLevelChb').click( function(){
					IndexManager.initSecLevel(this);
				});

				IndexManager.initSecLevel($('input.SecurityLevelChb:checked')[0]);
				
			});
		},
		confirmExit:function()
		{
		/*  if(IndexManager.modified)
			  return "You have attempted to leave this page.  If you have made any changes to the fields without clicking the Save button, your changes will be lost.  Are you sure you want to exit this page?";
		  */
			return null;
		},
		showWait:function()
		{
			$.blockUI({ css: { 
		        border: 'none', 
		        padding: '15px', 
		        backgroundColor: '#000', 
		        '-webkit-border-radius': '10px', 
		        '-moz-border-radius': '10px', 
		        opacity: .5, 
		        color: '#fff' 
		    } }); 
		},
		hideWait:function()
		{
			$.unblockUI();
		},
}

$(document).ready(function(){
	window.onbeforeunload = IndexManager.confirmExit;
	IndexManager.init();
});