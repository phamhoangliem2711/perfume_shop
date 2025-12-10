<?php
    function kTraDoiXung($str){
        $str = trim($str);
        $strNguoc = strrev($str);
        if($str == $strNguoc)
            return true;
        else
            return false;
    }
    if(kTraDoiXung("abcbaaaa"))
        echo "doi xung";
    else
        echo "ko doi xung";
?>