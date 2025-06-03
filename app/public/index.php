<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\UserController;
use App\Middleware\CorsMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

// Загрузка переменных из .env файла
$dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/../');
$dotenv->load();
$app = AppFactory::create();

// CORS
$app->add(CorsMiddleware::class);

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

// Роуты
$app->get('/user/get/{id}', [UserController::class, 'getById']);
$app->get('/user/search', [UserController::class, 'search']);
$app->post('/user/register', [UserController::class, 'register']);
$app->post('/user/login', [UserController::class, 'login']);

$app->any('{routes:.+}', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $response->withStatus(404);
});

// Запустить приложение
$app->run();
