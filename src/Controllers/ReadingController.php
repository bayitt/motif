<?php

declare(strict_types=1);

namespace Motif\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Motif\Services\ReadingService;


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
        return $response;
    }
}
