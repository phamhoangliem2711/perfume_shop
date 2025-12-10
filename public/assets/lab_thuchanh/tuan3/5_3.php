<?php
    function tinhTongChu($str){
        $s = 0;
        $len = strlen($str);
        for($i=0;$i<$len;$i++){
            $ch = $str[$i];
            if(($ch>=0 &&$ch<=9)){
                $s += (int)$ch;
            }
        }
        return $s;
    }
    echo tinhTongChu("ngay15thang7nam2015");
?>