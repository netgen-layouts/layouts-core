<?php

namespace Netgen\BlockManager\Locale\Context;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Locale\LocaleException;
use Netgen\BlockManager\Locale\LocaleContextInterface;

class ChainedLocaleContext implements LocaleContextInterface
{
    /**
     * @var \Netgen\BlockManager\Locale\LocaleContextInterface[]
     */
    protected $localeContexts = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Locale\LocaleContextInterface[] $localeContexts
     */
    public function __construct(array $localeContexts = array())
    {
        foreach ($localeContexts as $localeContext) {
            if (!$localeContext instanceof LocaleContextInterface) {
                throw new InvalidInterfaceException(
                    'Locale context',
                    get_class($localeContext),
                    LocaleContextInterface::class
                );
            }
        }

        $this->localeContexts = $localeContexts;
    }

    /**
     * Returns the currently available locale codes.
     *
     * @throws \Netgen\BlockManager\Exception\Locale\LocaleException If no locales were found
     *
     * @return string[]
     */
    public function getLocaleCodes()
    {
        $localeCodes = null;

        foreach ($this->localeContexts as $localeContext) {
            try {
                $localeCodes = $localeContext->getLocaleCodes();
            } catch (LocaleException $e) {
                // Do nothing
                continue;
            }

            return $localeCodes;
        }

        throw LocaleException::noLocale();
    }
}
