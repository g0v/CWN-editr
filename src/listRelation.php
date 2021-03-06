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
 		//return "(".$id.")<br /> ".$res['lemma_type'];
 		return '('.$id.')<br /> <a href="http://lope.linguistics.ntu.edu.tw/cwnvis/index.php/senses?lid='.substr($cwn_id,0,6).'" target="_blank">'.$res['lemma_type'].'</a>';
	}
}

function findDef($cwn_id){
	$tableName = "cwn_sense";
	$idName = "sense_id";
	$defName = "sense_def";
	$id = substr($cwn_id, 6, 8);
	if (strlen($cwn_id)==10){
		$tableName = "cwn_facet";
		$idName = "facet_id";
		$defName = "facet_def";
		$id = substr($cwn_id, 6, 10);
	}
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
	$query = "SELECT * FROM \"".$tableName."\" WHERE \"".$idName."\" LIKE '$cwn_id'";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
 		return "(".$id.") ".$res[$defName];
	}
}

function listRelation($page, $filter){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    
    if ($filter=="nothingtofilter"){
		$query = "SELECT COUNT(cwn_id) as count FROM cwn_relation";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$row = $result->fetchArray();
		$totalrows = $row['count'];
	}else{
		$query = "SELECT cwn_lemma, rel_lemma FROM cwn_relation WHERE cwn_lemma LIKE '$filter' OR rel_lemma LIKE '$filter'";
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
		$query = "SELECT * FROM cwn_relation ORDER BY relation_id DESC LIMIT '$offset', '$rowsperpage'";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$j = -1;
	}else{
		$query = "SELECT * FROM cwn_relation WHERE cwn_lemma LIKE '$filter' OR rel_lemma LIKE '$filter' ORDER BY relation_id DESC";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	          $j++; 
	    } 
	}

	if ($j==0){
		echo "找不到記錄 (No records found)";
	}else{
		$table = "<div id=\"relation_table\" class=\"table-responsive\"><table style=\"margin:0;\" class=\"table table-striped table-hover\">
		              <thead>
		                <tr>
		                  <th class=\"text-right\">來源詞<br />(Source)</th>
		                  <th>來源詞義/義面<br />(Source Sense/Facet)</th>
		                  <th class=\"text-right\">標的詞<br />(Target)</th>
		                  <th>標的詞義/義面<br />(Target Sense/Facet)</th>
		                  <th>關係<br />(Relation)</th>
		                  <th></th>
		                </tr>
		              </thead>
		              <tbody>";
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	    	$source_IDlemma = findLemma($res['cwn_id']);
	    	$source_IDdef = findDef($res['cwn_id']);
	    	$target_lemma = preg_replace('/[0-9]+/', '', $res['rel_lemma']);
	    	$target_IDlemma = "(".substr($res['rel_cwnid'],0, 6).")<br /><a href=\"http://lope.linguistics.ntu.edu.tw/cwnvis/index.php/senses?lid=".substr($res['rel_cwnid'],0, 6)."\" target=\"_blank\">".$target_lemma."</a>";
	     	if ($filter!="nothingtofilter"){
	    		$marked = "<mark>".$filter."</mark>";
	    		$source_IDlemma = str_ireplace($filter, $marked, $source_IDlemma);
	    		$target_IDlemma = str_ireplace($filter, $marked, $target_IDlemma);
	    	}
	     	$target_ID = $res['rel_cwnid'];
	     	if (substr($res['rel_facet'],-1)!='0'){
	     		$target_ID = $res['rel_cwnid']."0".substr($res['rel_facet'],-1);
	     	}
	    	$target_IDdef = findDef($target_ID);

	    	switch ($res['rel_type']) {
			    case "=":
			        $relationType = "同義 (synonymy)";
			        break;
			    case "!":
			        $relationType = "反義 (antonymy)";
			        break;
			    case "@":
			        $relationType = "上位 (hypernymy)";
			        break;
			    case "~":
			        $relationType = "下位 (hyponymy)";
			        break;
			    case "#":
			        $relationType = "整體 (holonymy)";
			        break;
			    case "%":
			        $relationType = "部分 (meronymy)";
			        break;
			    case "&":
			        $relationType = "近義 (near-synonymy)";
			        break;
			    case "+":
			        $relationType = "類義 (paranymy)";
			        break;
			    case "?":
			        $relationType = "附屬 (pertainymy)";
			        break;
			    case ">":
			        $relationType = "致使 (causality)";
			        break;
			    case "<":
			        $relationType = "方式 (troponymy)";
			        break;
			    case "^":
			        $relationType = "異體 (variant)";
			        break;
			}

			if($res['rel_type']=="%"){
				$relationSymbol = "|%";
			}else{
				$relationSymbol = $res['rel_type'];
			}

	    	//output table rows
	     	$table .= "<tr>";
	     	$table .= "<td class=\"text-right\">".$source_IDlemma."</td>";
	     	$table .= "<td>".$source_IDdef."</td>";
	     	$table .= "<td class=\"text-right\">".$target_IDlemma."</td>";
	     	$table .= "<td>".$target_IDdef."</td>";
	     	$table .= "<td>".$relationType."</td>";
	     	$table .= "<td><button type=\"button\" class=\"delModal btn btn-danger btn-xs\" style=\"margin:10px 10px 0 0;\" data-toggle=\"modal\" data-target=\"#delModal\" data-cwn_id=\"".$res['cwn_id']."\" data-rel_type=\"".$relationSymbol."\" data-rel_cwnid=\"".$res['rel_cwnid']."\" data-rel_facet=\"".$res['rel_facet']."\"><span class=\"glyphicon glyphicon-remove\"></span></button></td>";
	     	$table .= "</tr>";
	    }
	    $table .= "	  </tbody>
	            	</table>
	              </div>";

	    if ($filter=="nothingtofilter"){
		    //$button = "<div class=\"col-xs-2 col-sm-2\"><button class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#addModal\"><span class=\"glyphicon glyphicon-plus\"></span></button></div>";
		    $info = "<div class=\"col-xs-5 col-sm-7\">共 ".$totalrows." 筆"." (".$totalrows." records)</div>";
		}else{
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
		listRelation($_GET['p'], $_GET['filter']);
	}else{
		listRelation("1", $_GET['filter']);
	}
}else{
	if(isset($_GET['p'])) {
		listRelation($_GET['p'], "nothingtofilter");
	}else{
		listRelation("1", "nothingtofilter");
	}
}
?>