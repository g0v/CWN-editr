<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);	
set_time_limit(0);
ini_set('memory_limit', '64M');

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT cwn_id, pos FROM cwn_pos WHERE pos LIKE 'Na' OR pos LIKE 'Nb' OR pos LIKE 'Nc' OR pos LIKE 'Ncd' OR pos LIKE 'Nd' OR pos LIKE 'Nh'";
	$result = $db->query($query);
	$results = array();
	$i=0;
	while($res = $result->fetchArray(SQLITE3_ASSOC)){
   	    $results[$i] = substr($res['cwn_id'], 0, 6);
   	    $i++;
	}
	$re = array_unique($results);
	var_dump($re);
	/*
	for($j=0;$j<11307;$j++){
		
		if (isset($re[$j])){
			$thing = $re[$j];
			$query2 = "SELECT lemma_id, lemma_type FROM cwn_lemma WHERE lemma_id LIKE '$thing'";
			$result2 = $db->query($query2);
			while($res2 = $result2->fetchArray(SQLITE3_ASSOC)){
		   	    echo $res2['lemma_type']." (".$res2['lemma_id'].")<br/>";
			}
		}
	}
	*/
?>