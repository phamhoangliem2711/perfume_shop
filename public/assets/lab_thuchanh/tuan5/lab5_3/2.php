<?php
function postIndex($index, $value="")
{
	if (!isset($_POST[$index]))	return $value;
	return $_POST[$index];
}

$sm 	= postIndex("submit");
$ten 	= postIndex("ten");
$gt 	= postIndex("gt");
$arrImg = array("image/png", "image/jpeg", "image/bmp");

if ($sm=="") {
				header("location:1.php"); exit;//quay ve 1.php
			}

$err = "";
if ($ten=="") $err .="Phải nhập tên <br>";
if ($gt=="") $err .="Phải chọn giới tính <br>";

// $errFile = $_FILES["hinh"]["error"];
// if ($errFile>0)
// 	$err .="Lỗi file hình <br>";
// else
// {
// 	$type = $_FILES["hinh"]["type"];
// 	if (!in_array($type, $arrImg))
// 		$err .="Không phải file hình <br>";
// 	else
// 	{	$temp = $_FILES["hinh"]["tmp_name"];
// 		$name = $_FILES["hinh"]["name"];
// 		if (!move_uploaded_file($temp, "image/".$name))
// 			$err .="Không thể lưu file<br>";
		
// 	}
// }
$uploadedFiles = []; // Mảng lưu các tệp đã upload

// Kiểm tra và xử lý các file hình ảnh
if (isset($_FILES["hinh"])) {
    $totalFiles = count($_FILES["hinh"]["name"]); // Số lượng file được chọn
    for ($i = 0; $i < $totalFiles; $i++) {
        $fileError = $_FILES["hinh"]["error"][$i]; // Kiểm tra lỗi của từng file
        if ($fileError > 0) {
            $err .= "Lỗi file hình tại vị trí $i <br>";
        } else {
            $type = $_FILES["hinh"]["type"][$i]; // Kiểm tra loại file
            if (!in_array($type, $arrImg)) {
                $err .= "Không phải file hình tại vị trí $i <br>";
            } else {
                $temp = $_FILES["hinh"]["tmp_name"][$i]; // Tên tạm của file
                $name = $_FILES["hinh"]["name"][$i]; // Tên thật của file
                $uploadPath = "image/" . $name; // Đường dẫn để lưu file

                // Di chuyển file lên thư mục 'image'
                if (!move_uploaded_file($temp, $uploadPath)) {
                    $err .= "Không thể lưu file tại vị trí $i<br>";
                } else {
                    // Lưu đường dẫn file vào mảng nếu upload thành công
                    $uploadedFiles[] = $uploadPath;
                }
            }
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lab5_3/2</title>
</head>
<body>
<?php
if ($err !="")
  echo $err;
else
{
	if($gt =="1") echo "Chào Anh: $ten ";
	else echo "Chào Chị $ten ";
	?><hr>
    <?php
    // Hiển thị tất cả các hình ảnh đã upload
    foreach ($uploadedFiles as $file) {
        echo '<img src="' . $file . '" width="200" height="200"><br>';
    }
}
?>
<p>
<a href="1.php">Tiếp tục</a>
</p>
</body>
</html>