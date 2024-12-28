<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
$base_url = '/EventTogether'; // Корень вашего проекта
?>
<header>
    <nav>
        <a href="<?= $base_url ?>/index.php">Главная</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?= $base_url ?>/php/profile.php">Профиль</a>
            <a href="<?= $base_url ?>/php/chat.php">Чаты</a>
            <a href="<?= $base_url ?>/php/create_event.php">Создать событие</a>
            <a href="<?= $base_url ?>/php/my_events.php">Мои мероприятия</a>
            <a href="<?= $base_url ?>/php/logout.php">Выход</a>
        <?php else: ?>
            <a href="<?= $base_url ?>/php/login.php">Вход</a>
            <a href="<?= $base_url ?>/php/register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>
