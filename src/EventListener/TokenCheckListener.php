<?php

namespace App\EventListener;

use App\ApiManager\LeagueApi;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TokenCheckListener
{
    public function __construct(
        private readonly LeagueApi $leagueApi
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $tokenValue = $request->headers->get('Authorization');

        $requestUri = $request->getRequestUri();

        if ($requestUri === '/login') {
            return;
        }

        if (!$this->isValidToken($tokenValue)) {
            $response = new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
            $event->setResponse($response);
        }

        $session = new Session();

        $deserialized = $this->deserialize($tokenValue);

        $session->set('data', $deserialized);

        $this->leagueApi->setServer($deserialized['server']);
        $request->setSession($session);
    }

    private function isValidToken($tokenValue): bool
    {
        if (!str_contains($tokenValue, 'Bearer ')) {
            return false;
        }

        $tokenParts = explode('Bearer ', $tokenValue);

        $tokenData = $this->leagueApi->decodeKey($tokenParts[1]);

        if ($tokenData) {
            return true;
        }

        return false;
    }

    private function deserialize($tokenValue): array
    {
        $tokenParts = explode('Bearer ', $tokenValue);

        return $this->leagueApi->decodeKey($tokenParts[1]);
    }
}
