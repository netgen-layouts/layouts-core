<?php

namespace Netgen\BlockManager\Collection\QueryType\Configuration;

use RuntimeException;

class Configuration
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $forms = array();

    /**
     * Constructor.
     *
     * @param string $type
     * @param array $forms
     */
    public function __construct($type, array $forms)
    {
        $this->type = $type;
        $this->forms = $forms;
    }

    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
}
