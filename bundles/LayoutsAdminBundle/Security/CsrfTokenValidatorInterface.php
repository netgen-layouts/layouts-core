<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Security;

use Symfony\Component\HttpFoundation\Request;

interface CsrfTokenValidatorInterface
{
    public const CSRF_TOKEN_HEADER = 'X-CSRF-Token';

    /**
     * Returns if the provided request has a valid CSRF token with provided ID.
     *
     * Only unsafe requests which have a session should be checked and the token
     * should be stored in self::CSRF_TOKEN_HEADER request header.
     */
    public function validateCsrfToken(Request $request, string $csrfTokenId): bool;
}
