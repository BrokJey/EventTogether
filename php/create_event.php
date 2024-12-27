<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Проверяем роль пользователя
$stmt = $db->prepare('SELECT role FROM users WHERE id = :id');
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'organizer') {
    die('У вас нет прав для создания мероприятий.');
}

// Обработка формы создания мероприятия
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $price = $_POST['price'];

    $stmt = $db->prepare('INSERT INTO events (title, description, date, location, price, created_by) 
                          VALUES (:title, :description, :date, :location, :price, :created_by)');
    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'date' => $date,
        'location' => $location,
        'price' => $price,
        'created_by' => $_SESSION['user_id']
    ]);

    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Создать мероприятие</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../templatess/header.php'; ?>
    <h1>Создать мероприятие</h1>
    <form action="create_event.php" method="POST">
        <label for="title">Название:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Описание:</label>
        <textarea id="description" name="description"></textarea>

        <label for="date">Дата:</label>
        <input type="date" id="date" name="date" required>

        <label for="location">Местоположение:</label>
        <input type="text" id="location" name="location">

        <label for="price">Цена:</label>
        <input type="number" id="price" name="price" step="0.01">

        <button type="submit">Создать</button>
    </form>
</body>
</html>
