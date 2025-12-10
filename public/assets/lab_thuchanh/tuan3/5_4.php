<?php
    function hinhChuNhatRong($d, $r) {
        $kq = " "; 
        for ($i = 1; $i <= $r; $i++) {
            for ($j = 1; $j <= $d; $j++) {
                if ($i == 1 || $i == $r || $j == 1 || $j == $d)
                    $kq .= "* ";
                else
                    $kq .= "  ";
            }
            $kq .= "<br/>";
        }
        return $kq;
    }

    echo hinhChuNhatRong(6, 4);
?>