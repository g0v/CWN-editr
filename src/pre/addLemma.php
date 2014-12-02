<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set("memory_limit","2048M");
set_time_limit(0);

function addLemma($relation_id, $cwn_lemma){
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "UPDATE cwn_relation SET cwn_lemma = '$cwn_lemma' WHERE relation_id LIKE '$relation_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
}

$handle = fopen("rest_p16.txt", "r");
if ($handle) {
	$i = 0;
    while (($line = fgets($handle)) !== false) {
        $pieces = explode(" ", $line);
        addLemma($pieces[0], preg_replace( "/\r|\n/", "", $pieces[1]));
        $i++;
    }
    echo $i;
} 
?>