<?php
require 'db.php';

if (!isset($_GET['id'])) {
    echo "Идентификатор события не указан.";
    exit;
}

$event_id = $_GET['id'];

try {
    // Удаляем записи участников события
    $stmt = $db->prepare('DELETE FROM event_attendees WHERE event_id = :event_id');
    $stmt->execute(['event_id' => $event_id]);

    // Удаляем событие
    $stmt = $db->prepare('DELETE FROM events WHERE id = :id');
    $stmt->execute(['id' => $event_id]);

    echo "Событие и его участники успешно удалены.";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>