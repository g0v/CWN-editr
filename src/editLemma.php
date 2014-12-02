<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

include('writeLog.php');

function sense_updater($sense_id, $gloss){
	$lemma_id = substr($sense_id, 0, 6);
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM cwn_sense WHERE sense_id LIKE '$sense_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $i = 0; 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $i++; 
    } 
	if($i>0){
    	$result2 = $db->query("UPDATE cwn_sense SET sense_def = '$gloss' WHERE sense_id LIKE '$sense_id'");
	}else{
	    $result2 = $db->query("INSERT INTO cwn_sense (sense_id, lemma_id, sense_def) VALUES ('$sense_id','$lemma_id','$gloss')");
	}
}

function facet_updater($facet_id, $gloss){
	$sense_id = substr($facet_id, 0, 8);
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM cwn_facet WHERE facet_id LIKE '$facet_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $i = 0; 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $i++; 
    } 
	if($i>0){
    	$result2 = $db->query("UPDATE cwn_facet SET facet_def = '$gloss' WHERE facet_id LIKE '$facet_id'");
	}else{
	    $result2 = $db->query("INSERT INTO cwn_facet (facet_id, sense_id, facet_def) VALUES ('$facet_id','$sense_id','$gloss')");
	}
}

function note_updater($cwn_id, $note){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM cwn_note WHERE cwn_id LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $i = 0; 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $i++; 
    } 
	if($i>0){
    	$result2 = $db->query("UPDATE cwn_note SET note_cont = '$note' WHERE cwn_id LIKE '$cwn_id' AND note_sno LIKE '01'");
	}else{
	    $result2 = $db->query("INSERT INTO cwn_note (cwn_id, note_sno, note_cont) VALUES ('$cwn_id','01','$note')");
	}
}

function pos_updater($cwn_id, $pos){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM cwn_pos WHERE cwn_id LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $i = 0; 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $i++; 
    } 
	if($i>0){
    	$result2 = $db->query("UPDATE cwn_pos SET pos = '$pos' WHERE cwn_id LIKE '$cwn_id' AND pos_sno LIKE '01'");
	}else{
	    $result2 = $db->query("INSERT INTO cwn_pos (cwn_id, pos_sno, pos) VALUES ('$cwn_id','01','$pos')");
	}
}

function ex_updater($cwn_id, $example){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM cwn_example WHERE cwn_id LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $i = 0; 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $i++; 
    } 
	if($i>0){
    	$result2 = $db->query("UPDATE cwn_example SET example_cont = '$example' WHERE cwn_id LIKE '$cwn_id' AND example_sno LIKE '01'");
	}else{
	    $result2 = $db->query("INSERT INTO cwn_example (cwn_id, example_sno, example_cont) VALUES ('$cwn_id','01','$example')");
	}
}
	if(isset($_GET['cwn_id'])){
		$cwn_id = $_GET['cwn_id'];
		$lemma_id = substr($cwn_id, 0, 6);
		$pos = $_GET['pos'];
		$gloss = $_GET['gloss'];
		$example = $_GET['example'];
		$note = $_GET['note'];
		$editor = $_GET['editor'];
		pos_updater($cwn_id, $pos);
		ex_updater($cwn_id, $example);
		note_updater($cwn_id, $note);
		if(strlen($cwn_id)==8) {
			sense_updater($cwn_id, $gloss);	
			$details = "SENSE#".$cwn_id." of LEMMA#".$lemma_id;		
		}else if(strlen($cwn_id)==10) {
			facet_updater($cwn_id, $gloss);
			$details = "FACET#".$cwn_id." of LEMMA#".$lemma_id;
		}
		writeLog($editor, "mod", $details);
		echo "Success";
	}
				
?>