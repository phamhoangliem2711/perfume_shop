<?php
   function showArray($arr)
   {
       ?>
       <html>
        <body>
            <table border="1">
                <tr>
                    <th>Stt</th>
                    <th>Mã Sản Phẩm</th>
                    <th>Tên Sản Phẩm</th>
                </tr>
                <?php
                    $stt = 1;
                    foreach ($arr as $product) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $stt++; ?>
                            </td>
                            <td>
                                <?php echo $product['id']; ?>
                            </td>
                            <td>
                                <?php echo $product['name']; ?>
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

   $arr = array();

   $arr[] = array("id" => "sp1", "name" => "Sản phẩm 1");
   $arr[] = array("id" => "sp2", "name" => "Sản phẩm 2");
   $arr[] = array("id" => "sp3", "name" => "Sản phẩm 3");
   showArray($arr);
?>
