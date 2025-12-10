<?php
    function tongCacSo($chuoi){
        preg_match_all('/-?\d+/',$chuoi,$matches);
        print_r($matches);
        $s=0;
        foreach($matches[0] as $so){
            $s += (int)$so;
        }
        return $s;
    }
    echo tongCacSo("15THANG7");
?>