
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>ch09-2-1.php</title>
</head>

<body>

<?php
require "about_me.php";

echo "W11 Ex01<br/>";
echo "<HR>";

$file = basename($_SERVER["PHP_SELF"], ".php");

// 開啟檔案
$fp = fopen($file . ".php", "r")
    or exit("檔案 $file.php 開啟錯誤<br/>");

echo "檔案名稱: $file.php 開啟成功<br/>";
echo "檔案大小: " . filesize($file . ".php") . " 位元組<br/>";

fclose($fp); // 關閉檔案

echo "檔案 $file.php 已經關閉<br/>";
?>

</body>
</html>
