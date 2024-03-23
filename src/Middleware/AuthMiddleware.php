<?php

declare(strict_types=1);

namespace Motif\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new Response();
        $response = $response->withStatus(401);
        $payload = json_encode(
            [
            "code" => "auth_001",
            "message" => "Unauthenticated."
            ]
        );
        $authHeader = $request->getHeader("Authorization");
        $authHeaderSplits = explode(" ", $authHeader ?? "");

        if (!$authHeader || count($authHeaderSplits) !== 2) {
            $response->getBody()->write($payload);
            return $response;
        }

        try {
            JWT::decode($authHeaderSplits[1], new Key($_ENV["JWT_KEY"], "HS256"));
        }
        catch (Exception $exception) {
            $response->getBody()->write($payload);
            return $response;   
        }
        return $handler->handle($request);
    }
}
