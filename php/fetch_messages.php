<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$user_id = $_SESSION['user_id'];
$chat_with = isset($_GET['chat_with']) ? $_GET['chat_with'] : null;

if (!$chat_with) {
    echo json_encode(['error' => 'Неверный пользователь']);
    exit;
}

$stmt = $db->prepare('
    SELECT * FROM messages 
    WHERE (sender_id = :user_id AND receiver_id = :chat_with) 
    OR (sender_id = :chat_with AND receiver_id = :user_id) 
    ORDER BY created_at ASC
');
$stmt->execute(['user_id' => $user_id, 'chat_with' => $chat_with]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
exit;
