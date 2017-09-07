<?php

namespace Netgen\BlockManager\Locale;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var string[]
     */
    protected $enabledLocales;

    /**
     * Constructor.
     *
     * @param string[] $enabledLocales
     */
    public function __construct(array $enabledLocales = array())
    {
        $this->enabledLocales = $enabledLocales;
    }

    /**
     * Returns the list of locales available in the system.
     *
     * Keys are locale codes and values are locale names.
     *
     * @return string[]
     */
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

    /**
     * Returns the list of locale codes available for the provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string[]
     */
    public function getRequestLocales(Request $request)
    {
        $requestLocale = $request->getLocale();

        if (empty($this->enabledLocales) || in_array($requestLocale, $this->enabledLocales, true)) {
            return array($requestLocale);
        }

        return array();
    }
}
