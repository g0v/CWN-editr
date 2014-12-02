<?php

function lemma_total_counter(){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT meet_date FROM cwn_lemma WHERE meet_date IS NOT NULL";

	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	$i = 0; 

	while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	      $i++;
	} 

	return $i;
}

function sense_total_counter(){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT sense_id FROM cwn_sense";

	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	$i = 0; 

	while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	      $i++;
	} 

	return $i;
}

function facet_total_counter(){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT facet_id FROM cwn_facet";

	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	$i = 0; 

	while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	      $i++;
	} 

	return $i;
}

function synset_total_counter(){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT id FROM cwn_goodSynset";

	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	$i = 0; 

	while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	      $i++;
	} 

	return $i;
}

function lemma_relation_counter($relation){

	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT rel_type FROM cwn_relation WHERE rel_type LIKE '$relation' ESCAPE '|'";

	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 

	$i = 0; 

	while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
	      $i++;
	} 

	return $i;
}

$arr = array('lemma_addition' => lemma_total_counter()-10653,
			 'sense_addition' => sense_total_counter()-28090,
			 'facet_addition' => facet_total_counter()-6268,
			 'synset_addition' => synset_total_counter()-3986,
			 'sense_synonymy' => lemma_relation_counter('=')-32326,
			 'sense_antonymy' => lemma_relation_counter('!')-5093,
			 'sense_hypernymy' => lemma_relation_counter('@')-1104,
			 'sense_hyponym' => lemma_relation_counter('~')-548,
			 'sense_holonymy' => lemma_relation_counter('#')-18,
			 'sense_meronymy' => lemma_relation_counter('|%')-10,
			 'sense_near_synonymy' => lemma_relation_counter('&')-4907,
			 'sense_paranymy' => lemma_relation_counter('+')-0,
			 'sense_pertainymy' => lemma_relation_counter('?')-0,
			 'sense_causality' => lemma_relation_counter('>')-0,
			 'sense_troponymy' => lemma_relation_counter('<')-0,
			 'sense_variant' => lemma_relation_counter('^')-1893,
			);

$json_string = json_encode($arr);

echo $json_string;

?>