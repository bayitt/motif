<?php

declare(strict_types=1);

namespace Motif\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\Key;

class LoginMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeader("Authorization");
        return $handler->handle($request);
    }
}