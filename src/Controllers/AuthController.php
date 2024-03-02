<?php

declare(strict_types=1);

namespace Motif\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Motif\Services\MagicLinkService;
use Mailgun\Mailgun;

class AuthController 
{
    /** @var MagicLinkService $magicLinkService */
    private MagicLinkService $magicLinkService;

    /** @var Mailgun $mailgun */
    private Mailgun $mailgun;

    public function __construct(MagicLinkService $magicLinkService)
    {   
        $this->magicLinkService = $magicLinkService;
        $this->mailgun = Mailgun::create($_ENV["MAIL_API_KEY"]);
    }

    public function initiateLogin(Request $request, Response $response, array $args): Response
    {
        $magicLink = $this->magicLinkService->create();
        $uuid = $magicLink->getUuid();
        $this->mailgun->messages()->send($_ENV["MAIL_DOMAIN"], [
            "from" => $_ENV["MAIL_FROM"],
            "to" => $_ENV["AUTH_EMAIL"],
            "subject" => "Motif Login",
            "text" => sprintf("Here is your magic link to login => %s", $_ENV["CLIENT_URL"] . "/login/$uuid")
        ]);

        $payload = json_encode(["message" => "Magic link has been sent to your email address"]);
        $response->getBody()->write($payload);
        return $response;
    }

    public function login(Request $request, Response $response, array $args): Response
    {
        $error = $request->getAttribute("error");

        if ($error) {
            $response = $response->withStatus($error["code"]);
            $payload = json_encode(["message" => $error["message"]]);
            $response->getBody()->write($payload);
            return $response;
        }
        return $response;
    }
}