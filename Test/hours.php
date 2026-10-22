<?php
$message = "";
$detail = "";
$kind = "";
$hours = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hours = trim($_POST["hours"] ?? "");
    if ($hours === "") {
        $message = "請輸入靠泊時間";
        $detail = "欄位不能留白，請輸入小時數。";
        $kind = "error";
    } elseif (!preg_match('/^(?:\d+(?:\.\d+)?|\.\d+)$/', $hours) || !is_numeric($hours)) {
        $message = "請輸入有效的數字";
        $detail = "請輸入數字或小數，例如 20 或 19.5。";
        $kind = "error";
    } elseif ((float)$hours < 0) {
        $message = "靠泊時間不能是負數";
        $detail = "請輸入 0 或大於 0 的靠泊時數。";
        $kind = "error";
    } elseif ((float)$hours >= 20) {
        $message = "長時間靠泊";
        $detail = "輸入時間達 20 小時或以上。";
        $kind = "long";
    } else {
        $message = "一般靠泊";
        $detail = "輸入時間未滿 20 小時。";
        $kind = "normal";
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
:root {
  --site-primary: #123B4A;
  --site-on-primary: #FFFFFF;
  --site-secondary: #DCEFEA;
  --site-on-secondary: #183E43;
  --site-background: #F4F8F7;
  --site-on-background: #18343B;
  --site-font-body: "Noto Sans TC", "Microsoft JhengHei", system-ui, sans-serif;
  --site-font-heading: "Noto Sans TC", "Microsoft JhengHei", system-ui, sans-serif;
  --site-heading-scale: 1;
  --site-spacing-scale: 1;
  --site-radius-scale: 1;
  --site-decoration-opacity: 1;
  color-scheme: light dark;
}
* { box-sizing: border-box; }
body {
  margin: 0;
  font-family: var(--site-font-body);
  background: light-dark(#F4F8F7, #101D22);
  color: light-dark(#18343B, #E5F0F2);
}
.app-root {
  min-height: 100vh;
  width: 100%;
  border-radius: 0;
  background-color: light-dark(var(--site-background), #101D22);
  color: light-dark(var(--site-on-background), #E5F0F2);
  overflow: hidden;
}
.hero {
  background: var(--site-primary);
  color: var(--site-on-primary);
  padding: calc(38px * var(--site-spacing-scale)) 22px calc(76px * var(--site-spacing-scale));
  position: relative;
}
.hero:after {
  content: "";
  position: absolute;
  width: 280px; height: 280px;
  border: 1px solid rgba(255,255,255,.13);
  border-radius: 50%;
  right: -70px; top: -135px;
  box-shadow: 0 0 0 28px rgba(255,255,255,.035), 0 0 0 58px rgba(255,255,255,.025);
  pointer-events: none;
  opacity: var(--site-decoration-opacity);
}
.hero-inner, main { max-width: 960px; margin: 0 auto; }
.eyebrow { text-transform: uppercase; letter-spacing: .15em; font-size: 12px; opacity: .75; font-weight: 700; }
h1 {
  font-family: var(--site-font-heading);
  font-size: calc(34px * var(--site-heading-scale));
  line-height: 1.2; margin: 12px 0 12px; letter-spacing: -.03em;
}
.hero p { max-width: 580px; line-height: 1.8; margin: 0; color: #D8E9EB; font-size: 15px; }
.hero-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 22px; }
.pill { border: 1px solid rgba(255,255,255,.25); background: rgba(255,255,255,.09); border-radius: 999px; padding: 7px 12px; font-size: 12px; }
main { padding: 0 20px 50px; margin-top: -44px; position: relative; }
.layout { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr); gap: 18px; align-items: start; }
.card {
  background: light-dark(#FFFFFF, #18282E);
  border: 1px solid light-dark(#E0EAE8, #2B4148);
  border-radius: calc(20px * var(--site-radius-scale));
  box-shadow: 0 12px 34px rgba(18,59,74,.08);
  padding: calc(24px * var(--site-spacing-scale));
}
.card h2 { font-size: calc(18px * var(--site-heading-scale)); margin: 0 0 7px; color: light-dark(#18343B, #F0F7F8); }
.muted { color: light-dark(#637C81, #A3B8BD); font-size: 13px; line-height: 1.7; }
.field-label { display: block; font-weight: 700; font-size: 14px; margin: 24px 0 9px; }
.input-wrap { display: flex; gap: 10px; align-items: stretch; }
input[type="text"] {
  min-width: 0; flex: 1; width: 100%;
  border: 1px solid light-dark(#C9DAD8, #3A555C);
  background: light-dark(#FBFDFC, #101D22);
  color: light-dark(#18343B, #F0F7F8);
  border-radius: 12px; padding: 14px 15px; font: inherit; font-size: 16px;
  outline: none; transition: border-color .18s, box-shadow .18s;
}
input[type="text"]:focus { border-color: #23877F; box-shadow: 0 0 0 3px rgba(35,135,127,.15); }
button { font: inherit; cursor: pointer; border: 0; }
.submit {
  background: var(--site-primary); color: var(--site-on-primary); border-radius: 12px;
  padding: 0 19px; font-weight: 700; transition: transform .15s, opacity .15s;
}
.submit:hover { opacity: .9; transform: translateY(-1px); }
.hint { margin-top: 9px; font-size: 12px; color: light-dark(#71868A, #9CB0B5); }
.result {
  display: none; margin-top: 20px; padding: 16px; border-radius: 14px;
  border: 1px solid light-dark(#C8E8DC, #31594E);
  background: light-dark(#EFFAF5, #1A332D);
}
.result.show { display: block; }
.result-top { display: flex; gap: 10px; align-items: center; }
.result-icon { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 12px; background: #D5F0E4; color: #17664D; font-size: 19px; }
.result strong { display: block; font-size: 17px; }
.result small { display: block; margin-top: 3px; color: light-dark(#607B70, #A6C4B7); }
.rule { display: flex; align-items: center; gap: 12px; margin: 18px 0; }
.rule-line { height: 1px; background: light-dark(#E5ECEB, #2B4148); flex: 1; }
.rule span { color: light-dark(#748A8D, #A3B8BD); font-size: 11px; }
.quick-tests { display: flex; flex-wrap: wrap; gap: 8px; }
.quick-tests button { padding: 8px 11px; background: var(--site-secondary); color: var(--site-on-secondary); border-radius: 9px; font-size: 12px; font-weight: 700; }
.quick-tests button:hover { filter: brightness(.97); }
.side-card { padding: 22px; }
.status-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 13px; background: var(--site-secondary); color: var(--site-on-secondary); margin-bottom: 15px; }
.threshold { display: flex; align-items: center; gap: 12px; margin-top: 22px; }
.threshold-number { font-size: 34px; font-weight: 800; letter-spacing: -.04em; color: light-dark(#123B4A, #9EDDD4); }
.threshold-label { font-size: 12px; color: light-dark(#637C81, #A3B8BD); line-height: 1.5; }
.mini-rule { height: 8px; background: light-dark(#E6EFED, #2A4046); border-radius: 99px; overflow: hidden; margin: 16px 0 10px; }
.mini-rule span { display: block; height: 100%; width: 66%; background: #399B8C; border-radius: inherit; }
.legend { display: grid; gap: 10px; margin-top: 19px; }
.legend-row { display: flex; align-items: center; gap: 9px; font-size: 13px; }
.dot { width: 9px; height: 9px; border-radius: 50%; flex: 0 0 auto; }
.dot.long { background: #399B8C; } .dot.normal { background: #D3A24E; } .dot.error { background: #C66D65; }
.footer { max-width: 960px; margin: 0 auto; padding: 0 20px 28px; color: light-dark(#71868A, #82999F); font-size: 12px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
@media (max-width: 700px) {
  .hero { padding-top: 30px; }
  h1 { font-size: calc(28px * var(--site-heading-scale)); }
  .layout { grid-template-columns: 1fr; }
  main { margin-top: -38px; padding-left: 14px; padding-right: 14px; }
  .card { padding: 20px; }
  .input-wrap { flex-direction: column; }
  .submit { min-height: 48px; }
  .footer { padding-left: 16px; padding-right: 16px; }
}
</style>
</head>
<body>
<div class="app-root" data-sites-preview-theme>
  <header class="hero">
    <div class="hero-inner">
      <div class="eyebrow">MARITIME · SMART PORT</div>
      <h1>船舶靠泊時間判斷</h1>
      <p>輸入船舶靠泊時間，快速確認屬於長時間靠泊還是一般靠泊。輸入滿 20 小時，即判定為長時間靠泊。</p>
      <div class="hero-pills">
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
        <form id="hoursForm" method="post" novalidate>
          <label class="field-label" for="hours">靠泊時間 <span class="muted">（小時）</span></label>
          <div class="input-wrap">
            <input id="hours" name="hours" type="text" placeholder="例如：20 或 19.5" autocomplete="off" value="<?php echo htmlspecialchars($hours, ENT_QUOTES, 'UTF-8'); ?>">
            <button class="submit" type="submit">開始判斷 →</button>
          </div>
          <div class="hint">判斷標準：20 小時為分界，包含 20 小時本身。</div>
        </form>
        <?php if ($message !== ""): ?>
        <div id="result" class="result show" role="status" aria-live="polite">
          <div class="result-top">
            <div class="result-icon" id="resultIcon"><?php echo $kind === "error" ? "!" : ($kind === "normal" ? "↘" : "✓"); ?></div>
            <div>
              <strong><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></strong>
              <small><?php echo htmlspecialchars($detail, ENT_QUOTES, "UTF-8"); ?></small>
            </div>
          </div>
        </div>
        <style>
        .result.show { <?php echo $kind === "error" ? "background: light-dark(#FFF4F1, #3B2523); border-color: light-dark(#F0D0C9, #6A3F39);" : ($kind === "normal" ? "background: light-dark(#FFF8E9, #3A3020); border-color: light-dark(#F0DFB8, #685331);" : "background: light-dark(#EFFAF5, #1A332D); border-color: light-dark(#C8E8DC, #31594E);"); ?> }
        </style>
        <?php endif; ?>
        <div class="rule"><div class="rule-line"></div><span>快速測試</span><div class="rule-line"></div></div>
        <div class="quick-tests" aria-label="快速測試輸入">
          <button type="button" data-value="20">20 小時</button>
          <button type="button" data-value="19.9">19.9 小時</button>
          <button type="button" data-value="">空白</button>
          <button type="button" data-value="-5">-5 負數</button>
          <button type="button" data-value="20小時">20小時</button>
          <button type="button" data-value="二十">中文數字</button>
        </div>
      </section>
      <aside class="card side-card">
        <div class="status-icon" aria-hidden="true">⚓</div>
        <h2>判斷標準</h2>
        <p class="muted">依照輸入的時數，系統會將靠泊時間分成兩種狀態。</p>
        <div class="threshold">
          <div class="threshold-number">20<span style="font-size:16px;font-weight:600"> hr</span></div>
          <div class="threshold-label">關鍵分界<br>20 小時（含）以上</div>
        </div>
        <div class="mini-rule"><span></span></div>
        <div class="legend">
          <div class="legend-row"><span class="dot long"></span><span><strong>長時間靠泊</strong>　20 小時以上</span></div>
          <div class="legend-row"><span class="dot normal"></span><span><strong>一般靠泊</strong>　未滿 20 小時</span></div>
          <div class="legend-row"><span class="dot error"></span><span><strong>輸入錯誤</strong>　空白或無效格式</span></div>
        </div>
      </aside>
    </div>
  </main>
  <footer class="footer">
    <span>船舶靠泊時間判斷工具</span>
    <span>課堂練習 · PHP 邏輯示範</span>
  </footer>
</div>
<script>
document.querySelectorAll('[data-value]').forEach(button => {
  button.addEventListener('click', () => {
    const input = document.getElementById('hours');
    input.value = button.dataset.value;
    document.getElementById('hoursForm').requestSubmit();
  });
});
</script>
</body>
</html>