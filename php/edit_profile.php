<?php
session_start();
require 'db.php';

// Проверьте, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: ../php/login.php');
    exit;
}

// Получение текущей информации пользователя
$user_id = $_SESSION['user_id'];
$query = "SELECT username, about, interests, avatar FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? $user['username'];
    $about = $_POST['about'] ?? '';
    $interests = $_POST['interests'] ?? '';

    // Обновление информации
    $update_query = "UPDATE users SET username = :username, about = :about, interests = :interests WHERE id = :user_id";
    $stmt = $db->prepare($update_query);
    $stmt->execute([
        'username' => $username,
        'about' => $about,
        'interests' => $interests,
        'user_id' => $user_id
    ]);

    // Загрузка нового аватара, если он есть
    if (!empty($_FILES['avatar']['name'])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);

        // Обновление аватара в базе данных
        $avatar_update_query = "UPDATE users SET avatar = :avatar WHERE id = :user_id";
        $stmt = $db->prepare($avatar_update_query);
        $stmt->execute([
            'avatar' => $target_file,
            'user_id' => $user_id
        ]);
    }

    $_SESSION['success'] = "Профиль успешно обновлен.";
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать профиль</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../templatess/header.php'; ?>

    <div class="container">
        <h2>Редактировать профиль</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?= $_SESSION['success'] ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <label for="username">Имя пользователя:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="about">О себе:</label>
            <textarea name="about" id="about" rows="4"><?= htmlspecialchars($user['about']) ?></textarea>

            <label for="interests">Интересы:</label>
            <textarea name="interests" id="interests" rows="4"><?= htmlspecialchars($user['interests']) ?></textarea>

            <label for="avatar">Загрузить аватар:</label>
            <input type="file" name="avatar" id="avatar">

            <?php if ($user['avatar']): ?>
                <p>Текущий аватар:</p>
                <!-- Отображение аватара -->
                <?php if (!empty($participant['avatar']) && file_exists($participant['avatar'])): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" style="width: 150px; height: auto; border-radius: 50%;">
                <?php else: ?>
                <img src="../uploads/default-avatar.png" alt="Default Avatar" style="width: 150px; height: auto; border-radius: 50%;">
                <?php endif; ?>
            <?php endif; ?>

            <button type="submit">Сохранить изменения</button>
        </form>
    </div>
</body>
</html>
