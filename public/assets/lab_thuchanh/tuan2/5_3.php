<?php
    $a=0;
    $b=3;
    $c=2;
    $delta = $b*$b-4*$a*$c;
    if($a!=0){
        if($delta>0){
            $x1=(-$b+sqrt($delta)/(2*$a));
            $x2=(-$b-sqrt($delta)/(2*$a));
            echo "Phương trình có 2 nghiệm x1= $x1, x2= $x2";
        }
        elseif($delta=0){
            $x=(-$b/(2*$a));
            echo "PT có nghiệm kép x1=x2= $x";
        }
        else{
            echo "PT vô nghiêm;";
        }
    }
    else{
        if($b==0){
            if($c==0){
                echo "PT vô số nghiệm";
            }
            else{
                echo "PT vô nghiệm";
            }
        }
        else{
            $x=-$c/$b;
            echo "PT có nghiệm x= $x";
        }
    }
?>