<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

include('writeLog.php');

function editSynset($id, $pwn_id, $pwn_gloss, $pwn_word, $editor){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$pwn_gloss = trim($pwn_gloss, " ");
	$pwn_gloss = SQLite3::escapeString($pwn_gloss);
	$pwn_word = SQLite3::escapeString($pwn_word);

	$query = "UPDATE cwn_goodSynset SET pwn_id = '$pwn_id', pwn_gloss = '$pwn_gloss', pwn_word = '$pwn_word' WHERE id LIKE '$id';";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	if($result){
	    $details = "CWN#".sprintf("%09d", $id)." to PWN#".$pwn_id;
		writeLog($editor, "map", $details);
		echo "Success";
	}
}

		if(isset($_GET['pwn_id'])) {
			$id = $_GET['id'];
			$pwn_id = $_GET['pwn_id'];
			$pwn_gloss = $_GET['pwn_gloss'];
			$pwn_word = $_GET['pwn_word'];
			$editor = $_GET['editor'];
			editSynset($id, $pwn_id, $pwn_gloss, $pwn_word, $editor);
		}	
			
?>