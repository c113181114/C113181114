<?php
include("about_me.php");

echo "EX04<br/>";

$ip = $_SERVER['REMOTE_ADDR']; // 取得 IP 位址
echo "IP ADDRESS:" . $ip . "<br/>";

$path = $_SERVER["SCRIPT_NAME"];
echo "PATH:" . $path . "<br/>";
?>

<table border="1">
    <tr>
        <td>Key</td>
        <td>Value</td>
    </tr>

<?php
foreach ($_SERVER as $key => $value) {
    echo "<tr><td>" . $key . "</td>";
    echo "<td>" . $value . "</td></tr>";
}
?>

</table>