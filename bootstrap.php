<?php

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use UMA\DIC\Container;
use Motif\Services\ReadingService;
use Motif\Services\MagicLinkService;
use Motif\Controllers\AuthController;
use Motif\Controllers\ReadingController;
use Motif\Middleware\LoginMiddleware;
use Motif\Middleware\AuthMiddleware;
use Motif\Middleware\ReadingMiddleware;

require_once __DIR__ . "/vendor/autoload.php";

$container = new Container(require __DIR__ . "/settings.php");

$container->set(
    EntityManager::class, static function (Container $container): EntityManager {
        $settings = $container->get("settings");

        $cache = $settings["doctrine"]["dev_mode"] ? DoctrineProvider::wrap(new ArrayAdapter())
        : DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings["doctrine"]["cache_dir"]));

        $config = Setup::createAttributeMetadataConfiguration($settings["doctrine"]["metadata_dirs"], $settings["doctrine"]["dev_mode"], null, $cache);

        return EntityManager::create($settings["doctrine"]["connection"], $config);
    }
);

// Setting the services in the container
$container->set(
    ReadingService::class, static function (Container $container): ReadingService {
        return new ReadingService($container->get(EntityManager::class));
    }
);

$container->set(
    MagicLinkService::class, static function (Container $container): MagicLinkService {
        return new MagicLinkService($container->get(EntityManager::class));
    }
);

// Setting the controllers in the container
$container->set(
    AuthController::class, static function (Container $container): AuthController {
        return new AuthController($container->get(MagicLinkService::class));
    }
);

$container->set(
    ReadingController::class, static function (Container $container): ReadingController {
        return new ReadingController($container->get(ReadingService::class));
    }
);

// Setting the middleware in the container
$container->set(
    "LoginMiddleware", static function (Container $container): LoginMiddleware {
        return new LoginMiddleware($container->get(MagicLinkService::class));
    }
);

$container->set(
    "AuthMiddleware", static function (Container $container): AuthMiddleware {
        return new AuthMiddleware();
    }
);

$container->set("ReadingMiddleware", static function (Container $container): ReadingMiddleware {
    return new ReadingMiddleware($container->get(ReadingService::class));
});

return $container;
