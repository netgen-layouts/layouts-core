<?php

namespace Netgen\BlockManager\Tests\Locale\Stubs;

use Netgen\BlockManager\Exception\Locale\LocaleException;
use Netgen\BlockManager\Locale\LocaleContextInterface;

class LocaleContext implements LocaleContextInterface
{
    /**
     * @var array
     */
    protected $localeCodes;

    /**
     * Constructor.
     *
     * @param array $localeCodes
     */
    public function __construct(array $localeCodes = array())
    {
        $this->localeCodes = $localeCodes;
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
        if (empty($this->localeCodes)) {
            throw LocaleException::noLocale();
        }

        return $this->localeCodes;
    }
}
