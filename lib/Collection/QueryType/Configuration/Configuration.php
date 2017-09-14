<?php

namespace Netgen\BlockManager\Collection\QueryType\Configuration;

use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\ValueObject;

/**
 * This class and the corresponding namespace are the object representation
 * of the query type configuration, which includes basic query type
 * information (name, identifier) as well as the list of forms available to edit
 * the query.
 */
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
     * @throws \Netgen\BlockManager\Exception\Collection\QueryTypeException If query type does not have the form
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    public function getForm($formName)
    {
        if (!$this->hasForm($formName)) {
            throw QueryTypeException::noForm($this->type, $formName);
        }

        return $this->forms[$formName];
    }
}
