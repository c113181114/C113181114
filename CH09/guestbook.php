
<?php
session_start();

require "about_me.php";
echo "W11 Ex03<br/>";
echo "<HR>";

// 初始化留言資料
if (!isset($_SESSION["messages"])) {
    $_SESSION["messages"] = [];
}

// 初始化訊息
$success = "";
$error = "";

// 接收表單資料
if (isset($_POST["send"])) {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $content = trim($_POST["content"]);

    // 檢查欄位是否填寫
    if ($name != "" && $email != "" && $content != "") {

        // 新增留言
        $_SESSION["messages"][] = [
            "name" => $name,
            "email" => $email,
            "content" => $content
        ];

        // 設定成功訊息
        $_SESSION["success"] = "新增留言成功！";

        // 重新導向
        header("Location: guestbook.php");
        exit();

    } else {
        $error = "請填寫所有欄位！";
    }
}

// 顯示新增成功訊息
if (isset($_SESSION["success"])) {
    $success = $_SESSION["success"];
    unset($_SESSION["success"]);
}
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<title>線上留言板</title>

<style>

body {
    font-family: "Microsoft JhengHei", sans-serif;
    font-size: 20px;
}

table {
    width: 450px;
    border: 1px solid gray;
    padding: 2px;
    table-layout: fixed;
}

td {
    border: 1px solid gray;
    padding: 5px;
}

td:first-child {
    width: 110px;
    white-space: nowrap;
}

input[type="text"],
input[type="email"] {
    width: 95%;
    height: 28px;
    font-size: 18px;
    box-sizing: border-box;
}

textarea {
    width: 95%;
    height: 90px;
    font-size: 18px;
    box-sizing: border-box;
}

input[type="submit"],
input[type="reset"] {
    font-size: 17px;
    padding: 4px 10px;
    margin: 3px;
    cursor: pointer;
}

.success {
    color: green;
    font-weight: bold;
}

.error {
    color: red;
}

.links {
    margin-top: 50px;
}

</style>
</head>

<body>

<?php

// 新增留言畫面
if (!isset($_GET["page"]) || $_GET["page"] == "add") {

?>

<form method="POST" action="guestbook.php">

<table>

<tr>
    <td>姓名：</td>
    <td>
        <input type="text" name="name"
        value="<?php echo isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : ""; ?>">
    </td>
</tr>

<tr>
    <td>電子郵件：</td>
    <td>
        <input type="email" name="email"
        value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""; ?>">
    </td>
</tr>

<tr>
    <td>留言內容：</td>
    <td>
        <textarea name="content"><?php
        echo isset($_POST["content"]) ? htmlspecialchars($_POST["content"]) : "";
        ?></textarea>
    </td>
</tr>

<tr>
    <td colspan="2" align="center">

        <input type="submit"
        name="send"
        value="送出留言">

        <input type="reset"
        value="清除欄位">

    </td>
</tr>

</table>

</form>

<?php

// 顯示成功訊息
if ($success != "") {
    echo "<p class='success'>";
    echo htmlspecialchars($success);
    echo "</p>";
}

// 顯示錯誤訊息
if ($error != "") {
    echo "<p class='error'>";
    echo htmlspecialchars($error);
    echo "</p>";
}

} else {

// 檢視留言畫面

?>

<h2>留言列表</h2>

<?php

if (count($_SESSION["messages"]) > 0) {

    foreach ($_SESSION["messages"] as $msg) {

        echo "<hr>";

        echo "姓名：" .
        htmlspecialchars($msg["name"]) . "<br>";

        echo "電子郵件：" .
        htmlspecialchars($msg["email"]) . "<br>";

        echo "留言內容：" .
        nl2br(htmlspecialchars($msg["content"])) . "<br>";
    }

} else {

    echo "目前沒有留言！";

}

}

?>

<!-- 下方功能連結 -->
<div class="links">

    | <a href="guestbook.php?page=add">新增留言</a> |

    <a href="guestbook.php?page=view">檢視留言</a> |

</div>

</body>
</html>
