<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	

	
		if(isset($_GET['q'])) {
			$queryString = trim(preg_replace("/\([^)]+\)/","",$_GET['q']));
			
			$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
			$sql = "SELECT lemma_type, lemma_id, meet_date FROM cwn_lemma WHERE lemma_type LIKE '$queryString%' ORDER BY length(lemma_type) LIMIT 10";
			$results = $db->query($sql);
			if($results){
				$i=0;
				while ($row = $results->fetchArray()) {
					$myarray[$i] = $row['lemma_type']." (".$row['lemma_id'].")";
					$i++;
				}
			}
			if(isset($myarray)){
				echo json_encode($myarray);
			}
		}
				
			
?>