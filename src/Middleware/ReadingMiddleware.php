<?php

declare(strict_types=1);

namespace Motif\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use Motif\Services\ReadingService;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;
use DateTime;

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

        switch($method) {
        case "post":
            return $this->validateReadingCreation($request, $handler);
        case "get":
            return $this->validateReadingRead($request, $handler);
        case "put":
            return $this->validateReadingUpdate($request, $handler);
        case "delete":
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

    private function validateReadingRead(Request $request, RequestHandler $handler): Response
    {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams["start_date"]) && !isset($queryParams["end_date"]))
            return $handler->handle($request);

        $payload = json_encode(
            [
            "code" => "reading_001",
            "message" => "Query parameter start_date or end_date is not a valid date"
            ]
        );

        $format = "Y-m-d";

        if (isset($queryParams["start_date"])) {
            $date = $queryParams["start_date"];
            $dateTime = DateTime::createFromFormat($format, $date);
            if (!$dateTime || ($dateTime->format($format) !== $date)) {
                $response = new Response();
                $response->getBody()->write($payload);
                return $response;
            }
        }

        if (isset($queryParams["end_date"])) {
            $date = $queryParams["end_date"];
            $dateTime = DateTime::createFromFormat($format, $date);
            if (!$dateTime || ($dateTime->format($format) !== $date)) {
                $response = new Response();
                $response->getBody()->write($payload);
                return $response;
            }
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
