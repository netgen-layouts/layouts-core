<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Locale;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

final class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var string[]
     */
    private $enabledLocales;

    /**
     * @param string[] $enabledLocales
     */
    public function __construct(array $enabledLocales = [])
    {
        $this->enabledLocales = $enabledLocales;
    }

    public function getAvailableLocales()
    {
        $availableLocales = Intl::getLocaleBundle()->getLocaleNames();

        $enabledLocales = [];

        if (!empty($this->enabledLocales)) {
            foreach ($this->enabledLocales as $locale) {
                if (isset($availableLocales[$locale])) {
                    $enabledLocales[$locale] = $availableLocales[$locale];
                }
            }

            return $enabledLocales;
        }

        return $availableLocales;
    }

    public function getRequestLocales(Request $request)
    {
        $requestLocale = $request->getLocale();

        if (empty($this->enabledLocales) || in_array($requestLocale, $this->enabledLocales, true)) {
            return [$requestLocale];
        }

        return [];
    }
}
