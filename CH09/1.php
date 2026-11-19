
<?php
session_start();

$file = "guestbook.json";

if (!isset($_SESSION["token"])) {
    $_SESSION["token"] = bin2hex(random_bytes(32));
}

function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function countChars($value) {
    preg_match_all('/./us', $value, $matches);
    return count($matches[0]);
}

function getMessages($file) {
    if (!file_exists($file)) {
        return array();
    }

    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : array();
}

function saveMessage($file, $message) {
    $fp = fopen($file, "c+");

    if (!$fp) {
        return false;
    }

    flock($fp, LOCK_EX);

    $content = stream_get_contents($fp);
    $messages = json_decode($content, true);

    if (!is_array($messages)) {
        $messages = array();
    }

    $messages[] = $message;

    rewind($fp);
    ftruncate($fp, 0);

    $result = fwrite(
        $fp,
        json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );

    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return $result !== false;
}

$msg = "";
$msgType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["token"] ?? "";

    if (!hash_equals($_SESSION["token"], $token)) {

        $msg = "表單驗證失敗，請重新整理頁面。";
        $msgType = "error";

    } else {

        $name = trim($_POST["Name"] ?? "");
        $email = trim($_POST["Email"] ?? "");
        $message = trim($_POST["Message"] ?? "");

        if ($name === "" || $email === "" || $message === "") {

            $msg = "請完整填寫所有欄位！";
            $msgType = "error";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $msg = "Email 格式不正確！";
            $msgType = "error";

        } elseif (countChars($name) > 30) {

            $msg = "姓名不能超過 30 個字！";
            $msgType = "error";

        } elseif (countChars($message) > 300) {

            $msg = "留言不能超過 300 個字！";
            $msgType = "error";

        } else {

            $newMessage = array(
                "id" => bin2hex(random_bytes(8)),
                "name" => $name,
                "email" => $email,
                "message" => $message,
                "time" => date("Y-m-d H:i:s")
            );

            if (saveMessage($file, $newMessage)) {

                $_SESSION["flash"] = "留言成功！感謝你的分享。";

                header("Location: 1.php");
                exit();

            } else {

                $msg = "留言儲存失敗，請確認資料夾權限。";
                $msgType = "error";
            }
        }
    }
}

if (isset($_SESSION["flash"])) {
    $msg = $_SESSION["flash"];
    $msgType = "success";
    unset($_SESSION["flash"]);
}

$allMessages = getMessages($file);

$total = count($allMessages);
$today = date("Y-m-d");
$todayCount = 0;

foreach ($allMessages as $item) {
    if (isset($item["time"]) &&
        substr($item["time"], 0, 10) === $today) {
        $todayCount++;
    }
}

// 搜尋
$keyword = trim($_GET["q"] ?? "");
$messages = $allMessages;

if ($keyword !== "") {
    $messages = array_filter($messages, function($item) use ($keyword) {
        return stripos($item["name"] ?? "", $keyword) !== false ||
               stripos($item["message"] ?? "", $keyword) !== false;
    });
}

// 排序
$sort = $_GET["sort"] ?? "new";

usort($messages, function($a, $b) use ($sort) {
    $aTime = $a["time"] ?? "";
    $bTime = $b["time"] ?? "";

    return $sort === "old"
        ? strcmp($aTime, $bTime)
        : strcmp($bTime, $aTime);
});

// 分頁
$perPage = 5;
$page = max(1, (int)($_GET["page"] ?? 1));
$filteredTotal = count($messages);
$totalPages = max(1, (int)ceil($filteredTotal / $perPage));

$page = min($page, $totalPages);

$messages = array_slice(
    array_values($messages),
    ($page - 1) * $perPage,
    $perPage
);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>NEON SPACE | 動態留言板</title>

<style>

:root {
    --bg: #090b18;
    --panel: #13172b;
    --panel2: #191f38;
    --text: #f2f3ff;
    --sub: #929bbd;
    --purple: #9b7cff;
    --blue: #43c8ff;
    --pink: #ff65c3;
    --border: #ffffff16;
}

* {
    box-sizing: border-box;
    scroll-behavior: smooth;
}

body {
    margin: 0;
    font-family: "Microsoft JhengHei", Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    overflow-x: hidden;
    transition: .4s;
}

/* 滾動進度 */
#progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 4px;
    background: linear-gradient(90deg, var(--purple), var(--blue), var(--pink));
    z-index: 9999;
    box-shadow: 0 0 15px var(--blue);
}

/* 背景光球 */
.background {
    position: fixed;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: -2;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: .28;
    animation: floatOrb 12s infinite alternate ease-in-out;
}

.orb.one {
    width: 400px;
    height: 400px;
    background: #7545ff;
    top: -120px;
    left: -120px;
}

.orb.two {
    width: 350px;
    height: 350px;
    background: #007dff;
    right: -100px;
    top: 35%;
    animation-delay: 2s;
}

.orb.three {
    width: 300px;
    height: 300px;
    background: #e34cc5;
    bottom: -100px;
    left: 30%;
    animation-delay: 4s;
}

@keyframes floatOrb {
    from { transform: translate(0, 0) scale(1); }
    to { transform: translate(80px, 60px) scale(1.2); }
}

/* 粒子 */
#particles {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    pointer-events: none;
}

/* 滑鼠光暈 */
#cursorGlow {
    position: fixed;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, #8c68ff20, transparent 70%);
    pointer-events: none;
    transform: translate(-50%, -50%);
    z-index: -1;
}

/* 導覽列 */
header {
    position: sticky;
    top: 0;
    z-index: 100;
    padding: 17px 7%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    background: #090b18d9;
    backdrop-filter: blur(18px);
    border-bottom: 1px solid var(--border);
}

.logo {
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 2px;
    background: linear-gradient(90deg, #fff, #a68bff, #50d5ff);
    -webkit-background-clip: text;
    color: transparent;
}

nav {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 24px;
}

nav a {
    color: var(--sub);
    text-decoration: none;
    transition: .3s;
    font-size: 14px;
}

nav a:hover {
    color: var(--blue);
    text-shadow: 0 0 12px var(--blue);
}

.theme-btn {
    border: 1px solid var(--border);
    background: var(--panel);
    color: var(--text);
    border-radius: 30px;
    padding: 9px 15px;
    cursor: pointer;
}

/* 首頁 */
.hero {
    min-height: 490px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 80px 20px;
    position: relative;
}

.hero-content {
    max-width: 850px;
}

.hero-label {
    display: inline-block;
    padding: 9px 20px;
    border: 1px solid #9b7cff66;
    color: #bcaaff;
    background: #9b7cff12;
    border-radius: 30px;
    letter-spacing: 3px;
    font-size: 12px;
    animation: fadeIn 1s;
}

.hero h1 {
    font-size: clamp(36px, 6vw, 66px);
    margin: 25px 0;
    line-height: 1.3;
    background: linear-gradient(90deg, #ffffff, #a88aff, #51d8ff);
    -webkit-background-clip: text;
    color: transparent;
    background-size: 200%;
    animation: gradientText 5s infinite alternate, fadeUp 1s;
}

@keyframes gradientText {
    from { background-position: 0%; }
    to { background-position: 100%; }
}

.hero p {
    color: var(--sub);
    line-height: 2;
    font-size: 16px;
}

.typing {
    color: var(--blue);
    border-right: 2px solid var(--blue);
    padding-right: 5px;
    animation: blink .8s infinite;
}

@keyframes blink {
    50% { border-color: transparent; }
}

.hero-clock {
    margin-top: 30px;
    color: #aeb7d8;
    font-size: 15px;
    letter-spacing: 1px;
}

.hero-button {
    display: inline-block;
    margin-top: 30px;
    padding: 14px 28px;
    border-radius: 30px;
    background: linear-gradient(100deg, #7655e8, #397edc);
    color: white;
    text-decoration: none;
    box-shadow: 0 0 25px #795cff55;
    transition: .3s;
}

.hero-button:hover {
    transform: translateY(-5px) scale(1.04);
    box-shadow: 0 0 35px #795cffaa;
}

/* 主要區域 */
.container {
    width: 90%;
    max-width: 1150px;
    margin: 20px auto 80px;
}

/* 區塊標題 */
.section-heading {
    margin-bottom: 25px;
}

.section-heading small {
    color: var(--blue);
    letter-spacing: 3px;
}

.section-heading h2 {
    font-size: 28px;
    margin: 10px 0;
}

.section-heading p {
    color: var(--sub);
}

/* 統計卡片 */
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 60px;
}

.stat-card {
    padding: 25px;
    background: linear-gradient(145deg, #171d36, #101426);
    border: 1px solid var(--border);
    border-radius: 17px;
    position: relative;
    overflow: hidden;
    transition: .35s;
}

.stat-card::before {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #9575ff22;
    right: -45px;
    bottom: -55px;
    transition: .4s;
}

.stat-card:hover {
    transform: translateY(-8px);
    border-color: #987cff;
    box-shadow: 0 12px 35px #7655e833;
}

.stat-card:hover::before {
    transform: scale(2);
}

.stat-icon {
    font-size: 30px;
}

.stat-card p {
    color: var(--sub);
    margin-bottom: 8px;
}

.stat-card h2 {
    font-size: 36px;
    margin: 5px 0;
    color: #b7a2ff;
}

/* 通用卡片 */
.card {
    background: linear-gradient(145deg, #151a30ee, #101426ee);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 35px;
    box-shadow: 0 12px 35px #0003;
    backdrop-filter: blur(15px);
    transition: .3s;
}

.card:hover {
    border-color: #8d76d744;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 24px;
    margin-top: 0;
}

.section-title::before {
    content: "";
    width: 5px;
    height: 27px;
    background: linear-gradient(var(--purple), var(--blue));
    border-radius: 10px;
    box-shadow: 0 0 12px var(--purple);
}

.subtitle {
    color: var(--sub);
    margin-bottom: 25px;
    line-height: 1.8;
}

/* 表單 */
label {
    display: block;
    margin: 20px 0 9px;
    color: #c8cbe0;
    font-size: 14px;
}

input,
textarea,
select {
    width: 100%;
    padding: 15px;
    background: #0b1022;
    border: 1px solid #ffffff1b;
    border-radius: 11px;
    color: white;
    font-family: inherit;
    font-size: 15px;
    outline: none;
    transition: .3s;
}

input:focus,
textarea:focus,
select:focus {
    border-color: var(--purple);
    box-shadow: 0 0 0 4px #9b7cff20, 0 0 18px #9b7cff22;
}

input::placeholder,
textarea::placeholder {
    color: #69718f;
}

textarea {
    min-height: 140px;
    resize: vertical;
}

.char-info {
    text-align: right;
    color: var(--sub);
    font-size: 13px;
    margin-top: 8px;
}

.char-track {
    height: 5px;
    background: #ffffff15;
    border-radius: 20px;
    margin-top: 10px;
    overflow: hidden;
}

.char-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--purple), var(--blue));
    transition: width .2s;
}

/* 即時預覽 */
.preview {
    margin-top: 25px;
    background: #0b1022;
    border: 1px solid #ffffff15;
    border-radius: 14px;
    padding: 20px;
}

.preview h3 {
    color: #b9a5ff;
    margin-top: 0;
}

#previewText {
    color: var(--sub);
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    line-height: 1.8;
    min-height: 25px;
}

/* 按鈕 */
.button-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 25px;
}

.btn {
    border: none;
    padding: 13px 24px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 15px;
    transition: .3s;
    position: relative;
    overflow: hidden;
}

.btn-primary {
    color: white;
    background: linear-gradient(100deg, #7655e8, #397edc);
    box-shadow: 0 5px 20px #7655e844;
}

.btn-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px #7655e877;
}

.btn-light {
    background: #252b43;
    color: #c5c8df;
    border: 1px solid var(--border);
}

.btn-light:hover {
    background: #343c5c;
}

.btn:disabled {
    opacity: .6;
    cursor: wait;
}

/* 波紋 */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: #ffffff55;
    transform: scale(0);
    animation: ripple .6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* 提示 */
.alert {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    animation: fadeUp .5s;
}

.success {
    background: #153d35;
    color: #7fffd4;
    border: 1px solid #35b99155;
}

.error {
    background: #431e32;
    color: #ff9cba;
    border: 1px solid #ff65c355;
}

/* 搜尋 */
.search-row {
    display: grid;
    grid-template-columns: 1fr 160px auto;
    gap: 12px;
    margin-bottom: 20px;
}

/* 留言卡片 */
.message-item {
    position: relative;
    padding: 23px;
    margin-bottom: 17px;
    border: 1px solid #ffffff16;
    border-radius: 14px;
    background: #10162b;
    transition: .35s;
    animation: fadeUp .6s both;
}

.message-item:hover {
    transform: translateX(7px);
    border-color: #8d76d7;
    box-shadow: 0 0 25px #7655e822;
}

.message-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.message-name {
    font-size: 18px;
    font-weight: bold;
    color: #bba8ff;
}

.message-time {
    color: #7883a7;
    font-size: 13px;
}

.message-email {
    display: inline-block;
    color: var(--blue);
    font-size: 13px;
    margin-top: 10px;
    text-decoration: none;
}

.message-content {
    margin-top: 15px;
    color: #c6cbe1;
    line-height: 1.9;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

/* 分頁 */
.pagination {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 30px;
}

.pagination a {
    color: #c7c8e0;
    text-decoration: none;
    padding: 10px 16px;
    background: #222943;
    border-radius: 8px;
    transition: .25s;
}

.pagination a:hover,
.pagination a.active {
    background: linear-gradient(100deg, #7655e8, #397edc);
    color: white;
    box-shadow: 0 0 15px #7655e855;
}

/* 功能卡片 */
.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 18px;
}

.quick-card {
    padding: 25px;
    background: linear-gradient(145deg, #171d36, #101426);
    border: 1px solid var(--border);
    border-radius: 15px;
    color: white;
    text-align: center;
    text-decoration: none;
    transition: .35s;
}

.quick-card:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: var(--blue);
    box-shadow: 0 0 25px #43c8ff25;
}

.quick-card .icon {
    font-size: 35px;
}

.quick-card p {
    color: var(--sub);
    font-size: 13px;
}

/* 頁尾 */
footer {
    padding: 40px 20px;
    text-align: center;
    background: #070914;
    border-top: 1px solid var(--border);
    color: var(--sub);
}

footer h3 {
    color: #b7a2ff;
    letter-spacing: 4px;
}

/* 回頂部 */
#topButton {
    position: fixed;
    right: 25px;
    bottom: 25px;
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 50%;
    color: white;
    background: linear-gradient(130deg, #7655e8, #397edc);
    box-shadow: 0 0 20px #7655e855;
    cursor: pointer;
    opacity: 0;
    transform: translateY(20px);
    pointer-events: none;
    transition: .3s;
    z-index: 200;
}

#topButton.show {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

/* 區塊進場 */
.reveal {
    opacity: 0;
    transform: translateY(35px);
    transition: .8s ease;
}

.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}

/* 淺色模式 */
body.light {
    --bg: #eef1ff;
    --panel: #ffffff;
    --panel2: #ffffff;
    --text: #252b45;
    --sub: #68708e;
    --border: #252b4520;
}

body.light header {
    background: #ffffffdd;
}

body.light .logo {
    background: linear-gradient(90deg, #6545c7, #168bc5);
    -webkit-background-clip: text;
    color: transparent;
}

body.light .card,
body.light .stat-card,
body.light .quick-card {
    background: #ffffffee;
    color: #252b45;
    border-color: #252b451a;
}

body.light .message-item,
body.light .preview {
    background: #f5f6ff;
}

body.light .message-content,
body.light .message-name,
body.light label {
    color: #303957;
}

body.light input,
body.light textarea,
body.light select {
    background: white;
    color: #252b45;
    border-color: #d9ddef;
}

body.light footer {
    background: #dfe5fa;
}

body.light .btn-light {
    background: #e7eaff;
    color: #343d62;
}

@media(max-width:700px) {

    header {
        padding: 15px 5%;
    }

    nav {
        gap: 13px;
    }

    .hero {
        min-height: 400px;
    }

    .hero h1 {
        font-size: 38px;
    }

    .card {
        padding: 20px;
    }

    .search-row {
        grid-template-columns: 1fr;
    }

    .message-top {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media(prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        scroll-behavior: auto !important;
        transition-duration: .01ms !important;
    }
}

</style>
</head>

<body>

<div id="progress"></div>

<div class="background">
    <div class="orb one"></div>
    <div class="orb two"></div>
    <div class="orb three"></div>
</div>

<canvas id="particles"></canvas>
<div id="cursorGlow"></div>

<header>

    <div class="logo">✦ NEON SPACE</div>

    <nav>
        <a href="1.php">首頁</a>
        <a href="#write">我要留言</a>
        <a href="#messages">留言列表</a>
        <a href="#features">功能中心</a>

        <button class="theme-btn" id="themeButton" onclick="toggleTheme()">
            ☀ 淺色模式
        </button>
    </nav>

</header>

<section class="hero">

    <div class="hero-content">

        <div class="hero-label">WELCOME TO MY DIGITAL WORLD</div>

        <h1>WELCOME TO<br>NEON SPACE</h1>

        <p>
            一個專屬於你的互動留言空間<br>
            <span class="typing" id="typing"></span>
        </p>

        <div class="hero-clock">
            📅 <span id="clock"></span>
        </div>

        <a href="#write" class="hero-button">
            開始探索 ↓
        </a>

    </div>

</section>

<div class="container">

    <!-- 統計區 -->
    <div class="stats reveal">

        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <p>全部留言</p>
            <h2 class="counter" data-target="<?php echo $total; ?>">0</h2>
            <p>ALL MESSAGES</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <p>今日留言</p>
            <h2 class="counter" data-target="<?php echo $todayCount; ?>">0</h2>
            <p>TODAY MESSAGES</p>
        </div>

        <div class="stat-card">
            <div class="stat-icon">✍️</div>
            <p>留言字數上限</p>
            <h2>300</h2>
            <p>MAX CHARACTERS</p>
        </div>

    </div>

    <!-- 留言表單 -->
    <div class="card reveal" id="write">

        <div class="section-heading">
            <small>CREATE SOMETHING</small>
            <h2 class="section-title">✍️ 發表你的留言</h2>
            <p>留下你的想法，讓每一段文字都有自己的故事。</p>
        </div>

        <?php if ($msg !== "") { ?>
            <div class="alert <?php echo clean($msgType); ?>">
                <?php echo clean($msg); ?>
            </div>
        <?php } ?>

        <form method="POST" action="1.php" id="guestForm">

            <input type="hidden" name="token"
                   value="<?php echo clean($_SESSION["token"]); ?>">

            <label>👤 姓名 Name</label>
            <input type="text"
                   name="Name"
                   maxlength="30"
                   placeholder="請輸入你的姓名..."
                   required>

            <label>📧 電子郵件 Email</label>
            <input type="email"
                   name="Email"
                   placeholder="example@gmail.com"
                   required>

            <label>💭 留言內容 Message</label>
            <textarea name="Message"
                      id="messageInput"
                      maxlength="300"
                      placeholder="在這裡寫下你的想法..."
                      required></textarea>

            <div class="char-info">
                <span id="charCount">0</span> / 300 字
            </div>

            <div class="char-track">
                <div class="char-fill" id="charFill"></div>
            </div>

            <div class="preview">
                <h3>◈ 即時預覽 LIVE PREVIEW</h3>
                <div id="previewText">你的留言會即時顯示在這裡...</div>
            </div>

            <div class="button-row">

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    ✦ 送出留言
                </button>

                <button type="reset" class="btn btn-light">
                    ↻ 清除內容
                </button>

            </div>

        </form>

    </div>

    <!-- 留言列表 -->
    <div class="card reveal" id="messages">

        <div class="section-heading">
            <small>COMMUNITY</small>
            <h2 class="section-title">📚 最新留言</h2>
            <p>看看大家留下了什麼內容。</p>
        </div>

        <form method="GET" action="1.php">

            <div class="search-row">

                <input type="text"
                       name="q"
                       value="<?php echo clean($keyword); ?>"
                       placeholder="🔍 輸入姓名或留言內容...">

                <select name="sort">

                    <option value="new"
                        <?php echo $sort === "new" ? "selected" : ""; ?>>
                        最新留言
                    </option>

                    <option value="old"
                        <?php echo $sort === "old" ? "selected" : ""; ?>>
                        最舊留言
                    </option>

                </select>

                <button type="submit" class="btn btn-primary">
                    搜尋留言
                </button>

            </div>

        </form>

        <p class="subtitle">
            SEARCH RESULT：<?php echo $filteredTotal; ?> 則留言
        </p>

        <?php if (count($messages) > 0) { ?>

            <?php foreach ($messages as $index => $item) { ?>

                <div class="message-item"
                     style="animation-delay: <?php echo $index * 0.1; ?>s;">

                    <div class="message-top">

                        <div class="message-name">
                            ◈ <?php echo clean($item["name"] ?? "訪客"); ?>
                        </div>

                        <div class="message-time">
                            🕒 <?php echo clean($item["time"] ?? ""); ?>
                        </div>

                    </div>

                    <a class="message-email"
                       href="mailto:<?php echo clean($item["email"] ?? ""); ?>">
                        📧 <?php echo clean($item["email"] ?? ""); ?>
                    </a>

                    <div class="message-content"><?php
                        echo clean($item["message"] ?? "");
                    ?></div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="preview" style="text-align:center;">
                <h3>✦ 暫時沒有留言</h3>
                <p>成為第一個留下紀錄的人吧！</p>
            </div>

        <?php } ?>

        <?php if ($totalPages > 1) { ?>

            <div class="pagination">

                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>

                    <a class="<?php echo $i === $page ? "active" : ""; ?>"
                       href="?q=<?php echo urlencode($keyword); ?>&sort=<?php echo clean($sort); ?>&page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>

                <?php } ?>

            </div>

        <?php } ?>

    </div>

    <!-- 功能中心 -->
    <div class="reveal" id="features">

        <div class="section-heading">
            <small>EXPLORE MORE</small>
            <h2>✦ 功能探索中心</h2>
            <p>使用不同功能，體驗完整的互動網站。</p>
        </div>

        <div class="quick-links">

            <a href="#write" class="quick-card">
                <div class="icon">✍️</div>
                <h3>新增留言</h3>
                <p>留下你的心情與想法</p>
            </a>

            <a href="#messages" class="quick-card">
                <div class="icon">📚</div>
                <h3>留言瀏覽</h3>
                <p>查看所有留言紀錄</p>
            </a>

            <a href="#messages" class="quick-card">
                <div class="icon">🔍</div>
                <h3>留言搜尋</h3>
                <p>快速找到指定內容</p>
            </a>

            <a href="#" class="quick-card"
               onclick="toggleTheme(); return false;">
                <div class="icon">🌗</div>
                <h3>主題切換</h3>
                <p>切換不同網頁風格</p>
            </a>

            <a href="#" class="quick-card"
               onclick="window.print(); return false;">
                <div class="icon">🖨️</div>
                <h3>列印網頁</h3>
                <p>列印目前頁面</p>
            </a>

            <a href="#" class="quick-card"
               onclick="window.scrollTo({top:0,behavior:'smooth'}); return false;">
                <div class="icon">🚀</div>
                <h3>返回頂端</h3>
                <p>快速回到首頁</p>
            </a>

        </div>

    </div>

</div>

<footer>

    <h3>✦ NEON SPACE ✦</h3>

    <p>每一份留言，都是值得保存的紀錄。</p>

    <p>© 2026 NEON SPACE. ALL RIGHTS RESERVED.</p>

    <p id="footerTime"></p>

</footer>

<button id="topButton" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    ↑
</button>

<script>

// 即時時鐘
function updateClock() {

    const now = new Date();

    const text = now.toLocaleString("zh-TW", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        weekday: "long",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false
    });

    document.getElementById("clock").textContent = text;
    document.getElementById("footerTime").textContent =
        "SYSTEM TIME：" + text;
}

updateClock();
setInterval(updateClock, 1000);


// 打字機動畫
const typingWords = [
    "分享生活中的每個瞬間。",
    "記錄每一份值得珍藏的回憶。",
    "讓你的想法被更多人看見。",
    "歡迎來到專屬你的留言空間。"
];

let wordIndex = 0;
let charIndex = 0;
let deleting = false;

function typeEffect() {

    const element = document.getElementById("typing");
    const word = typingWords[wordIndex];

    if (!deleting) {

        element.textContent = word.substring(0, charIndex + 1);
        charIndex++;

        if (charIndex === word.length) {
            deleting = true;
            setTimeout(typeEffect, 1800);
            return;
        }

    } else {

        element.textContent = word.substring(0, charIndex - 1);
        charIndex--;

        if (charIndex === 0) {
            deleting = false;
            wordIndex = (wordIndex + 1) % typingWords.length;
        }
    }

    setTimeout(typeEffect, deleting ? 45 : 85);
}

typeEffect();


// 留言即時預覽
const messageInput = document.getElementById("messageInput");
const charCount = document.getElementById("charCount");
const charFill = document.getElementById("charFill");
const previewText = document.getElementById("previewText");

messageInput.addEventListener("input", function() {

    const length = Array.from(this.value).length;

    charCount.textContent = length;
    charFill.style.width = (length / 300 * 100) + "%";

    previewText.textContent =
        this.value || "你的留言會即時顯示在這裡...";

});


// 清除表單
document.getElementById("guestForm").addEventListener("reset", function() {

    setTimeout(function() {
        charCount.textContent = "0";
        charFill.style.width = "0%";
        previewText.textContent = "你的留言會即時顯示在這裡...";
    }, 0);

});


// 送出留言
document.getElementById("guestForm").addEventListener("submit", function(e) {

    if (!this.checkValidity()) {
        return;
    }

    if (!confirm("確定要送出留言嗎？")) {
        e.preventDefault();
        return;
    }

    const button = document.getElementById("submitBtn");

    button.disabled = true;
    button.textContent = "⏳ 正在傳送...";
});


// 主題切換
function toggleTheme() {

    document.body.classList.toggle("light");

    const isLight = document.body.classList.contains("light");

    localStorage.setItem("neonTheme", isLight ? "light" : "dark");

    document.getElementById("themeButton").textContent =
        isLight ? "🌙 深色模式" : "☀ 淺色模式";
}

if (localStorage.getItem("neonTheme") === "light") {
    document.body.classList.add("light");
    document.getElementById("themeButton").textContent = "🌙 深色模式";
}


// 滾動進度與回頂部
window.addEventListener("scroll", function() {

    const scrollTop = window.scrollY;

    const height =
        document.documentElement.scrollHeight - window.innerHeight;

    const percentage = height > 0 ? scrollTop / height * 100 : 0;

    document.getElementById("progress").style.width =
        percentage + "%";

    document.getElementById("topButton").classList.toggle(
        "show",
        scrollTop > 300
    );

});


// 區塊進場動畫
const observer = new IntersectionObserver(function(entries) {

    entries.forEach(function(entry) {

        if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            observer.unobserve(entry.target);
        }

    });

}, { threshold: 0.12 });

document.querySelectorAll(".reveal").forEach(function(element) {
    observer.observe(element);
});


// 數字動畫
document.querySelectorAll(".counter").forEach(function(counter) {

    const target = Number(counter.dataset.target);
    let current = 0;

    const step = Math.max(1, Math.ceil(target / 40));

    function animate() {

        current += step;

        if (current >= target) {
            current = target;
            counter.textContent = current;
            return;
        }

        counter.textContent = current;

        requestAnimationFrame(animate);
    }

    animate();

});


// 按鈕波紋效果
document.querySelectorAll(".btn, .hero-button").forEach(function(button) {

    button.addEventListener("click", function(e) {

        const rect = this.getBoundingClientRect();

        const ripple = document.createElement("span");

        ripple.className = "ripple";

        const size = Math.max(rect.width, rect.height);

        ripple.style.width = size + "px";
        ripple.style.height = size + "px";

        ripple.style.left = (e.clientX - rect.left - size / 2) + "px";
        ripple.style.top = (e.clientY - rect.top - size / 2) + "px";

        this.appendChild(ripple);

        setTimeout(function() {
            ripple.remove();
        }, 600);

    });

});


// 滑鼠光暈
const cursorGlow = document.getElementById("cursorGlow");

window.addEventListener("mousemove", function(e) {

    cursorGlow.style.left = e.clientX + "px";
    cursorGlow.style.top = e.clientY + "px";

});


// 背景粒子動畫
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");

let particles = [];

function resizeCanvas() {

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    particles = [];

    const amount = Math.min(
        90,
        Math.floor(window.innerWidth / 15)
    );

    for (let i = 0; i < amount; i++) {

        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            size: Math.random() * 2 + 1,
            speedX: (Math.random() - .5) * .5,
            speedY: (Math.random() - .5) * .5
        });

    }
}

function animateParticles() {

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    particles.forEach(function(p) {

        p.x += p.speedX;
        p.y += p.speedY;

        if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
        if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;

        ctx.beginPath();

        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);

        ctx.fillStyle = "rgba(160,145,255,0.7)";
        ctx.fill();

    });

    requestAnimationFrame(animateParticles);
}

resizeCanvas();
animateParticles();

window.addEventListener("resize", resizeCanvas);

</script>

</body>
</html>
