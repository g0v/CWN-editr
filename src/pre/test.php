<?php
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$q= "時";
$q = $q+'%';
$query = "SELECT cwn_lemma, rel_lemma FROM cwn_relation WHERE cwn_lemma LIKE '$q'";
 		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$i = 0; 
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	          $i++; 
	    } 
		echo $i;
?>