<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
set_time_limit(0);
ini_set('memory_limit', '128000M');


	function findSense($id){
		$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
		$query = "SELECT sense_id, sense_def FROM cwn_sense WHERE sense_id LIKE '$id';";
		$result = $db->query($query);
		while($res = $result->fetchArray(SQLITE3_ASSOC)){
	   	    $def = $res['sense_def'];
		}
		return $def;
	}

	function findFacet($id){
		$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
		$query = "SELECT facet_id, facet_def FROM cwn_facet WHERE facet_id LIKE '$id';";
		$result = $db->query($query);
		while($res = $result->fetchArray(SQLITE3_ASSOC)){
	   	    $def = $res['facet_def'];
		}
		return $def;
	}

	function findDef($id){
		if (strlen($id)==8){
			return findSense($id);
		}elseif(strlen($id)==10){
			return findFacet($id);			
		}
	}

$handle = fopen("synonyms_p1.txt", "r");
if ($handle) {

    while (($line = fgets($handle)) !== false) {
        $pieces = explode("=", $line);
        $source = preg_replace("/\r|\n/", "", $pieces[0]);
        $target = preg_replace("/\r|\n/", "", $pieces[1]);
       	$source_gloss = findDef($source);        
        $target_gloss = findDef($target);
        $db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
        if($source_gloss!=$target_gloss){
			$query = "UPDATE cwn_anomaly SET ident = 'n' WHERE cwn_id LIKE '$source' AND rel_id LIKE '$target';";
        }
        $result = $db->query($query);
	}
}
