<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\Locales as SymfonyLocales;

use function class_exists;

final class Locales
{
    /**
     * @return string[]
     */
    public static function getLocales(): array
    {
        if (class_exists(SymfonyLocales::class)) {
            return SymfonyLocales::getLocales();
        }

        return Intl::getLocaleBundle()->getLocales();
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        if (class_exists(SymfonyLocales::class)) {
            return SymfonyLocales::getAliases();
        }

        return Intl::getLocaleBundle()->getAliases();
    }

    public static function exists(string $locale): bool
    {
        if (class_exists(SymfonyLocales::class)) {
            return SymfonyLocales::exists($locale);
        }

        $locales = Intl::getLocaleBundle()->getLocaleNames();

        return isset($locales[$locale]);
    }

    public static function getName(string $locale, ?string $displayLocale = null): string
    {
        if (class_exists(SymfonyLocales::class)) {
            return SymfonyLocales::getName($locale, $displayLocale);
        }

        return Intl::getLocaleBundle()->getLocaleName($locale, $displayLocale) ?? $locale;
    }

    /**
     * @return string[]
     */
    public static function getNames(?string $displayLocale = null): array
    {
        if (class_exists(SymfonyLocales::class)) {
            return SymfonyLocales::getNames($displayLocale);
        }

        return Intl::getLocaleBundle()->getLocaleNames($displayLocale);
    }
}
