<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set("memory_limit","2048M");
set_time_limit(0);

function findLemma($cwn_id){
	$id = substr($cwn_id, 0, 6);
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT lemma_id, lemma_type FROM cwn_lemma WHERE lemma_id LIKE '$id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
 		return $res['lemma_type'];
	}
}
	
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT cwn_id, relation_id FROM cwn_relation ORDER BY relation_id DESC LIMIT 49137, 4082";//LIMIT 2684
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	echo $res['relation_id'];
    	echo " ";
    	echo findLemma($res['cwn_id'])."<br/>";
	}

?>