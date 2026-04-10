<?php
$pdo = new PDO('mysql:host=db;dbname=reha_db;charset=utf8', 'root', 'root');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // バリデーション
  if (empty($_POST["patient"])) {
    $errors[] = "患者名は必須です";
  }

  if (empty($_POST["date"])) {
    $errors[] = "日付は必須です";
  }

  if ($_POST["pain"] < 0 || $_POST["pain"] > 10) {
    $errors[] = "痛みは0〜10で入力してください";
  }

  // エラーなければ保存
  if (empty($errors)) {
    $stmt = $pdo->prepare("
            INSERT INTO records (patient, date, exercise, pain, memo, life, goal)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
    $stmt->execute([
      $_POST["patient"],
      $_POST["date"],
      $_POST["exercise"],
      $_POST["pain"],
      $_POST["memo"],
      $_POST["life"],
      $_POST["goal"]
    ]);
  }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>訪問リハビリ記録アプリ</title>

    <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      padding: 0px;
    }

/* タイトル */
    h1 {
      text-align: center;
      color: #333;
      margin-top: 30px;
      margin-bottom: 20px;
    }

/* カード風 */
    form, .card {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      max-width: 600px;
      margin: 40px auto;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

/* 入力 */
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

/* ボタン */
    button {
      background: #2E8B57;
      font-size: 16px;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      width: 100%;
    }

/* 一覧 */
    .list {
      max-width: 600px;
      margin: 20px auto;
    }

    .list p {
      display: flex;
      justify-content: space-between;
      align-items: center;

      background: #fff;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .list p:hover {
      background: #f1f1f1;
      cursor: pointer;
    }

    form p {
        background: none;
        padding: 0;
        font-weight: bold;
        margin-bottom: 5px;
    }

/* リンク */
    a {
      margin-left: 10px;
      font-size: 14px;
      color: #007BFF;
      text-decoration: none;
    }

    .error {
      color: red;
      max-width: 600px;
      margin: 0 auto;
    }

</style>
</head>
<body>

<h1>訪問リハビリ記録</h1>

<?php if (!empty($errors)): ?>
  <div class="error">
    <?php foreach ($errors as $error): ?>
      <p><?= $error ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="POST">
    <p>患者名：<input type="text" name="patient" required></p>
    <p>訪問日：<input type="date" name="date" required></p>
    <p>生活状況：</p>
    <textarea name="life" required></textarea>
    <p>目標：</p>
    <input type="text" name="goal">
    <p>運動内容：</p>
    <textarea name="exercise" required></textarea>
    <p>痛み（NRS 0〜10）：<input type="number" name="pain" min="0" max="10" required></p>
    <p>所見・メモ：</p>
    <textarea name="memo"></textarea>
    <br><br>
    <button type="submit">保存</button>
</form>

<h2 style="text-align:center;">記録一覧</h2>

<div class="list">
<?php
$stmt = $pdo->query("SELECT * FROM records ORDER BY date DESC");

foreach ($stmt as $row) {
    echo "<p>";
    echo htmlspecialchars($row["date"], ENT_QUOTES, 'UTF-8') . " / ";
    echo htmlspecialchars($row["patient"], ENT_QUOTES, 'UTF-8');
    echo " 痛み:" . htmlspecialchars($row["pain"], ENT_QUOTES, 'UTF-8');
    echo " <a href='detail.php?id=" . $row["id"] . "'>詳細</a>";
    echo " <a href='edit.php?id=" . $row["id"] . "'>編集</a>";
    echo " <a href='delete.php?id=" . $row["id"] . "' onclick='return confirm(\"削除する？\")'>削除</a>";
    echo "</p>";
}
?>
</div>
<hr>