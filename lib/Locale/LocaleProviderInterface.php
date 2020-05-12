<?php

declare(strict_types=1);

namespace Netgen\Layouts\Locale;

use Symfony\Component\HttpFoundation\Request;

interface LocaleProviderInterface
{
    /**
     * Returns the list of locales available in the system.
     *
     * Keys are locale codes and values are locale names.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array;

    /**
     * Returns the list of locale codes available for the provided request.
     *
     * @return string[]
     */
    public function getRequestLocales(Request $request): array;
}
