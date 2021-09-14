<?php
//Called by BuildWholeSite
//XML with all Quake info

global $DBConn;

function BuildQuakeDetailXML($ConnString){
	
	//ITA

	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }

	$QuakeDetailQuery = mysqli_query($DBConn, "SELECT cat, nterr, nperiod, datanum, data_label, anno, mese, giorno, time_label, ora, minu, sec, lat, lon, rel, level, io, imax, npun, ee_nt, ee_np, mm, earthquakelocation, epicenter_type, country, flagcomments, flagfalseeq, new2018 FROM nterrs order by nterr");

	LoopQuake($QuakeDetailQuery);
	

		
		
	//MED
	
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }
	
	$QuakeDetailQuery = mysqli_query($DBConn, "SELECT cat, nterr, nperiod, datanum, data_label, anno, mese, giorno, time_label, ora, minu, sec, lat, lon, rel, level, io, imax, npun, ee_nt, ee_np, mm, earthquakelocation, epicenter_type, country, flagcomments, flagfalseeq FROM nterrs_med order by nterr");

	LoopQuake($QuakeDetailQuery);



}	


function LoopQuake($QuakeDetailQuery) {
	while($Detail = mysqli_fetch_assoc($QuakeDetailQuery)){
		$flagcomm = $Detail['flagcomments'];
		
		$XMLSource = "quakeSources/{$Detail['nterr']}.xml";
		
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

		$Biblios = $XMLQuakeDetail->createElement('Biblios');
		$root->appendChild($Biblios);

		$Localities = $XMLQuakeDetail->createElement('Localities');
		$root->appendChild($Localities);
		
		$LocComments = $XMLQuakeDetail->createElement('LocComments');
		$root->appendChild($LocComments);

		$Seqs = $XMLQuakeDetail->createElement('Seqs');
		$root->appendChild($Seqs);

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
		if ($flagcomm == 4 or $flagcomm == 5) {
			$CommentDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
			 if ($CommentDB ->connect_error) {
				die('Errore di connessione (' . $mysqli->connect_errno . ') '
				. $CommentDB ->connect_error);
			} else {
				echo 'Connesso. ' . $CommentDB ->host_info . "\n";
			}

			$CommentsQuery = mysqli_query($CommentDB, "select codtab, testocomm from commgen where nperiod = '{$Detail['nperiod']}' order by codtab");
			
			while($Row = mysqli_fetch_assoc($CommentsQuery)){
				if ( ($Row['codtab'] == 'D0' && $flagcomm == 5) || ($flagcomm == 4) || ($Row['codtab'] == 'E0')) {
				$elem = $XMLQuakeDetail->createElement('Comment');

				foreach($Row as $key => $value){
					$child = $XMLQuakeDetail->createElement($key);
					$child = $elem->appendChild($child);

					$value = $XMLQuakeDetail->createTextNode($value);
					$value = $child->appendChild($value);
				}

				$Comments->appendChild($elem);
				}
			}
			mysqli_close($CommentDB);
		}
		//Bibliography
		$BibliographyDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
		 if ($BibliographyDB ->connect_error) {
			die('Errore di connessione (' . $mysqli->connect_errno . ') '
			. $BibliographyDB ->connect_error);
		} else {
			echo 'Connesso. ' . $BibliographyDB ->host_info . "\n";
		}
		$BibliographyQueryB = mysqli_query($BibliographyDB, "SELECT codbib FROM schede_b WHERE nperiod = '{$Detail['nperiod']}' ");
		
		while($Row = mysqli_fetch_assoc($BibliographyQueryB)){
			
			$BibliographyQuery = mysqli_query($BibliographyDB, "SELECT codbib, autore1,titolo1, luogoed,datauni,dataun2 FROM schede_a WHERE codbib = '{$Row['codbib']}' ");
			$elem = $XMLQuakeDetail->createElement('Bibliography');
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
			
			$child = $XMLQuakeDetail->createElement('codbib');
			$child = $elem->appendChild($child);

			$value = $XMLQuakeDetail->createTextNode($Row2['codbib']);
			$value = $child->appendChild($value);
			
			$child = $XMLQuakeDetail->createElement('autore1');
			$child = $elem->appendChild($child);

			$value = $XMLQuakeDetail->createTextNode($Row2['autore1']);
			$value = $child->appendChild($value);
			
			$child = $XMLQuakeDetail->createElement('titolo1');
			$child = $elem->appendChild($child);

			$value = $XMLQuakeDetail->createTextNode($Row2['titolo1']);
			$value = $child->appendChild($value);
			
			$child = $XMLQuakeDetail->createElement('luogoed');
			$child = $elem->appendChild($child);

			$value = $XMLQuakeDetail->createTextNode($Row2['luogoed']);
			$value = $child->appendChild($value);
			
			$child = $XMLQuakeDetail->createElement('datacompl');
			$child = $elem->appendChild($child);

			$value = $XMLQuakeDetail->createTextNode($datacompl);
			$value = $child->appendChild($value);
			

			$Biblios->appendChild($elem);
		}
		mysqli_close($BibliographyDB);

		if ($Detail['cat']== 'ITA'){
			//Effetti sulle localita'
		$LocalityDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
		 if ($LocalityDB ->connect_error) {
			die('Errore di connessione (' . $mysqli->connect_errno . ') '
			. $LocalityDB ->connect_error);
		} else {
			echo 'Connesso. ' . $LocalityDB->host_info . "\n";
		}
	
		$LocalityQuery = mysqli_query($LocalityDB, "SELECT intpq, desloc, lat_wgs84, lon_wgs84, provlet, nazione, intpqnum, nterr, nloc, nloc_cfti, desloc_cfti, notesito FROM pq WHERE nterr = '{$Detail['nterr']}' ORDER BY intpqnum DESC, desloc");
		} else {
		
		//Effetti sulle localita'
		$LocalityDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
		 if ($LocalityDB ->connect_error) {
			die('Errore di connessione (' . $mysqli->connect_errno . ') '
			. $LocalityDB ->connect_error);
		} else {
			echo 'Connesso. ' . $LocalityDB ->host_info . "\n";
		}
	
		$LocalityQuery = mysqli_query($LocalityDB, "SELECT intpq, desloc, lat_wgs84, lon_wgs84, provlet, nazione, intpqnum, nterr, nloc, nloc_cfti, desloc_cfti, notesito FROM pq_med WHERE nterr = '{$Detail['nterr']}' ORDER BY intpqnum DESC, desloc");
		
		}
		
	while($Row = mysqli_fetch_assoc($LocalityQuery)){
			$elem = $XMLQuakeDetail->createElement('Locality');
			
			foreach($Row as $key => $value){
				if ($key == 'nloc') {
				} else {
				$child = $XMLQuakeDetail->createElement($key);
				$child = $elem->appendChild($child);
				$value = $XMLQuakeDetail->createTextNode(trim($value));
				$value = $child->appendChild($value);		
				}					
			}
			

			$nloc = trim($Row['nloc']);
			if ($flagcomm == 4) {
				$LocCommentQuery = mysqli_query($LocalityDB, "SELECT * FROM d1 WHERE nperiod = '{$Detail['nperiod']}' and nloc = '{$nloc}'");
				
				$Row2 = mysqli_fetch_assoc($LocCommentQuery);
				
				
				// echo $Row2['nloc'];
				$comm = $Row2['testocomm'];

				$child = $XMLQuakeDetail->createElement('COMM');
				$child = $elem->appendChild($child);

				$value = $XMLQuakeDetail->createTextNode($comm);
				$value = $child->appendChild($value);	
			} else {
				$child = $XMLQuakeDetail->createElement('COMM');
				$child = $elem->appendChild($child);
			}
			$LocComments->appendChild($elem);
		}


		mysqli_close($LocalityDB);	
			if(1){ //Create Debug XML files
		
			$XMLQuakeDetail->save($XMLSource);
			echo "$XMLSource \n";
		}
	// mysqli_close($DBConn);

		
	}	
}



?>
