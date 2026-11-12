
<?php
require("about_me.php");
echo "W10-EX02" . "<br/>";
session_start();
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
$total = 0;
$flag = false;
$colors = array("#FF99CC", "#99FFCC");
$i = 0;

if (isset($_SESSION["Cart"]) && !empty($_SESSION["Cart"])) {

    foreach ($_SESSION["Cart"] as $item) {

        $flag = true;

        // 取得商品資料
        $id = isset($item["Id"]) ? $item["Id"] : "";
        $name = isset($item["Name"]) ? $item["Name"] : "";
        $price = isset($item["Price"]) ? $item["Price"] : 0;
        $quantity = isset($item["Quantity"]) ? $item["Quantity"] : 0;

        // 顯示商品資料，方便檢查編號
        echo "<!-- ";
        var_dump($item);
        echo " -->";

        // 計算小計
        $subtotal = $price * $quantity;
        $total += $subtotal;

        // 交錯顏色
        $color = $colors[$i % 2];
        $i++;

        echo "<tr bgcolor='" . $color . "'>";

        // 刪除功能
        if ($id != "") {
            echo "<td><a href='delete.php?Id=" . urlencode($id) . "'>刪除</a></td>";
        } else {
            echo "<td>無編號</td>";
        }

        echo "<td>" . htmlspecialchars($id) . "</td>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td>$" . $price . "</td>";
        echo "<td>" . $quantity . "</td>";
        echo "<td>$" . $subtotal . "</td>";

        echo "</tr>";
    }
}

// 購物車沒有商品
if ($flag == false) {
    echo "<tr><td colspan='6'>購物車目前沒有商品！</td></tr>";
}
?>

</table>

<?php
// 顯示總金額
if ($flag == true) {
    echo "<h3>總金額：$" . $total . "</h3>";
}
?>

<hr/>

| <a href="catalog.php">Catalog</a> |
<a href="shoppingcart.php">Mini Cart</a> |

<br/><br/>

<a href="clearcart.php">清空購物車</a>

</body>
</html>
