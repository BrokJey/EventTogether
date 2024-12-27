<?php
$host = 'localhost';
$db = 'event_together_db';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>
