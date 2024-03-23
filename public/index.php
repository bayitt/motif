<?php 

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Motif\Controllers\AuthController;
use Motif\Controllers\ReadingController;
use Dotenv\Dotenv;

require __DIR__ . "/../vendor/autoload.php";

$container = include_once __DIR__ . "/../bootstrap.php";

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$app = AppFactory::createFromContainer($container);

$app->addBodyParsingMiddleware();

$app->post("/login/initiate", [AuthController::class, "initiateLogin"]);

$app->post("/login", [AuthController::class, "login"])->add("LoginMiddleware");

$app->group(
    "/readings", function (RouteCollectorProxy $group) {
        $group->post("", [ReadingController::class, "create"]);
        $group->put("/{uuid}", [ReadingController::class, "update"]);
        $group->delete("/{uuid}", [ReadingController::class, "delete"]);
    }
)->add("AuthMiddleware")->add("ReadingMiddleware");

$app->run();
