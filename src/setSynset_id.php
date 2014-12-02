<?php
include('writeLog.php');

function findMember($id) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "SELECT id, member FROM cwn_goodSynset WHERE id = '$id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
        return $res['member'];
    }
} 

function updateSense($sense_id, $sense_def) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "UPDATE cwn_sense SET sense_def = '$sense_def' WHERE sense_id LIKE '$sense_id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
	if($result){
        return "yes";
    }
}

function updateMember($id, $member) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "UPDATE cwn_goodSynset SET member = '$member', mod_time = datetime('now') WHERE id = '$id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
	if($result){
        return "yes";
    }
}

function updateGloss($id, $gloss) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "UPDATE cwn_goodSynset SET gloss = '$gloss' WHERE id = '$id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
	if($result){
        return "yes";
    }
} 

function setSynset_id($skip, $sense_id, $synset_id, $editor){

	$skip = trim($skip);
	$skip = preg_replace('/\s(?=\s)/', '', $skip);
	$skip = preg_replace('/[\n\r\t]/', ' ', $skip);
	$skip = preg_replace( "/\s/", "" , $skip);

	if($skip=="yes"){
		$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
		$query = "UPDATE cwn_sense SET synset_id = 'db-skipped' WHERE sense_id LIKE '$sense_id';";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		if($result){
			$details = $sense_id." to CWN#".$synset_id;
			writeLog($editor, "skip", $details);
			echo "Success";
		}
	}else{
		$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
		$query = "UPDATE cwn_sense SET synset_id = '$synset_id' WHERE sense_id LIKE '$sense_id';";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		if(updateSense($sense_id, $skip)){
			$details = $sense_id." to CWN#".sprintf("%09d", $synset_id);
			writeLog($editor, "add", $details);

			$member = findMember($synset_id);
			$new_member = $member.", ".$sense_id;
		    updateMember($synset_id, $new_member);

			$members = explode(", ", $member);
	        for ($i = 0; $i < count($members); $i++) {
				updateSense($members[$i], $skip);
	        }
	        updateGloss($synset_id, $skip);
			echo "Success";
		}
		
	}
}

if(isset($_GET['skip'])&&isset($_GET['sense_id'])&&isset($_GET['synset_id'])&&isset($_GET['editor'])){
	setSynset_id($_GET['skip'], $_GET['sense_id'], $_GET['synset_id'], $_GET['editor']);
}
?>