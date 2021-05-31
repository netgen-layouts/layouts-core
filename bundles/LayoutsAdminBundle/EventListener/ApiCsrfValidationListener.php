<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidatorInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiCsrfValidationListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    private CsrfTokenValidatorInterface $csrfTokenValidator;

    private string $csrfTokenId;

    public function __construct(CsrfTokenValidatorInterface $csrfTokenValidator, string $csrfTokenId)
    {
        $this->csrfTokenValidator = $csrfTokenValidator;
        $this->csrfTokenId = $csrfTokenId;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Validates if the current request has a valid token.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException if no valid token exists
     */
    public function onKernelRequest($event): void
    {
        if (!$this->isMainRequest($event)) {
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
