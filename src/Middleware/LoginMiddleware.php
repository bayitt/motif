<?php 

declare(strict_types=1);

namespace Motif\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
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
            $response = new Response();
            $response = $response->withStatus(400);
            $payload = json_encode(
                [
                "code" => "login_001",
                "message" => "Parameter magic_link_uuid is either missing from the request body or is not a string"
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        $magicLink = $this->magicLinkService->findOne(["uuid" => $body["magic_link_uuid"]]);

        if (!$magicLink) {
            $response = new Response();
            $response = $response->withStatus(404);
            $payload = json_encode(
                [
                "code" => "login_002",
                "message" => sprintf("Magic link with uuid %s does not exist", $body["magic_link_uuid"])
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        if ($magicLink->getIsUsed()) {
            $response = new Response();
            $response = $response->withStatus(400);
            $payload = json_encode(
                [
                "code" => "login_003",
                "message" => sprintf("Magic link with uuid has been used")
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        $now = new DateTimeImmutable("now");

        if ($now->getTimestamp() >= $magicLink->getExpiresAt()->getTimestamp()) {
            $response = new Response();
            $response = $response->withStatus(404);
            $payload = json_encode(
                [
                "code" => "login_002",
                "message" => sprintf("Magic link with uuid %s does not exist", $body["magic_link_uuid"])
                ]
            );
            $response->getBody()->write($payload);
            return $response;
        }

        $request = $request->withAttribute("magicLink", $magicLink);
        return $handler->handle($request);
    }
}
