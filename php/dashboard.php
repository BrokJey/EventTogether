<?php
// Подключение к базе данных
require '../config/database.php';
session_start();

// Проверяем, что пользователь авторизован и является организатором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    die('Доступ запрещен');
}

// Получаем мероприятия, созданные текущим организатором
$stmt = $db->prepare('SELECT * FROM events WHERE created_by = :created_by');
$stmt->execute(['created_by' => $_SESSION['user_id']]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои мероприятия</title>
</head>
<body>
    <?php include '../templatess/header.php'; ?>

    <h1>Мои мероприятия</h1>
    <a href="create_event.php">Создать новое мероприятие</a>

    <table border="1">
        <tr>
            <th>Название</th>
            <th>Описание</th>
            <th>Дата</th>
            <th>Локация</th>
            <th>Цена</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['description']) ?></td>
                <td><?= htmlspecialchars($event['date']) ?></td>
                <td><?= htmlspecialchars($event['location']) ?></td>
                <td><?= htmlspecialchars($event['price'] ?: 'Бесплатно') ?></td>
                <td>
                    <a href="edit_event.php?id=<?= $event['id'] ?>">Редактировать</a>
                    <a href="delete_event.php?id=<?= $event['id'] ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
