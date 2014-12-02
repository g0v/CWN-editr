<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

function findAllSenses($lemma_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT sense_id, lemma_id FROM cwn_sense WHERE lemma_id LIKE '$lemma_id' ORDER BY sense_id ASC";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $allSenses = "";
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	$id = substr($res['sense_id'], 6, 8);
 		$allSenses .= "<option value=".$res['sense_id'].">".$id."</option>";
	}
	//if($allSenses==""){$allSenses="<option value=".$lemma_id."01>01</option>";}
	$allSenses .= "<option value=addSense>＋</option>";
	return $allSenses;
}

function findAllFacets($sense_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT facet_id, sense_id, facet_def FROM cwn_facet WHERE sense_id LIKE '$sense_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $allFacets = "<option value=".$sense_id."></option>";
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	$id = substr($res['facet_id'], 8, 10);
 		$allFacets .= "<option value=".$res['facet_id'].">".$id."</option>";
	}
	$allFacets .= "<option value=addFacet>＋</option>";
	return $allFacets;
}

//read pos
$file = fopen("../docs/pos.txt", "r");
$pos = array();
while (!feof($file)) {
   $pos[] = fgets($file);
}
fclose($file);

$pos_output = "<option value=nogood></option>";
for($i=0;$i<count($pos);$i++){
	$tagAll = explode("（", $pos[$i]);
    $pos_output .= "<option value={$tagAll[0]}>{$pos[$i]}</option>";
}

function findDef($cwn_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT sense_id, sense_def FROM cwn_sense WHERE sense_id LIKE '$cwn_id'";
	if(strlen($cwn_id)==10){
	$query = "SELECT facet_id, facet_def FROM cwn_facet WHERE facet_id LIKE '$cwn_id'";
	}
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	if(strlen($cwn_id)==10){
    		return $res['facet_def'];
    	}else{
    		return $res['sense_def'];
    	}
	}
}


function findPOS($cwn_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT cwn_id, pos FROM cwn_pos WHERE cwn_id LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
		return $res['pos'];
	}
}

function findExample($cwn_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM cwn_example WHERE cwn_id LIKE '$cwn_id' AND example_sno LIKE '01';";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	return $res['example_cont'];
    }
}

function findNote($cwn_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT cwn_id, note_cont FROM cwn_note WHERE cwn_id LIKE '$cwn_id' AND note_sno LIKE '01';";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	return $res['note_cont'];
	}
}

if(isset($_GET['cwn_id'])) {
			$lemma = substr($_GET['cwn_id'], 0, 6);
			$sense_output = findAllSenses($lemma);
			if(strlen($_GET['cwn_id'])==8){
				$old = "<option value=".$_GET['cwn_id'].">";
	            $new = "<option value=".$_GET['cwn_id']." selected=selected>";
				$sense_output = str_replace($old, $new, $sense_output);
				$facet_output = findAllFacets($_GET['cwn_id']);
				$facet_ultimate_output = '
						  <label for="facetSelect" id="facetLabel" class="col-sm-3 control-label">義面 (Facet)
	                      </label>
	                      <div class="col-sm-3">
	                        <select class="form-control" id="facetSelect" onchange="changeFacet(this.value);">
							'.$facet_output.'
							</select>
	                      </div>';
	            if($sense_output=="<option value=addSense>＋</option>"){
	            	$facet_ultimate_output = "";
				}
			}elseif(strlen($_GET['cwn_id'])==10){
				$sense = substr($_GET['cwn_id'],0,8);
				$old = "<option value=".$sense.">";
	            $new = "<option value=".$sense." selected=selected>";
				$sense_output = str_replace($old, $new, $sense_output);
				$facet_output = findAllFacets($sense);
				$old2 = "<option value=".$_GET['cwn_id'].">";
	            $new2 = "<option value=".$_GET['cwn_id']." selected=selected>";
				$facet_output = str_replace($old2, $new2, $facet_output);
				$facet_ultimate_output = '
						  <label for="facetSelect" id="facetLabel" class="col-sm-3 control-label">義面 (Facet)
	                      </label>
	                      <div class="col-sm-3">
	                        <select class="form-control" id="facetSelect" onchange="changeFacet(this.value);">
							'.$facet_output.'
							</select>
	                      </div>';
			}
			$def = findDef($_GET['cwn_id']);
			$pos = findPOS($_GET['cwn_id']);
            $old3 = "<option value=".$pos.">";
            $new3 = "<option value=".$pos." selected=selected>";
			$pos_output = str_replace($old3, $new3, $pos_output);
			$example = findExample($_GET['cwn_id']);
			$note = findNote($_GET['cwn_id']);
	    	echo '<br />
                  <br />
	                <div class="col-sm-1"></div>
	                <div class="col-sm-10">
	                  <form class="form-horizontal" role="form">
 						<div class="form-group">
	                      <label for="senseSelect" class="col-sm-3 control-label">詞義 (Sense)
	                      </label>
	                      <div class="col-sm-3">
	                        <select class="form-control" id="senseSelect" onchange="changeSense(this.value);">
							'.$sense_output.'
							</select>
	                      </div>
							'.$facet_ultimate_output.'
	                    </div>
	                    <div class="form-group">
	                      <label for="posSelect" class="col-sm-3 control-label">詞性 (POS)
	                      </label>
	                      <div class="col-sm-9">
	                        <select class="form-control" id="posSelect">
	                        '.$pos_output.'
	                        <select>
	                      </div>
	                    </div>	                    
	                    <div class="form-group">
	                      <label for="glossBox" class="col-sm-3 control-label">釋義 (Gloss)
	                      </label>
	                      <div class="col-sm-9">
	                        <input type="text" class="form-control" id="glossBox" value="'.$def.'">
	                      </div>
	                    </div>
	                    <div class="form-group">
	                      <label for="exampleBox" class="col-sm-3 control-label">例句 (Ex Sent)
	                      </label>
	                      <div class="col-sm-9">
	                        <input type="text" class="form-control" id="exampleBox" value="'.$example.'">
	                      </div>
	                    </div>	
	                    <div class="form-group">
	                      <label for="noteBox" class="col-sm-3 control-label">附註 (Note)
	                      </label>
	                      <div class="col-sm-9">
	                        <input type="text" class="form-control" id="noteBox" value="'.$note.'">
	                      </div>
	                    </div>
	                    <div class="form-group">
	                      <div class="col-sm-offset-3 col-sm-9">
	                        <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" id="editLemmaButton" onclick="editLemma();"><span class="ladda-label">儲存 (Save)</span></button>
	                      </div>
	                    </div>
	                  </form>
	                </div>
	                <div class="col-sm-1"></div>
	                <br />
	                <br />
	                <br />
	                <br />
	                <br />
	                <br />
	                <br />
';
}
?>