
<?php
session_start(); // 啟用交談期
?>

<html>
<head>
    <meta charset="UTF-8">
    <title>Mini Cart</title>
</head>

<body bgcolor="#FFCC77" text="blue">

<h2>我的購物車</h2>

<table border="0" cellpadding="8">

<tr bgcolor="#CC99FF">
    <td>功能</td>
    <td>編號</td>
    <td>名稱</td>
    <td>價格</td>
    <td>數量</td>
    <td>小計</td>
</tr>

<?php

$flag = false;
$total = 0;

// 讀取 Session 購物車
if (isset($_SESSION["Cart"])) {

    foreach ($_SESSION["Cart"] as $item) {

        $flag = true;

        $name = $item["Name"];
        $price = $item["Price"];
        $quantity = $item["Quantity"];

        $subtotal = $price * $quantity;
        $total += $subtotal;

        echo "<tr>";

        echo "<td>已加入</td>";
        echo "<td>-</td>";
        echo "<td>" . $name . "</td>";
        echo "<td>$" . $price . "</td>";
        echo "<td>" . $quantity . "</td>";
        echo "<td>$" . $subtotal . "</td>";

        echo "</tr>";
    }
}

// 購物車沒有商品
if ($flag == false) {
    echo "<tr>";
    echo "<td colspan='6'>購物車目前沒有商品！</td>";
    echo "</tr>";
}

?>

</table>

<?php
if ($flag == true) {
    echo "<h3>總金額：$" . $total . "</h3>";
}
?>

<hr/><br/>

| <a href="catalog.php">Catalog</a> |
<a href="shoppingcart.php">Mini Cart</a> |

<br/><br/>

<a href="clearcart.php">清空購物車</a>

</body>
</html>
