<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);	
set_time_limit(0);

	
		if(isset($_POST['queryString'])) {

			if(preg_match('#\((.*?)\)#', $_POST['queryString'])) {
				preg_match('#\((.*?)\)#', $_POST['queryString'], $match);
				$queryString = SQLite3::escapeString($_POST['queryString']);
				$pieces = explode(" (", $queryString);
				$queryString = trim(preg_replace("/\([^)]+\)/","",$pieces[1]));
				$db = new SQLite3('../../ajax/wordnet/sqlite-31.db');
				$sql = "SELECT lemma, pos, definition, synsetid FROM wordsXsensesXsynsets WHERE lemma LIKE '$pieces[0]' AND pos LIKE '$match[1]' ORDER BY CAST(wordsXsensesXsynsets.synsetid AS SIGNED) ASC";
				$results = $db->query($sql);
				
				if($results){
					$cwn_records = "<select id=\"synset_list\" class=\"form-control input-lg\">";
					while ($row = $results->fetchArray()) {
						$cwn_records .= "<option value=\"".$row['synsetid']."\">(".$row['synsetid'].") ".$row['definition']."</option>";
					}
					$cwn_records .= "</select>";
				
					if ($cwn_records=="<select id=\"synset_list\" class=\"form-control input-lg\"></select>"){
						$cwn_records = "<select id=\"synset_list\" class=\"form-control input-lg\"><option value=\"nogood\">尚未定義 (not defined yet)</option></select>";
					}
				echo $cwn_records;
				}

			}else{
			
				$queryString = SQLite3::escapeString($_POST['queryString']);
				$queryString = trim(preg_replace("/\([^)]+\)/","",$queryString));
				$db = new SQLite3('../../ajax/wordnet/sqlite-31.db');
				$sql = "SELECT lemma, pos, definition, synsetid FROM wordsXsensesXsynsets WHERE lemma LIKE '$queryString' ORDER BY CAST(wordsXsensesXsynsets.synsetid AS SIGNED) ASC";
				$results = $db->query($sql);
				
				if($results){
					$cwn_records = "<select id=\"synset_list\" class=\"form-control input-lg\">";
					while ($row = $results->fetchArray()) {
						$cwn_records .= "<option value=\"".$row['synsetid']."\">(".$row['synsetid'].") ".$row['definition']."</option>";
					}
					$cwn_records .= "</select>";
				
					if ($cwn_records=="<select id=\"synset_list\" class=\"form-control input-lg\"></select>"){
						$cwn_records = "<select id=\"synset_list\" class=\"form-control input-lg\"><option value=\"nogood\">尚未定義 (not defined yet)</option></select>";
					}
				echo $cwn_records;
				}		 
			}
		}
				
			
?>