<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
set_time_limit(0);
ini_set('memory_limit', '128M');


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

function addAnomaly($cwn_id, $rel_type, $rel_id){
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "INSERT INTO cwn_anomaly (cwn_id, rel_type, rel_id) VALUES ('$cwn_id', '$rel_type', '$rel_id')";
	$result = $db->query($query);
}


$handle = fopen("synonyms_p1.txt", "r");
if ($handle) {
	$i = 0;
	$j = 0;
	$k = 0;
    while (($line = fgets($handle)) !== false) {
        $pieces = explode("=", $line);
        $source = preg_replace("/\r|\n/", "", $pieces[0]);
        //$source = substr($source, 0, 8);
        $target = preg_replace("/\r|\n/", "", $pieces[1]);
        //$target = substr($target, 0, 8);
        /*$source_gloss = findSense($source);
        $target_gloss = findSense($target);
        if ($source_gloss!=$target_gloss){
         $j++;
        }
        $i++;
        if($i==1000){
        	break;
        }
        */
	addAnomaly($source, "=", $target);
    $k++;
    }
    //echo $j."/".$i;
    echo $k;
} 
?>