<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Page;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use InvalidArgumentException;
use Netgen\BlockManager\Behat\Exception\PageException;
use Throwable;

abstract class Page
{
    /**
     * @var \Behat\Mink\Session
     */
    private $session;

    /**
     * @var array|\ArrayAccess
     */
    private $minkParameters;

    /**
     * @var \Behat\Mink\Element\DocumentElement|null
     */
    private $document;

    /**
     * @param \Behat\Mink\Session $session
     * @param array|\ArrayAccess $minkParameters
     */
    public function __construct(Session $session, $minkParameters = [])
    {
        $this->session = $session;
        $this->minkParameters = $minkParameters;
    }

    public function open(array $urlParameters = []): void
    {
        $this->tryToOpen($urlParameters);
        $this->verify($urlParameters);
    }

    public function isOpen(array $urlParameters = []): bool
    {
        try {
            $this->verify($urlParameters);
        } catch (Throwable $t) {
            return false;
        }

        return true;
    }

    public function tryToOpen(array $urlParameters = []): void
    {
        $this->getSession()->visit($this->getUrl($urlParameters));
    }

    public function verify(array $urlParameters = []): void
    {
        $this->verifyStatusCode();
        $this->verifyUrl($urlParameters);
    }

    public function verifyFragment(string $fragment): void
    {
        $parsedUrl = parse_url($this->getDriver()->getCurrentUrl());

        if (!is_array($parsedUrl) || !array_key_exists('fragment', $parsedUrl)) {
            throw new PageException(sprintf('%s URL is not valid or does not contain a fragment', $this->getDriver()->getCurrentUrl()));
        }

        if (mb_strpos($parsedUrl['fragment'], $fragment) !== false) {
            return;
        }

        throw new PageException(sprintf('Expected to have "%s" fragment but found "%s" instead', $fragment, $parsedUrl['fragment']));
    }

    abstract protected function getUrl(array $urlParameters = []): string;

    /**
     * Overload to verify if the current url matches the expected one. Throw an exception otherwise.
     *
     * @throws \Netgen\BlockManager\Behat\Exception\PageException
     */
    protected function verifyUrl(array $urlParameters = []): void
    {
        if ($this->getSession()->getCurrentUrl() !== $this->getUrl($urlParameters)) {
            throw new PageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    /**
     * @throws \Netgen\BlockManager\Behat\Exception\PageException
     */
    protected function verifyStatusCode(): void
    {
        try {
            $statusCode = $this->getSession()->getStatusCode();
        } catch (DriverException $exception) {
            return; // Ignore drivers which cannot check the response status code
        }

        if ($statusCode >= 200 && $statusCode <= 299) {
            return;
        }

        $currentUrl = $this->getSession()->getCurrentUrl();
        $message = sprintf('Could not open the page: "%s". Received an error status code: %s', $currentUrl, $statusCode);

        throw new PageException($message);
    }

    /**
     * @return mixed
     */
    protected function getParameter(string $name)
    {
        return $this->minkParameters[$name] ?? null;
    }

    /**
     * Defines elements by returning an array with items being:
     *  - :elementName => :cssLocator
     *  - :elementName => [:selectorType => :locator].
     */
    protected function getDefinedElements(): array
    {
        return [];
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        $element = $this->createElement($name, $parameters);

        if (!$this->getDocument()->has('xpath', $element->getXpath())) {
            throw new ElementNotFoundException(
                $this->getSession(),
                sprintf('Element named "%s" with parameters %s', $name, implode(', ', $parameters)),
                'xpath',
                $element->getXpath()
            );
        }

        return $element;
    }

    protected function hasElement(string $name, array $parameters = []): bool
    {
        return $this->getDocument()->has('xpath', $this->createElement($name, $parameters)->getXpath());
    }

    protected function waitForElement(int $timeout, string $elementName, array $parameters = [], bool $waitForRemoval = false): void
    {
        $this->getDocument()->waitFor(
            $timeout,
            function () use ($elementName, $parameters, $waitForRemoval): bool {
                $hasElement = $this->hasElement($elementName, $parameters);

                return $waitForRemoval ? !$hasElement : $hasElement;
            }
        );
    }

    protected function getSession(): Session
    {
        return $this->session;
    }

    protected function getDriver(): DriverInterface
    {
        return $this->session->getDriver();
    }

    protected function getDocument(): DocumentElement
    {
        if (null === $this->document) {
            $this->document = new DocumentElement($this->session);
        }

        return $this->document;
    }

    private function createElement(string $name, array $parameters = []): NodeElement
    {
        $definedElements = $this->getDefinedElements();

        if (!isset($definedElements[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Could not find a defined element with name "%s". The defined ones are: %s.',
                $name,
                implode(', ', array_keys($definedElements))
            ));
        }

        $elementSelector = $this->resolveParameters($name, $parameters, $definedElements);

        return new NodeElement(
            $this->getSelectorAsXpath($elementSelector, $this->session->getSelectorsHandler()),
            $this->session
        );
    }

    /**
     * @param string|array $selector
     * @param \Behat\Mink\Selector\SelectorsHandler $selectorsHandler
     *
     * @return string
     */
    private function getSelectorAsXpath($selector, SelectorsHandler $selectorsHandler): string
    {
        $selectorType = is_array($selector) ? (string) key($selector) : 'css';
        $locator = is_array($selector) ? $selector[$selectorType] : $selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }

    /**
     * @return string|array
     */
    private function resolveParameters(string $name, array $parameters, array $definedElements)
    {
        if (!is_array($definedElements[$name])) {
            return strtr($definedElements[$name], $parameters);
        }

        array_map(
            function (string $definedElement) use ($parameters): string {
                return strtr($definedElement, $parameters);
            }, $definedElements[$name]
        );

        return $definedElements[$name];
    }
}
