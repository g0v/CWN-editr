<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
set_time_limit(0);
ini_set('memory_limit', '64M');

	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT sense_id, cwn_sense.sense_def FROM cwn_sense INNER JOIN( SELECT sense_def FROM cwn_sense GROUP BY sense_def HAVING COUNT(sense_id) >1 )temp ON cwn_sense.sense_def LIKE temp.sense_def";
	$result = $db->query($query);
	$i=0;
	while($res = $result->fetchArray(SQLITE3_ASSOC)){
   	    echo $res['sense_id']."|".$res['sense_def']."<br/>";
   	    $i++;
	}
?>