<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set("memory_limit","2048M");
set_time_limit(0);

function addSynset_id($synset_id, $sense_id){
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "UPDATE cwn_sense SET synset_id = '$synset_id' WHERE sense_id LIKE '$sense_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
}

$handle = fopen("member_04.txt", "r");
if ($handle) {
	$i = 1;
    while (($line = fgets($handle)) !== false) {
    	$pieces_lg = explode("|", preg_replace( "/\r|\n/", "", $line));
        $pieces_sm = explode(", ", $pieces_lg[1]);
        for ($j=0; $j<count($pieces_sm); $j++){
        	addSynset_id($pieces_lg[0], $pieces_sm[$j]);
        }
        /*
         if(is_int($i/99)){
			sleep(5);
		}
		*/
		$i++;
    }
    echo $i;
} 

?>