<?php

namespace Netgen\BlockManager\Locale;

interface LocaleContextInterface
{
    /**
     * Returns the currently available locale codes.
     *
     * @throws \Netgen\BlockManager\Exception\Locale\LocaleException If no locales were found
     *
     * @return string[]
     */
    public function getLocaleCodes();
}
