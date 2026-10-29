<?php
require("about_me.php");
echo "W08-EX04" . "<br/>";

if (isset($_COOKIE["ItemName"])) {
    $itemName = $_COOKIE["ItemName"];
    $quantity = $_COOKIE["Quantity"];

    echo "取得ItemName的Cookie值：" . $itemName . "<br/>";
    echo "取得Quantity的Cookie值：" . $quantity . "<br/>";

    setcookie("ItemName", "", time() - 3600);
    setcookie("Quantity", "", time() - 3600);
} else {
    $itemName = "white iPhone";
    $quantity = "10";
    $date = strtotime("+10 days", time());

    setcookie("ItemName", $itemName, $date);
    setcookie("Quantity", $quantity, $date);

    echo "新增名為ItemName的Cookie：" . $itemName . "<br/>";
    echo "新增名為Quantity的Cookie：" . $quantity . "<br/>";
    echo "Cookie期限：" . date("l F j Y h:i:s A", $date);
}
?>