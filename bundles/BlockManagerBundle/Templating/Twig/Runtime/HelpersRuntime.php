<?php

declare(strict_types=1);

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
     */
    public function getLocaleName(string $locale, string $displayLocale = null): ?string
    {
        $localeBundle = Intl::getLocaleBundle();

        return $localeBundle->getLocaleName($locale, $displayLocale);
    }
}
