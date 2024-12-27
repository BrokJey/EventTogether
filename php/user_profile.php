<?php
session_start();
require 'db.php';

if (!isset($_GET['user_id'])) {
    echo "Пользователь не найден.";
    exit;
}

$user_id = $_GET['user_id'];

// Получаем данные пользователя
$stmt = $db->prepare("SELECT username, avatar, about, interests FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Пользователь не найден.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?> - Профиль</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../templatess/header.php'; ?>

    <h1>Профиль: <?php echo htmlspecialchars($user['username']); ?></h1>
    <div>
        <?php if (!empty($user['avatar'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%;">
        <?php else: ?>
            <img src="../uploads/default-avatar.png" alt="Default Avatar" style="width: 100px; height: 100px; border-radius: 50%;">
        <?php endif; ?>
    </div>
    <p><strong>О себе:</strong> <?php echo htmlspecialchars($user['about'] ?: 'Информация отсутствует'); ?></p>
    <p><strong>Интересы:</strong> <?php echo htmlspecialchars($user['interests'] ?: 'Информация отсутствует'); ?></p>
</body>
</html>
