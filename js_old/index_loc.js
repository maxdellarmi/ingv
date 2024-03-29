var xmlServiceLoc = 'LocList.xml';
var NumLoc;

var flagFilterLOC = 0; // questo serve per distinguere la prima volta da quelle in cui si fa un filtro col menu (click su ok)

var nloc=[];
var descloc=[];
var prov=[];
var country=[];
var descloc_prov=[];
var arrayLoc = [];
var flagProv=[];
var ris=[];
var maxint=[];
var maxintROM=[];
var locLat=[];
var locLon=[];
var noteLoc = [];
var EEnum= [];

var NlocOld;

var LOCMarkers = [];

function requestLocData(){
	var mySelf = this;
	var callBackBlock;

	var ajaxUpdater = new Manajax(xmlServiceLoc);
		ajaxUpdater.TxType = 'GET';
		ajaxUpdater.responseType = 'xml';
		this.callBackBlock = 'map';
		ajaxUpdater.callBackFunc = this.parseLocData;
		ajaxUpdater.toScroll = false;
		ajaxUpdater.requestAction();
}

function parseLocData(XmlText){

	XMLQuakeList = new DOMParser().parseFromString(XmlText.trim(), 'text/xml');
	XMLQuakeListArrived = true;

	var locs = XMLQuakeList.documentElement.getElementsByTagName("Loc");

	// ---- EXPORT
	var d = new Date();
	filename = 'CFTI5_LocList_selection_' + d.getFullYear() + '_' + (Number(d.getMonth())+1) + '_' + d.getDate() + '_' + d.getHours() + '_' + d.getMinutes() + '_' + d.getSeconds() ;
	ExportText = "Name;Italian Province or Country;NMO (Number of Macroseismic Observations at site);NEE (Number of Effects on natural environment at site);Ismax (Maximum MCS Intensity reported at site);Ismax R. (Maximum MCS Intensity reported at site - Roman Numerals);Latitude;Longitude"


	if(locs.length > 0){

		for (var i = 0; i < locs.length; i++){

			 nloc[i] = String(locs[i].getElementsByTagName("nloc_cfti")[0].childNodes[0].nodeValue);
			 descloc[i] = locs[i].getElementsByTagName("desloc_cfti")[0].childNodes[0].nodeValue;
			var flagProv = locs[i].getElementsByTagName("provlet")[0].childNodes.length;
			if (flagProv > 0) {
				 prov[i] = locs[i].getElementsByTagName("provlet")[0].childNodes[0].nodeValue;
			} else {
				 prov[i] = ""
			};
			var flagCount = locs[i].getElementsByTagName("nazione")[0].childNodes.length;
			if (flagCount > 0) {
				 country[i] = locs[i].getElementsByTagName("nazione")[0].childNodes[0].nodeValue;
			} else {
				 country[i] = ""
			};
			if (prov[i] != "")  descloc_prov[i] = descloc[i] + ' (' + prov[i] + ')';
			else descloc_prov[i] = descloc[i] + ' (' + country[i] + ')';


			 ris[i] = locs[i].getElementsByTagName("risentimenti")[0].childNodes[0].nodeValue;
			 EEnum[i] = locs[i].getElementsByTagName("ee")[0].childNodes[0].nodeValue;


			 maxint[i] = parseFloat(locs[i].getElementsByTagName("maxint")[0].childNodes[0].nodeValue);
			 if (maxint[i] == 11){
				 maxintROM[i] = "XI";
			 } else if (maxint[i] == 10.5){
				 maxintROM[i] = "XI-X";
			 }else if (maxint[i] == 10){
				 maxintROM[i] = "X";
			 } else if (maxint[i] == 9.5){
				 maxintROM[i] = "IX-X";
			 }else if (maxint[i] == 9.1){
				 maxint[i] = 9;
				 maxintROM[i] = "IX";
			 } else if ( maxint[i] == 9){
				 maxintROM[i] = "IX";
			 } else if ( maxint[i] == 8.5){
				 maxintROM[i] = "VIII-IX";
			 } else if (maxint[i] == 8.2){
				 maxint[i] = 8;
				 maxintROM[i] = "VIII";
			 } else if (maxint[i] == 8.1){
				 maxint[i] = 8;
				 maxintROM[i] = "VIII";
			 } else if (maxint[i] == 8){
				 maxintROM[i] = "VIII";
			 } else if (maxint[i] == 7.5){
				 maxintROM[i] = "VII-VIII";
			 } else if (maxint[i] == 7){
				 maxintROM[i] = "VII";
			 } else if (maxint[i] == 6.5){
				 maxintROM[i] = "VI-VII";
			 } else if (maxint[i] == 6.1) {
				 maxint[i] = 6;
				 maxintROM[i] = "VI";
			 } else if (maxint[i] == 6.6) {
				 maxint[i] = 6.5;
				 maxintROM[i] = "VI-VII";
			 } else if (maxint[i] == 6){
				 maxintROM[i] = "VI";
			 } else if (maxint[i] == 5.5){
				 maxintROM[i] = "V-VI";
			 } else if (maxint[i] == 5.1) {
				 maxint[i] = 5;
				 maxintROM[i] = "V";
			 } else if (maxint[i] == 5){
				 maxintROM[i] = "V";
			 } else if (maxint[i] == 4.6) {
				 maxint[i] = 4.5;
				 maxintROM[i] = "IV-V";
			 } else if (maxint[i] == 4.5) {
				 maxintROM[i] = "IV-V";
			 } else if (maxint[i] == 4){
				 maxintROM[i] = "IV";
			 } else if (maxint[i] == 3.5){
				 maxintROM[i] = "III-IV";
			 } else if (maxint[i] == 3) {
				 maxintROM[i] = "III";
			 } else if (maxint[i] == 2.5) {
				 maxintROM[i] = "II-III";
			 } else if (maxint[i] == 2) {
				 maxintROM[i] = "II";
			 } else if (maxint[i] == 1) {
				 maxintROM[i] = "I";
			 } else if (maxint[i] == 0.2) {
				 maxintROM[i] = "G";
			 } else if (maxint[i] == 0) {
				 maxintROM[i] = "NF";
			 } else if (maxint[i] == 0.1) {
				 maxintROM[i] = "N";
			 } else if (maxint[i] == -1) {
				 maxintROM[i] = "NC";
			 } else if (maxint[i] == -2) {
				 maxintROM[i] = "-";
			 }

			 // se 0 risentimenti (solo EE), maxint = '-'

			 locLat[i] = parseFloat(locs[i].getElementsByTagName("lat_wgs84")[0].childNodes[0].nodeValue).toFixed(3);
			 locLon[i] = parseFloat(locs[i].getElementsByTagName("lon_wgs84")[0].childNodes[0].nodeValue).toFixed(3);

			var flagNote = locs[i].getElementsByTagName("notesito")[0].childNodes.length;
			if (flagNote > 0) {
				 noteLoc[i] =  locs[i].getElementsByTagName("notesito")[0].childNodes[0].nodeValue + '<br>';
			} else {
				 noteLoc[i] = ""
			};
			//var per ricerca per località

		}
		createTableandPlot({
			StartImax: parseFloat(document.getElementById('StartImax').value),
			StopImax: parseFloat(document.getElementById('StopImax').value),
		});
	}

}

function createTableandPlot(Filters){

	var IntFlag = false;

	clearMap();

	var tbody = document.getElementById('Loc_data');
	tbody.innerHTML = ""

	var imarker = 0;
    var s = 0;
    arrayLoc = [];
	ExportKmlR = '';

	for (var i = 0; i < descloc.length; i++){

		if (EEnum[i]==0) {
			if (maxint[i] >= 11) {var icon = {url: "images/IS/11.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "11"}
			if (maxint[i] <= 10.9 && maxint[i] > 9.9) {var icon = {url: "images/IS/10.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "10"}
			if (maxint[i] <= 9.9 && maxint[i] > 8.9) {var icon = {url: "images/IS/9.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "9"}
			if (maxint[i] <= 8.9 && maxint[i] > 7.9) {var icon = {url: "images/IS/8.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "8"}
			if (maxint[i] <= 7.9 && maxint[i] > 6.9) {var icon = {url: "images/IS/7.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "7"}
			if (maxint[i] <= 6.9 && maxint[i] > 5.9 ) {var icon = {url: "images/IS/6.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "6"}
			if (maxint[i] <= 5.9 && maxint[i] > 4.9) {var icon = {url: "images/IS/5.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "5"}
			if (maxint[i] <= 4.9 && maxint[i] > 3.9) {var icon = {url: "images/IS/4.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "4"}
			if (maxint[i] <= 3.9 ) {var icon = {url: "images/IS/3.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "3"}
		} else if (EEnum[i]>0 && ris[i]>0){
			if (maxint[i] >= 11) {var icon = {url: "images/IS/11EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "11EE"}
			if (maxint[i] <= 10.9 && maxint[i] > 9.9) {var icon = {url: "images/IS/10EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "10EE"}
			if (maxint[i] <= 9.9 && maxint[i] > 8.9) {var icon = {url: "images/IS/9EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "9EE"}
			if (maxint[i] <= 8.9 && maxint[i] > 7.9) {var icon = {url: "images/IS/8EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "8EE"}
			if (maxint[i] <= 7.9 && maxint[i] > 6.9) {var icon = {url: "images/IS/7EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "7EE"}
			if (maxint[i] <= 6.9 && maxint[i] > 5.9 ) {var icon = {url: "images/IS/6EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "6EE"}
			if (maxint[i] <= 5.9 && maxint[i] > 4.9) {var icon = {url: "images/IS/5EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "5EE"}
			if (maxint[i] <= 4.9 && maxint[i] > 3.9) {var icon = {url: "images/IS/4EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "4EE"}
			if (maxint[i] <= 3.9 ) {var icon = {url: "images/IS/3EE.png", scaledSize: new google.maps.Size(10, 10)}; IsIcon = "3EE"}
		} else {
			var icon = {url: "images/IS/EE.png", scaledSize: new google.maps.Size(10, 10)}
			IsIcon = "EE"
		}

		var markerLOC = new google.maps.Marker({
			position: new google.maps.LatLng(locLat[i], locLon[i]),
			map: null,
			icon: icon,

		});
		LOCMarkers.push(markerLOC);

		// Information box that pops up on click (on marker or line of quakes table)
		var titleIWloc = '<div class="iw-title localityColor"><b>' + descloc_prov[i] + '</b><br /><p><i>' + noteLoc[i] + '</i></p></div>'
		if (ris[i] != 0){
			var intEN = '<br /> Maximum MCS Intensity: <b>' + maxintROM[i] + '</b>';
			var intIT = '<br /> Massima intensità MCS: <b>' + maxintROM[i] + '</b>';
		} else {
			var intEN = '<br /> Maximum MCS Intensity: <b>' + '-' + '</b>';
			var intIT = '<br /> Massima intensità MCS: <b>' + '-' + '</b>';
		}

		var eeEN = '<br /> Number of Effects on the natural environment: <b>' + EEnum[i] + '</b>';
		var eeIT = '<br /> Numero di Effetti sismo-indotti sull\'ambiente naturale: <b>' + EEnum[i] + '</b>';
		var nomEN = '<br /> Number of Macroseismic Observations: <b>' + ris[i] + '</b>';
		var nomIT = '<br /> Numero di Osservazioni Macrosismiche: <b>' + ris[i] + '</b>';

		var OnClickTextEN = [
			'<div class="IW"><div id = "IWclose"><a onclick="infowindow.close(); turnoffRow()" href="#"><img src="images/close.png" height="10px"></a></div>',
			titleIWloc, '<div class="commentsIW">', intEN, nomEN, eeEN + '<br /><br />', '<a href="locality.php?'+nloc[i]+'EN" target="_blank">Locality page </a>',
			'</div></div>'
		].join('\n');

		var OnClickTextIT = [
			'<div class="IW"><div id = "IWclose"><a onclick="infowindow.close(); turnoffRow()" href="#"><img src="images/close.png" height="10px"></a></div>',
	titleIWloc, '<div class="commentsIW">',intIT, nomIT, eeIT + '<br /><br />', '<a href="locality.php?'+nloc[i]+'IT" target="_blank">Pagina della località</a>',
			'</div></div>'
		].join('\n');

		openPopupLOC(markerLOC, OnClickTextEN, OnClickTextIT, nloc[i])


		if(Filters['StartImax']==0) Filters['StartImax'] = -2
		IntFlag = ( Filters['StartImax'] <= maxint[i] && Filters['StopImax'] >= maxint[i]) ? true : false;


		if ( IntFlag ){

			var row = document.createElement("tr");
			row.setAttribute('id', nloc[i]);

			var cell1 = document.createElement("td");
			cell1.setAttribute('class', 'nameLOC');
			if (descloc[i].length>24) cell1.innerHTML = '<abbr title= "'+ descloc[i] + '"><a onclick=onclickListLOC('+ i +') href="#">' + descloc[i].substring(0, 24) + '...' + '</a></abbr>'
			else cell1.innerHTML =  '<a onclick=onclickListLOC('+ i +') href="#">' + descloc[i] + '</a>';

			var cell2 = document.createElement('td');
			cell2.setAttribute('class', 'provLOC');
			// if (prov[i].length>6) prov[i] = '<abbr title= "'+ prov[i] + '">' + prov[i].substring(0, 6) + '...' + '</abbr>'
			var provcoun ="";
			if (prov[i] != "") {
				provcoun = prov[i];
			} else {
				provcoun = country[i];
			};
			cell2.innerHTML = provcoun;

			var cell3 = document.createElement('td');
			cell3.setAttribute('class', 'sites');
			cell3.innerHTML = ris[i];

			var cell4 = document.createElement('td');
			cell4.setAttribute('class', 'EEnum');
			cell4.innerHTML = EEnum[i];

			var cell5 = document.createElement('td');
			cell5.setAttribute('class', 'imax');
			cell5.setAttribute('data-text', maxint[i]);
		 	cell5.innerHTML = maxintROM[i];

			var cell6 = document.createElement('td');
			cell6.setAttribute('class', 'latLOC');
			cell6.innerHTML = locLat[i];

			var cell7 = document.createElement('td');
			cell7.setAttribute('class', 'lonLOC');
			cell7.innerHTML = locLon[i];

			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			row.appendChild(cell5);
			row.appendChild(cell6);
			row.appendChild(cell7)
			tbody.appendChild(row);

			var maxintN;
			if (maxint[i] < 1) {
				maxintN = 0;
			} else {
				maxintN = maxint[i];
			}

			var maxintR;
			switch (maxintROM[i]) {
				case "NF":
					maxintR ="NF (not felt)";
					break;
				case "N":
					maxintR ="N (no evidence found in contemporary sources)";
					break;
				case "NC":
					maxintR ="NC (unrated)";
					break;
				case "-":
					maxintR ="no macroseismic observations available";
					break;
				default:
					maxintR =maxintROM[i];
			}

			ExportText = ExportText + CarRet + descloc[i] + ';' + provcoun + ';' + ris[i] + ';' + EEnum[i] + ";" + maxintN + ';' + maxintR+ ";" + locLat[i] + ';' + locLon[i]

			ExportKmlR = ExportKmlR + "<Placemark> <name>" + descloc[i] + " (" + provcoun + ")</name>" + CarRet + "<description><![CDATA["
			ExportKmlR = ExportKmlR + "<b>" +  descloc[i] + " (" + provcoun + ")</b><br><br>Latitude: <b>" + locLat[i] + "</b> <br>Longitude: <b>" + locLon[i] + "</b> <br><br>Maximum MCS Intensity reported: <b>" + maxintR + "</b> <br>Number of Macroseismic Observations: <b>" + ris[i] + "</b> <br>Number of Effects on natural environment: <b>" + EEnum[i] + "</b><br><br><b><a href=" + virg + "http://storing.ingv.it/cfti/cfti5/locality.php?" + nloc[i] + "EN" + virg + ">Locality page </a></b>"
			ExportKmlR = ExportKmlR + "]]></description>" + CarRet + "<LookAt>" + CarRet + "<longitude>" + locLon[i] + "</longitude>" + CarRet + "<latitude>" + locLat[i] + "</latitude>" + CarRet + "<range></range>" + CarRet + "<tilt></tilt>" + CarRet + "<heading></heading>" + CarRet + "</LookAt>" + CarRet + "<styleUrl>" + IsIcon + "</styleUrl>" + CarRet + "<Point>" + CarRet + "<coordinates>"+ locLon[i] + ","+ locLat[i] + "</coordinates>" + CarRet + "</Point>" + CarRet + "</Placemark>"


			//var per ricerca per località
			arrayLoc[s] = descloc_prov[i];

			markerLOC.setMap(map)

			imarker ++
			s++
		}
	}

	ExportKml = "";
		jQuery.get('KML/locality_a.txt', function(data){
			ExportKml = data;
			ExportKml = ExportKml + "<name>CFTI5Med - " + iMarker + " localities selected</name>";
			ExportKml = ExportKml + "<open>1</open>";
			ExportKml = ExportKml + "<description>";
			ExportKml = ExportKml + "<![CDATA[<body><a href="+virg+"http://storing.ingv.it/cfti/cfti5/"+virg+"> <img src="+virg+"http://storing.ingv.it/cfti/cfti5/images/banner_CFTI_newG_thin_EN"+virg+" alt="+virg+"Logo CFTI5Med"+virg+" height="+virg+"32px"+virg+"></a></body>]]>";
			ExportKml = ExportKml + "</description>";
			ExportKml = ExportKml + "<visibility>1</visibility>";
			ExportKml = ExportKml + "<Folder><name>Localities</name>";

			ExportKml = ExportKml + ExportKmlR;

			jQuery.get('KML/locality_b.txt', function(dataB){
				ExportKml = ExportKml + dataB;
		})
	})



	// Table sorting
	if (flagFilterLOC == 0){
		$(document).ready(function() {
			// call the tablesorter plugin
			$("#Loc_info").tablesorter({
				//	sort on the first, second and third (order desc) column
				sortList: [[0,0],[2,0],[3,0]]
			});
		});
		flagFilterLOC = 1;
	} else {
		$('.tablesorter').trigger('updateAll');
	}


	NumLocSel = document.getElementById("NumSel");
	if (Langsel == "EN") NumLocSel.innerHTML = "<b>" + imarker + ' </b><span id="numLoc"> localities </span>';
	else NumLocSel.innerHTML = "<b>" + imarker + ' </b><span id="numLoc"> località</span>';

	$( function() {
		$( "#tags" ).autocomplete({
			source: arrayLoc,
			minLength: 3,
			select: function(event,ui){
				var selectedLOC = ui.item.label;
				var posSelLoc = descloc_prov.indexOf(selectedLOC);
				FlagSel = 1
				onclickListLOC(posSelLoc)
				FlagSel = 0
			}
		});
	});
	$('#loading').hide();

}



function openPopupLOC(marker, textEN, textIT, NlocI){
	google.maps.event.addListener(marker, 'click', function() {

		// specify language of popup window
		if (Langsel == "EN") {
			infowindow.setContent(textEN);
		} else {
			infowindow.setContent(textIT);
		}

		// open popup window
		infowindow.open(map, marker);

		var rows = document.getElementById(NlocI);
		// scroll to selected table row
		if (FlagScroll == 1){ // do it only if selection from map marker
			rows.scrollIntoView(false);
		}
		FlagScroll = 1;


		// turn off previously selected table row when clicking on new marker
		if (NlocOld) {
			var rowsOld = document.getElementById(NlocOld);
			rowsOld.style.backgroundColor = "#ffffff";
		}

		// highlight new table row
		rows.style.backgroundColor = "#ffffaa";
		NlocOld = NlocI;
	})
}

// When clicking on table row, trigger event on Gmap marker (used to trigger popup window when clicking on table row)
function onclickListLOC(prog){
	if (FlagSel == 1) {
		FlagScroll == 0
		} else {
		FlagScroll = 0
	};
	google.maps.event.trigger(LOCMarkers[prog], 'click');

	//center map on selected event (when selecting from table line)
	var center = new google.maps.LatLng(locLat[prog], locLon[prog]);
    map.panTo(center);

	map.setZoom(10);
	//map.fitBounds(bounds);

}
