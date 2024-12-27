<?php
require 'db.php';
session_start();

// Проверяем, что пользователь авторизован и является организатором
$stmt = $db->prepare('SELECT role FROM users WHERE id = :id');
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'organizer') {
    die('Доступ запрещен');
}

// Получаем мероприятия, созданные текущим организатором
$stmt = $db->prepare('SELECT * FROM events WHERE created_by = :created_by ORDER BY date DESC');
$stmt->execute(['created_by' => $_SESSION['user_id']]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои мероприятия</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../templatess/header.php'; ?>

    <h1>Мои мероприятия</h1>
    <a href="create_event.php">Создать новое мероприятие</a>

    <?php if (count($events) > 0): ?>
        <table>
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
                    <td><?= htmlspecialchars(substr($event['description'], 0, 50)) ?>...</td>
                    <td><?= htmlspecialchars($event['date']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= $event['price'] ? htmlspecialchars($event['price']) : 'Бесплатно' ?></td>
                    <td>
                        <a href="edit_event.php?id=<?= $event['id'] ?>">Редактировать</a> |
                        <a href="delete_event.php?id=<?= $event['id'] ?>" onclick="return confirm('Вы уверены, что хотите удалить это мероприятие?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>У вас пока нет созданных мероприятий.</p>
    <?php endif; ?>
</body>
</html>
