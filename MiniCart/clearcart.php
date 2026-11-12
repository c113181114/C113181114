<?php
session_start();

// 清除購物車資料
unset($_SESSION["Cart"]);
unset($_SESSION["Name"]);
unset($_SESSION["Price"]);
unset($_SESSION["Quantity"]);

// 返回購物車頁面
header("Location: shoppingcart.php");
exit();
?>