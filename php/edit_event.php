<?php
require 'db.php';
session_start();

// Проверяем авторизацию и роль

$stmt = $db->prepare('SELECT role FROM users WHERE id = :id');
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'organizer') {
    die('Доступ запрещен');
}

// Получаем ID мероприятия
$event_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обновляем мероприятие
    $stmt = $db->prepare('UPDATE events SET title = :title, description = :description, date = :date, location = :location, price = :price WHERE id = :id AND created_by = :created_by');
    $stmt->execute([
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'date' => $_POST['date'],
        'location' => $_POST['location'],
        'price' => $_POST['price'] ?: null,
        'id' => $event_id,
        'created_by' => $_SESSION['user_id'],
    ]);
    header('Location: my_events.php');
    exit;
} else {
    // Загружаем мероприятие
    $stmt = $db->prepare('SELECT * FROM events WHERE id = :id AND created_by = :created_by');
    $stmt->execute(['id' => $event_id, 'created_by' => $_SESSION['user_id']]);
    $event = $stmt->fetch();
    if (!$event) {
        die('Мероприятие не найдено');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать мероприятие</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../templatess/header.php'; ?>

    <h1>Редактировать мероприятие</h1>
    <form method="POST">
        <label>Название: <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required></label><br>
        <label>Описание: <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea></label><br>
        <label>Дата: <input type="date" name="date" value="<?= htmlspecialchars($event['date']) ?>" required></label><br>
        <label>Локация: <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required></label><br>
        <label>Цена: <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($event['price']) ?>"></label><br>
        <button type="submit">Сохранить</button>
    </form>
</body>
</html>
