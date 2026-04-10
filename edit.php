<?php
$pdo = new PDO('mysql:host=db;dbname=reha_db;charset=utf8', 'root', 'root');

$errors = [];

$id = $_GET["id"];

// 更新処理
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

    // エラーなければ更新
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE records 
            SET patient=?, date=?, life=?, goal=?, exercise=?, pain=?, memo=?
            WHERE id=?
        ");
        $stmt->execute([
            $_POST["patient"],
            $_POST["date"],
            $_POST["life"],
            $_POST["goal"],
            $_POST["exercise"],
            $_POST["pain"],
            $_POST["memo"],
            $id
        ]);

        header("Location: index.php");
        exit;
    }
}

// データ取得
$stmt = $pdo->prepare("SELECT * FROM records WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <title>編集</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: 40px auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        form p {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #2E8B57;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
        }

        .back-btn {
            display: block;
            margin-top: 10px;
            padding: 10px;
            background: #ccc;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            color: #333;
        }

    </style>
</head>

<body>

    <h1>編集</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p>
                    <?= $error ?>
                </p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <p>患者名：</p>
        <input type="text" name="patient" value="<?= htmlspecialchars($record["patient"], ENT_QUOTES, 'UTF-8') ?>">

        <p>日付：</p>
        <input type="date" name="date" value="<?= $record["date"] ?>">

        <p>生活状況：</p>
        <textarea name="life"><?= htmlspecialchars($record["life"], ENT_QUOTES, 'UTF-8') ?></textarea>

        <p>目標：</p>
        <input type="text" name="goal" value="<?= $record["goal"] ?>">

        <p>運動内容：</p>
        <textarea name="exercise"><?= $record["exercise"] ?></textarea>

        <p>痛み：</p>
        <input type="number" name="pain" value="<?= $record["pain"] ?>">

        <p>メモ：</p>
        <textarea name="memo"><?= $record["memo"] ?></textarea>

        <button type="submit">更新</button>

        <a href="index.php" class="back-btn">戻る</a>
    </form>

</body>

</html>