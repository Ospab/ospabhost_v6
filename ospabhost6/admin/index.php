<?php
// Панель администратора
require_once '../app/config/database.php';
require_once '../app/lib/Auth.php';
require_once '../app/lib/Router.php';

Auth::startSession();

// Здесь можно добавить маршруты и логику для админки
echo "<h1>Панель администратора</h1>";

// Пример: выводим ссылку на заказы
echo '<a href="/admin/orders">Список заказов</a>';
