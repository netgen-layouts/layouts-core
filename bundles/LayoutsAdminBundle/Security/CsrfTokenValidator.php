<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CsrfTokenValidator implements CsrfTokenValidatorInterface
{
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function validateCsrfToken(Request $request, string $csrfTokenId): bool
    {
        // Skip CSRF validation if no session is available
        if (!$request->hasSession() || !$request->getSession()->isStarted()) {
            return true;
        }

        if (Kernel::VERSION_ID >= 40400 ? $request->isMethodSafe() : $request->isMethodSafe(false)) {
            return true;
        }

        if ($request->attributes->get('_nglayouts_no_csrf', false) === true) {
            return true;
        }

        if (!$request->headers->has(self::CSRF_TOKEN_HEADER)) {
            return false;
        }

        $token = $request->headers->get(self::CSRF_TOKEN_HEADER) ?? '';

        return $this->csrfTokenManager->isTokenValid(new CsrfToken($csrfTokenId, $token));
    }
}
