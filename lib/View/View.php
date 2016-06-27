<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\HttpFoundation\Response;
use OutOfBoundsException;

abstract class View implements ViewInterface
{
    /**
     * @var mixed
     */
    protected $valueObject;

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
    protected $internalParameters = array();

    /**
     * Returns the value object in this view.
     *
     * @return mixed
     */
    public function getValueObject()
    {
        return $this->valueObject;
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
     * @throws \OutOfBoundsException If view does not have the parameter.
     *
     * @return mixed
     */
    public function getParameter($identifier)
    {
        if (!$this->hasParameter($identifier)) {
            throw new OutOfBoundsException("View does not have the '{$identifier}' parameter.");
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
        return $this->internalParameters + $this->parameters;
    }

    /**
     * Sets the view parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameters to the view.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters = array())
    {
        $this->parameters = $parameters + $this->parameters;
    }
}
