<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$event_id = $_GET['id'] ?? null;

if ($event_id) {
    $query = $db->prepare('INSERT INTO event_attendees (event_id, user_id) VALUES (:event_id, :user_id)');
    $query->execute([
        'event_id' => $event_id,
        'user_id' => $_SESSION['user_id']
    ]);
}

header("Location: event.php?id=$event_id");
exit();
?>
