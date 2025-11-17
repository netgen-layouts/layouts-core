<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ApiCsrfValidationListener implements EventSubscriberInterface
{
    public function __construct(
        private CsrfTokenValidatorInterface $csrfTokenValidator,
        private string $csrfTokenId,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => 'onKernelRequest'];
    }

    /**
     * Validates if the current request has a valid token.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException if no valid token exists
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        if ($this->csrfTokenValidator->validateCsrfToken($request, $this->csrfTokenId)) {
            return;
        }

        throw new AccessDeniedHttpException('Missing or invalid CSRF token');
    }
}
