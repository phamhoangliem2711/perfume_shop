<?php
    function BCC($n,$colorHead="red",$color1="blue", $color2="black")
    {
        $html = "<table border='1' cellspacing='0' cellpadding='5'>";
        $html .= "<tr style='background-color:$colorHead; text-align:center; font-weight:bold;'>
                    <td colspan='3'>Bảng cửu chương $n</td>
                  </tr>";
    
        for ($i = 1; $i <= 10; $i++) {
            $bg = ($i % 2 == 0) ? $color2 : $color1;
            $html .= "<tr style='background-color:$bg; text-align:center;'>
                        <td>$n</td>
                        <td>x $i</td>
                        <td>" . ($n * $i) . "</td>
                      </tr>";
        }
    
        $html .= "</table>";
        return $html;
    }
    function BanCo($size = 8){
    $html = "<div id='banco'>";
    for ($i = 1; $i <= $size; $i++) {
        for ($j = 1; $j <= $size; $j++) {
            $classCss = (($i + $j) % 2 == 0) ? "cellWhite" : "cellBlack";
            $html .= "<div class='$classCss'></div>";
        }
        $html .= "<div class='clear'></div>";
    }
    $html .= "</div>";
    return $html;
    }
?>