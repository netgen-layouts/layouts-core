<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use function in_array;

final class RequestBodyListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => 'onKernelRequest'];
    }

    /**
     * Extracts the request payload and stores it in the request attributes.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest()) {
            return;
        }

        if (!$request->attributes->getBoolean(SetIsAppRequestListener::APP_API_FLAG_NAME)) {
            return;
        }

        if (!$this->isRequestAcceptable($request)) {
            return;
        }

        try {
            $request->attributes->set('data', $request->getPayload());
        } catch (JsonException $e) {
            throw new BadRequestHttpException('Request body has an invalid format', $e);
        }
    }

    /**
     * Check if we the request is acceptable.
     */
    private function isRequestAcceptable(Request $request): bool
    {
        if (
            !in_array(
                $request->getMethod(),
                [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE],
                true,
            )
        ) {
            return false;
        }

        return $request->getContentTypeFormat() === 'json';
    }
}
