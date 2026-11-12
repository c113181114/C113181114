
<?php
session_start(); // 啟用交談期

if (isset($_POST["Item"])) {

    // 接收商品數量
    $_SESSION["Quantity"] = (int)$_POST["Quantity"];

    // 接收商品編號
    $id = $_POST["Item"];

    // 判斷商品
    switch (strtoupper($id)) {

        case "S001":
            $_SESSION["Name"] = "10吋平板電腦";
            $_SESSION["Price"] = 12000;
            break;

        case "S002":
            $_SESSION["Name"] = "15.6吋筆記型電腦";
            $_SESSION["Price"] = 27000;
            break;

        case "S003":
            $_SESSION["Name"] = "iPhone智慧型手機";
            $_SESSION["Price"] = 21000;
            break;

        default:
            exit("商品代碼錯誤！");
    }

    // 跳轉到 savecart.php
    header("Location: savecart.php");
    exit();
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <title>商品目錄</title>
</head>

<body bgcolor="#FFCC77" text="blue">

<h2>商品目錄</h2>

<form action="catalog.php" method="post">

    選擇商品：

    <select name="Item">
        <option value="S001">10吋平板電腦 - $12000</option>
        <option value="S002">15.6吋筆記型電腦 - $27000</option>
        <option value="S003">iPhone智慧型手機 - $21000</option>
    </select>

    <input type="text" name="Quantity" value="1" size="5" />

    <input type="submit" value="訂購" />

</form>

<hr/>

| <a href="catalog.php">Catalog</a> |
<a href="shoppingcart.php">Mini Cart</a> |

</body>
</html>
