<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfValidationListener implements EventSubscriberInterface
{
    private static $csrfTokenHeader = 'X-CSRF-Token';

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var string
     */
    private $csrfTokenId;

    /**
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @param string $csrfTokenId
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, $csrfTokenId)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenId = $csrfTokenId;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * This method validates CSRF token if CSRF protection is enabled.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException If token is invalid
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Skip CSRF validation if no session is available
        if (!$request->hasSession() || !$request->getSession()->isStarted()) {
            return;
        }

        if ($request->isMethodSafe(false)) {
            return;
        }

        if (!$this->validateCsrfToken($request)) {
            throw new AccessDeniedHttpException('Missing or invalid CSRF token');
        }
    }

    /**
     * Validates the CSRF token.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    private function validateCsrfToken(Request $request)
    {
        if (!$request->headers->has(self::$csrfTokenHeader)) {
            return false;
        }

        return $this->csrfTokenManager->isTokenValid(
            new CsrfToken(
                $this->csrfTokenId,
                $request->headers->get(self::$csrfTokenHeader)
            )
        );
    }
}
