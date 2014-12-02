<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);	
//set_time_limit(0);

	
		if(isset($_POST['queryString'])) {

			preg_match('#\((.*?)\)#', $_POST['queryString'], $match);


			if(strlen($match[1])==6) {

				$pieces = explode(" ", $_POST['queryString']);

				$queryString = trim(preg_replace("/\([^)]+\)/","",$pieces[0]));
				$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
				$sql = "SELECT * FROM cwn_sense WHERE lemma_id LIKE '$match[1]' ORDER BY CAST(cwn_sense.sense_id AS SIGNED) ASC";
				$results = $db->query($sql);
				
				if($results){
					$cwn_records = "<select id=\"target_list\" class=\"form-control input-lg\">";
					while ($row = $results->fetchArray()) {
						$cwn_records .= "<option value=\"".$row['sense_id']."\">(".$row['sense_id'].") ".$row['sense_def']."</option>";
					}
					$cwn_records .= "</select>";
				
					if ($cwn_records=="<select id=\"target_list\" class=\"form-control input-lg\"></select>"){
						$cwn_records = "<select id=\"target_list\" class=\"form-control input-lg\"><option value=\"nogood\">尚未定義 (not defined yet)</option></select>";
					}
				echo $cwn_records;
				}

			}else{
			
				$queryString = trim(preg_replace("/\([^)]+\)/","",$_POST['queryString']));
				$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
				$sql = "SELECT * FROM cwn_lemma, cwn_sense WHERE cwn_lemma.lemma_type LIKE '$queryString' AND cwn_sense.lemma_id LIKE cwn_lemma.lemma_id AND cwn_lemma.meet_date IS NOT NULL ORDER BY CAST(cwn_sense.sense_id AS SIGNED) ASC";
				$results = $db->query($sql);
				
				if($results){
					$cwn_records = "<select id=\"target_list\" class=\"form-control input-lg\">";
					while ($row = $results->fetchArray()) {
						$cwn_records .= "<option value=\"".$row['sense_id']."\">(".$row['sense_id'].") ".$row['sense_def']."</option>";
					}
					$cwn_records .= "</select>";
				
					if ($cwn_records=="<select id=\"target_list\" class=\"form-control input-lg\"></select>"){
						$cwn_records = "<select id=\"target_list\" class=\"form-control input-lg\"><option value=\"nogood\">尚未定義 (not defined yet)</option></select>";
					}
				echo $cwn_records;
				}
				 
			}
		}
				
			
?>