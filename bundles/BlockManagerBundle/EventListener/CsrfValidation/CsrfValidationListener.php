<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfValidationListener implements EventSubscriberInterface
{
    const CSRF_TOKEN_HEADER = 'X-CSRF-Token';

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var string
     */
    protected $csrfTokenId;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @param string $csrfTokenId
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager = null, $csrfTokenId = null)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenId = $csrfTokenId;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
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
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if (!$this->csrfTokenManager instanceof CsrfTokenManagerInterface || $this->csrfTokenId === null) {
            return;
        }

        // Skip CSRF validation if no session is available
        if (!$event->getRequest()->getSession()->isStarted()) {
            return;
        }

        if ($this->isMethodSafe($event->getRequest()->getMethod())) {
            return;
        }

        if (!$this->validateCsrfToken($event->getRequest())) {
            throw new AccessDeniedHttpException('Missing or invalid CSRF token');
        }
    }

    /**
     * Returns true if method is considered safe.
     *
     * @param string $method
     *
     * @return bool
     */
    protected function isMethodSafe($method)
    {
        return in_array(
            $method,
            array(
                Request::METHOD_GET,
                Request::METHOD_HEAD,
                Request::METHOD_OPTIONS,
            )
        );
    }

    /**
     * Validates the CSRF token.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    protected function validateCsrfToken(Request $request)
    {
        if (!$request->headers->has(self::CSRF_TOKEN_HEADER)) {
            return false;
        }

        return $this->csrfTokenManager->isTokenValid(
            new CsrfToken(
                $this->csrfTokenId,
                $request->headers->get(self::CSRF_TOKEN_HEADER)
            )
        );
    }
}
