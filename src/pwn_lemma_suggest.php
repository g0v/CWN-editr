<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	

	
		if(isset($_GET['q'])) {
			$queryString = trim(preg_replace("/\([^)]+\)/","",$_GET['q']));
			$queryString = SQLite3::escapeString($queryString);
			$db = new SQLite3('../../ajax/wordnet/sqlite-31.db');
			$sql = "SELECT DISTINCT lemma, pos FROM wordsXsensesXsynsets WHERE lemma LIKE '{$queryString}%' LIMIT 10";
			$results = $db->query($sql);
			if($results){
				$i=0;
				while ($row = $results->fetchArray()) {
					$myarray[$i] = $row['lemma']." (".$row['pos'].")";
					$i++;
				}
			}
			echo json_encode($myarray);
		}
				
			
?>