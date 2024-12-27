<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (empty($message)) {
        echo 'Сообщение не может быть пустым.';
        exit;
    }

    $stmt = $db->prepare('INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)');
    $stmt->execute([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $message
    ]);

    echo 'Сообщение отправлено.';
    exit;
}
