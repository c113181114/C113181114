
<?php
require "about_me.php";

echo "W11 Ex01<br/>";
echo "<HR>";

$file = basename($_SERVER["PHP_SELF"], ".php");
$path = realpath($file . ".php"); //取得檔案實際路徑

echo "real path: $path <BR/>";

$part = pathinfo($path); //取得路徑資訊

echo "path: " . $part["dirname"] . "<BR/>";
echo "filename: " . $part["basename"] . "<BR/>";
echo "extension: " . $part["extension"] . "<HR/>";
?>
</body>
</html>
