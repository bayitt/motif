<?php 

declare(strict_types=1);

namespace Motif\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Motif\Services\MagicLinkService;
use DateTimeImmutable;

class LoginMiddleware
{
    /**
     * 
     *
     * @var MagicLinkService $magicLinkService 
     */
    private MagicLinkService $magicLinkService;

    public function __construct(MagicLinkService $magicLinkService)
    {
        $this->magicLinkService = $magicLinkService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $body = $request->getParsedBody();

        if (!isset($body["magic_link_uuid"]) || gettype($body["magic_link_uuid"]) !== "string") {
            $request = $request->withAttribute("error", ["code" => 400, "message" => "Parameter magic_link_uuid is either missing from the request body or is not a string"]);
            return $handler->handle($request);
        }

        $magicLink = $this->magicLinkService->findOne(["uuid" => $body["magic_link_uuid"]]);

        if (!$magicLink) {
            $request = $request->withAttribute("error", ["code" => 404, "message" => sprintf("Magic link with uuid %s does not exist", $body["magic_link_uuid"])]);
            return $handler->handle($request);
        }

        $now = new DateTimeImmutable("now");

        if ($now->getTimestamp() >= $magicLink->getExpiresAt()->getTimestamp()) {
            $request = $request->withAttribute("error", ["code" => 404, "message" => sprintf("Magic link with uuid %s does not exist", $body["magic_link_uuid"])]);
            return $handler->handle($request);
        }

        return $handler->handle($request);
    }
}
