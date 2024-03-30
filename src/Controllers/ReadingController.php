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
        $created_at = isset($body["date"]) ? DateTimeImmutable::createFromFormat("Y-m-d", $body["date"]) : null;
        $reading = $this->readingService->create($body["value"], $created_at);
        $response = $response->withStatus(201);
        $payload = json_encode($reading->jsonSerialize());

        $response->getBody()->write($payload);
        return $response;
    }

    public function get(Request $request, Response $response, Array $args): Response
    {
        $queryParams = $request->getQueryParams();
        $start_date = isset($queryParams["start_date"]) ? $queryParams["start_date"] : date("Y-m-d");
        $end_date = isset($queryParams["end_date"]) ? $queryParams["end_date"] : date("Y-m-d");
        $readings = $this->readingService->findBetweenDates($start_date, $end_date);
        $payload = json_encode($readings);
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
