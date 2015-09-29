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

var processManager=null;

// Funzione che schedula i processi selezionati dai check presenti sulla seconda colonna. Se il check relativo alla concatenazione dei
// processi (ovvero il check "Concatenate") è selezionato allora il processi vengono schedulati in cascata, altrimenti ogni processo
// verrà lanciato indipendentemente dagli altri. Questa funziona cicla su tutte le righe della tabella (scansando le prime 2 che fanno
// parte dell'header), e per ognuna, dopo aver memorizzato il contenuto di ciascun checkbox, lancia le relative funzioni di schedulazione.
// Se il checkBox "Concatenate" non è stato selezionato allora i processi selezionati vengono schedulati dalla funzione "scheduleSingleProcess()", 
// altrimenti l'ultimo processo viene schedulato singolarmente con la funzione "scheduleSingleProcess()", e i restanti vengono schedulati in maniera 
// concatenata con la funzione "scheduleConcatProcess()"
function scheduleProcess() {
	if($(".checkBoxes input:checked").length==0)
	{
		$('#alertDlg .modal-title').html("Execute Process");
		$('#alertDlg .modal-body .message').html("No data selected! Select data and activities before execute ");
		$('#alertDlg').modal();
		return;
	}
    var table = document.getElementById("processManager");
    /*for (var i = 0, row; row = table.rows[i]; i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        if (i >1 && i != table.rows.length - 1) {*/
    for (var i = 0; i< table.tBodies[0].rows.length;  i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        var row = table.tBodies[0].rows[i];
            //Contentuto dei checkbox
            var checkBoxI = row.getElementsByClassName("I")[0];
            var checkBoxQI = row.getElementsByClassName("QI")[0];
            var checkBoxT = row.getElementsByClassName("T")[0];
            var checkBoxV = row.getElementsByClassName("V")[0];
            var checkBoxR = row.getElementsByClassName("R")[0];
            var checkBoxesConcat = row.getElementsByClassName("Concat")[0];

            var checkedButtons = [];
            if (checkBoxI.checked) {
            	checkedButtons.push("I");
            }
            if (checkBoxQI.checked) {
            	checkedButtons.push("QI");
            }
            if (checkBoxT.checked) {
            	checkedButtons.push("T");
            }
            if (checkBoxV.checked) {
            	checkedButtons.push("V");
            }
            if (checkBoxR.checked) {
            	checkedButtons.push("R");
            }
		
            if (checkedButtons.length != 0) {

	            //Lancio dei processi in modo indipendente, ovvero nessun avvio di processo e legato a nessun altro.
	            if (!checkBoxesConcat.checked) {
			
	            	for (var k =0 ; k <= checkedButtons.length - 1; k++) {
	            		// console.log(checkedButtons[k]);
	            		scheduleSingleProcess(row, checkedButtons[k], true);
	            	}
	            }
	            
	            else if (!checkBoxesConcat.checked || checkedButtons.length == 1) {
			
	            	scheduleSingleProcess(row, checkedButtons[0], true);
	            }
		    //Lancio dei processi in modo concatenato	            
            	    else {
			
            		for (var j = checkedButtons.length - 1; j >= 0; j--) {
            			//console.log(checkedButtons[j]);
            			if (j == checkedButtons.length - 1) {
					
            				scheduleSingleProcess(row, checkedButtons[j], false);
            			}
            			else if (j == 0) {
				
            				scheduleConcatProcess(row, checkedButtons[j], checkedButtons[j+1], true);
            			}
            			else {
					
            				scheduleConcatProcess(row, checkedButtons[j], checkedButtons[j+1], false);
            			}

            		}
            	}	            
	        }
       // }
    }
}

function checkDeleteProcess()
{
	if($(".checkBoxes input:checked").length==0)
	{
		$('#alertDlg .modal-title').html("Delete Process");
		$('#alertDlg .modal-body .message').html("No data selected! Select data and activities before delete ");
		$('#alertDlg').modal();
		return false;
	}
	return true;
}

// Elimina i processi selezionai dallo scheduler.
// Cicla su tutte le righe della tabella HTML (processManager) ed elimina, per ognuna di queste, i processi selezionati dai relativi checkBox.
function deleteProcess() {
	
    var table = document.getElementById("processManager");
    for (var i = 0; i< table.tBodies[0].rows.length;  i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        var row = table.tBodies[0].rows[i];
    	//if (i >1 && i != table.rows.length - 1) {

            //Contentuto dei checkbox
            var checkBoxI = row.getElementsByClassName("I")[0];
            var checkBoxQI = row.getElementsByClassName("QI")[0];
            var checkBoxT = row.getElementsByClassName("T")[0];
            var checkBoxV = row.getElementsByClassName("V")[0];
            var checkBoxR = row.getElementsByClassName("R")[0];
            var checkBoxesConcat = row.getElementsByClassName("Concat")[0];

            //Value extraction from rows
            var process = row.getElementsByClassName("process")[0].innerHTML
            var Category = row.getElementsByClassName("Category")[0].innerHTML

            //Array which should be converted in Json and sent to the PHP process
            processArray = {}
            processArray["id"] = "deleteJob"

            if (checkBoxI.checked) {
                processArray["jobName"] = process + "_I"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "deleteProcess","deleteProcess")
            }
            if (checkBoxQI.checked) {
                processArray["jobName"] = process + "_QI"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "deleteProcess","deleteProcess")
            }
            if (checkBoxT.checked) {
                processArray["jobName"] = process + "_T"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "deleteProcess","deleteProcess")
            }
            if (checkBoxV.checked) {
                processArray["jobName"] = process + "_V"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "deleteProcess","deleteProcess")
            }
            if (checkBoxR.checked) {
                processArray["jobName"] = process + "_R"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "deleteProcess","deleteProcess")
            }
        //}

    }
}

// Funzione che lancia in ajax il comando passato come parametro (ovvero il parametro action)
// Questa funzione riceve in input 2 parametri, ovvero processArray che è l'array da passare 
// all'opportuno file php, e action, che specifica il nome del file php da chiamare.
function ajaxCommand(processArray, action,containter) {
    $.ajax({
        type: "post",
       // url: "php/" + action + ".php",
        url: "ProcessManager/AjaxCommand/" + action,
        data: {
            'processArrayJson': processArray
        },
        dataType:"json",
        beforeSend:function()
        {
        	showWait();
        },
        // In caso di successo viene aperto il popup che ha come id lo stesso nome del file php appena lanciato
        success: function(data, status, jqXHR) {
           // Funzione jquery che apre il popup con id uguale a action (passato come parametro)
        	if($("#"+containter+" p.message").length>0 && data['result'])
        	{
        		$("#"+containter+" p.message").html(data['result']);
        	}
            $("#"+containter).modal('show');
            processManager.ajax.reload();
        },
        error: function(data, status, jqXHR) {
        	$('#alertDlg p.message').html(data.responseText);
        	$('#alertDlg').modal('show');
        	//alert("Server not responding!");
        },
        complete:function(data, status, jqXHR)
        {
        	hideWait();
        }
    });
}

function showWait()
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
}

function hideWait()
{
	$.unblockUI();
}

//Funzione che crea il vettore contenente il path del processo che lo scheduler
//deve lanciare e i relativi parametri.
// Il parametro path contiene il perscorso relativo al comando da lanciare (nella forma "sh /percorso/nomefile.sh parametro1 parametro2")
// La funzione separa la stringa in corrispondenza degli spazi e crea un array associativo contenente il comando da inserire sullo scheduler
// (con chiave "processPath"), e tutti i parametri.
// Sullo scheduler non va riportato il termine "sh" prima del comando, quindi il primo elemento dell'array contenente i valori separati (pathSplitted)
// verrà scartato
function createParametersArray(path) {
    pathSplitted = path.split(" ")
    pathFixed = pathSplitted[0]
    parameterArray = {}
    parameterArray["processPath"] = pathFixed
    for (i = 0; i < pathSplitted.length; i++) {
        if (i != 0) {
            if (pathSplitted[i].indexOf("=") > -1) {
                // Per il momento i parametri hanno tutti la forma "key=value", quindi avviene uno split in corrispondenza
                // del simbolo "=", e il risultato diventerà chiave e valore anche nell'array associativo.
                key = pathSplitted[i].split("=")[0]
                parameterArray[key] = pathSplitted[i]
            } else {
                parameterArray[pathSplitted[i]] = pathSplitted[i]
            }
        }
    }
    return parameterArray
}

// Funzione che alla pressione di un checkBox sull'header, seleziona automaticamente i relativi checkBox su tutte le righe.
function toggleAllCheckBoxes(type, name) {
    var table = document.getElementById("processManager");
    for (var i = 0, row; row = table.rows[i]; i++) { //Scorre tutte le righe tranne la prima e l'ultima (header e footer)
        if (i > 1 && i != table.rows.length) {
            checkBox = row.getElementsByClassName(type).namedItem(name)
            if (checkBox.checked == false) {
                checkBox.checked = true
            } else {
                checkBox.checked = false
            }
        }
    }
}


// Funzione che schedula un singolo processo.
// row = oggetto row che contiene tutte le celle di una riga
// type = dice quale processo della riga esequire (processo I, oppure QI, oppure T ...)
// withTrig = specifica se il processo deve essere schedulato con o senza trigger
function scheduleSingleProcess(row, type, withTrig) {
    //Value extraction from rows
    var process = row.getElementsByClassName("process")[0].innerHTML
    var Category = row.getElementsByClassName("Category")[0].innerHTML
    var period = row.getElementsByClassName("period")[0].innerHTML
    var timeout = row.getElementsByClassName("overtime")[0].innerHTML

    var trigName = process + "_trig"
    var trigGroup = Category + "_trig"

    //Genero la data attuale per l'esecuzione del processo nello scheduler
    var dataAttuale = new Date();

    //Vengono momentaneamente aggiunti 2 minuti per un problema di sincronizzazione
    //con lo scheduler. Successivamente questa aggiunta di tempo andrà fatta su
    //php/scheduleProcess()
    var startAt = dataAttuale.getTime() + 120000

    //processParametersA=JSON.stringify(parameterArrayA)
    jobDataMap = {}
    jobDataMap["#isNonConcurrent"] = "true"
    jobDataMap["#jobTimeout"] = timeout

    //Array which should be converted in Json and sent to the PHP process
    processArray = {}
    //processArray["startAt"] = String(startAt)
    processArray["storeDurably"] = "true"
    processArray["requestRecovery"] = "false"
    processArray["id"] = "scheduleJob"
    processArray["jobClass"] = "ProcessExecutorJob"
    jobConstraints = {}
    jobConstraints["operator"] = "<"
    jobConstraints["systemParameterName"] = "systemCpuLoad"
    jobConstraints["value"] = "0.75" 

    // Mapping dei nomi dei processi. Questa parte di codice è necessaria in quanto le classi delle celle contenenti
    // i path dei processi hanno gli stessi nomi presenti nelle colonne della tabella MySQL processManager (ovvero A,B,C,D,E).
    // I nomi delle colonne sulla tabella HTML invece si chiamano "I, QI, T, V, R", quindi è necessaria una conversione.
    
    if (type == 'I') {
        var path = row.getElementsByClassName('A')[0].innerHTML;
    }
     if (type == 'QI') {
        var path = row.getElementsByClassName('B')[0].innerHTML;
    }
    if (type == 'T') {
        var path = row.getElementsByClassName('C')[0].innerHTML;
    }
    if (type == 'V') {
        var path = row.getElementsByClassName('D')[0].innerHTML;
    }
    if (type == 'R') {
        var path = row.getElementsByClassName('E')[0].innerHTML;
    }

    processArray["withJobIdentityNameGroup"] = [process + "_"+type, process]
    jobDataMap["#processParameters"] = createParametersArray(path)
    jobConstraints["jobName"] = process + "_"+type;
    jobConstraints["jobGroup"] = process
    jobDataMap["#jobConstraints"] = jobConstraints
    processArray["jobDataMap"] = jobDataMap

    // Caso in cui il processo deve essere lanciato con il trigger
   
    if (withTrig){
    	processArray["withIdentityNameGroup"] = [trigName + "_"+type, trigGroup]  // Parametri trigger
    	processArray["withPriority"] = "5"
        processArray["repeatForever"] = "true"
        processArray["withIntervalInSeconds"] = period
        ajaxCommand(processArray, "scheduleProcess","scheduleProcess");
    }
    // Caso in cui il processo deve essere lanciato senza trigger
    else {
	  	
	processArray["id"] = "addJob";
    	ajaxCommand(processArray, "scheduleProcessWithoutTrig","scheduleProcess")
    }
}

// Funzione che schedula 2 processi concatenati.
// row = oggetto row che contiene tutte le celle di una riga
// process1 = dice quale processo della riga esequire (processo I, oppure QI, oppure T ...)
// process2 = dice quale processo della riga esequire (processo I, oppure QI, oppure T ...)
// withTrig = specifica se il primo processo (process1) deve essere schedulato con o senza trigger
function scheduleConcatProcess(row, process1, process2, withTrig) {
    //Value extraction from rows
    var process = row.getElementsByClassName("process")[0].innerHTML
    var Category = row.getElementsByClassName("Category")[0].innerHTML
    var period = row.getElementsByClassName("period")[0].innerHTML
    var timeout = row.getElementsByClassName("overtime")[0].innerHTML

    var trigName = process + "_trig"
    var trigGroup = Category + "_trig"

    //processParametersA=JSON.stringify(parameterArrayA)
    jobDataMap = {}
    jobDataMap["#isNonConcurrent"] = "true"
    jobDataMap["#jobTimeout"] = timeout

    //Array which should be converted in Json and sent to the PHP process
    processArray = {}
    processArray["storeDurably"] = "true"
    processArray["requestRecovery"] = "false"
    processArray["id"] = "scheduleJob"
    processArray["jobClass"] = "ProcessExecutorJob"
    nextJobs = {}
    nextJobs["operator"] = "=="
    nextJobs["result"] = 1

    // Mapping dei nomi dei processi. Questa parte di codice è necessaria in quanto le classi delle celle contenenti
    // i path dei processi hanno gli stessi nomi presenti nelle colonne della tabella MySQL processManager (ovvero A,B,C,D,E).
    // I nomi delle colonne sulla tabella HTML invece si chiamano "I, QI, T, V, R", quindi è necessaria una conversione.
    if (process1 == 'I') {
        var path = row.getElementsByClassName('A')[0].innerHTML;
    }
     if (process1 == 'QI') {
        var path = row.getElementsByClassName('B')[0].innerHTML;
    }
    if (process1 == 'T') {
        var path = row.getElementsByClassName('C')[0].innerHTML;
    }
    if (process1 == 'V') {
        var path = row.getElementsByClassName('D')[0].innerHTML;
    }
    if (process1 == 'R') {
        var path = row.getElementsByClassName('E')[0].innerHTML;
    }

	processArray["withJobIdentityNameGroup"] = [process + "_"+process1, process]
	jobDataMap["#processParameters"] = createParametersArray(path)
	nextJobs["jobName"] = process +"_"+process2
	nextJobs["jobGroup"] = process
    jobDataMap["#nextJobs"] = nextJobs
    processArray["jobDataMap"] = jobDataMap

    // Caso in cui il processo deve essere lanciato con il trigger
    if (withTrig) {
        processArray["withIdentityNameGroup"] = [trigName + "_"+process1, trigGroup]
        processArray["withIntervalInSeconds"] = period
        processArray["withPriority"] = "5"
        processArray["repeatForever"] = "true"
        ajaxCommand(processArray, "scheduleConcatProcess","scheduleProcess")
    }
    // Caso in cui il processo deve essere lanciato senza trigger
    else {
    	processArray["id"] = "addJob"
        ajaxCommand(processArray, "scheduleConcatProcessWithoutTrig","scheduleProcess")
    }
}

// Funzione per il forzamente del lancio di un processo
// button = parametro che contiene l'oggetto bottone
// Dall'oggetto button verrà estratto il nome che servirà per lanciare il processo giusto
function launchProcess(button) {
    row = button.parentNode.parentNode
    //Value extraction from rows
    var process = row.getElementsByClassName("process")[0].innerHTML
    var Category = row.getElementsByClassName("Category")[0].innerHTML
    processArray = {}
    processArray["id"] = "triggerJob"

    // Mapping dei nomi dei processi. Questa parte di codice è necessaria in quanto le classi delle celle contenenti
    // i path dei processi hanno gli stessi nomi presenti nelle colonne della tabella MySQL processManager (ovvero A,B,C,D,E).
    // I nomi delle colonne sulla tabella HTML invece si chiamano "I, QI, T, V, R", quindi è necessaria una conversione.
    if (button.name == "button A") {
        processArray["jobName"] = process + "_I"
    }
    if (button.name == "button B") {
        processArray["jobName"] = process + "_QI"
    }
    if (button.name == "button C") {
        processArray["jobName"] = process + "_T"
    }
    if (button.name == "button D") {
        processArray["jobName"] = process + "_V"
    }
    if (button.name == "button R") {
        processArray["jobName"] = process + "_R"
    }
    processArray["jobGroup"] = process

    ajaxCommand(processArray, "launchProcess","launchProcess")
}

function checkPauseProcess()
{
	if($(".checkBoxes input:checked").length==0)
	{
		$('#alertDlg .modal-title').html("Pause Process");
		$('#alertDlg .modal-body .message').html("No data selected! Select data and activities before pause ");
		$('#alertDlg').modal();
		return false;
	}
	return true;
}
// Funzione usata per mettere in stato di "pausa" i processi.
// Questa funzione viene lanciata quando sulla pagina HTML viene premuto il tasto Pause.
// Viene eseguito un ciclo su tutte le righe (tranne le prime 2 e l'ultima, ovvero headers e footer) e vengono
// messi in pausa tutti i processi selezionati dai checkBox
function pauseProcess(){
	
    var table = document.getElementById("processManager");
  /*  for (var i = 0, row; row = table.rows[i]; i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        if (i >1 && i != table.rows.length - 1) {*/
    for (var i = 0; i< table.tBodies[0].rows.length;  i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        var row = table.tBodies[0].rows[i];

            //Contentuto dei checkbox
            var checkBoxI = row.getElementsByClassName("I")[0];
            var checkBoxQI = row.getElementsByClassName("QI")[0];
            var checkBoxT = row.getElementsByClassName("T")[0];
            var checkBoxV = row.getElementsByClassName("V")[0];
            var checkBoxR = row.getElementsByClassName("R")[0];
            var checkBoxesConcat = row.getElementsByClassName("Concat")[0];

            //Value extraction from rows
            var process = row.getElementsByClassName("process")[0].innerHTML
            var Category = row.getElementsByClassName("Category")[0].innerHTML

            //Array which should be converted in Json and sent to the PHP process
            processArray = {}
            processArray["id"] = "pauseJob"

            
            if (checkBoxI.checked) {
                processArray["jobName"] = process + "_I"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "pauseProcess","pauseProcess")
            }
            if (checkBoxQI.checked) {
                processArray["jobName"] = process + "_QI"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "pauseProcess","pauseProcess")
            }
            if (checkBoxT.checked) {
                processArray["jobName"] = process + "_T"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "pauseProcess","pauseProcess")
            }
            if (checkBoxV.checked) {
                processArray["jobName"] = process + "_V"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "pauseProcess","pauseProcess")
            }
            if (checkBoxR.checked) {
                processArray["jobName"] = process + "_R"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "pauseProcess","pauseProcess")
            }
        }
    //}
}
function checkResumeProcess()
{
	if($(".checkBoxes input:checked").length==0)
	{
		$('#alertDlg .modal-title').html("Resume Process");
		$('#alertDlg .modal-body .message').html("No data selected! Select data and activities before resume ");
		$('#alertDlg').modal();
		return false;
	}
	return true;
}
// Funzione usata per riesumare i processi che sono in stato di "pausa".
// Questa funzione viene lanciata quando sulla pagina HTML viene premuto il tasto Resume.
// Viene eseguito un ciclo su tutte le righe (tranne le prime 2 e l'ultima, ovvero headers e footer) e vengono
// riesumati tutti i processi selezionati dai checkBox.
function resumeProcess(){
	
    var table = document.getElementById("processManager");
  /*  for (var i = 0, row; row = table.rows[i]; i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        if (i > 1 && i != table.rows.length - 1) {*/
    for (var i = 0; i< table.tBodies[0].rows.length;  i++) { //Scorre tutte le righe tranne la prima e l'ultima (headere footer)
        var row = table.tBodies[0].rows[i];

            //Contentuto dei checkbox
            var checkBoxI = row.getElementsByClassName("I")[0];
            var checkBoxQI = row.getElementsByClassName("QI")[0];
            var checkBoxT = row.getElementsByClassName("T")[0];
            var checkBoxV = row.getElementsByClassName("V")[0];
            var checkBoxR = row.getElementsByClassName("R")[0];
            var checkBoxesConcat = row.getElementsByClassName("Concat")[0];

            //Value extraction from rows
            var process = row.getElementsByClassName("process")[0].innerHTML
            var Category = row.getElementsByClassName("Category")[0].innerHTML

            //Array which should be converted in Json and sent to the PHP process
            processArray = {}
            processArray["id"] = "resumeJob"

            if (checkBoxI.checked) {
                processArray["jobName"] = process + "_I"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "resumeProcess","resumeProcess")
            }
            if (checkBoxQI.checked) {
                processArray["jobName"] = process + "_QI"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "resumeProcess","resumeProcess")
            }
            if (checkBoxT.checked) {
                processArray["jobName"] = process + "_T"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "resumeProcess","resumeProcess")
            }
            if (checkBoxV.checked) {
                processArray["jobName"] = process + "_V"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "resumeProcess","resumeProcess")
            }
            if (checkBoxR.checked) {
                processArray["jobName"] = process + "_R"
                processArray["jobGroup"] = process
                ajaxCommand(processArray, "resumeProcess","resumeProcess")
            }
        }
   // }
}

// Funzione che ritorna una stringa HTML contenente i bottoni che sono presenti nella prima colonna della tabella HTML.
// Il css relativo al singolo bottone cambia se è presente o meno la data relativa al processo. 
// Nel caso la data relativa al processo interessato dal bottone sia presente, significa che il processo è presente sullo scheduler,
// ed in tal caso il bottone viene visualizzato come cliccabile.
// Ad esempio se il contenuto di row['Time A'] è vuoto il bottone relativo la processo di ingestion (che finisce con "_I") non è cliccabile
function returnButtons(row) {
    timeA = row['Time A']
    timeB = row['Time B']
    timeC = row['Time C']
    timeD = row['Time D']
    timeE = row['Time E']

    if (timeA != '') {
        buttonA = '<button type="button" name="button A" class="btn btn-primary btn-xs" onclick="launchProcess(this)">I</button>'
    }
    else {
        buttonA = '<button type="button" name="button A" class="btn btn-primary btn-xs" disabled="disabled" onclick="launchProcess(this)">I</button>'
    }
    if (timeB != '') {
        buttonB = '<button type="button" name="button B" class="btn btn-primary btn-xs" onclick="launchProcess(this)">QI</button>'
    }
    else {
        buttonB = '<button type="button" name="button B" class="btn btn-primary btn-xs" disabled="disabled" onclick="launchProcess(this)">QI</button>'
    }
    if (timeC != '') {
        buttonC = '<button type="button" name="button C" class="btn btn-primary btn-xs" onclick="launchProcess(this)">T</button>'
    }
    else {
        buttonC = '<button type="button" name="button C" class="btn btn-primary btn-xs" disabled="disabled" onclick="launchProcess(this)">T</button>'
    }
    if (timeD != '') {
        buttonD = '<button type="button" name="button D" class="btn btn-primary btn-xs" onclick="launchProcess(this)">V</button>'
    }
    else {
        buttonD = '<button type="button" name="button D" class="btn btn-primary btn-xs" disabled="disabled" onclick="launchProcess(this)">V</button>'
    }
    if (timeE != '') {
        buttonE = '<button type="button" name="button E" class="btn btn-primary btn-xs" onclick="launchProcess(this)">R</button>'
    }
    else {
        buttonE = '<button type="button" name="button E" class="btn btn-primary btn-xs" disabled="disabled" onclick="launchProcess(this)">R</button>'
    }
    buttons = buttonA+buttonB+buttonC+buttonD+buttonE

    return buttons
}

function activaTab(tab){
	    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
	};
 		

// Funzione che inserisce sulla tabella MySQL (processManager2) la righa selezionata
function addRow() {
	CKEDITOR.instances["LicenseText"].updateElement();
	var form = $( 'form#addRowForm' );
    errorList = $( "ul.errorMessages", $('#validationResult') );
    $('#validationResult').hide();
    $("form#addRowForm div.form-group").removeClass("has-error");
    var showAllErrorMessages = function() {
        errorList.empty();
       
        // Find all invalid fields within the form.
        var invalidFields = form.find( ":invalid" ).each( function( index, node ) {
        	$(node).parent().addClass("has-error");
            // Find the field's corresponding label
           // var label = $( "label[for=" + node.id + "] "),
        	  var label = node.attributes.placeholder.textContent,
                // Opera incorrectly does not fill the validationMessage property.
                message = node.validationMessage || 'Invalid value.';
        	var tab = $(node).data("tab");
        	var edit="<button class='button btn-sm' onclick=activaTab('"+tab+"')>Edit</button>";
        	
            errorList
                .show()
                .append( "<li><span><b>" + label + "</b></span> " + message +" "+ edit +"</li>");
           
            $('#validationResult').show();
        });
    };
    
	$('form#addRowForm').on( "submit", function( event ) {
	        if ( this.checkValidity && !this.checkValidity() ) {
	            $( this ).find( ":invalid" ).first().focus();
	            event.preventDefault();
	        }
	    });
				
	if($('form#addRowForm')[0].checkValidity())
	{
	
		row = {}
		var inputsCollection = document.getElementsByClassName('processRow');
	
		for(var i=0;i<inputsCollection.length;i++) // Ciclo su tutte le celle della riga
		{
		   row[inputsCollection[i].name] = inputsCollection[i].value; // Costruzione array associativo
		   if(inputsCollection[i].name=="Real_time"){
			   if(inputsCollection[i].value=="yes")
				   row['exec_A'] = 'yes';
			   else
				   row['exec_A'] = 'no';
		   }
			   
		}
		ajaxCommand(row, "addRow","addRow");
		$("#OpenDataEdit").modal('hide');
	}
	else
	{
		showAllErrorMessages();
		//$('form#addRowForm input#save').click();
	}
		

}

function checkDeleteOpenData()
{
	var table = document.getElementById("processManager");
	selectedRows = table.getElementsByClassName("DTTT_selected");
	if(selectedRows.length==0)
	{
		$('#alertDlg .modal-title').html("Delete Open Data");
		$('#alertDlg .modal-body .message').html("No data selected! Select data before delete");
		$('#alertDlg').modal();
		return false;
	}
	return true;
}

//Funzione che rimuove la riga selezionata dalla tabella HTML. La riga viene eliminata dalla tabella HTML
//ma non dalla tabella MySQL. L'operazione che viene eseguita consiste nel settare a 'no' la colonna exec_A 
//sulla tabella MySQL (processManager2), relativa alle righe selezionate. Se la colonna exec_A è settata a 'no'
//tale riga non verrà visualizzata in fase di caricamento.
function deleteOpenData() {
	var table = document.getElementById("processManager");
	selectedRows = table.getElementsByClassName("DTTT_selected");
	
 var process;
 var processesId = [];
	
 
 for (var i = 0, row; row = selectedRows[i]; i++) {
     process = row.getElementsByClassName("process")[0].innerHTML;
     processesId.push(process);
 }
 // Al comando removeRow viene passato solo il nome del processo (il contenuto della riga process).
 // Il comando php si occuperà di rimuovere la riga dal DB
 ajaxCommand(processesId, "deleteRow", "deleteRow");
}

function checkDisableRow()
{
	var table = document.getElementById("processManager");
	selectedRows = table.getElementsByClassName("DTTT_selected");
	if(selectedRows.length==0)
	{
		$('#alertDlg .modal-title').html("Disabling Process");
		$('#alertDlg .modal-body .message').html("No data selected! Select data before Disabling a Process");
		$('#alertDlg').modal();
		return false;
	}
	return true;
}

// Funzione che rimuove la riga selezionata dalla tabella HTML. La riga viene eliminata dalla tabella HTML
// ma non dalla tabella MySQL. L'operazione che viene eseguita consiste nel settare a 'no' la colonna exec_A 
// sulla tabella MySQL (processManager2), relativa alle righe selezionate. Se la colonna exec_A è settata a 'no'
// tale riga non verrà visualizzata in fase di caricamento.
function disableRow() {
	var table = document.getElementById("processManager");
	selectedRows = table.getElementsByClassName("DTTT_selected");
	
    var process;
    var processesId = [];
	
    
    for (var i = 0, row; row = selectedRows[i]; i++) {
        process = row.getElementsByClassName("process")[0].innerHTML;
        processesId.push(process);
    }
    // Al comando removeRow viene passato solo il nome del processo (il contenuto della riga process).
    // Il comando php si occuperà di scrivere 'no' sulla cella del database (processManager2).
    ajaxCommand(processesId, "disableRow", "disableRow");
}

function checkEnableRow()
{
	var table = document.getElementById("processManager");
	selectedRows = table.getElementsByClassName("DTTT_selected");
	if(selectedRows.length==0)
	{
		$('#alertDlg .modal-title').html("Enabling Process");
		$('#alertDlg .modal-body .message').html("No data selected! Select data before Enabling a Process");
		$('#alertDlg').modal();
		return false;
	}
	return true;
}

// Funzione che rimuove la riga selezionata dalla tabella HTML. La riga viene eliminata dalla tabella HTML
// ma non dalla tabella MySQL. L'operazione che viene eseguita consiste nel settare a 'no' la colonna exec_A 
// sulla tabella MySQL (processManager2), relativa alle righe selezionate. Se la colonna exec_A è settata a 'no'
// tale riga non verrà visualizzata in fase di caricamento.
function enableRow() {
	var table = document.getElementById("processManager");
	selectedRows = table.getElementsByClassName("DTTT_selected");
	
    var process;
    var processesId = [];
	
    
    for (var i = 0, row; row = selectedRows[i]; i++) {
        process = row.getElementsByClassName("process")[0].innerHTML;
        processesId.push(process);
    }
    // Al comando removeRow viene passato solo il nome del processo (il contenuto della riga process).
    // Il comando php si occuperà di scrivere 'no' sulla cella del database (processManager2).
    ajaxCommand(processesId, "enableRow", "enableRow");
}



// Funzione che viene chiamata in fase di caricamento della pagina HTML. Questa funzione riempie il body
// della tabella MySQL secondo i parametri specificati al plugin "Datatables".
function loadData() {
    var selected = [];
    if ( $.fn.dataTable.isDataTable('#processManager') ) {
    	processManager.ajax.reload();
		return null;
	}
    processManager = $('#processManager').DataTable( {
    	"sScrollX": "100%",
    	"sScrollY": "500px",
        "sScrollXInner": "110%",
        "sScrollYInner": "110%",
        
        "ajax": {
            url: "ProcessManager/MultiTables",
            type: "POST",
	    	data : function (d) {
	    		
	    		/*if ($('input#SDataWithTriples').is(':checked')) {
	    			d.columns[5].search.value="201";
	    		}}*/
	    			if ($('input[name="ODStatus"]').is(':checked')) {
	    				var val = $('input[name="ODStatus"]:checked').val()
	    				if(val!=-1)
	    					d.columns[43].search.value=""+val;
	    			}
	    			if ($('input[name="ODRTime"]').is(':checked')) {
		    			var val = $('input[name="ODRTime"]:checked').val()
		    			if(val!=-1)
		    			{
		    				d.columns[11].search.regex=true;
		    				d.columns[11].search.value=""+val;
		    			}
	    			}
	    		
	    	}
        },
        "rowCallback": function( row, data ) {
            if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
                $(row).addClass('selected');
            }
        },
        // Colonne che dalla tabella MySQL devono finire sulla tabella MySQL
        "columns": [
                    { "orderable": false,
                    	"render": function (row, type, val, meta) {
                    		
                    	    var controls = OpenDataManager.CreateActions(val);
                    		return controls;
                    	}
                    },
                { "orderable": false, // Prima colonna contenente i bottoni
                  "render": function (row, type, val, meta) {
                	  if(val.exec_A!=undefined && val.exec_A=="yes")
                		  return returnButtons(val);
                	  else
                  		return "";
                    }
                },
                { "orderable": false, // Seconda colonna contenente i checkBox
                "render": function (row, type, val, meta) {
                    if(val.exec_A!=undefined && val.exec_A=="yes")
                    	return  '<label><input type="checkbox" name="checkBoxA" class="I form-control"/>I</label><label><input type="checkbox" name="checkBoxB" class="QI form-control"/>QI</label><label><input type="checkbox" name="checkBoxC" class="T form-control"/>T</label><label><input type="checkbox" name="checkBoxD" class="V form-control"/>V</label><label><input type="checkbox" name="checkBoxE" class="R form-control"/>R</label><label><input type="checkbox" name="checkBoxConcat" class="Concat form-control"/>Concatenate</label>';
                	else
                		return "<label class=status>Processing Disabled</label>";
                	}
                },
                { "data": "process" },
                { "data": "Resource" },
                { "data": "Resource_Class" },
                { "data": "Category" },
                { "data": "Format" },
                { "data": "Automaticity" },
                { "data": "Process_type" },
                { "data": "Access" },
                { "data": "Real_time" },
                { "data": "Source" },
                { "data": "SecurityLevel","targets":"Security","render": function (row, type, val, meta) {
                	var label ="N.A";
                	if(val.SecurityLevel!=undefined)
                    {
                    	
                    	switch(val.SecurityLevel)
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
                    return "<label class=status>"+label+"</label>";
                }
                },
                { "data": "A" },
                { "data": "status_A" },
                { "data": "time_A" },
                { "data": "last_update" },
                { "data": "error_A" },
                { "data": "B" },
                { "data": "status_B" },
                { "data": "time_B" },
                { "data": "error_B" },
                { "data": "C" },
                { "data": "status_C" },
                { "data": "time_C" },
                { "data": "last_triples" },
                { "data": "error_C" },
                { "data": "D" },
                { "data": "status_D" },
                { "data": "time_D" },
                { "data": "Triples_count" },
                { "data": "error_D" },
                { "data": "E" },
                { "data": "status_E" },
                { "data": "time_E" },
                { "data": "Triples_countRepository" },
                { "data": "error_E" },
                { "data": "period" },
                { "data": "overtime" },
                { "data": "param" },
                { "data": "error" },
                { "data": "description" },
                { "data": "exec_A","visible":false },
                { "data": "LicenseUrl","visible":false },
                { "data": "LicenseText","visible":false },
                
        ],
        // Assegnazione dei nomi delle classi relative alle colonne. Per ogni colonna, specificata attraverso la posizione (numerica),
        // viene specificata la classe che verrà assegnata all'elemento della riga nel body.
        "columnDefs": [
            { className: "actions", "targets": [ 0 ] },
            { className: "buttons", "targets": [ 1 ] },
            { className: "checkBoxes", "targets": [ 2 ] },
            { className: "process", "targets": [ 3 ] },
            { className: "Resource", "targets": [ 4 ] },
            { className: "Resource_Class", "targets": [ 5 ] },
            { className: "Category", "targets": [ 6 ] },
            { className: "Format", "targets": [ 7 ] },
            { className: "Automaticity", "targets": [ 8 ] },
            { className: "Process_type", "targets": [ 9 ] },
            { className: "Access", "targets": [ 10 ] },
            { className: "Real_time", "targets": [ 11 ] },
            { className: "Source", "targets": [ 12 ] },
            { className: "Security", "targets": [ 13 ] },
            { className: "A danger", "targets": [ 14 ] },
            { className: "status_A danger", "targets": [ 15 ] },
            { className: "time_A danger", "targets": [ 16 ] },
            { className: "last_update danger", "targets": [ 17 ] },
            { className: "error_A danger", "targets": [ 18 ] },
            { className: "B active", "targets": [ 19 ] },
            { className: "status_B active", "targets": [ 20 ] },
            { className: "time_B active", "targets": [ 21 ] },
            { className: "error_B active", "targets": [ 22 ] },
            { className: "C info", "targets": [ 23 ] },
            { className: "status_C info", "targets": [ 24 ] },
            { className: "time_C info", "targets": [ 25 ] },
            { className: "last_triples info", "targets": [26 ] },
            { className: "error_C info", "targets": [ 27 ] },
            { className: "D warning", "targets": [ 28 ] },
            { className: "status_D warning", "targets": [ 29 ] },
            { className: "time_D warning", "targets": [ 30 ] },
            { className: "Triples_count warning", "targets": [ 31 ] },
            { className: "error_D warning", "targets": [ 32 ] },
            { className: "E success", "targets": [ 33 ] },
            { className: "status_E success", "targets": [ 34 ] },
            { className: "time_E success", "targets": [ 35 ] },
            { className: "Triples_countRepository success", "targets": [ 36 ] },
            { className: "error_E success", "targets": [ 37 ] },
            { className: "period", "targets": [ 38 ] },
            { className: "overtime", "targets": [ 39 ] },
            { className: "param", "targets": [ 40 ] },
            { className: "error", "targets": [ 41 ] },
            { className: "description", "targets": [ 42 ] },
            ],
            "language": {
                "decimal": ",",
                "thousands": "."
            },
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[3, 'asc']],
            "pagingType": "full_numbers",
            "processing": true,
            "serverSide": true,
            "stateSave": true,
            "dom": '<l<"#ODRTime.toolbar"><"#ODStatus.toolbar">>TCfrtip',
            "colVis": {
                'exclude': ['all'],
                'groups': [
                         {
                             title: "Ingestion",
                             columns: [ 14, 15,  16, 17, 18 ]
                         },
                         {
                             title: "Quality Improv.",
                             columns: [ 19, 20, 21, 22 ]
                         },
                         {
                             title: "Triples Gen.",
                             columns: [ 23, 24, 25, 26, 27 ]
                         },
                         {
                             title: "Validation",
                             columns: [28, 29, 30, 31, 32 ]
                         },
                         {
                             title: "Reconciliation",
                             columns: [ 33, 34, 35, 36, 37 ]
                         },
                         {
                             title: "General",
                             columns: [38, 39, 40, 41, 42]
                         }
                         
                     ]
            },
            "tableTools": {
                "sRowSelect": "multi",
                "sRowSelector":"td:nth-child(n+4)",
                "aButtons": [ "select_all", "select_none",
                              {
					"sExtends":    "text",
					"sButtonText": "Refresh",
					
					
					"fnClick": function ( nButton, oConfig, oFlash ) {
						processManager.ajax.reload();
					}
   
              }], 
                "fnRowSelected":function(node){
                	for(var n in node)
                	{
                		 var index = $.inArray(node[n].id, selected);

                         if ( index === -1 ) {
                             selected.push( node[n].id );
                         } 
                        
                	
                	}
                },
                "fnRowDeselected":function(node){
                	for(var n in node)
                	{
                		 var index = $.inArray(node[n].id, selected);

                         if ( index>-1 ) 
                         {
                             selected.splice( index, 1 );
                         }
                	
                	}
                }
                
            },

    });
        
        // Funzione JQuery che fa sì che al doppio click su una riga quest'ultima venga evidenziata
        // e ne venga cambiata la classe (aggiungendo la scritta "selected").
        // Il doppio click non viene considerato se fatto nelle prime due colonne (contententi bottoni e checkBox)
  /*      $('#processManager tbody').on('dblclick', 'td', function () {
            if ($(this).hasClass('buttons') || $(this).hasClass('checkBoxes')) {
            }
            else {
                parent = $(this).parent().get(0);
                var id = parent.id;
                var index = $.inArray(id, selected);

                if ( index === -1 ) {
                    selected.push( id );
                } else {
                    selected.splice( index, 1 );
                }

                $(parent).toggleClass('selected'); 
            }
        } );
    */

        // Funzione che permettere di scrivere all'interno di una cella.
        // Quando si clicca su una cella viene aperto un form sul quale è possibile scrivere. Una volta inserito il testo,
        // non appena viene cliccato invio, quest'ultimo viene iviato in ajax al file editCell.php, che fa l'update sulla tabella.
        $('#processManager tbody').on('dblclick', 'td:nth-child(n+5)', function() {
            if ($(this).hasClass('buttons') || $(this).hasClass('checkBoxes') || $(this).hasClass('process')) {
            }
            else {
                var OriginalContent = $(this).text();
                var aData = processManager.cell( this ).index().row;
                var nRow = $('#processManager tbody tr')[aData];
				TableTools.fnGetInstance( 'processManager' ).fnDeselect( nRow );
                $(this).addClass("cellEditing");
                $(this).html("<input type='text' value='" + OriginalContent + "' />");
                var first = $(this).children().first();
                first.focus();

                $(this).children().first().keydown(function (e) {
                    var code = e.keyCode || e.which;
                    if (code == 13) {
                        processArray = {};
                        processArray["process"] = $(this).parent().parent().children('td.process')[0].innerHTML;
                        var newContent = $(this).val();
                        var column = $(this).parent()
                        column = column.removeClass("cellEditing");
                        processArray["column"] = column.attr('class').split(' ')[0];
                        $(this).parent().text(newContent);
                        $(this).parent().removeClass("cellEditing");
                        processArray["newContent"] = newContent
                        ajaxCommand(processArray, "editCell", "editCell");
                    }
                    else if(code==27)
                    {
                    	var column = $(this).parent()
                        column = column.removeClass("cellEditing");
                       
                        first.blur();
                    }
                });

                $(this).children().first().blur(function(){
                    $(this).parent().text(OriginalContent);
                    $(this).parent().removeClass("cellEditing");
                });
                }
        });
        $("div#ODStatus.toolbar").html('with processing status <input type=radio name="ODStatus" value="-1" checked/><b>Any</b> <input type=radio name="ODStatus" value="yes"/><b>Enabled</b> <input type=radio name="ODStatus" value="no|NULL"/><b>Disabled</b>');
        $("div#ODRTime.toolbar").html(' for <input type=radio name="ODRTime" value="yes" /><b>Real Time</b> <input type=radio name="ODRTime" value="no|NULL" /><b>Static</b> <input type=radio name="ODRTime" value="-1" checked/><b>Any</b> data');
       
        $('input[name="ODStatus"]').on('change',function(){
        	processManager.ajax.reload();
        });
        
        $('input[name="ODRTime"]').on('change',function(){
        	processManager.ajax.reload();
        });

       
        
       
        
}



/*
//Codice di visualizzazione dei modal di Bootstrap
$('#exampleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-title').text('New message to ' + recipient)
  modal.find('.modal-body input').val(recipient)
});

$('#modConfirm').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-title').text('New message to ' + recipient)
  modal.find('.modal-body input').val(recipient)
});
*/
