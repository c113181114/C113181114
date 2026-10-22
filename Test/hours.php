```php
<?php
$result = "";
$detail = "";
$type = "";
$hours = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hours = trim($_POST["hours"] ?? "");

    if ($hours === "") {
        $result = "請輸入靠泊時間";
        $detail = "欄位不能留白，請輸入小時數。";
        $type = "error";
    } elseif (
        !preg_match('/^(?:\d+(?:\.\d+)?|\.\d+)$/', $hours) ||
        !is_numeric($hours)
    ) {
        $result = "請輸入有效的數字";
        $detail = "請輸入數字或小數，例如 20 或 19.5。";
        $type = "error";
    } elseif ((float)$hours < 0) {
        $result = "靠泊時間不能是負數";
        $detail = "請輸入 0 或大於 0 的靠泊時數。";
        $type = "error";
    } elseif ((float)$hours >= 20) {
        $result = "長時間靠泊";
        $detail = "輸入時間達 20 小時或以上。";
        $type = "long";
    } else {
        $result = "一般靠泊";
        $detail = "輸入時間未滿 20 小時。";
        $type = "normal";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>船舶靠泊時間判斷</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Microsoft JhengHei", sans-serif;
    color: #18343b;
    background: #f2f7f6;
    overflow-x: hidden;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(18px); }
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes popIn {
    0% {
        opacity: 0;
        transform: translateY(12px) scale(.95);
    }
    70% {
        transform: translateY(-3px) scale(1.01);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes shine {
    from { background-position: 0% 50%; }
    to { background-position: 200% 50%; }
}

.hero {
    position: relative;
    overflow: hidden;
    padding: 48px 24px 90px;
    color: white;
    background: linear-gradient(
        120deg,
        #102f42,
        #17645f,
        #123b4a,
        #20877c
    );
    background-size: 250% 250%;
    animation: gradientMove 10s ease-in-out infinite alternate;
}

.hero::after {
    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    top: -100px;
    right: -60px;
    border: 2px solid rgba(255,255,255,.2);
    border-radius: 50%;
    box-shadow:
        0 0 0 25px rgba(255,255,255,.05),
        0 0 0 55px rgba(255,255,255,.04);
    animation: float 6s ease-in-out infinite;
    pointer-events: none;
}

.hero-inner {
    max-width: 960px;
    margin: auto;
    position: relative;
    z-index: 1;
}

.eyebrow {
    font-size: 12px;
    letter-spacing: 3px;
    opacity: .8;
    font-weight: bold;
}

h1 {
    font-size: 36px;
    margin: 16px 0;
    line-height: 1.3;
}

.hero p {
    max-width: 600px;
    line-height: 1.9;
    color: #d8e9eb;
}

.pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 24px;
}

.pill {
    padding: 8px 14px;
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 30px;
    background: rgba(255,255,255,.1);
    font-size: 13px;
    backdrop-filter: blur(5px);
}

main {
    max-width: 1000px;
    margin: -45px auto 0;
    padding: 0 20px 45px;
    position: relative;
    z-index: 2;
}

.layout {
    display: grid;
    grid-template-columns: 1.15fr .85fr;
    gap: 22px;
    align-items: start;
}

.card {
    background: white;
    border: 1px solid #e0eae8;
    border-radius: 22px;
    padding: 28px;
    box-shadow: 0 12px 35px rgba(18,59,74,.08);
    animation: fadeUp .7s both;
    transition: transform .3s, box-shadow .3s;
}

.card:nth-child(2) {
    animation-delay: .15s;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 22px 45px rgba(18,59,74,.14);
}

h2 {
    font-size: 20px;
    margin: 0 0 10px;
}

.muted {
    color: #71868a;
    font-size: 13px;
    line-height: 1.8;
}

label {
    display: block;
    font-weight: bold;
    margin: 25px 0 10px;
    font-size: 14px;
}

.input-wrap {
    display: flex;
    gap: 10px;
}

input {
    width: 100%;
    min-width: 0;
    flex: 1;
    padding: 15px;
    border: 1px solid #c9dad8;
    border-radius: 12px;
    background: #fbfdfc;
    font-size: 16px;
    outline: none;
    transition: .25s;
}

input:focus {
    border-color: #23877f;
    box-shadow: 0 0 0 4px rgba(35,135,127,.13);
    transform: translateY(-2px);
}

button {
    font-family: inherit;
    cursor: pointer;
    border: none;
}

.submit {
    padding: 0 20px;
    border-radius: 12px;
    color: white;
    font-weight: bold;
    white-space: nowrap;
    background: linear-gradient(110deg, #123b4a, #20877c, #123b4a);
    background-size: 200% 100%;
    transition: .3s;
}

.submit:hover {
    background-position: 100% 0;
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(32,135,124,.3);
}

.submit:active {
    transform: scale(.96);
}

.hint {
    color: #71868a;
    font-size: 12px;
    margin-top: 10px;
}

.result {
    display: none;
    margin-top: 22px;
    padding: 18px;
    border-radius: 15px;
    border: 1px solid transparent;
}

.result.show {
    display: block;
    animation: popIn .4s ease both;
}

.result.long {
    background: #effaf5;
    border-color: #c8e8dc;
}

.result.normal {
    background: #fff8e9;
    border-color: #f0dfb8;
}

.result.error {
    background: #fff4f1;
    border-color: #f0d0c9;
}

.result-title {
    font-size: 18px;
    font-weight: bold;
}

.result-desc {
    margin-top: 7px;
    font-size: 13px;
    line-height: 1.7;
}

.result.long .result-title { color: #17664d; }
.result.normal .result-title { color: #825b12; }
.result.error .result-title { color: #963d32; }

.rule {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 25px 0 18px;
    color: #748a8d;
    font-size: 12px;
}

.rule::before,
.rule::after {
    content: "";
    height: 1px;
    background: #e5eceb;
    flex: 1;
}

.quick-tests {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
}

.quick-tests button {
    padding: 9px 12px;
    border-radius: 9px;
    background: #dcefea;
    color: #183e43;
    font-size: 12px;
    font-weight: bold;
    transition: .2s;
}

.quick-tests button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(18,59,74,.15);
    filter: brightness(.97);
}

.quick-tests button:active {
    transform: scale(.96);
}

.status-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    background: #dcefea;
    font-size: 24px;
    margin-bottom: 18px;
}

.threshold {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 25px;
}

.threshold-number {
    font-size: 42px;
    font-weight: 800;
    color: #123b4a;
}

.threshold-label {
    font-size: 12px;
    line-height: 1.7;
    color: #71868a;
}

.mini-rule {
    height: 8px;
    border-radius: 20px;
    overflow: hidden;
    margin: 18px 0;
    background: #e6efed;
}

.mini-rule span {
    display: block;
    width: 66%;
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #399b8c, #76d8c3, #399b8c);
    background-size: 200% 100%;
    animation: shine 3s linear infinite;
}

.legend {
    display: grid;
    gap: 14px;
    margin-top: 20px;
    font-size: 13px;
}

.legend-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.dot.long { background: #399b8c; }
.dot.normal { background: #d3a24e; }
.dot.error { background: #c66d65; }

footer {
    max-width: 1000px;
    margin: auto;
    padding: 0 20px 30px;
    color: #71868a;
    font-size: 12px;
    display: flex;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

@media (max-width: 700px) {
    .hero {
        padding: 35px 20px 75px;
    }

    h1 {
        font-size: 28px;
    }

    main {
        padding: 0 14px 35px;
    }

    .layout {
        grid-template-columns: 1fr;
    }

    .card {
        padding: 22px;
    }

    .input-wrap {
        flex-direction: column;
    }

    .submit {
        min-height: 48px;
    }

    footer {
        padding: 0 16px 25px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
</style>
</head>

<body>

<header class="hero">
    <div class="hero-inner">
        <div class="eyebrow">MARITIME · SMART PORT</div>
        <h1>船舶靠泊時間判斷</h1>
        <p>
            輸入船舶靠泊時間，快速確認屬於長時間靠泊還是一般靠泊。
            輸入滿 20 小時，即判定為長時間靠泊。
        </p>

        <div class="pills">
            <span class="pill">簡單輸入</span>
            <span class="pill">即時判斷</span>
            <span class="pill">錯誤提示</span>
        </div>
    </div>
</header>

<main>
    <div class="layout">

        <section class="card">
            <h2>試算靠泊時間</h2>
            <div class="muted">請輸入靠泊時數，可輸入整數或小數。</div>

            <form method="post" action="" id="hoursForm">
                <label for="hours">
                    靠泊時間 <span class="muted">（小時）</span>
                </label>

                <div class="input-wrap">
                    <input
                        id="hours"
                        name="hours"
                        type="text"
                        placeholder="例如：20 或 19.5"
                        autocomplete="off"
                        value="<?php echo htmlspecialchars($hours, ENT_QUOTES, 'UTF-8'); ?>"
                    >
                    <button class="submit" type="submit">
                        開始判斷 →
                    </button>
                </div>

                <div class="hint">
                    判斷標準：20 小時為分界，包含 20 小時本身。
                </div>
            </form>

            <?php if ($result !== ""): ?>
            <div class="result show <?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>" role="status">
                <div class="result-title">
                    <?php
                    if ($type === "long") {
                        echo "✓ ";
                    } elseif ($type === "normal") {
                        echo "↘ ";
                    } else {
                        echo "! ";
                    }
                    echo htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
                    ?>
                </div>
                <div class="result-desc">
                    <?php echo htmlspecialchars($detail, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="rule">快速測試</div>

            <div class="quick-tests">
                <button type="button" data-value="20">20 小時</button>
                <button type="button" data-value="19.9">19.9 小時</button>
                <button type="button" data-value="">空白</button>
                <button type="button" data-value="-5">-5 負數</button>
                <button type="button" data-value="20小時">20小時</button>
                <button type="button" data-value="二十">中文數字</button>
            </div>
        </section>

        <aside class="card">
            <div class="status-icon">⚓</div>
            <h2>判斷標準</h2>
            <p class="muted">
                依照輸入的時數，系統會將靠泊時間分成兩種狀態。
            </p>

            <div class="threshold">
                <div class="threshold-number">
                    20<span style="font-size:16px"> hr</span>
                </div>
                <div class="threshold-label">
                    關鍵分界<br>20 小時（含）以上
                </div>
            </div>

            <div class="mini-rule"><span></span></div>

            <div class="legend">
                <div class="legend-row">
                    <span class="dot long"></span>
                    <span><strong>長時間靠泊</strong>　20 小時以上</span>
                </div>

                <div class="legend-row">
                    <span class="dot normal"></span>
                    <span><strong>一般靠泊</strong>　未滿 20 小時</span>
                </div>

                <div class="legend-row">
                    <span class="dot error"></span>
                    <span><strong>輸入錯誤</strong>　空白或無效格式</span>
                </div>
            </div>
        </aside>

    </div>
</main>

<footer>
    <span>船舶靠泊時間判斷工具</span>
    <span>課堂練習 · PHP 邏輯示範</span>
</footer>

<script>
const input = document.getElementById("hours");
const form = document.getElementById("hoursForm");

document.querySelectorAll("[data-value]").forEach(button => {
    button.addEventListener("click", () => {
        input.value = button.dataset.value;
        form.requestSubmit();
    });
});
</script>

</body>
</html>
```