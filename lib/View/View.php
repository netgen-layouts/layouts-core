<?php

namespace Netgen\BlockManager\View;

use RuntimeException;

abstract class View implements ViewInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Value
     */
    protected $value;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $internalParameters = array();

    /**
     * Returns the value in this view.
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function getValue()
    {
        return $this->value;
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
     * Returns the view parameter by identifier or null if parameter does not exist.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If view does not have the parameter.
     *
     * @return mixed
     */
    public function getParameter($identifier)
    {
        if (!$this->hasParameter($identifier)) {
            throw new RuntimeException("View does not have the '{$identifier}' parameter.");
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
