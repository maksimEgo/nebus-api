<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ApiTokenListener
{
    public function __construct(
        private readonly string $apiKey
    ) { }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/organization')) {
            return;
        }

        $token = $request->headers->get('X-API-KEY');

        if ($token !== $this->apiKey) {
            $event->setResponse(new JsonResponse(['error' => 'Invalid API Key'], 403));
        }
    }
}