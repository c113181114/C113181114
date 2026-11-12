
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

// 建立購物車
if (!isset($_SESSION["Cart"])) {
    $_SESSION["Cart"] = array();
}

// 檢查商品資料
if (!isset($_SESSION["Name"]) ||
    !isset($_SESSION["Price"]) ||
    !isset($_SESSION["Quantity"])) {

    exit("錯誤：沒有接收到商品資料，請返回商品目錄重新訂購！");
}

$name = $_SESSION["Name"];
$price = $_SESSION["Price"];
$quantity = (int)$_SESSION["Quantity"];

if ($quantity <= 0) {
    exit("錯誤：商品數量必須大於 0！");
}

// 檢查是否已有相同商品
$found = false;

foreach ($_SESSION["Cart"] as &$item) {
    if ($item["Name"] == $name) {
        $item["Quantity"] += $quantity;
        $found = true;
        break;
    }
}
unset($item);

// 新增商品
if (!$found) {
    $_SESSION["Cart"][] = array(
        "Name" => $name,
        "Price" => $price,
        "Quantity" => $quantity
    );
}

// 清除暫存資料
unset($_SESSION["Name"]);
unset($_SESSION["Price"]);
unset($_SESSION["Quantity"]);

// 前往購物車
header("Location: shoppingcart.php");
exit();
?>
