<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Utils\FlagGenerator;
use Symfony\Component\Intl\Intl;
use Throwable;

final class HelpersRuntime
{
    /**
     * @var \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface
     */
    private $localeBundle;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->localeBundle = Intl::getLocaleBundle();
        $this->layoutService = $layoutService;
    }

    /**
     * Returns the locale name in specified locale.
     *
     * If $displayLocale is specified, name translated in that locale will be returned.
     */
    public function getLocaleName(string $locale, ?string $displayLocale = null): ?string
    {
        return $this->localeBundle->getLocaleName($locale, $displayLocale);
    }

    /**
     * Returns the layout name for specified layout ID.
     *
     * @param int|string $layoutId
     */
    public function getLayoutName($layoutId): string
    {
        try {
            $layout = $this->layoutService->loadLayout($layoutId);

            return $layout->getName();
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Returns the country flag as an emoji string for provided country code.
     *
     * If the flag cannot be generated, the country code is returned as is.
     *
     * @param string $countryCode
     */
    public function getCountryFlag(string $countryCode): string
    {
        try {
            return FlagGenerator::fromCountryCode($countryCode);
        } catch (Throwable $t) {
            return $countryCode;
        }
    }
}
