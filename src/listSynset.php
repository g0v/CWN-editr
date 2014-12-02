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

function listSynset($page, $filter){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
    
    if ($filter=="nothingtofilter"){
		$query = "SELECT COUNT(id) as count FROM cwn_goodSynset";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$row = $result->fetchArray();
		$totalrows = $row['count'];
	}else{
		$query = "SELECT gloss FROM cwn_goodSynset WHERE gloss LIKE '%{$filter}%'";
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
		$query = "SELECT * FROM cwn_goodSynset ORDER BY mod_time DESC LIMIT '$offset', '$rowsperpage'";
		$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
		$j = -1;
	}else{
		$query = "SELECT * FROM cwn_goodSynset WHERE gloss LIKE '%{$filter}%' ORDER BY mod_time DESC";
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
		                  <th>釋義<br />(Gloss)</th>
		                  <th>成員<br />(Members)</th>
		                  <th>PWN 代號<br />(PWN ID)</th>
		                  <th>PWN 釋義<br />(PWN Gloss)</th>
		                  <th></th>
		                </tr>
		              </thead>
		              <tbody>";
	    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	    	//output table rows
	     	$all_mem_id = explode(", ", $res['member']);
	     	$all_mem_char = "";
	     	$gloss = $res['gloss'];
	     	if ($filter!="nothingtofilter"){
	    		$marked = "<mark>".$filter."</mark>";
	    		$gloss = str_ireplace($filter, $marked, $gloss);
	    	}
	     	for($k=0; $k<count($all_mem_id); $k++){
	     		$all_mem_char .= findLemma($all_mem_id[$k])."、"; 
	     	}
	     	$all_mem_char = rtrim($all_mem_char, "、");
	     	$table .= "<tr>";
	     	$table .= "<td>".sprintf("%09d", $res['id'])."<br /><br /></td>";
	     	$table .= "<td>".$gloss."</td>";
	     	$table .= "<td>".$all_mem_char."</td>";
	     	$table .= "<td>".$res['pwn_id']."</td>";
	     	$table .= "<td>".$res['pwn_gloss']."</td>";
	     	$table .= "<td><button type=\"button\" class=\"editModal btn btn-primary btn-xs\" style=\"margin:10px 10px 0 0;\" data-toggle=\"modal\" data-target=\"#editModal\" data-id=\"".$res['id']."\"data-pwn_id=\"".$res['pwn_id']."\"data-pwn_gloss=\"".$res['pwn_gloss']."\"data-pwn_word=\"".$res['pwn_word']."\"><span class=\"glyphicon glyphicon-pencil\"></span></button></td>";
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
	echo "<br/><div class=\"col-xs-5 col-sm-5\"></div><div class=\"col-xs-2 col-sm-2 text-center\"><div class=\"input-group input-group-sm\">
   <span class=\"input-group-addon\">PAGE</span><input id=\"pageNumber\" type=\"number\" class=\"form-control text-center\" min=\"1\" max=\"$totalpages\" step=\"1\" value=\"$currentpage\">
       <span class=\"input-group-btn\">
        <button class=\"btn btn-default\" type=\"button\" onclick=\"goToPage();\">GO</button>
      </span>
</div><div class=\"col-xs-5 col-sm-5\"></div>";
}
if(isset($_GET['filter'])&&isset($_GET['code'])){
	echo $_GET['code'];
	if(isset($_GET['p'])) {
		listSynset($_GET['p'], $_GET['filter']);
	}else{
		listSynset("1", $_GET['filter']);
	}
}else{
	if(isset($_GET['p'])) {
		listSynset($_GET['p'], "nothingtofilter");
	}else{
		listSynset("1", "nothingtofilter");
	}
}
?>