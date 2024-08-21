<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Contracts\RouterInterface;
use App\Core\Contracts\ValidatorInterface;
use App\Core\Router;
use App\Core\Validation\Validator;
use App\Core\Http\Request;
use App\Core\Container;
use App\Controllers\ProductController;
use App\Models\ProductRepository;
use App\Services\SessionStorage;

require_once __DIR__ . '/../vendor/autoload.php';

// Конфигурация приложения
$config = [
    'debug' => true,
];

// Создаем контейнер зависимостей
$container = new Container();

// Регистрируем зависимости
$container->set(Request::class, fn() => new Request());
$container->set(RouterInterface::class, fn() => new Router());
$container->set(Validator::class, fn() => new Validator());
$container->set(ValidatorInterface::class, fn() => new Validator());
$container->set(SessionStorage::class, fn() => new SessionStorage());
$container->set(ProductRepository::class, fn($c) => new ProductRepository($c->get(SessionStorage::class)));
$container->set(ProductController::class, fn($c) => new ProductController($c->get(ProductRepository::class), $c->get(Request::class), $c->get(ValidatorInterface::class)));

// Создаем роутер и регистрируем маршруты
$router = $container->get(RouterInterface::class);
$router->get('/', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products', [ProductController::class, 'store']);
$router->post('/products/delete/{id}', [ProductController::class, 'delete']);

// Создаем и запускаем приложение
$app = new Application($container, $router, $config);
$app->run();