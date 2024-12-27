<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <nav>
        <a href="../index.php">Главная</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="php/profile.php">Профиль</a>
            <a href="php/chat.php">Чаты</a>
            <a href="php/create_event.php">Создать событие</a>
            <a href="php/my_events.php">Мои мероприятия</a>
            <a href="php/logout.php">Выход</a>
        <?php else: ?>
            <a href="php/login.php">Вход</a>
            <a href="php/register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>
