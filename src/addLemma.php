<?php

include('writeLog.php');

if(isset($_GET['lemma'])&&isset($_GET['editor'])){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT MAX(CAST(lemma_id AS UNSIGNED)) AS lemma_id FROM cwn_lemma;";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $largest = $res['lemma_id'];
    }
    $id = $largest+1;
	$lemma = trim(preg_replace("/\([^)]+\)/","",$_GET['lemma']));
	$query2 = "INSERT INTO cwn_lemma (lemma_id, lemma_type) VALUES ('$id','$lemma')";
	$result2 = $db->query($query2) or die("Error in query: <span style='color:red;'>$query</span>"); 
	if($result2){
		$editor = $_GET['editor'];
		$details = "LEMMA#".$id;
		writeLog($editor, "add", $details);
		echo "Success";
	}
}
?>