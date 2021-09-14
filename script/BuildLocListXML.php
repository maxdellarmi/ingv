<?php
//Called by BuildWholeSite
//XML to populate home page GMAP
echo "Building LOC Details PHP\n======================\n";
function BuildLocListXML($ConnString){
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }

	$XMLLocList = new DomDocument('1.0', 'UTF-8');
	$XMLLocList->preserveWhiteSpace = true;
	$XMLLocList->formatOutput = true;
	
	$root = $XMLLocList->createElement('Locs');
	$XMLLocList->appendChild($root);

	$LocListQuery = mysqli_query($DBConn, "SELECT nloc_cfti, desloc_cfti, provlet, nazione, risentimenti, ee, maxint, lat_wgs84, lon_wgs84, notesito FROM locind ORDER BY desloc_cfti");

	
	while($Row = mysqli_fetch_assoc($LocListQuery)){
		$elem = $XMLLocList->createElement('Loc');
		
			foreach($Row as $key => $value){
			$child = $XMLLocList->createElement($key);
			$child = $elem->appendChild($child);
			$value = $XMLLocList->createTextNode(trim($value));
			$value = $child->appendChild($value);					
			}
			
		$root->appendChild($elem);
	}

	$XMLLocList->save('../LocList.xml');
	mysqli_close($DBConn);
}
?>
