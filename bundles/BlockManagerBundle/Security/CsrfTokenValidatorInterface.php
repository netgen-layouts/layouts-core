<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Security;

use Symfony\Component\HttpFoundation\Request;

interface CsrfTokenValidatorInterface
{
    /**
     * Returns if the provided request validates against the provided CSRF token ID.
     */
    public function validateCsrfToken(Request $request, string $csrfTokenId): bool;
}
