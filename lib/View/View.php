<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Exception\View\ViewException;
use Symfony\Component\HttpFoundation\Response;

abstract class View implements ViewInterface
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $fallbackContext;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $customParameters = array();

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getFallbackContext()
    {
        return $this->fallbackContext;
    }

    public function setFallbackContext($fallbackContext)
    {
        $this->fallbackContext = $fallbackContext;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getResponse()
    {
        if ($this->response === null) {
            $this->response = new Response();
        }

        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function hasParameter($identifier)
    {
        $parameters = $this->getParameters();

        return isset($parameters[$identifier]);
    }

    public function getParameter($identifier)
    {
        if (!$this->hasParameter($identifier)) {
            throw ViewException::parameterNotFound($identifier, get_class($this));
        }

        $parameters = $this->getParameters();

        return $parameters[$identifier];
    }

    public function getParameters()
    {
        return $this->parameters + $this->customParameters;
    }

    public function addParameter($parameterName, $parameterValue)
    {
        $this->customParameters[$parameterName] = $parameterValue;
    }

    public function addParameters(array $parameters = array())
    {
        $this->customParameters = $parameters + $this->customParameters;
    }
}
