<?php
require_once 'php/db.php';

// Получение списка событий
$query = $db->query('SELECT events.*, users.username AS creator FROM events JOIN users ON events.created_by = users.id ORDER BY date DESC');
$events = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>События</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templatess/header.php'; ?>

    <h1>EventTogether</h1>
    <?php if (!empty($events)): ?>
        <ul>
            <?php foreach ($events as $event): ?>
                <li>
                    <h2><?= htmlspecialchars($event['title']) ?></h2>
                    <p><?= htmlspecialchars($event['description']) ?></p>
                    <p>Дата: <?= htmlspecialchars($event['date']) ?></p>
                    <p>Место: <?= htmlspecialchars($event['location'] ?? 'Не указано') ?></p>
                    <p>Стоимость: <?= htmlspecialchars($event['price'] ? $event['price'] . ' руб.' : 'Бесплатно') ?></p>
                    <p>Создатель: <?= htmlspecialchars($event['creator']) ?></p>
                    <a href="php/event.php?id=<?= $event['id'] ?>">Подробнее</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Пока нет событий.</p>
    <?php endif; ?>
</body>
</html>
