<?php


function BuildLocalityDetailXML($ConnString){
$crlf = chr(13) . chr(10);	
echo "Entering Loc List\n======================\n";

$report_codbib_not_found = fopen("testfile.txt", "w");
	
	$DBConn = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
	 if ($DBConn ->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
        . $DBConn ->connect_error);
    } else {
        echo 'Connesso. ' . $DBConn ->host_info . "\n";
    }

	$LocalityDetailQuery = mysqli_query($DBConn, "SELECT nloc_cfti FROM locind");
	
	 while($Detail = mysqli_fetch_assoc($LocalityDetailQuery)){
		$NLOC = trim($Detail['nloc_cfti']);
		 // $NLOC ="052305.00";
		$XMLSource = "localitySources/{$NLOC}.xml";
		// $HTMLOutput = "../localities/{$NLOC}.html";	
		
		$XMLLocalityDetail = new DomDocument('1.0', 'UTF-8');
		$XMLLocalityDetail->preserveWhiteSpace = true;
		$XMLLocalityDetail->formatOutput = true;

		$root = $XMLLocalityDetail->createElement('Locality');
		$XMLLocalityDetail->appendChild($root);
		$root->setAttribute('alone', 'true');
	
		$Details = $XMLLocalityDetail->createElement('Details');
		$root->appendChild($Details);

		$Quakes = $XMLLocalityDetail->createElement('Quakes');
		$root->appendChild($Quakes);
		

		// Locality Detail
		$LocalityDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
		 if ($LocalityDB ->connect_error) {
			die('Errore di connessione (' . $mysqli->connect_errno . ') '
			. $LocalityDB ->connect_error);
		} else {
			echo 'Connesso. ' . $LocalityDB ->host_info . "\n";
		}
	
		$LocindQuery = mysqli_query($LocalityDB, "SELECT nloc_cfti, desloc_cfti, provlet, nazione, risentimenti, maxint, lat_wgs84, lon_wgs84, notesito FROM locind WHERE nloc_cfti = '{$NLOC}' LIMIT 1");

		$elem = $XMLLocalityDetail->createElement('Detail');

		$LocalityDetail = mysqli_fetch_assoc($LocindQuery);
		foreach($LocalityDetail as $key => $value){
			$child = $XMLLocalityDetail->createElement($key);
			$child = $elem->appendChild($child);
			$value = $XMLLocalityDetail->createTextNode(trim($value));
			$value = $child->appendChild($value);					

		}

		$Details->appendChild($elem);

		//Quakes
		
		$QuakeDB = new mysqli('localhost', 'root', 'sonoio', 'cfti5');
		 if ($QuakeDB ->connect_error) {
			die('Errore di connessione (' . $mysqli->connect_errno . ') '
			. $QuakeDB ->connect_error);
		} else {
			echo 'Connesso. ' . $QuakeDB ->host_info . "\n";
		}
		
		
		$n = 0;
		$QuakesQuery = mysqli_query($QuakeDB, "SELECT nterrs.cat, nterrs.nterr, nterrs.nperiod, nterrs.datanum, nterrs.data_label, nterrs.anno, nterrs.mese, nterrs.giorno, nterrs.time_label, nterrs.ora, nterrs.minu, nterrs.sec, nterrs.lat, nterrs.lon, nterrs.rel, nterrs.level, nterrs.io, nterrs.imax, nterrs.npun, nterrs.mm, nterrs.earthquakelocation, nterrs.epicenter_type, nterrs.country, nterrs.ee_nt, nterrs.ee_np, nterrs.flagcomments, nterrs.flagfalseeq, pq.intpqnum, pq.intpq FROM nterrs INNER JOIN pq ON (nterrs.nterr = pq.nterr) WHERE nloc_cfti = '{$NLOC}'");
			// $a = mysqli_num_rows($QuakesQuery);
			// echo $a;
			// echo "\r\n";
		// echo "\r\n";
		// echo "NLOC: ";
		// echo '049824.00';
		echo "\r\n";
		while($Row = mysqli_fetch_assoc($QuakesQuery)){
			$elem = $XMLLocalityDetail->createElement('Quake');
			//$nperiod = $Row['nperiod'];
			$sel[$n]=$Row['nperiod']; 
			foreach($Row as $key => $value){
				$child = $XMLLocalityDetail->createElement($key);
				$child = $elem->appendChild($child);

				$value = $XMLLocalityDetail->createTextNode(trim($value));
				$value = $child->appendChild($value);	
			}
			$nperiod = $sel[$n];
			
			echo "\r\n";
			echo "NPERIOD: ";
			echo $nperiod;
			echo "\r\n";
			$n = $n + 1;
			echo $n;
			echo "\r\n";
			$comm = "";
			$flagcomm = $Row['flagcomments'];
			if ($flagcomm == 4) {
				$LocCommentQuery = mysqli_query($QuakeDB, "SELECT * FROM d1 WHERE nperiod = '{$nperiod}' and nloc LIKE '{$NLOC}%'");
				
				if(mysqli_num_rows($LocCommentQuery)){
				
					$Row2 = mysqli_fetch_assoc($LocCommentQuery);
						
					echo "\r\n";
					$comm = $Row2['testocomm'];
					
					echo "\r\n";
						
						$child = $XMLLocalityDetail->createElement('D1');
						$child = $elem->appendChild($child);

						$value = $XMLLocalityDetail->createTextNode($comm);
						$value = $child->appendChild($value);	
								
				} else {
						$child = $XMLLocalityDetail->createElement('D1');
						$child = $elem->appendChild($child);
					fwrite($report_codbib_not_found,"D1 mancante:" . $nperiod . " " . $NLOC. $crlf );
				}
			} else {
						$child = $XMLLocalityDetail->createElement('D1');
						$child = $elem->appendChild($child);
			}
			$Quakes->appendChild($elem);	
		}
		
	//BIBLIO
	
		$Biblio = $XMLLocalityDetail->createElement('Biblios');
		$root->appendChild($Biblio);
		
		$n = 0;
		$BiblioQuery = mysqli_query($QuakeDB, "SELECT * FROM schede_c WHERE nloc LIKE '{$NLOC}%'");
		echo "\r\n";
		echo "NLOC: ";
		echo $NLOC;
		echo "\r\n";
		//$Row = mysqli_fetch_assoc($BiblioQuery);
		
		while($Row = mysqli_fetch_assoc($BiblioQuery)){
			//$nperiod = $Row['nperiod'];
			$sel[$n]=$Row['codbib']; 

			$codbib = $sel[$n];
			
			echo "\r\n";
			echo "codbib: ";
			echo $codbib;
			echo "\r\n";
			$n = $n + 1;
		
			$BiblioDetQuery = mysqli_query($QuakeDB, "SELECT codbib, autore1,titolo1, luogoed,datauni,dataun2 FROM schede_a WHERE codbib = '{$codbib}' ");
			if(mysqli_num_rows($BiblioDetQuery)){
				
				$Row2 = mysqli_fetch_assoc($BiblioDetQuery);
					
				echo "\r\n";
				
			
				$elem = $XMLLocalityDetail->createElement('Bibliography');
				
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
				
				$child = $XMLLocalityDetail->createElement('codbib');
				$child = $elem->appendChild($child);

				$value = $XMLLocalityDetail->createTextNode($Row2['codbib']);
				$value = $child->appendChild($value);
				
				$child = $XMLLocalityDetail->createElement('autore1');
				$child = $elem->appendChild($child);

				$value = $XMLLocalityDetail->createTextNode($Row2['autore1']);
				$value = $child->appendChild($value);
				
				$child = $XMLLocalityDetail->createElement('titolo1');
				$child = $elem->appendChild($child);

				$value = $XMLLocalityDetail->createTextNode($Row2['titolo1']);
				$value = $child->appendChild($value);
				
				$child = $XMLLocalityDetail->createElement('luogoed');
				$child = $elem->appendChild($child);

				$value = $XMLLocalityDetail->createTextNode($Row2['luogoed']);
				$value = $child->appendChild($value);
				
				$child = $XMLLocalityDetail->createElement('datacompl');
				$child = $elem->appendChild($child);

				$value = $XMLLocalityDetail->createTextNode($datacompl);
				$value = $child->appendChild($value);


					
				$Biblio->appendChild($elem);
			} else {
				fwrite($report_codbib_not_found,"BIBLIO D1 mancante:" . $codbib . " " . $Detail['nloc_cfti'] . $crlf );
			}
			
		}	

		
		mysqli_close($QuakeDB);
		
		

		if(1){
			$XMLLocalityDetail->save($XMLSource);
			echo "$XMLSource \n";
		}
	}
		mysqli_close($DBConn) ;
		fclose($report_codbib_not_found);
}
?>
