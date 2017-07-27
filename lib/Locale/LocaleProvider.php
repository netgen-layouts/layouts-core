<?php

namespace Netgen\BlockManager\Locale;

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
}
