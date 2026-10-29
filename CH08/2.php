
<?php
include("about_me.php");

$name = "";
$username = "";
$pass1 = "";
$pass2 = "";
$error = "";
$showform = true;

if (isset($_GET["Reg"])) {

    $name = $_GET["Name"] ?? "";
    $username = $_GET["UserName"] ?? "";
    $pass1 = $_GET["Pass1"] ?? "";
    $pass2 = $_GET["Pass2"] ?? "";

    if (empty($username)) {
        $error = "帳號欄位空白<br/>";
    } elseif (empty($pass1)) {
        $error = "密碼欄位空白<br/>";
    } elseif ($pass1 != $pass2) {
        $error = "兩次輸入的密碼不相同<br/>";
    } else {
        $showform = false;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <title>W08-EX03</title>
</head>

<body>

<hr>

<?php
if ($showform) {
?>

<div style="color:red">
    <?php echo $error; ?>
</div>

<form action="2.php" method="get">

    姓名：
    <input type="text" name="Name" size="10"
    value="<?php echo htmlspecialchars($name); ?>"><br>

    帳號：
    <input type="text" name="UserName" size="10"
    value="<?php echo htmlspecialchars($username); ?>"><br>

    密碼：
    <input type="password" name="Pass1" size="10"><br>

    再輸一次密碼：
    <input type="password" name="Pass2" size="10"><br><br>

    <input type="submit" name="Reg" value="註冊使用者">

</form>

<?php
} else {
?>

W08-EX03<br>
Name: <?php echo htmlspecialchars(strtoupper($name)); ?><br>
UserName: <?php echo htmlspecialchars($username); ?><br>
Password: <?php echo htmlspecialchars($pass1); ?><br>

<?php
}
?>

</body>
</html>
