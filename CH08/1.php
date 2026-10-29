
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8" />
    <title>ch8-1-2.php</title>
</head>

<body>
    <?php
    echo "SID: C113181114<br/>";
    echo "Name: 郭韋廷<br/>";
    echo "<hr/>";

    $ip = $_SERVER["REMOTE_ADDR"];
    $path = $_SERVER["SCRIPT_NAME"];
    $server = $_SERVER["SERVER_SOFTWARE"];

    print "IP位址: " . $ip . "<br/>";
    print "路徑: " . $path . "<br/>";
    print "Web伺服器: " . $server . "<br/>";
    ?>

    <br/>

    <table border="1" cellpadding="5">
        <tr>
            <th>名稱</th>
            <th>值</th>
        </tr>

        <?php
        // 顯示所有 $_SERVER 變數
        foreach ($_SERVER as $key => $value) {
            echo "<tr><td>" . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . "</td>";
            echo "<td>" . htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8") . "</td></tr>";
        }
        ?>
    </table>
</body>

</html>
