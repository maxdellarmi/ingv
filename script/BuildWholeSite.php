#!/usr/bin/php5
<?php
 require_once('BuildQuakeListXML.php');
 require_once('BuildQuakeDetailXML.php');
 require_once('BuildQuakeDetailXMLCOMM.php');
 require_once('BuildLocListXML.php');
 require_once('BuildLocalityDetailXML.php');

 require_once('EEListAndBiblioXML.php');


$MYSQLConnString = '';
$Settings = array(

	'host' => 'localhost',
	'user' => 'root',
	'password' => 'sonoio',
	'dbname' => 'cfti5',
	'port' => '3306'
);

foreach($Settings as $key => $value)
	$MYSQLConnString .= "{$key}={$value} "; 

 echo "Building Quake List\n======================\n";
 BuildQuakeListXML($MYSQLConnString);
 
  echo "Building Loc List\n======================\n";
 BuildLocListXML($MYSQLConnString);
 
  echo "Building Quake Details\n======================\n";
 BuildQuakeDetailXML($MYSQLConnString);

 echo "Building Quake Details\n======================\n";
 BuildQuakeDetailXMLCOMM($MYSQLConnString);


 
 echo "Building Locality Detail\n======================\n";
 BuildLocalityDetailXML($MYSQLConnString);

  echo "Building EE List&Biblio\n======================\n";
 BuildEEDetailXML($MYSQLConnString);


?>
