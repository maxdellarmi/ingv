<?php
//Called by BuildWholeSite
//XML with all Quake info

global $DBConn;

function BuildEEDetailXML($ConnString){
	
	//ITA
	$XMLSource = "../EEList.xml";
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }
		
		$XMLEEDetail = new DomDocument('1.0', 'UTF-8');
		$XMLEEDetail->preserveWhiteSpace = true;
		$XMLEEDetail->formatOutput = true;

		$root = $XMLEEDetail->createElement('EELIST');
		$XMLEEDetail->appendChild($root);
		$root->setAttribute('alone', 'true');
		
	$EEDetailQuery = mysqli_query($DBConn, "SELECT * FROM EE order by nperiod");

		while($Row = mysqli_fetch_assoc($EEDetailQuery)){

				$elem = $XMLEEDetail->createElement('EE');

				foreach($Row as $key => $value){
					$child = $XMLEEDetail->createElement($key);
					$child = $elem->appendChild($child);

					$value = $XMLEEDetail->createTextNode($value);
					$value = $child->appendChild($value);
				}

				$root->appendChild($elem);

			}
			mysqli_close($DBConn);	

	$XMLEEDetail->save($XMLSource);	
		
	//MED 
	$elem = "";
	$XMLSource2 = "../EEList_MED.xml";
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }
	
		$XMLEE_MEDDetail = new DomDocument('1.0', 'UTF-8');
		$XMLEE_MEDDetail->preserveWhiteSpace = true;
		$XMLEE_MEDDetail->formatOutput = true;
		$root2 = $XMLEE_MEDDetail->createElement('EELIST');
		$XMLEE_MEDDetail->appendChild($root2);
		$root2->setAttribute('alone', 'true');
	$EEDetailQueryM = mysqli_query($DBConn, "SELECT * FROM EE_MED order by nperiod");

		while($Row = mysqli_fetch_assoc($EEDetailQueryM)){
				$elem = $XMLEE_MEDDetail->createElement('EE_MED');

				foreach($Row as $key => $value){
					$child = $XMLEE_MEDDetail->createElement($key);
					$child = $elem->appendChild($child);

					$value = $XMLEE_MEDDetail->createTextNode($value);
					$value = $child->appendChild($value);
				}
				$root2->appendChild($elem);
			}
			mysqli_close($DBConn);	
	$XMLEE_MEDDetail->save($XMLSource2);	
	
	
	//Biblio EE
	$elem = "";
	$XMLSource3 = "../BiblioEE.xml";
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }
		$XMLEE_BibDetail = new DomDocument('1.0', 'UTF-8');
		$XMLEE_BibDetail->preserveWhiteSpace = true;
		$XMLEE_BibDetail->formatOutput = true;
		$root3 = $XMLEE_BibDetail->createElement('EELIST');
		$XMLEE_BibDetail->appendChild($root3);
		$root3->setAttribute('alone', 'true');
		
		$BibliographyQueryB = mysqli_query($DBConn, "SELECT codbib FROM BIBLIO_EE");
		
		while($Row = mysqli_fetch_assoc($BibliographyQueryB)){
			
			$BibliographyQuery = mysqli_query($DBConn, "SELECT codbib, autore1,titolo1, luogoed,datauni,dataun2 FROM schede_a WHERE codbib = '{$Row['codbib']}' ");
			$elem = $XMLEE_BibDetail->createElement('BIBLIO_EE');
			$Row2 = mysqli_fetch_assoc($BibliographyQuery);
			$data_1 = $Row2['datauni'];
			$data_2 = $Row2['dataun2'];
			if ($data_1 == '0009') {$data_1 = 'IX sec.';};
			if ($data_2 == '0009') {$data_2 = 'IX sec.';};
			if ($data_1 == '0010') {$data_1 = 'X sec.';};
			if ($data_2 == '0010') {$data_2 = 'X sec.';};
			if ($data_1 == '0011') {$data_1 = 'XI sec.';};
			if ($data_2 == '0011') {$data_2 = 'XI sec.';};
			if ($data_1 == '0012') {$data_1 = 'XII sec.';};
			if ($data_2 == '0012') {$data_2 = 'XII sec.';};
			if ($data_1 == '0013') {$data_1 = 'XIII sec.';};
			if ($data_2 == '0013') {$data_2 = 'XIII sec.';};
			if ($data_1 == '0014') {$data_1 = 'XIV sec.';};
			if ($data_2 == '0014') {$data_2 = 'XIV sec.';};
			if ($data_1 == '0015') {$data_1 = 'XV sec.';};
			if ($data_2 == '0015') {$data_2 = 'XV sec.';};
			if ($data_1 == '0016') {$data_1 = 'XVI sec.';};
			if ($data_2 == '0016') {$data_2 = 'XVI sec.';};
			if ($data_1 == '0017') {$data_1 = 'XVII sec.';};
			if ($data_2 == '0017') {$data_2 = 'XVII sec.';};
			if ($data_1 == '0018') {$data_1 = 'XVIII sec.';};
			if ($data_2 == '0018') {$data_2 = 'XVIII sec.';};
			if ($data_1 == '0019') {$data_1 = 'XIX sec.';};
			if ($data_2 == '0019') {$data_2 = 'XIX sec.';};
			if ($data_1 == '0020') {$data_1 = 'XX sec.';};
			if ($data_2 == '0020') {$data_2 = 'XX sec.';};	
			if ($data_1 == '0021') {$data_1 = 'XXI sec.';};
			if ($data_2 == '0021') {$data_2 = 'XXI sec.';};				
			
			if ($data_2=='') {
				$datacompl = $data_1;
			} else {
				$datacompl = $data_1 . " - " . $data_2;
			};
			
			$child = $XMLEE_BibDetail->createElement('codbib');
			$child = $elem->appendChild($child);

			$value = $XMLEE_BibDetail->createTextNode($Row2['codbib']);
			$value = $child->appendChild($value);
			
			$child = $XMLEE_BibDetail->createElement('autore1');
			$child = $elem->appendChild($child);

			$value = $XMLEE_BibDetail->createTextNode($Row2['autore1']);
			$value = $child->appendChild($value);
			
			$child = $XMLEE_BibDetail->createElement('titolo1');
			$child = $elem->appendChild($child);

			$value = $XMLEE_BibDetail->createTextNode($Row2['titolo1']);
			$value = $child->appendChild($value);
			
			$child = $XMLEE_BibDetail->createElement('luogoed');
			$child = $elem->appendChild($child);

			$value = $XMLEE_BibDetail->createTextNode($Row2['luogoed']);
			$value = $child->appendChild($value);
			
			$child = $XMLEE_BibDetail->createElement('datacompl');
			$child = $elem->appendChild($child);

			$value = $XMLEE_BibDetail->createTextNode($datacompl);
			$value = $child->appendChild($value);
			

			$root3->appendChild($elem);
		}
		mysqli_close($DBConn);
	$XMLEE_BibDetail->save($XMLSource3);	
}	




?>
