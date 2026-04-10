<?php
$pdo = new PDO('mysql:host=db;dbname=reha_db;charset=utf8', 'root', 'root');

$id = $_GET["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $stmt = $pdo->prepare("
    INSERT INTO comments (record_id, comment, staff)
    VALUES (?, ?, ?)
  ");
  $stmt->execute([
    $id,
    $_POST["comment"],
    $_POST["staff"]
  ]);

  header("Location: detail.php?id=" . $id);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM records WHERE id = ?");
$stmt->execute([$id]);

$record = $stmt->fetch();

// コメント取得
$stmt = $pdo->prepare("SELECT * FROM comments WHERE record_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>記録詳細</title>
    <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      padding: 0px;
    }

    h1 {
      text-align: center;
      color: #333;
      margin-top: 30px;
      margin-bottom: 20px;
    }

    h2 {
      margin-top: 20px;
      color: #555;
      border-left: 4px solid #2E8B57;
      padding-left: 10px;
    }

    p {
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }

    .comment {
      background: #f9f9f9;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    a {
      display: inline-block;
      margin-top: 20px;
      padding: 8px 12px;
      background: #007BFF;
      color: white;
      border-radius: 5px;
      text-decoration: none;
    }

    .card {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      max-width: 600px;
      margin: 40px auto;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    </style>
</head>
<body>

<div class="card">

<h1>記録詳細</h1>

<h2>基本情報</h2>
<p>患者名: <?= htmlspecialchars($record["patient"], ENT_QUOTES, 'UTF-8') ?></p>
<p>日付: <?= htmlspecialchars($record["date"], ENT_QUOTES, 'UTF-8') ?>
</p>

<h2>評価・目標</h2>
<p>生活状況: <?= htmlspecialchars($record["life"], ENT_QUOTES, 'UTF-8') ?>
</p>
<p>目標:
  <?= htmlspecialchars($record["goal"], ENT_QUOTES, 'UTF-8') ?>
</p>

<h2>リハ内容</h2>
<p>運動内容: <?= htmlspecialchars($record["exercise"], ENT_QUOTES, 'UTF-8') ?>
</p>
<p>痛み:
  <?= htmlspecialchars($record["pain"], ENT_QUOTES, 'UTF-8') ?>
</p>
<p>メモ:
  <?= htmlspecialchars($record["memo"], ENT_QUOTES, 'UTF-8') ?>
</p>

<h2>コメント</h2>

<?php foreach ($comments as $c): ?>
  <div class="comment">
    <strong>
      <?= htmlspecialchars($c["staff"] ?? '', ENT_QUOTES, 'UTF-8') ?>
    </strong><br>
    <?= htmlspecialchars($c["comment"], ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endforeach; ?>

<form method="POST">
  <input type="text" name="staff" placeholder="名前">
  <br>
  <textarea name="comment" required></textarea>
  <br>
  <button type="submit">コメント追加</button>
</form>

<a href="index.php">戻る</a>

</div>
</body>
</html>