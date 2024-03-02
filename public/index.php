<?php 

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Motif\Controllers\AuthController;
use Motif\Middleware\LoginMiddleware;
use Dotenv\Dotenv;

require __DIR__ . "/../vendor/autoload.php";

$container = require_once __DIR__ . "/../bootstrap.php";

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$app = AppFactory::createFromContainer($container);

$app->post("/login/initiate", [AuthController::class, "initiateLogin"]);

$app->post("/login", [AuthController::class, "login"])->add("LoginMiddleware");

$app->run();
