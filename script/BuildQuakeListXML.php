<?php
//Called by BuildWholeSite
//XML to populate home page GMAP
echo "Entering Quake List\n======================\n";
function BuildQuakeListXML($ConnString){
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }

	$XMLQuakeList = new DomDocument('1.0', 'UTF-8');
	$XMLQuakeList->preserveWhiteSpace = true;
	$XMLQuakeList->formatOutput = true;

	$root = $XMLQuakeList->createElement('Quakes');
	$XMLQuakeList->appendChild($root);

	$QuakeListQuery = mysqli_query($DBConn,"SELECT nterr, nperiod, anno, mese, giorno, data_label, ora, minu, sec, time_label, lat, lon, earthquakelocation, country, epicenter_type, io, imax, mm, npun, ee_nt, ee_np, level, rel, flagfalseeq, new2018, cat FROM nterrs");
	while($Row = mysqli_fetch_assoc($QuakeListQuery)){
		$elem = $XMLQuakeList->createElement('Quake');

		foreach($Row as $key => $value){
			$child = $XMLQuakeList->createElement($key);
			$child = $elem->appendChild($child);
			
			$value = $XMLQuakeList->createTextNode(trim($value));
			$value = $child->appendChild($value);
		}

		$root->appendChild($elem);
	}
	$QuakeListQuery = mysqli_query($DBConn,"SELECT nterr, nperiod, anno, mese, giorno, data_label, ora, minu, sec, time_label, lat, lon, earthquakelocation, country, epicenter_type, io, imax, mm, npun, ee_nt, ee_np, level, rel, flagfalseeq, cat FROM nterrs_med");
	while($Row = mysqli_fetch_assoc($QuakeListQuery)){
		$elem = $XMLQuakeList->createElement('Quake');

		foreach($Row as $key => $value){
			$child = $XMLQuakeList->createElement($key);
			$child = $elem->appendChild($child);
			
			$value = $XMLQuakeList->createTextNode(trim($value));
			$value = $child->appendChild($value);
		}

		$root->appendChild($elem);
	}
	
	$XMLQuakeList->save('../QuakeList.xml');
	mysqli_close($DBConn);
}
?>
