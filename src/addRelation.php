<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

include('writeLog.php');

function addRelation($source, $relation, $lemma, $target, $editor, $lemma2){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT * FROM cwn_relation WHERE cwn_id LIKE '$source' AND rel_cwnid LIKE '$target' AND rel_type LIKE '$relation' ESCAPE '|';";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

    $i = 0; 

    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $i++; 
    } 
	if($i>0){
    	echo "n";
	}else{
		if($relation=="|%"){$relation="%";};
	    $result2 = $db->query("INSERT INTO cwn_relation(cwn_id, rel_type, rel_lemma, rel_cwnid, rel_facet, cwn_lemma) VALUES ('$source', '$relation', '$lemma', '$target', '0', '$lemma2')");
	    $details = $source." and ".$target." as ".$relation;
	    writeLog($editor, "add", $details);
	    echo "y";
	}

}
	
		if(isset($_GET['source'])) {
			$source = $_GET['source'];
			$relation = urldecode($_GET['relation']);		
			$lemma = $_GET['lemma'];
			$target = $_GET['target'];	
			$editor = $_GET['editor'];
			$lemma2 = $_GET['lemma2'];
			addRelation($source,$relation,$lemma,$target,$editor,$lemma2);

			if(isset($_GET['bidirection'])) {
				$new_source = $target;
		    	switch ($relation) {
				    case "="://synonymy
				        $new_relation = "=";
				        break;
				    case "!"://antonymy
				        $new_relation = "!";
				        break;
				    case "@"://hypernymy
				        $new_relation = "~";
				        break;
				    case "~"://hyponymy
				        $new_relation = "@";
				        break;
				    case "#"://holonymy
				        $new_relation = "|%";
				        break;
				    case "|%"://meronymy
				        $new_relation = "#";
				        break;
				    case "&"://near-synonymy
				        $new_relation = "&";
				        break;
				    case "+"://paranymy
				        $new_relation = "+";
				        break;
				    case "^"://variant
				        $new_relation = "^";
				        break;
				}
				$new_lemma = $_GET['lemma2'];
				$new_target = $source;
				$new_lemma2 = $_GET['lemma'];				
				addRelation($new_source,$new_relation,$new_lemma,$new_target,$editor,$new_lemma2);
			}
		}	
			
?>