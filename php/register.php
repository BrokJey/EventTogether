<?php
require_once 'db.php';
$errors = [];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Проверка полей
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'Заполните все поля!';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают!';
    }

    // Проверка уникальности пользователя
    if (empty($errors)) {
        $query = $db->prepare('SELECT * FROM users WHERE username = :username OR email = :email');
        $query->execute(['username' => $username, 'email' => $email]);
        if ($query->rowCount() > 0) {
            $errors[] = 'Пользователь с таким именем или email уже существует!';
        }
    }

    // Сохранение пользователя
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = $db->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        $query->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password
        ]);
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Регистрация</h1>
    <form action="register_process.php" method="POST">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Email" required>

        <label for="role">Тип аккаунта:</label>
        <select id="role" name="role" required>
            <option value="user">Пользователь</option>
            <option value="organizer">Организатор</option>
        </select>

        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>
