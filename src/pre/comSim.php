<?php 
$var_1 = '以儀器確定速度、長度等數值，或有關地形、地物之高低、大小等狀態。'; 
$var_2 = '以儀器確定速度、長度等數值，或有關地形、地物之高低、大小等狀態。'; 

similar_text($var_1, $var_2, $percent); 

echo $percent; 
// 27.272727272727 

echo "<br/>";
similar_text($var_2, $var_1, $percent); 

echo $percent; 
// 18.181818181818 
?>