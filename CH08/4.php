
<?php
function printHeader()
{
    echo "SID: C1234567<br>";
    echo "Name: Smith<br>";
    echo "<hr>";
}

printHeader();

echo "W08-EX04<br><br>";

if (isset($_POST["Receive"])) {
    echo "取得ItemName的Cookie值: " . $_COOKIE["ItemName"] . "<br>";
    echo "取得Quantity的Cookie值: " . $_COOKIE["Quantity"] . "<br>";
}
?>

<form method="post">
    <input type="submit" name="Receive" value="Receive Cookie">
</form>
