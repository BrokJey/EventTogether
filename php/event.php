<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo "Мероприятие не найдено.";
    exit;
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Получение информации о событии
$stmt = $db->prepare("SELECT * FROM events WHERE id = :event_id");
$stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Мероприятие не найдено.";
    exit;
}

// Проверка, присоединился ли пользователь
$stmt = $db->prepare("SELECT * FROM event_participants WHERE event_id = :event_id AND user_id = :user_id");
$stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$is_participating = $stmt->rowCount() > 0;

// Получение списка участников
$stmt = $db->prepare("SELECT users.id, users.username 
                      FROM event_participants 
                      JOIN users ON event_participants.user_id = users.id 
                      WHERE event_id = :event_id");
$stmt->bindValue(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    

    </html>
    <?php include '../templatess/header.php'; ?>

    <h2><?php echo htmlspecialchars($event['title']); ?></h2>
    <p><?php echo htmlspecialchars($event['description']); ?></p>
    <p><strong>Дата:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
    <p><strong>Место:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
    <p><strong>Цена:</strong> <?php echo $event['price'] ? htmlspecialchars($event['price']) . ' ₽' : 'Бесплатно'; ?></p>
    <h3>Участники:</h3>
    <ul>
        <?php foreach ($participants as $participant): ?>
            <li>
                <a href="user_profile.php?user_id=<?php echo htmlspecialchars($participant['id']); ?>" style="text-decoration: none;">  
                <?php echo htmlspecialchars($participant['username']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="post" action="event_participation.php">
        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
        <?php if ($is_participating): ?>
            <button type="submit" name="action" value="leave">Покинуть мероприятие</button>
        <?php else: ?>
            <button type="submit" name="action" value="join">Присоединиться</button>
        <?php endif; ?>
    </form>

</body>