<?php

declare(strict_types=1);

namespace Motif\Controllers;

use DateInterval;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Motif\Services\MagicLinkService;
use Mailgun\Mailgun;
use Firebase\JWT\JWT;
use DateTimeImmutable;

class AuthController
{
    /**
     * 
     *
     * @var MagicLinkService $magicLinkService 
     */
    private MagicLinkService $magicLinkService;

    /**
     * 
     *
     * @var Mailgun $mailgun 
     */
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
        $this->mailgun->messages()->send(
            $_ENV["MAIL_DOMAIN"], [
            "from" => $_ENV["MAIL_FROM"],
            "to" => $_ENV["AUTH_EMAIL"],
            "subject" => "Motif Login",
            "text" => sprintf("Here is your magic link to login => %s", $_ENV["CLIENT_URL"] . "/login/$uuid")
            ]
        );

        $payload = json_encode(["message" => "Magic link has been sent to your email address"]);
        $response->getBody()->write($payload);
        return $response;
    }

    public function login(Request $request, Response $response, array $args): Response
    {
        $error = $request->getAttribute("error");

        if ($error) {
            $response = $response->withStatus($error["code"]);
            $payload = json_encode(["code" => $error["error_id"], "message" => $error["message"]]);
            $response->getBody()->write($payload);
            return $response;
        }

        $now = new DateTimeImmutable("now");
        $expires = $now->add(new DateInterval("PT72H0M0S"));

        $jwtPayload = [
            "iss" => $_ENV["APP_URL"],
            "aud" => $_ENV["CLIENT_URL"],
            "exp" => $expires->getTimestamp(),
        ];

        $jwt = JWT::encode($jwtPayload, $_ENV["JWT_KEY"], 'HS256');
        $payload = json_encode(["token" => $jwt]);

        $magicLink = $request->getAttribute("magicLink");
        $magicLink->setIsUsed();
        $this->magicLinkService->flush();

        $response->getBody()->write($payload);
        return $response;
    }
}
