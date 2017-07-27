<?php

namespace Netgen\BlockManager\Locale\Context;

use Netgen\BlockManager\Exception\Locale\LocaleException;
use Netgen\BlockManager\Locale\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestLocaleContext implements LocaleContextInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (!$currentRequest instanceof Request) {
            throw LocaleException::noLocale();
        }

        return array($currentRequest->getLocale());
    }
}
