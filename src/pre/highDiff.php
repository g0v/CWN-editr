<?php
 $string1 = "表猜測的語氣。";
    $string2 = "表推測的語氣。";
    
    $string1 = explode(" ", $string1);
    $string2 = explode(" ", $string2);
    $tmp = array();
    
     foreach($string2 as $k=>$value){
        if($value != $string1[$k]){
            $tmp[] = "<b>$value </b>";
        }
        else {
            $tmp[] = $value;
        }       $i++;
    }
    echo implode(' ',$tmp);
?>