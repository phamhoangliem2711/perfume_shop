<?php
    function kiemtranguyento($x)//Kiểm tra 1 số có nguyên tố hay không
    {
	if($x<2)
		return false;
	if($x==2)
		return true;
    $i = 2;
	do{
        if($x%$i==0)
            return false;
        $i++;
    }while($i <= sqrt($x));
    return true;
    }
    function xuat_n_nguyento($n)
    {
        $count = 0;
        $num = 2;    
        $result = "";
    
        while ($count < $n) {
            if (kiemtranguyento($num)) {
                $result .= $num . " ";
                $count++;
            }
            $num++;
        }
    
        return trim($result);
    }
    echo xuat_n_nguyento(4);
?>