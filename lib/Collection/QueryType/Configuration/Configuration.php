<?php

namespace Netgen\BlockManager\Collection\QueryType\Configuration;

use Netgen\BlockManager\Exception\RuntimeException;

class Configuration
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $forms = array();

    /**
     * @var array
     */
    protected $defaults = array();

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $name
     * @param array $forms
     * @param array $defaults
     */
    public function __construct($type, $name, array $forms = array(), array $defaults = array())
    {
        $this->type = $type;
        $this->name = $name;
        $this->forms = $forms;
        $this->defaults = $defaults;
    }

    /**
     * Returns the query type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Returns if the query type has a form with provided identifier.
     *
     * @param $formIdentifier
     *
     * @return bool
     */
    public function hasForm($formIdentifier)
    {
        return isset($this->forms[$formIdentifier]);
    }

    /**
     * Returns the form for provided form identifier.
     *
     * @param $formIdentifier
     *
     * @throws \RuntimeException If query type does not have the form
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    public function getForm($formIdentifier)
    {
        if (!$this->hasForm($formIdentifier)) {
            throw new RuntimeException(
                "Form '{$formIdentifier}' does not exist in '{$this->type}' query type."
            );
        }

        return $this->forms[$formIdentifier];
    }

    /**
     * Returns the default query values.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns the default query parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return isset($this->defaults['parameters']) ? $this->defaults['parameters'] : array();
    }
}
