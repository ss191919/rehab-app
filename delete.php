<?php
$pdo = new PDO('mysql:host=db;dbname=reha_db;charset=utf8', 'root', 'root');

$id = $_GET["id"];

$stmt = $pdo->prepare("DELETE FROM records WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;