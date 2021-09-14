<?php
//Called by BuildWholeSite
//XML with all Quake info
//EXPORT PER B2 OB.3 Lucia Margheriti 

function BuildQuakeDetailXMLCOMM($ConnString){

	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }

	$QuakeDetailQuery = mysqli_query($DBConn, "SELECT cat, nterr, nperiod, datanum, data_label, anno, mese, giorno, time_label, ora, minu, sec, lat, lon, rel, io, imax, npun, mm, earthquakelocation, epicenter_type, country, flagcomments FROM nterrs order by nterr");
	
	while($Detail = mysqli_fetch_assoc($QuakeDetailQuery)){
		$flagcomm = $Detail['flagcomments'];
		
		$XMLSource = "../XML_COMM/{$Detail['nterr']}_COMM.xml";
		
		$XMLQuakeDetail = new DomDocument('1.0', 'UTF-8');
		$XMLQuakeDetail->preserveWhiteSpace = true;
		$XMLQuakeDetail->formatOutput = true;

		$root = $XMLQuakeDetail->createElement('Quake');
		$XMLQuakeDetail->appendChild($root);
		$root->setAttribute('alone', 'true');
		
		$Details = $XMLQuakeDetail->createElement('Details');
		$root->appendChild($Details);

		$Comments = $XMLQuakeDetail->createElement('Comments');
		$root->appendChild($Comments);

		

		// Quake Detail
		$elem = $XMLQuakeDetail->createElement('Detail');
		foreach($Detail as $key => $value){
			$child = $XMLQuakeDetail->createElement($key);
			$child = $elem->appendChild($child);

			$value = $XMLQuakeDetail->createTextNode(trim($value));
			$value = $child->appendChild($value);
		}

		$Details->appendChild($elem);

		//Comments
		
		$elemA = $XMLQuakeDetail->createElement('effects');
		$child = $XMLQuakeDetail->createElement('description');
		$child = $elemA->appendChild($child);
		
		if ($flagcomm == 4 or $flagcomm == 5) {
			$CommentDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
			 if ($CommentDB ->connect_error) {
				die('Errore di connessione (' . $mysqli->connect_errno . ') '
				. $CommentDB ->connect_error);
			} else {
				echo 'Connesso. ' . $DBConn ->host_info . "\n";
			}

				
			$CommentsQuery = mysqli_query($CommentDB, "select codtab, testocomm from commgen where nperiod = '{$Detail['nperiod']}'  order by codtab");
						
			
			$TEST = 0;
				while($Row = mysqli_fetch_assoc($CommentsQuery)){
					
					$codComm = $Row['codtab'];
					if ($codComm  == 'D0' ) {
						$value = '<b>Maggiori effetti del PERIODO SISMICO:</b> <br> ';
						$value = $XMLQuakeDetail->createTextNode($value);
						$value = $child->appendChild($value);
						
						$value = $Row['testocomm'];
					
						$value = $XMLQuakeDetail->createTextNode($value);
						$value = $child->appendChild($value);
					} 

					if ($codComm  == 'F0' ) {
						$TEST = 1;
						$child = $XMLQuakeDetail->createElement('sequence');
						$child = $elemA->appendChild($child);
						echo 'SEQUENZA';
						$value = '<b>Sequenza delle maggiori scosse del PERIODO SISMICO:</b> <br> ';
						$value = $XMLQuakeDetail->createTextNode($value);
						$value = $child->appendChild($value);
						
						$value = $Row['testocomm'];
					
						$value = $XMLQuakeDetail->createTextNode($value);
						$value = $child->appendChild($value);
					} 
					
					if ($codComm  == 'F1' ) {
						if ($TEST == 0) {
							$child = $XMLQuakeDetail->createElement('sequence');
							$child = $elemA->appendChild($child);
							$value = '<b>Sequenza del PERIODO SISMICO:</b> <br> ';
						// echo '0';	
						} else {
							$value = '<br><br><b>Sequenza del PERIODO SISMICO:</b> <br> ';
						// echo $TEST ;	
						}
						echo 'SEQUENZA 2';
						
						$value = $XMLQuakeDetail->createTextNode($value);
						$value = $child->appendChild($value);
						
						$value = $Row['testocomm'];
					
						$value = $XMLQuakeDetail->createTextNode($value);
						$value = $child->appendChild($value);
					} 
				
			}
			mysqli_close($CommentDB);
			
			
		} else {
			// echo 'Per questo PERIODO SISMICO non è al momento disponibile la sintesi dei maggiori effetti';
			$value = '<b>Per questo PERIODO SISMICO non è al momento disponibile la sintesi dei maggiori effetti</b>';
			$value = $XMLQuakeDetail->createTextNode($value);
			$value = $child->appendChild($value);
		}
		
		$Comments->appendChild($elemA);
		
			$XMLQuakeDetail->save($XMLSource);
			echo "$XMLSource \n";
		}
		

	
	mysqli_close($DBConn);
}


?>
