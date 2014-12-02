<?php
	$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');

	$query = "SELECT MAX(CAST(lemma_id AS UNSIGNED)) AS lemma_id FROM cwn_lemma;";
	$result = $db->query($query) or die("Error in query: <span style='color:red;'>$query</span>"); 
    
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
          $largest = $res['lemma_id'];
    }
    $id = $largest+1;
    echo '<form class="form-horizontal" role="form">
		    <div class="form-group">
		      <label class="col-sm-4 control-label input-lg">代號 (ID)</label>
		      <div class="col-sm-8">
		        <p class="form-control-static input-lg" style="padding-left:0;">'.$id.'</p>
		      </div>
		    </div>
		    <div class="form-group">
		      <label for="inputPassword" class="col-sm-4 control-label input-lg">詞形 (Lemma)</label>
		      <div class="col-sm-8">
		        <input type="text" class="form-control input-lg" id="lemma" onkeyup="lemmaSearch(this.value);">
		      </div>
		    </div>
		  </form>';
?>