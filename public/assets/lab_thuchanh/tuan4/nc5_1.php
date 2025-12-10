<?php
   function showArray($arr)
   {
       ?>
       <html>
        <body>
            <table border="1">
                <tr>
                    <th>Index</th>
                    <th>Value</th>
                </tr>
                <?php
                    foreach ($arr as $k => $v){
                        ?>
                        <tr>
                            <td>
                                <?php
                                    echo "$k"
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo "$v"
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </body>
       </html>
       <?php
   }

   // Tạo một mảng mẫu
   $arr = array(1, 2, 3, 4);
   
   // Gọi hàm showArray để hiển thị mảng
   showArray($arr);
?>