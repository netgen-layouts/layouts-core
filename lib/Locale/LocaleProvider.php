<?php

declare(strict_types=1);

namespace Netgen\Layouts\Locale;

use Netgen\Layouts\Utils\BackwardsCompatibility\Locales;
use Symfony\Component\HttpFoundation\Request;

use function count;
use function in_array;

final class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var string[]
     */
    private array $enabledLocales;

    /**
     * @param string[] $enabledLocales
     */
    public function __construct(array $enabledLocales = [])
    {
        $this->enabledLocales = $enabledLocales;
    }

    public function getAvailableLocales(): array
    {
        $enabledLocales = [];

        if (count($this->enabledLocales) > 0) {
            foreach ($this->enabledLocales as $locale) {
                if (Locales::exists($locale)) {
                    $enabledLocales[$locale] = Locales::getName($locale);
                }
            }

            return $enabledLocales;
        }

        return Locales::getNames();
    }

    public function getRequestLocales(Request $request): array
    {
        $requestLocale = $request->getLocale();

        if (count($this->enabledLocales) === 0 || in_array($requestLocale, $this->enabledLocales, true)) {
            return [$requestLocale];
        }

        return [];
    }
}
