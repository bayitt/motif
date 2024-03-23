<?php

declare(strict_types=1);

namespace Motif\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use Motif\Services\ReadingService;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;

class ReadingMiddleware
{
    /**
     * 
     *
     * @var ReadingService $readingService 
     */
    private ReadingService $readingService;

    public function __construct(ReadingService $readingService)
    {
        $this->readingService = $readingService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $method = strtolower($request->getMethod());
        if ($method === "post") {
            return $this->validateReadingCreation($request, $handler);
        } else if($method === "put") {
            return $this->validateReadingUpdate($request, $handler);
        }
        else if($method === "delete") {
            return $this->validateReadingDeletion($request, $handler);
        }


        return $handler->handle($request);
    }

    private function validateReadingCreation(Request $request, RequestHandler $handler): Response
    {
        $body = $request->getParsedBody();

        if (!isset($body["value"]) || gettype($body["value"]) !== "integer") {
            $response = new Response();
            $response = $response->withStatus(400);
            $payload = json_encode(
                [
                "code" => "reading_001",
                "message" => "Parameter value is missing from the request body or is not an integer"
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        return $handler->handle($request);
    }

    private function validateReadingUpdate(Request $request, RequestHandler $handler): Response
    {
        $body = $request->getParsedBody();

        if (!isset($body["value"]) || gettype($body["value"]) !== "integer") {
            $response = new Response();
            $response = $response->withStatus(400);
            $payload = json_encode(
                [
                "code" => "reading_001",
                "message" => "Parameter value is missing from the request body or is not an integer"
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $uuid = $route->getArgument("uuid");

        $reading = $this->readingService->findOne(["uuid" => $uuid]);

        if (!$reading) {
            $response = new Response();
            $response = $response->withStatus(404);
            $payload = json_encode(
                [
                "code" => "reading_002",
                "message" => sprintf("Reading with uuid %s does not exist", $uuid)
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        $request = $request->withAttribute("reading", $reading);
        return $handler->handle($request);
    }

    private function validateReadingDeletion(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $uuid = $route->getArgument("uuid");

        $reading = $this->readingService->findOne(["uuid" => $uuid]);

        if (!$reading) {
            $response = new Response();
            $response = $response->withStatus(404);
            $payload = json_encode(
                [
                "code" => "reading_002",
                "message" => sprintf("Reading with uuid %s does not exist", $uuid)
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        $request = $request->withAttribute("reading", $reading);
        return $handler->handle($request);
    }
}
