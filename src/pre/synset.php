<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
set_time_limit(0);
ini_set('memory_limit', '128M');

function addGloss($gloss){
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "INSERT INTO cwn_goodSynset (gloss, member) VALUES ('$gloss', 'x')";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
}

function findSense($gloss){
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT sense_id, sense_def FROM cwn_sense WHERE sense_def LIKE '$gloss'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
	$members = "";
	while($res = $result->fetchArray(SQLITE3_ASSOC)){
		$members .= $res['sense_id'].", ";
	}
	echo rtrim($members, ", ");
}

function addMember($member, $id){
	$db = new SQLite3('../../../cwnvis/data/cwn_dirty.sqlite');
	$query = "UPDATE cwn_goodSynset SET member = '$member' WHERE id LIKE '$id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
}

$lines=array();
$fp=fopen('synset_p2.txt', 'r');
while (!feof($fp))
{
    $line=fgets($fp);

    //process line however you like
    $line=preg_replace( "/\r|\n/", "", $line);

    //add to array
    $lines[]=$line;

}
fclose($fp);
//print_r($lines);
$i=1997;
foreach($lines as $singleLine) {
   		addMember($singleLine, $i);
   		$i++;
	//}
}
echo $i;
?>