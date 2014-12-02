<?php	
	function writeLog($editor, $action, $details){
		$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	    $result = $db->query("INSERT INTO cwn_history(editor, time, action, details) VALUES ('$editor', datetime('now'), '$action', '$details')");
	}
?>