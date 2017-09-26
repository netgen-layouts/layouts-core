<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Symfony\Component\Intl\Intl;

final class HelpersRuntime
{
    /**
     * @var \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface
     */
    private $localeBundle;

    public function __construct()
    {
        $this->localeBundle = Intl::getLocaleBundle();
    }

    /**
     * Returns the locale name in specified locale.
     *
     * If $displayLocale is specified, name translated in that locale will be returned.
     *
     * @param string $locale
     * @param string $displayLocale
     *
     * @return string|null
     */
    public function getLocaleName($locale, $displayLocale = null)
    {
        $localeBundle = Intl::getLocaleBundle();

        return $localeBundle->getLocaleName($locale, $displayLocale);
    }
}
