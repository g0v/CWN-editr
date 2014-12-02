<?php

include('writeLog.php');

function delRelation($cwn_id, $rel_type, $rel_cwnid, $rel_facet, $editor){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "DELETE FROM cwn_relation WHERE cwn_id LIKE '$cwn_id' AND rel_cwnid LIKE '$rel_cwnid' AND rel_facet LIKE '$rel_facet' AND rel_type LIKE '$rel_type' ESCAPE '|';";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	if($result){
	    $details = $cwn_id." and ".$rel_cwnid." as ".$rel_type;
		writeLog($editor, "del", $details);
		echo "Success";
	}
}

if(isset($_GET['cwn_id'])&&isset($_GET['rel_type'])&&isset($_GET['rel_cwnid'])&&isset($_GET['rel_facet'])&&isset($_GET['editor'])) {
	$rel_type = urldecode($_GET['rel_type']);
	delRelation($_GET['cwn_id'],$rel_type,$_GET['rel_cwnid'],$_GET['rel_facet'],$_GET['editor']);
}
?>