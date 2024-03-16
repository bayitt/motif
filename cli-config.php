<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$container = include_once __DIR__ . "./bootstrap.php";

return ConsoleRunner::createHelperSet($container->get(EntityManager::class));
