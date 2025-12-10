<?php
    $a=3.14;
    if(is_int($a))
    {
        echo "$a là số nguyên";
    }
    elseif(is_double($a)){
        echo "$a là số thực";
    }
    else{
        echo "$a ko là số";
    }
?>