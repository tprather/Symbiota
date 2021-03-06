$(document).ready(function() {
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$( "#taxa" )
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			// don't honor ENTER key if an autocomplete is not selected yet
			if (event.keyCode === $.ui.keyCode.ENTER) {
				if (this.autocomplete_stage != 0) {
				    event.preventDefault();
				}
		    } else
			// don't navigate away from the field on tab when selecting an item
			if (event.keyCode === $.ui.keyCode.TAB) {
				if ($(this).autocomplete('widget').is(':visible')) {
					$(this).trigger("select");
					event.preventDefault();
				}
			} else {
				this.autocomplete_stage = 1;
			}
		})
		.autocomplete({
			source: $.proxy(function( request, response ) {
				$.getJSON( "rpc/taxalist.php", {
					term: extractLast( request.term ), t: function() { return document.harvestparams.taxontype.value; }
				}, response );
				this.autocomplete_stage = 0;
			},$('#taxa')[0]),
			autoFocus: true,
			search: function() {
				// custom minLength
				this.autocomplete_stage = 2;
				var term = extractLast( this.value );
				if ( term.length < 4 ) {
					return false;
				}
				this.autocomplete_stage = 3;
				return true;
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				this.value = terms.join( ", " );
				return false;
			}
		},{});
});

function displayTableView(f){
	f.action = "listtabledisplay.php";
	f.submit();	
}

function checkUpperLat(){
	if(document.harvestparams.upperlat.value != ""){
		if(document.harvestparams.upperlat_NS.value=='N'){
			document.harvestparams.upperlat.value = Math.abs(parseFloat(document.harvestparams.upperlat.value));
		}
		else{
			document.harvestparams.upperlat.value = -1*Math.abs(parseFloat(document.harvestparams.upperlat.value));
		}
	}
}

function checkBottomLat(){
	if(document.harvestparams.bottomlat.value != ""){
		if(document.harvestparams.bottomlat_NS.value == 'N'){
			document.harvestparams.bottomlat.value = Math.abs(parseFloat(document.harvestparams.bottomlat.value));
		}
		else{
			document.harvestparams.bottomlat.value = -1*Math.abs(parseFloat(document.harvestparams.bottomlat.value));
		}
	}
}

function checkRightLong(){
	if(document.harvestparams.rightlong.value != ""){
		if(document.harvestparams.rightlong_EW.value=='E'){
			document.harvestparams.rightlong.value = Math.abs(parseFloat(document.harvestparams.rightlong.value));
		}
		else{
			document.harvestparams.rightlong.value = -1*Math.abs(parseFloat(document.harvestparams.rightlong.value));
		}
	}
}

function checkLeftLong(){
	if(document.harvestparams.leftlong.value != ""){
		if(document.harvestparams.leftlong_EW.value=='E'){
			document.harvestparams.leftlong.value = Math.abs(parseFloat(document.harvestparams.leftlong.value));
		}
		else{
			document.harvestparams.leftlong.value = -1*Math.abs(parseFloat(document.harvestparams.leftlong.value));
		}
	}
}

function checkPointLat(){
	if(document.harvestparams.pointlat.value != ""){
		if(document.harvestparams.pointlat_NS.value=='N'){
			document.harvestparams.pointlat.value = Math.abs(parseFloat(document.harvestparams.pointlat.value));
		}
		else{
			document.harvestparams.pointlat.value = -1*Math.abs(parseFloat(document.harvestparams.pointlat.value));
		}
	}
}

function checkPointLong(){
	if(document.harvestparams.pointlong.value != ""){
		if(document.harvestparams.pointlong_EW.value=='E'){
			document.harvestparams.pointlong.value = Math.abs(parseFloat(document.harvestparams.pointlong.value));
		}
		else{
			document.harvestparams.pointlong.value = -1*Math.abs(parseFloat(document.harvestparams.pointlong.value));
		}
	}
}

function updateRadius(){
	var radiusUnits = document.getElementById("radiusunits").value;
	var radiusInMiles = document.getElementById("radiustemp").value;
	if(radiusUnits == "km"){
		radiusInMiles = radiusInMiles*0.6214;
	}
	document.getElementById("radius").value = radiusInMiles;
}

function checkHarvestParamsForm(frm){
	//make sure they have filled out at least one field.
	if((frm.taxa.value == '') && (frm.country.value == '') && (frm.state.value == '') && (frm.county.value == '') &&
		(frm.local.value == '') && (frm.elevlow.value == '') && (frm.upperlat.value == '') && (frm.pointlat.value == '') &&
		(frm.collector.value == '') && (frm.collnum.value == '') && (frm.eventdate1.value == '') && (frm.catnum.value == '') &&
		(frm.typestatus.checked == false) && (frm.hasimages.checked == false) && (frm.hasgenetic.checked == false)){
		alert("Please fill in at least one search parameter!");
		return false;
	}

	if(frm.upperlat.value != '' || frm.bottomlat.value != '' || frm.leftlong.value != '' || frm.rightlong.value != ''){
		// if Lat/Long field is filled in, they all should have a value!
		if(frm.upperlat.value == '' || frm.bottomlat.value == '' || frm.leftlong.value == '' || frm.rightlong.value == ''){
			alert("Error: Please make all Lat/Long bounding box values contain a value or all are empty");
			return false;
		}

		// Check to make sure lat/longs are valid.
		if(Math.abs(frm.upperlat.value) > 90 || Math.abs(frm.bottomlat.value) > 90 || Math.abs(frm.pointlat.value) > 90){
			alert("Latitude values can not be greater than 90 or less than -90.");
			return false;
		}
		if(Math.abs(frm.leftlong.value) > 180 || Math.abs(frm.rightlong.value) > 180 || Math.abs(frm.pointlong.value) > 180){
			alert("Longitude values can not be greater than 180 or less than -180.");
			return false;
		}
		if(parseFloat(frm.upperlat.value) < parseFloat(frm.bottomlat.value)){
			alert("Your northern latitude value is less then your southern latitude value. Please correct this.");
			return false;
		}
		if(parseFloat(frm.leftlong.value) > parseFloat(frm.rightlong.value)){
			alert("Your western longitude value is greater then your eastern longitude value. Please correct this. Note that western hemisphere longitudes in the decimal format are negitive.");
			return false;
		}
	}

	//Same with point radius fields
	if(frm.pointlat.value != '' || frm.pointlong.value != '' || frm.radius.value != ''){
		if(frm.pointlat.value == '' || frm.pointlong.value == '' || frm.radius.value == ''){
			alert("Error: Please make all Lat/Long point-radius values contain a value or all are empty");
			return false;
		}
	}

	return true;
}

function setHarvestParamsForm(){
	if(sessionStorage.querystr){
		var urlVar = parseUrlVariables(sessionStorage.querystr);
		var frm = document.harvestparams;
		
		if(typeof urlVar.usethes !== 'undefined' && (urlVar.usethes == "" || urlVar.usethes == "0")){frm.usethes.checked = false;}
		if(urlVar.taxontype){frm.type.value = urlVar.taxontype;}
		if(urlVar.taxa){frm.taxa.value = urlVar.taxa;}
		if(urlVar.country){
			countryStr = urlVar.country;
			countryArr = countryStr.split(";");
			if(countryArr.indexOf('USA') > -1 || countryArr.indexOf('usa') > -1) countryStr = countryArr[0];
			//if(countryStr.indexOf('United States') > -1) countryStr = 'United States';
			frm.country.value = countryStr;
		}
		if(urlVar.state){frm.state.value = urlVar.state;}
		if(urlVar.county){frm.county.value = urlVar.county;}
		if(urlVar.local){frm.local.value = urlVar.local;}
		if(urlVar.elevlow){frm.elevlow.value = urlVar.elevlow;}
		if(urlVar.elevhigh){frm.elevhigh.value = urlVar.elevhigh;}
		if(urlVar.llbound){
			var coordArr = urlVar.llbound.split(';');
			frm.upperlat.value = coordArr[0];
			frm.bottomlat.value = coordArr[1];
			frm.leftlong.value = coordArr[2];
			frm.rightlong.value = coordArr[3];
		}
		if(urlVar.llpoint){
			var coordArr = urlVar.llpoint.split(';');
			frm.pointlat.value = coordArr[0];
			frm.pointlong.value = coordArr[1];
			frm.radiustemp.value = coordArr[2];
			frm.radius.value = coordArr[2]*0.6214;
		}
		if(urlVar.collector){frm.collector.value = urlVar.collector;}
		if(urlVar.collnum){frm.collnum.value = urlVar.collnum;}
		if(urlVar.eventdate1){frm.eventdate1.value = urlVar.eventdate1;}
		if(urlVar.eventdate2){frm.eventdate2.value = urlVar.eventdate2;}
		if(urlVar.catnum){frm.catnum.value = urlVar.catnum;}
		//if(!urlVar.othercatnum){frm.includeothercatnum.checked = false;}
		if(typeof urlVar.typestatus !== 'undefined'){frm.typestatus.checked = true;}
		if(typeof urlVar.hasimages !== 'undefined'){frm.hasimages.checked = true;}
		if(typeof urlVar.hasgenetic !== 'undefined'){frm.hasgenetic.checked = true;}
	}
}

function parseUrlVariables(varStr) {
	var result = {};
	varStr.split("&").forEach(function(part) {
		if(!part) return;
		part = part.split("+").join(" "); 
		var eq = part.indexOf("=");
		var key = eq>-1 ? part.substr(0,eq) : part;
		var val = eq>-1 ? decodeURIComponent(part.substr(eq+1)) : "";
		result[key] = val;
	});
	return result;
}

function resetHarvestParamsForm(f){
	sessionStorage.removeItem('querystr');
}

function openPointRadiusMap() {
	mapWindow=open("mappointradius.php","pointradius","resizable=0,width=700,height=630,left=20,top=20");
	if (mapWindow.opener == null) mapWindow.opener = self;
	mapWindow.focus();
}

function openBoundingBoxMap() {
	mapWindow=open("mapboundingbox.php","boundingbox","resizable=0,width=700,height=630,left=20,top=20");
	if (mapWindow.opener == null) mapWindow.opener = self;
	mapWindow.focus();
}