<?php

namespace Netgen\BlockManager\Configuration\QueryType;

class QueryType
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $forms = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $forms
     */
    public function __construct($identifier, array $forms)
    {
        $this->identifier = $identifier;
        $this->forms = $forms;
    }

    /**
     * Returns the query type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Configuration\QueryType\Form[]
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
     * @return \Netgen\BlockManager\Configuration\QueryType\Form
     */
    public function getForm($formIdentifier)
    {
        return $this->forms[$formIdentifier];
    }
}
