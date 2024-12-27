<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'leave') {
            // Удаляем пользователя из списка участников
            $stmt = $db->prepare('DELETE FROM event_participants WHERE event_id = :event_id AND user_id = :user_id');
            $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);
            echo 'Вы покинули мероприятие.';
        } elseif ($action === 'join') {
            // Проверяем, есть ли пользователь уже в списке участников
            $stmt = $db->prepare('SELECT * FROM event_participants WHERE event_id = :event_id AND user_id = :user_id');
            $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);

            if ($stmt->rowCount() > 0) {
                // Пользователь уже участвует
                echo 'Вы уже участвуете в этом мероприятии.';
            } else {
                // Добавляем пользователя в список участников
                $stmt = $db->prepare('INSERT INTO event_participants (event_id, user_id) VALUES (:event_id, :user_id)');
                $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);
                echo 'Вы успешно присоединились к мероприятию!';
            }
        }
    }
    exit;
}

header('Location: ../index.php');
exit;
