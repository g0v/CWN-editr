<?php	
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

	function getLastChar($str){
		$relationType = "";
		switch ($str[strlen($str)-1]) {
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
		return str_replace($str[strlen($str)-1], $relationType, $str);
	}

	function convTime($time){
		$gmt = new DateTimeZone('GMT');
		$est = new DateTimeZone('Asia/Taipei');
		$dtime = new DateTime($time, $gmt);
		$dtime->setTimezone($est);
		return date('Y-m-d H:i:s', $dtime->getTimestamp());
	}
	function writeLog($filter){
		$db = new SQLite3('../../cwnvis/data/cwn_dirty.sqlite');
		$query = "SELECT * FROM cwn_history WHERE details LIKE '%{$filter}%' ORDER BY time DESC";
	    $result = $db->query($query);

		$action_short = array("add", "del", "mod", "map", "skip");
		$action_long = array("added", "deleted", "modified", "mapped", "skpped adding");

	    while($res = $result->fetchArray(SQLITE3_ASSOC)){
	    	$action = str_replace($action_short, $action_long, $res['action']);
	    	$details = $res['details'];
	    	if($filter=="and"){
	    		$details = getLastChar($res['details']);
	    	}
	    	$convTime = convTime($res['time']);
	    	echo "[ ".$convTime." ] ".$res['editor']." ".$action." ".$details."<br/>";
	    }
	}
	if(isset($_GET['filter'])){
	writeLog($_GET['filter']);
	}
?>