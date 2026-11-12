
<html>
<head>
    <title>Session 練習</title>
</head>

<body>
<?php
require("about_me.php");
echo "W10-EX01" . "<br/>";

session_start(); // 啟用交談期

echo "啟用交談期<br/>";

if (!isset($_SESSION["page_counter"])) {
    $_SESSION["page_counter"] = 1; // 新增Session變數
} else {
    // 將使用者進入網頁的次數加一
    $_SESSION["page_counter"]++;
}

$value = $_SESSION["page_counter"]; // 取得Session變數

echo "使用者Session ID: " . session_id() . "<br/>";
echo "進入網頁次數: $value<br/>";

if ($value >= 5) { // 如果次數大於等於5次

    // 刪除Session變數
    unset($_SESSION["page_counter"]);

    if (!isset($_SESSION["page_counter"])) {
        echo "Session變數page_counter不存在 !<br/>";
    }

    session_destroy(); // 關閉交談期
    echo "關閉交談期<br/>";
}
?>
</body>
</html>
