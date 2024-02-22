<?php

define("APP_ROOT", __DIR__);

$isDev = $_ENV["APP_ENV"] !== "prod";

return [
    "settings" => [
        "slim" => [
            "displayErrorDetails" => $isDev,
            "logErrors" => $isDev,
            "logErrorDetails" => $isDev
        ],
        "doctrine" => [
            "dev_mode" => $isDev,
            "cache_dir" => APP_ROOT . "/var/doctrine",
            "metadata_dirs" => [APP_ROOT . "/src/Models"],
            "connection" => [
                "driver" => "pdo_mysql",
                "host" => $_ENV["DB_HOST"],
                "port" => $_ENV["DB_PORT"],
                "dbname" => $_ENV["DB_NAME"],
                "user" => $_ENV["DB_USER"],
                "password" => $_ENV["DB_PASSWORD"],
                "charset" => "utf-8"
            ]
        ]
    ]
];