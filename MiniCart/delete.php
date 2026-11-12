<?php
session_start();

if (isset($_GET["Id"]) && isset($_SESSION["Cart"])) {

    $id = $_GET["Id"];

    foreach ($_SESSION["Cart"] as $key => $item) {

        if (isset($item["Id"]) && $item["Id"] == $id) {

            unset($_SESSION["Cart"][$key]);

            break;
        }
    }

    // 重新整理陣列編號
    $_SESSION["Cart"] = array_values($_SESSION["Cart"]);
}

// 返回購物車
header("Location: shoppingcart.php");
exit();
?>