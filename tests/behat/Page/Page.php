<?php

namespace Netgen\BlockManager\Behat\Page;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use Exception;
use InvalidArgumentException;
use Netgen\BlockManager\Behat\Exception\PageException;

abstract class Page
{
    /**
     * @var \Behat\Mink\Session
     */
    private $session;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var \Behat\Mink\Element\DocumentElement|null
     */
    private $document;

    public function __construct(Session $session, array $parameters = [])
    {
        $this->session = $session;
        $this->parameters = $parameters;
    }

    public function open(array $urlParameters = [])
    {
        $this->tryToOpen($urlParameters);
        $this->verify($urlParameters);
    }

    /**
     * @param array $urlParameters
     *
     * @return bool
     */
    public function isOpen(array $urlParameters = [])
    {
        try {
            $this->verify($urlParameters);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function tryToOpen(array $urlParameters = [])
    {
        $this->getSession()->visit($this->getUrl($urlParameters));
    }

    public function verify(array $urlParameters = [])
    {
        $this->verifyStatusCode();
        $this->verifyUrl($urlParameters);
    }

    public function verifyFragment($fragment)
    {
        $url = $this->getDriver()->getCurrentUrl();
        $urlFragment = parse_url($url)['fragment'];

        if (mb_strpos($urlFragment, $fragment) !== false) {
            return;
        }

        throw new PageException(sprintf('Expected to have "%s" fragment but found "%s" instead', $fragment, $urlFragment));
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    abstract protected function getUrl(array $urlParameters = []);

    /**
     * Overload to verify if the current url matches the expected one. Throw an exception otherwise.
     *
     * @param array $urlParameters
     *
     * @throws \Netgen\BlockManager\Behat\Exception\PageException
     */
    protected function verifyUrl(array $urlParameters = [])
    {
        if ($this->getSession()->getCurrentUrl() !== $this->getUrl($urlParameters)) {
            throw new PageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    /**
     * @throws \Netgen\BlockManager\Behat\Exception\PageException
     */
    protected function verifyStatusCode()
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
     * @param string $name
     *
     * @return \Behat\Mink\Element\NodeElement|null
     */
    protected function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Defines elements by returning an array with items being:
     *  - :elementName => :cssLocator
     *  - :elementName => [:selectorType => :locator].
     *
     * @return array
     */
    protected function getDefinedElements()
    {
        return [];
    }

    /**
     * @param string $name
     * @param array $parameters
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     *
     * @return \Behat\Mink\Element\NodeElement
     */
    protected function getElement($name, array $parameters = [])
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

    /**
     * @param string $name
     * @param array $parameters
     *
     * @return bool
     */
    protected function hasElement($name, array $parameters = [])
    {
        return $this->getDocument()->has('xpath', $this->createElement($name, $parameters)->getXpath());
    }

    /**
     * @param int $timeout
     * @param string $elementName
     * @param array $parameters
     * @param bool $waitForRemoval
     */
    protected function waitForElement($timeout, $elementName, $parameters = [], $waitForRemoval = false)
    {
        $this->getDocument()->waitFor(
            $timeout,
            function () use ($elementName, $parameters, $waitForRemoval) {
                $hasElement = $this->hasElement($elementName, $parameters);

                return $waitForRemoval ? !$hasElement : $hasElement;
            }
        );
    }

    /**
     * @return \Behat\Mink\Session
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * @return \Behat\Mink\Driver\DriverInterface
     */
    protected function getDriver()
    {
        return $this->session->getDriver();
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    protected function getDocument()
    {
        if (null === $this->document) {
            $this->document = new DocumentElement($this->session);
        }

        return $this->document;
    }

    /**
     * @param string $name
     * @param array $parameters
     *
     * @return \Behat\Mink\Element\NodeElement
     */
    private function createElement($name, array $parameters = [])
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
    private function getSelectorAsXpath($selector, SelectorsHandler $selectorsHandler)
    {
        $selectorType = is_array($selector) ? (string) key($selector) : 'css';
        $locator = is_array($selector) ? $selector[$selectorType] : $selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }

    /**
     * @param string $name
     * @param array $parameters
     * @param array $definedElements
     *
     * @return string|array
     */
    private function resolveParameters($name, array $parameters, array $definedElements)
    {
        if (!is_array($definedElements[$name])) {
            return strtr($definedElements[$name], $parameters);
        }

        array_map(
            function ($definedElement) use ($parameters) {
                return strtr($definedElement, $parameters);
            }, $definedElements[$name]
        );

        return $definedElements[$name];
    }
}
