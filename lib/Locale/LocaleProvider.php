<?php

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
     * Constructor.
     *
     * @param string[] $enabledLocales
     */
    public function __construct(array $enabledLocales = array())
    {
        $this->enabledLocales = $enabledLocales;
    }

    public function getAvailableLocales()
    {
        $availableLocales = Intl::getLocaleBundle()->getLocaleNames();

        $enabledLocales = array();

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
            return array($requestLocale);
        }

        return array();
    }
}
