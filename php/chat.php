<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем список участников для чата
$participants_stmt = $db->prepare('
    SELECT DISTINCT u.id, u.username 
    FROM users u 
    JOIN event_participants ep ON u.id = ep.user_id 
    WHERE ep.event_id IN (SELECT event_id FROM event_participants WHERE user_id = :user_id) AND u.id != :user_id
');
$participants_stmt->execute(['user_id' => $user_id]);
$participants = $participants_stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем сообщения с выбранным пользователем
$chat_with = isset($_GET['user']) ? $_GET['user'] : null;
$messages = [];

if ($chat_with) {
    $messages_stmt = $db->prepare('
        SELECT * FROM messages 
        WHERE (sender_id = :user_id AND receiver_id = :chat_with) 
        OR (sender_id = :chat_with AND receiver_id = :user_id) 
        ORDER BY created_at ASC
    ');
    $messages_stmt->execute(['user_id' => $user_id, 'chat_with' => $chat_with]);
    $messages = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Чат</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../templatess/header.php'; ?>
    <h1>Чат</h1>
    <div>
        <h2>Участники</h2>
        <ul>
            <?php foreach ($participants as $participant): ?>
                <li>
                    <a href="chat.php?user=<?= $participant['id'] ?>">
                        <?= htmlspecialchars($participant['username']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div>
        <h2>Сообщения</h2>
        <?php if ($chat_with): ?>
            <div id="messages">
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li>
                            <strong><?= $message['sender_id'] == $user_id ? 'Вы' : 'Собеседник' ?>:</strong>
                            <?= htmlspecialchars($message['message']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form id="message-form" action="send_message.php" method="POST">
                <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">
                <textarea name="message" id="message-input" placeholder="Введите сообщение"></textarea>
                <button type="submit">Отправить</button>
            </form>
        <?php else: ?>
            <p>Выберите пользователя для начала чата.</p>
        <?php endif; ?>
    </div>

    <script>
        <?php if ($chat_with): ?>
            function fetchMessages() {
                $.ajax({
                    url: 'fetch_messages.php?chat_with=<?= $chat_with ?>',
                    method: 'GET',
                    success: function(data) {
                        const messages = JSON.parse(data);
                        const messagesContainer = $('#messages ul');
                        messagesContainer.empty();

                        messages.forEach(function(message) {
                            const sender = message.sender_id == <?= $user_id ?> ? 'Вы' : 'Собеседник';
                            messagesContainer.append(`<li><strong>${sender}:</strong> ${message.message}</li>`);
                        });
                    },
                    error: function() {
                        console.error('Не удалось загрузить сообщения.');
                    }
                });
            }

            $(document).ready(function() {
                // Обновляем сообщения каждые 2 секунды
                setInterval(fetchMessages, 2000);

                // Отправка сообщения без перезагрузки
                $('#message-form').on('submit', function(e) {
                    e.preventDefault();
                    const message = $('#message-input').val();
                    if (!message.trim()) return;

                    $.post('send_message.php', {
                        receiver_id: <?= $chat_with ?>,
                        message: message
                    }, function() {
                        $('#message-input').val('');
                        fetchMessages(); // Обновляем после отправки
                    });
                });
            });
        <?php endif; ?>
    </script>
</body>
</html>
