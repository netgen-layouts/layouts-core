<?php

namespace Netgen\BlockManager\Configuration\BlockDefinition;

class BlockDefinition
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
     * @var \Netgen\BlockManager\Configuration\BlockDefinition\ViewType[]
     */
    protected $viewTypes = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $forms
     * @param \Netgen\BlockManager\Configuration\BlockDefinition\ViewType[] $viewTypes
     */
    public function __construct($identifier, array $forms, array $viewTypes)
    {
        $this->identifier = $identifier;
        $this->forms = $forms;
        $this->viewTypes = $viewTypes;
    }

    /**
     * Returns the block definition identifier.
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
     * @return array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Returns if the block definition has a form with provided identifier.
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
     * @return string
     */
    public function getForm($formIdentifier)
    {
        return $this->forms[$formIdentifier];
    }

    /**
     * Returns the block definition view types.
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\ViewType[]
     */
    public function getViewTypes()
    {
        return $this->viewTypes;
    }

    /**
     * Returns if the block definition has a view type with provided identifier.
     *
     * @param $viewTypeIdentifier
     *
     * @return bool
     */
    public function hasViewType($viewTypeIdentifier)
    {
        return isset($this->viewTypes[$viewTypeIdentifier]);
    }

    /**
     * Returns the view type with provided identifier.
     *
     * @param $viewTypeIdentifier
     *
     * @return \Netgen\BlockManager\Configuration\BlockDefinition\ViewType
     */
    public function getViewType($viewTypeIdentifier)
    {
        return $this->viewTypes[$viewTypeIdentifier];
    }
}
