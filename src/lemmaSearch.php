<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
		if(isset($_GET['q'])) {
			$queryString = trim(preg_replace("/\([^)]+\)/","",$_GET['q']));
			
			$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
			$sql = "SELECT lemma_type, lemma_id, meet_date FROM cwn_lemma WHERE lemma_type LIKE '$queryString' ORDER BY length(lemma_type) LIMIT 10";
			$results = $db->query($sql);
			$myarray=array();
			$i=0;
			if($results){
				while ($row = $results->fetchArray()) {
					$myarray[$i] = "<a target=\"_blank\" href=\"http://lope.linguistics.ntu.edu.tw/cwnvis/index.php/senses?lid=".$row['lemma_id']."\">".$row['lemma_type']."</a> (".$row['lemma_id'].")";
					$i++;
				}
			}
			if($i>0){
				$entry = "entry";
				if($i>1){$entry="entries";}
				$all = "已有 ".$i." 個詞條使用此詞形 (Found ".$i." ".$entry." having this lemma)：<br />";
				for($j=0; $j<$i; $j++){
					$all .= $myarray[$j]."、";
				}
				$all = rtrim($all, "、");
				$all .= "<br />確定還要新增嗎？(Still want to add a new one?)";
				echo $all; 
			}
		}		
?>