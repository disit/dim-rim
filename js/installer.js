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

var waitnote		=	'<img id=wait alt="" src="img/wait.gif"/>';
var Installer = {
		initStep:function()
		{
			$("form#form-step button").prepend($(waitnote));
			$('form#form-step button #wait').hide();
			$("form#form-step").submit(function(event) {
			        // process the form
					$('form#form-step button #wait').show();
			        $.ajax({
			            type        : $('form#form-step').attr("method"), // define the type of HTTP verb we want to use (POST for our form)
			            data        : $('form#form-step').serialize(), // our data object
			            success:function(html)
			            {
			            	$("div#installer").replaceWith($(html).find("div#installer"));
			            }
			        })
			            // using the done promise callback
			            .done(function(data) {
			            	$('#wait').hide();
			                Installer.initStep();
			            });

			        // stop the form from submitting the normal way and refreshing the page
			        event.preventDefault();
			});
		}
}
$(document).ready(function(){
	Installer.initStep();
});