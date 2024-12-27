<?php
require 'db.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

// Проверяем роль
if (!in_array($role, ['user', 'organizer'])) {
    die('Неверный тип аккаунта.');
}

// Проверяем, существует ли email
$stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    die('Пользователь с таким email уже существует.');
}

// Добавляем нового пользователя
$stmt = $db->prepare('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
$stmt->execute([
    'username' => $username,
    'email' => $email,
    'password' => $password,
    'role' => $role,
]);

header('Location: login.php');
exit;
?>
