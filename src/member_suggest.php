<?php
function findSense() {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $sql = "SELECT sense_id, synset_id FROM cwn_sense WHERE synset_id LIKE 'skipped' LIMIT 1";
    $results = $db->query($sql);
    $i = 0;
    while ($res = $results->fetchArray(SQLITE3_ASSOC)) {
        $sense = $res['sense_id'];
        $i++;
    }
    if ($i === 0) {
        return;
    } else {
        return $sense;
    }
}

function findSynonym($sense_id) {
    if (is_null($sense_id)) {
        return;
    } else {
        $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
        $sql = "SELECT cwn_id, rel_type, rel_cwnid FROM cwn_relation WHERE (cwn_id LIKE '$sense_id' OR rel_cwnid LIKE '$sense_id') AND (rel_type LIKE '=' AND rel_facet LIKE '0')";
        $results = $db->query($sql);
        $i = 0;
        while ($res = $results->fetchArray(SQLITE3_ASSOC)) {
            if ($res['cwn_id'] != $sense_id) {
                $members[$i] = $res['cwn_id'];
                $i++;
            }
            if ($res['rel_cwnid'] != $sense_id) {
                $members[$i] = $res['rel_cwnid'];
                $i++;
            }
        }
        if ($i !== 0) {
            $final_members = array_values(array_filter(array_unique($members)));
            
            //print_r($final_members);
            return $final_members;
        } else {
            return;
        }
    }
}

function findSynset($sense_id) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $sql = "SELECT id, member FROM cwn_goodSynset WHERE member LIKE '%{$sense_id}%'";
    $results = $db->query($sql);
    $i = 0;
    while ($res = $results->fetchArray(SQLITE3_ASSOC)) {
        $synset = $res['id'];
        $i++;
    }
    if ($i == 0) {
        return;
    } else {
        return $synset;
    }
}

function checkField($sense_id) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "UPDATE cwn_sense SET synset_id = 'tp-checked' WHERE sense_id LIKE '$sense_id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
    //echo $sense_id . " is set to 'db-checked'<br/>";
}

function findLemma($cwn_id) {
    $id = substr($cwn_id, 0, 6);
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "SELECT lemma_id, lemma_type FROM cwn_lemma WHERE lemma_id LIKE '$id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
    	$withLink = "<a target='_blank' href='http://lope.linguistics.ntu.edu.tw/cwnvis/index.php/senses?lid=" . substr($cwn_id, 0, 6) . "'>" . $res['lemma_type'] . "</a>"; 
        return $withLink;
    }
}

function findDef($cwn_id) {
    $tableName = "cwn_sense";
    $idName = "sense_id";
    $defName = "sense_def";
    $id = substr($cwn_id, 6, 8);
    if (strlen($cwn_id) == 10) {
        $tableName = "cwn_facet";
        $idName = "facet_id";
        $defName = "facet_def";
        $id = substr($cwn_id, 6, 10);
    }
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "SELECT * FROM \"" . $tableName . "\" WHERE \"" . $idName . "\" LIKE '$cwn_id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
        return $res[$defName];
    }
}

function findGloss($id) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "SELECT id, gloss FROM cwn_goodSynset WHERE id LIKE '$id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
        return $res['gloss'];
    }
}

function findMember($id) {
    $db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    $query = "SELECT id, member FROM cwn_goodSynset WHERE id LIKE '$id'";
    $result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>");
    while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
        return $res['member'];
    }
}

function findAllMem($id){
	$member = explode(", ", findMember($id));
    for($k=0; $k<count($member); $k++){
 		$all_mem_char .= findLemma($member[$k])." (".substr($member[$k],6,8).")"."、"; 
 	}
 	$all_mem_char = rtrim($all_mem_char, "、");
 	return $all_mem_char;
}

function ensemble() {
    $sense_id = findSense();
    if (is_null($sense_id )) {
    	echo "Congrats! All checked!! Contact Ajax asap!!!";
    	exit();
	}
    $sense_lemma = findLemma($sense_id);
    if (is_null($sense_lemma)) {
        $synonyms = null;
    } else {
        $synonyms = findSynonym($sense_id);
    }
    if (is_null($synonyms)) {
        checkField($sense_id);
        ensemble();
    } else {
        for ($i = 0; $i < count($synonyms); $i++) {
            if (is_null(findLemma($synonyms[$i]))) {
                $synsets[$i] = null;
            } else {
                $synsets[$i] = findSynset($synonyms[$i]);
            }
        }
        $late = array_values(array_filter(array_unique($synsets)));
        if (empty($late)) {
            checkField($sense_id);
            ensemble();
        } else {
            echo "<div class=\"panel panel-default text-center\" style=\"margin-bottom:0;\">
            	    <div id=\"senseHeading\" data-id=\"".$sense_id."\" class=\"panel-heading\">
            		  <h3 class=\"panel-title\">SENSE <span style=\"color:#3476BE;\">" . $sense_lemma . "</span> (" . substr($sense_id, 6, 8) . ")</h3>
            		</div>
            		<div id=\"senseBody\" class=\"panel-body\" data-gloss=\"".findDef($sense_id)."\">
            		  <textarea id=\"sense_ta\" class=\"form-control\" rows=\"2\" disabled style=\"background-color:white;\">" . findDef($sense_id) . "</textarea>
            		</div>
            	  </div>
            	  <div class=\"clearfix\" style=\"margin-bottom:0;\">
	                <div class=\"checkbox pull-right \" style=\"margin:7px 0 0 0;\">
	                  <label id=\"direction\">
	                    <input type=\"checkbox\" id=\"sense_cb\"> 使用此釋義 (use this gloss)
	                  </label>
	                </div>
				  </div>
            	  <center>
            	    <span class=\"glyphicon glyphicon-chevron-down\"></span>
            	  </center>
            	  <br/>
            	  <div class=\"panel panel-default text-center\" style=\"margin:7px 0 0 0;\">
            	    <div id=\"synsetHeading\" data-id=\"".$late[0]."\" class=\"panel-heading\">
            	      <h3 class=\"panel-title\">SYNSET <span id='plz' title=\"".findAllMem($late[0])."\" style='border-bottom: 1px dotted #3476BE;cursor:default;'>" . sprintf("%09d", $late[0]) . "</span></h3>
            	    </div>
            	    <div id=\"synsetBody\" class=\"panel-body\" data-gloss=\"".findGloss($late[0])."\">
            	      <textarea id=\"synset_ta\" class=\"form-control\" rows=\"2\" disabled style=\"background-color:white;\">". findGloss($late[0]) ."</textarea>
            	    </div>
            	  </div>
            	  <div class=\"clearfix\" style=\"margin-bottom:0;\">
	                <div class=\"checkbox pull-right\" style=\"margin:7px 0 0 0;\">
	                  <label id=\"direction\">
	                    <input type=\"checkbox\" id=\"synset_cb\"> 使用此釋義 (use this gloss)
	                  </label>
	                </div>
				  </div>";
        }
    }
}

ensemble();
?>