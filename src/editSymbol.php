<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

include('writeLog.php');

function pinyin_updater($lemma_id, $pinyin){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "UPDATE cwn_lemma SET cwn_pinyin = '$pinyin' WHERE lemma_id LIKE '$lemma_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
}

function zhuyin_updater($lemma_id, $zhuyin){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "UPDATE cwn_lemma SET cwn_zhuyin = '$zhuyin' WHERE lemma_id LIKE '$lemma_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
}

	if(isset($_GET['lemma_id'])){
		$lemma_id = $_GET['lemma_id'];
		$pinyin = $_GET['pinyin'];
		$zhuyin = $_GET['zhuyin'];
		$editor = $_GET['editor'];
		pinyin_updater($lemma_id, $pinyin);
		zhuyin_updater($lemma_id, $zhuyin);
		$details = "the phonetic symbols of #LEMMA".$lemma_id;
		writeLog($editor, "mod", $details);
		echo "Success";
	}
				
?>