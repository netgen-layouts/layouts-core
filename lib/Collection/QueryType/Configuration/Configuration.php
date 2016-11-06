<?php

namespace Netgen\BlockManager\Collection\QueryType\Configuration;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class Configuration extends ValueObject
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
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Form[]
     */
    protected $forms = array();

    /**
     * @var array
     */
    protected $defaults = array();

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
     * Returns if the query type has a form with provided name.
     *
     * @param $formName
     *
     * @return bool
     */
    public function hasForm($formName)
    {
        return isset($this->forms[$formName]);
    }

    /**
     * Returns the form for provided form name.
     *
     * @param $formName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If query type does not have the form
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    public function getForm($formName)
    {
        if (!$this->hasForm($formName)) {
            throw new InvalidArgumentException(
                'formName',
                sprintf(
                    'Form "%s" does not exist in "%s" query type.',
                    $formName,
                    $this->type
                )
            );
        }

        return $this->forms[$formName];
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
