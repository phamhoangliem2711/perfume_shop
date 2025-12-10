<?php
function postIndex($index, $value="")
{
	if (!isset($_POST[$index]))	return $value;
	return trim($_POST[$index]);
}

function checkUserName($string)
{
	if (preg_match("/^[a-zA-Z0-9._-]*$/",$string)) 
	  return true;
	return false;
}

function checkEmail($string)
{
	if (preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/", $string))
	 return true;
	return false;	
	
}
function checkPass($string){
  if(preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/",$string))
    return true;
  return false;
}
function checkPhone($string){
  if(preg_match("/^\+?[0-9]+$/",$string))
    return true;
  return false;
}
// function checkDate($string){
//   if(preg_match("/^(0[1-9]|[12][0-9]|3[01])([\/\-])(0[1-9]|1[012])([\/\-])((19|20)\d\d)$/",$string))
//     return true;
//   return false;
// }

$sm = postIndex("submit");
$username = postIndex("username");
$email = postIndex("email");
$phone = postIndex("phone");
$date = postIndex("date");
$pass = postIndex("password");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lab6_3</title>
<style>
fieldset{width:50%; margin:100px auto;}
.info{width:600px; color:#006; background:#6FC; margin:0 auto}
#frm1 input{width:300px}
</style>
</head>

<body>
<fieldset>
<legend style="margin:0 auto">Đăng ký thông tin </legend>
<form action="lab06_4_2.php" method="post" enctype="multipart/form-data" id='frm1'>
<table  align="center">
    <tr>
      <td width="88">UserName</td>
      <td width="317"><input type="text" name="username" value="<?php echo $username;?>"/>*</td></tr>
       <tr>
      <td>Mật khẩu</td>
      <td><input type="text" name="password"  />*</td></tr>
       <tr>
      <td>Email</td>
      <td><input type="text" name="email"  value="<?php echo $email;?>"  />*</td></tr>
       <tr>
      <td>Ngày sinh</td>
      <td><input type="text" name="date"  />*</td></tr>
       <tr>
      <td>Điện thoại</td>
      <td><input type="text" name="phone"  /></td></tr>
      
      <tr><td colspan="2" align="center"><input type="submit" value="submit" name="submit"></td></tr>
</table>
</form>
</fieldset>

<?php


if ($sm !="")
{
    $err_count = 0;
    $err = "";
    if (checkUserName($username)==false) {
        $err .= "Username: Các ký tự được phép: a-z, A-Z, số 0-9, ký tự ., _ và - <br>";
        $err_count++;
    }
    if (checkPass($pass)==false) {
        $err .= "Mật khẩu: Phải có tối thiểu 8 ký tự, bao gồm ít nhất 1 số, 1 chữ hoa, 1 chữ thường. <br>";
        $err_count++;
    }

    if (checkEmail($email)==false) {
        $err .= "Định dạng email sai! <br>";
        $err_count++;
    }

    // if (checkDate($date)==false) {
    //     $err .= "Ngày sinh: Định dạng ngày sai (cần dd/mm/yyyy hoặc dd-mm-yyyy). <br>";
    //     $err_count++;
    // }
    if ($phone != "" && checkPhone($phone)==false) {
        $err .= "Số điện thoại: Chỉ được nhập số (có thể có dấu + ở đầu). <br>";
        $err_count++;
    }

    if ($err_count > 0)
    {
      ?>
        <div class="error">
          <h3>Lỗi nhập liệu</h3>
          <?php echo $err; ?>
        </div>
      <?php
    } else {
      ?>
        <div class="success">
          <h3>Đăng ký thành công!</h3>
          <p>
            Username: **<?php echo $username; ?>**<br>
            Email: **<?php echo $email; ?>**<br>
            Ngày sinh: **<?php echo $date; ?>**<br>
            Điện thoại: **<?php echo $phone; ?>**
          </p>
        </div>
      <?php
    }
}
?>
</body>
</html>
