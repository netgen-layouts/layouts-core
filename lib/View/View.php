<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Exception\InvalidArgumentException;
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

    /**
     * Returns the view context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Returns the view fallback context.
     *
     * @return string|null
     */
    public function getFallbackContext()
    {
        return null;
    }

    /**
     * Sets the view context.
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Returns the view template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the view template.
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Returns the response which will be used to render the view.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        if ($this->response === null) {
            $this->response = new Response();
        }

        return $this->response;
    }

    /**
     * Sets the response which will be used to render the view.
     *
     * @param \Symfony\Component\HttpFoundation\Response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns if the view has a parameter.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasParameter($identifier)
    {
        $parameters = $this->getParameters();

        return isset($parameters[$identifier]);
    }

    /**
     * Returns the view parameter by identifier.
     *
     * @param string $identifier
     *
     * @throws \OutOfBoundsException If view does not have the parameter
     *
     * @return mixed
     */
    public function getParameter($identifier)
    {
        if (!$this->hasParameter($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'View does not have the "%s" parameter.',
                    $identifier
                )
            );
        }

        $parameters = $this->getParameters();

        return $parameters[$identifier];
    }

    /**
     * Returns the view parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters + $this->customParameters;
    }

    /**
     * Adds a parameter to the view.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function addParameter($parameterName, $parameterValue)
    {
        $this->customParameters[$parameterName] = $parameterValue;
    }

    /**
     * Adds parameters to the view.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters = array())
    {
        $this->customParameters = $parameters + $this->customParameters;
    }
}
