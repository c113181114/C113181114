
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hours = trim($_POST["hours"] ?? "");

    if ($hours === "") {
        $message = "請輸入靠泊時間";
    } elseif (!is_numeric($hours)) {
        $message = "請輸入有效的數字";
    } elseif ((float)$hours < 0) {
        $message = "靠泊時間不能是負數";
    } elseif ((float)$hours >= 20) {
        $message = "長時間靠泊";
    } else {
        $message = "一般靠泊";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>船舶靠泊時間</title>
</head>
<body>
    <h2>船舶靠泊時間判斷</h2>

    <form method="post">
        <label>請輸入靠泊時間（小時）：</label>
        <input type="text" name="hours">
        <button type="submit">送出</button>
    </form>

    <?php
    if (isset($message)) {
        echo "<p>" . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . "</p>";
    }
    ?>
</body>
</html>
