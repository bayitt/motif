<?php

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use UMA\DIC\Container;
use App\Services\ReadingService;
use App\Services\MagicLinkService;

require_once __DIR__ . "/vendor/autoload.php";

$container = new Container(require __DIR__ . "/settings.php");

$container->set(EntityManager::class, static function(Container $container): EntityManager {
    $settings = $container->get("settings");

    $cache = $settings["doctrine"]["dev_mode"] ? DoctrineProvider::wrap(new ArrayAdapter())
    : DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings["doctrine"]["cache_dir"]));

    $config = Setup::createAttributeMetadataConfiguration($settings["doctrine"]["metadata_dirs"], $settings["doctrine"]["dev_mode"], null, $cache);

    return EntityManager::create($settings["doctrine"]["connection"], $config);
});

// Setting the services in the container

$container->set(ReadingService::class, static function(Container $container): ReadingService {
    return new ReadingService($container->get(EntityManager::class));
});

$container->set(MagicLinkService::class, static function(Container $container): MagicLinkService {
    return new MagicLinkService($container->get(EntityManager::class));
});

return $container;