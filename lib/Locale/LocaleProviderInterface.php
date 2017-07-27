<?php

namespace Netgen\BlockManager\Locale;

interface LocaleProviderInterface
{
    /**
     * Returns the list of locales available in the system.
     *
     * Keys are locale codes and values are locale names.
     *
     * @return string[]
     */
    public function getAvailableLocales();
}
