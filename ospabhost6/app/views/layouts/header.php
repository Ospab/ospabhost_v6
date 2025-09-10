<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ospab.host' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="description" content="Надёжный хостинг VPS, облако и колокация в Великом Новгороде — ospab.host">
    <meta name="theme-color" content="#2c3e50">
    <link rel="icon" href="/assets/images/favicon.ico">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="/" class="nav-logo">ospab.host</a>
                <div class="nav-menu">
                    <a href="/" class="nav-link">Главная</a>
                    <a href="/app/views/services/vps.php" class="nav-link">VPS</a>
                    <a href="/app/views/services/cloud.php" class="nav-link">Облако</a>
                    <a href="/app/views/services/colocation.php" class="nav-link">Колокация</a>
                    <a href="/app/views/about.php" class="nav-link">О нас</a>
                    <a href="/app/views/contacts.php" class="nav-link">Контакты</a>
                    <?php if (class_exists('Auth') && Auth::isLoggedIn()): ?>
                        <a href="/app/views/dashboard.php" class="nav-link">Личный кабинет</a>
                        <a href="/app/views/auth/logout.php" class="nav-link">Выход</a>
                    <?php else: ?>
                        <a href="/app/views/auth/login.php" class="nav-link">Вход</a>
                        <a href="/app/views/auth/register.php" class="nav-link">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <main class="main-content">