<?php
require_once '../app/config/database.php';
require_once '../app/lib/Auth.php';
require_once '../app/lib/Router.php';

// Инициализируем сессии
Auth::startSession();

// Создаем и настраиваем роутер
$router = new Router();

// Основные маршруты
$router->addRoute('', 'HomeController', 'index');
$router->addRoute('home', 'HomeController', 'index');
$router->addRoute('about', 'HomeController', 'about');
$router->addRoute('contacts', 'HomeController', 'contacts');
$router->addRoute('services/vps', 'HomeController', 'vps');
$router->addRoute('services/cloud', 'HomeController', 'cloud');
$router->addRoute('services/colocation', 'HomeController', 'colocation');

// Маршруты аутентификации
$router->addRoute('auth/login', 'AuthController', 'login');
$router->addRoute('auth/register', 'AuthController', 'register');
$router->addRoute('auth/logout', 'AuthController', 'logout');

// Личный кабинет
$router->addRoute('dashboard', 'DashboardController', 'index');
$router->addRoute('dashboard/services', 'DashboardController', 'services');
$router->addRoute('dashboard/create-service', 'DashboardController', 'createService');
$router->addRoute('dashboard/invoice', 'DashboardController', 'invoice');

// Оплата
$router->addRoute('payment', 'PaymentController', 'index');
$router->addRoute('payment/upload', 'PaymentController', 'uploadCheque');

// Панель оператора
$router->addRoute('admin/orders', 'AdminController', 'orders');
$router->addRoute('admin/approve', 'AdminController', 'approveOrder');

// Запускаем маршрутизацию
$router->dispatch($_SERVER['REQUEST_URI']);
?>