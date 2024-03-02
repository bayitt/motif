<?php 

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Motif\Controllers\AuthController;
use Dotenv\Dotenv;

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$app = AppFactory::create();

$app->post("/login/initiate", [AuthController::class, "initiateLogin"]);

$app->run();
