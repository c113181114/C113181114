
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>ch9-2-2.php</title>
</head>

<body>

<?php
require "about_me.php";

echo "W11 Ex02<br/>";
echo "<HR>";

// 檔案名稱
$file = 'mybooks.txt';

$content = "PHP與MySQL網頁設計範例教本\r\n";

$fp = fopen($file, "w") //開啟檔案
    or exit("檔案 $file 開啟錯誤<br/>");

if (fwrite($fp, $content)) //寫入檔案
    print "寫入檔案 $file 成功<br/>";
else
    print "寫入檔案 $file 錯誤<br/>";

fclose($fp); //關閉檔案
?>

</body>
</html>
