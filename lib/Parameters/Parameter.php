<?php

namespace Netgen\BlockManager\Parameters;

class Parameter implements ParameterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $type;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     *
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     */
    public function __construct($name, ParameterTypeInterface $type, array $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return isset($this->options['required']) ? $this->options['required'] : false;
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return isset($this->options['default_value']) ? $this->options['default_value'] : null;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return isset($this->options['groups']) ? $this->options['groups'] : array();
    }
}
