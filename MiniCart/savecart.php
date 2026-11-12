<?php
session_start();

if (!isset($_SESSION["Cart"])) {
    $_SESSION["Cart"] = array();
}

if (!isset($_SESSION["Id"]) ||
    !isset($_SESSION["Name"]) ||
    !isset($_SESSION["Price"]) ||
    !isset($_SESSION["Quantity"])) {
    exit("商品資料不完整，請重新訂購！");
}

$id = $_SESSION["Id"];
$name = $_SESSION["Name"];
$price = $_SESSION["Price"];
$quantity = (int)$_SESSION["Quantity"];

if ($quantity <= 0) {
    exit("商品數量必須大於 0！");
}

$found = false;

foreach ($_SESSION["Cart"] as &$item) {

    if (isset($item["Id"]) && $item["Id"] == $id) {
        $item["Quantity"] += $quantity;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION["Cart"][] = array(
        "Id" => $id,
        "Name" => $name,
        "Price" => $price,
        "Quantity" => $quantity
    );
}

unset($_SESSION["Id"]);
unset($_SESSION["Name"]);
unset($_SESSION["Price"]);
unset($_SESSION["Quantity"]);

header("Location: shoppingcart.php");
exit();
?>