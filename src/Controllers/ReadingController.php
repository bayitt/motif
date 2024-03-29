<?php

declare(strict_types=1);

namespace Motif\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Motif\Services\ReadingService;
use DateTimeImmutable;


class ReadingController
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

    public function create(Request $request, Response $response, Array $args): Response
    {
        $body = $request->getParsedBody();
        $created_at = isset($body["created_at"]) ? DateTimeImmutable::createFromFormat("Y-m-d", $body["created_at"]) : null;
        $reading = $this->readingService->create($body["value"], $created_at);
        $response = $response->withStatus(201);
        $payload = json_encode($reading->jsonSerialize());

        $response->getBody()->write($payload);
        return $response;
    }

    public function update(Request $request, Response $response, Array $args): Response
    {
        $body = $request->getParsedBody();
        $reading = $request->getAttribute("reading");
        $reading->setValue($body["value"]);
        $this->readingService->flush();
        $payload = json_encode($reading->jsonSerialize());

        $response->getBody()->write($payload);
        return $response;
    }

    public function delete(Request $request, Response $response, Array $args): Response
    {
        $reading = $request->getAttribute("reading");
        $this->readingService->delete($reading);

        $response = $response->withStatus(204);
        return $response;
    }
}
