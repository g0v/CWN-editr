<?php
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT id, member FROM cwn_goodSynset";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
 		echo $res['id']."|".$res['member']."<br />"; 
 	}
?>