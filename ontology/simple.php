<?php

ini_set('display_errors',1); 
 error_reporting(E_ALL);
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');

$Lexicon = new SimpleXMLElement(file_get_contents('Lexicon80K.xml'));

for($i=0;$i<20589;$i++){

	echo $Lexicon->Word[$i]["item"];
	$features = $Lexicon->Word[$i]->SemanticFeatures;
	for($j=0;$j<10;$j++){
		echo "\t";
		echo $features[$j];
	}
	echo "<br />";
}
?>