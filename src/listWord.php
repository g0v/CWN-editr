<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

function findLemma($cwn_id){
	$id = substr($cwn_id, 0, 6);
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT lemma_id, lemma_type FROM cwn_lemma WHERE lemma_id LIKE '$id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
 		//return "(".$id.") ".$res['lemma_type'];
 		return '<a href="http://lope.linguistics.ntu.edu.tw/cwnvis/index.php/senses?lid='.substr($cwn_id,0,6).'" target="_blank">'.$res['lemma_type'].'</a> ('.substr($cwn_id,6,8).')';
	}
}

function findAllSenses($lemma_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT sense_id, lemma_id FROM cwn_sense WHERE lemma_id LIKE '$lemma_id' ORDER BY sense_id ASC";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $allSenses = "";
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
 		$allSenses .= $res['sense_id']."|";
	}
	$allSenses = rtrim($allSenses, "|");
	return $allSenses;
}

function findDef($sense_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT sense_id, sense_def FROM cwn_sense WHERE sense_id LIKE '$sense_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	$allExamples = findExample($sense_id);
		return "(".substr($sense_id, 6,8).") ".findPOS($sense_id)." ".$res['sense_def'].$allExamples.findNote($sense_id).findAllFacets($sense_id);
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

function findAllFacets($sense_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT facet_id, sense_id, facet_def FROM cwn_facet WHERE sense_id LIKE '$sense_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $allFacets = "";
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	$allExamples = findExample($res['facet_id']);
		$allFacets .= "<br/ ><span style=\"color:#3476BE;\">　└</span> (".substr($res['facet_id'], 8, 10).") ".findPOS($res['facet_id'])." ".$res['facet_def'].$allExamples.findNote($res['facet_id']);
	}
	return $allFacets;
}

function findExample($cwn_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT cwn_id, example_cont FROM cwn_example WHERE cwn_id LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $allExamples = "<ol style='padding-left:20px;margin:0;line-height:150%'>";
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	$example_cont = $res['example_cont'];
		$example_cont = str_replace("<", "<mark|", $example_cont);
		$example_cont = str_replace(">", "</mark>", $example_cont);
		$example_cont = str_replace("<mark|", "<mark>", $example_cont);
		$allExamples .= "<li>".$example_cont."</li>";
	}
	$allExamples .= "</ol>";
	if($allExamples=="<ol style='padding-left:20px;margin:0;line-height:150%'></ol>"){
		$allExamples = "";
	}else{
		$allExamples = rtrim($allExamples, "<br />");
		$allExamples = " <span title=\"".$allExamples."\" class=\"glyphicon glyphicon-play-circle\" style=\"color:#3476BE;\"></span>";
	}
	return $allExamples;
}

function findNote($cwn_id){
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT cwn_id, note_cont FROM cwn_note WHERE cwn_id LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    $allNotes = "";
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
    	$allNotes .= $res['note_cont'];
	}
	if ($allNotes!==""){
    	$allNotes = " <span title=\"".$allNotes."\" class=\"glyphicon glyphicon-comment\" style=\"color:#3476BE;\"></span>";
    }
	return $allNotes;
}

function listWord($page, $filter){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    
    if ($filter=="nothingtofilter"){
		$query = "SELECT COUNT(lemma_id) as count FROM cwn_lemma";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$row = $result->fetchArray();
		$totalrows = $row['count'];
	}else{
		$query = "SELECT lemma_type FROM cwn_lemma WHERE lemma_type LIKE '%{$filter}%'";
 		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$i = 0; 
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	          $i++; 
	    } 
		$totalrows = $i;
	}

	$rowsperpage = 10;
	$totalpages = ceil($totalrows/$rowsperpage);

	if (is_numeric($page)) {
		$currentpage = (int) $page;	
	} else {
		$currentpage = 1;
	}

	if ($currentpage > $totalpages) {
		$currentpage = $totalpages;
	}
	if ($currentpage < 1) {
		$currentpage = 1;
	}

	if ($currentpage==1){
		$first = "<li class=\"disabled\"><a href=\"#\">«</a></li>";
		$last = "<li><a href=\"#\" onclick=\"switchPages(2)\">»</a></li></ul>";
	}elseif ($currentpage==$totalpages){
		$previousPage = $totalpages-1;
		$first = "<li><a href=\"#\" onclick=\"switchPages($previousPage)\">«</a></li>";
		$last = "<li class=\"disabled\"><a href=\"#\">»</a></li></ul>";
	}else{
		$previousPage = $currentpage-1;
		$nextPage = $currentpage+1;
		$first = "<li><a href=\"#\" onclick=\"switchPages($previousPage)\">«</a></li>";
		$last = "<li><a href=\"#\" onclick=\"switchPages($nextPage)\">»</a></li></ul>";

	}

	if ($filter=="nothingtofilter"){
		$offset = ($currentpage - 1) * $rowsperpage;
		
		$pagination = "<span><ul style=\"margin:0;\" class=\"pagination pagination-sm pull-right\">";
		
		$pagination .= $first;

		$penultimatePage = $totalpages-1;
		if ($currentpage==$penultimatePage){
			$precedingPage = $currentpage-1;
			$followingPage = $currentpage+1;
			$pagination .= "<li><a href=\"#\" onclick=\"switchPages($precedingPage)\">$precedingPage</a></li>";
			$pagination .= "<li class=\"active\"><a href=\"#\">$currentpage</a></li>";
			$pagination .= "<li><a href=\"#\" onclick=\"switchPages($followingPage)\">$followingPage</a></li>";
		}elseif ($currentpage==$totalpages){
			$firstPage = $currentpage-2;
			$secondPage = $currentpage-1;
			$pagination .= "<li><a href=\"#\" onclick=\"switchPages($firstPage)\">$firstPage</a></li>";
			$pagination .= "<li><a href=\"#\" onclick=\"switchPages($secondPage)\">$secondPage</a></li>";
			$pagination .= "<li class=\"active\"><a href=\"#\">$currentpage</a></li>";
		}else{
			$pagination .= "<li class=\"active\"><a href=\"#\">$currentpage</a></li>";
			for ($i=1; $i<3; $i++) { 
				$pageNumber = $currentpage+$i;
				$pagination .= "<li><a href=\"#\" onclick=\"switchPages($pageNumber)\">$pageNumber</a></li>";
			}
		}
		$pagination .= $last."</span>";
		//$pagination .= "<span style=\"font-size: 16px; vertical-align:top;\">（共 $totalpages 頁）</span>";
	}else{
		$pagination = "";
	}

	$j=0;

	if ($filter=="nothingtofilter"){
		$query = "SELECT lemma_id, lemma_type, cwn_pinyin, cwn_zhuyin, supersense, mod_time FROM cwn_lemma ORDER BY mod_time DESC LIMIT '$offset', '$rowsperpage'";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$j = -1;
	}else{
		$query = "SELECT lemma_id, lemma_type, cwn_pinyin, cwn_zhuyin, supersense, mod_time FROM cwn_lemma WHERE lemma_type LIKE '$filter' ORDER BY mod_time DESC";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	          $j++; 
	    } 
	}

	if ($j==0){
		echo "找不到記錄 (No records found)";
	}else{
		$table = "<div id=\"synset_table\" class=\"table-responsive\"><table style=\"margin:0;\" class=\"table table-striped table-hover\">
		              <thead>
		                <tr>
		                  <th>代號<br />(ID)</th>
		                  <th>詞形<br />(Lemma)</th>
		                  <th>拼音<br />(Pinyin)</th>
		                  <th>注音<br />(Zhuyin)</th>
		                  <th>意類<br />(Supersense)</th>
		                  <th>詞義/義面<br />(Sense/Facet)</th>
		                  <th></th>
		                </tr>
		              </thead>
		              <tbody>";
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	    	//output table rows
	    	$lemma_type = $res['lemma_type'];
	    	if ($filter!="nothingtofilter"){
	    		$lemma_type = "<mark>".$lemma_type."</mark>";
	    	}
	    	$allSenses = explode("|", findAllSenses($res['lemma_id']));
	    	$allDef = "";
	    	for($i=0; $i<count($allSenses); $i++){
	    		$allDef .= findDef($allSenses[$i])."<br />";
	    	}
	    	$allDef = rtrim($allDef, "<br />");
	     	$table .= "<tr>";
	     	$table .= "<td>".$res['lemma_id']."<br /><br /></td>";
	     	$table .= "<td><a href=\"http://lope.linguistics.ntu.edu.tw/cwnvis/index.php/senses?lid=".$res['lemma_id']."\" target=\"_blank\">".$lemma_type."</a>".findNote($res['lemma_id'])."</td>";
	        $table .= "<td>".$res['cwn_pinyin']."</td>";
	     	$table .= "<td>".$res['cwn_zhuyin']."</td>";
	     	$table .= "<td>".$res['supersense']."</td>";
	     	$table .= "<td>".$allDef."</td>";
	     	$table .= "<td><button type=\"button\" class=\"editModal btn btn-primary btn-xs\" style=\"margin:10px 10px 0 0;\" data-toggle=\"modal\" data-target=\"#editModal\" data-id=\"".$res['lemma_id']."\"data-lemma=\"".$res['lemma_type']."\"data-pinyin=\"".$res['cwn_pinyin']."\"data-zhuyin=\"".$res['cwn_zhuyin']."\"data-supersense=\"".$res['supersense']."\"><span class=\"glyphicon glyphicon-pencil\"></span></button></td>";
	     	$table .= "</tr>";
	    }
	    $table .= "	  </tbody>
	            	</table>
	              </div>";

	    if ($filter=="nothingtofilter"){
		    //$button = "<div class=\"col-xs-2 col-sm-2\"><button class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#addModal\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>";
		    $info = "<div class=\"col-xs-5 col-sm-7\">共 ".$totalrows." 筆"." (".$totalrows." records)</div>";
		}else{
		    //$info = "<div class=\"col-xs-12 col-sm-12\">找到 ".$j." 筆"." (".$j." record(s) found)</div>";
			$s="s";
			if ($j=="1"){
				$s = ""; 
			}
		    $info = "<div class=\"col-xs-12 col-sm-12\">找到 ".$j." 筆"." (".$j." record".$s." found)</div>";
		}
	    echo "<div class=\"row\" style=\"margin-bottom:10px;\">";	   
	    echo $info;  
	    echo "<div class=\"col-xs-7 col-sm-5\">".$pagination."</div>";  
	    echo "</div>";
	    echo $table;
	}
}
if(isset($_GET['filter'])&&isset($_GET['code'])){
	echo $_GET['code'];
	if(isset($_GET['p'])) {
		listWord($_GET['p'], $_GET['filter']);
	}else{
		listWord("1", $_GET['filter']);
	}
}else{
	if(isset($_GET['p'])) {
		listWord($_GET['p'], "nothingtofilter");
	}else{
		listWord("1", "nothingtofilter");
	}
}
?>