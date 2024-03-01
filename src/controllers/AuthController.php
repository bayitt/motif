<?php

declare(strict_types=1);

namespace Motif\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Doctrine\ORM\EntityManager;
use Motif\Services\MagicLinkService;

class AuthController 
{

    public function __construct()
    {
        //
    }

    public function initiateLogin(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response;
    }
}