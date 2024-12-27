<?php
session_start();
require 'db.php';

// Проверьте, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: ../php/login.php');
    exit;
}

// Получение информации о пользователе
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, about, interests, avatar FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../templatess/header.php'; ?>

    <div class="container">
        <h2>Профиль</h2>

        <!-- Отображение аватара -->
        <?php if (!empty($user['avatar']) && file_exists("../uploads/" . $user['avatar'])): ?>
            <img src="<?= htmlspecialchars("../uploads/" . $user['avatar']) ?>" alt="Avatar" style="width: 150px; height: auto; border-radius: 50%;">
        <?php else: ?>
            <img src="../uploads/default-avatar.png" alt="Default Avatar" style="width: 150px; height: auto; border-radius: 50%;">
        <?php endif; ?>


        <p><strong>Имя пользователя:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>О себе:</strong> <?= htmlspecialchars($user['about']) ?: 'Не указано' ?></p>
        <p><strong>Интересы:</strong> <?= htmlspecialchars($user['interests']) ?: 'Не указано' ?></p>

        <a href="edit_profile.php">Редактировать профиль</a>
    </div>
</body>
</html>
