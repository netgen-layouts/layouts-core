<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Exception\RuntimeException;

class Configuration
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
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    protected $viewTypes = array();

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $forms
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[] $viewTypes
     */
    public function __construct($identifier, array $forms = array(), array $viewTypes = array())
    {
        $this->identifier = $identifier;
        $this->forms = $forms;
        $this->viewTypes = $viewTypes;
    }

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Returns if the block definition has a form with provided identifier.
     *
     * @param string $formIdentifier
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
     * @param string $formIdentifier
     *
     * @throws \RuntimeException If form does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    public function getForm($formIdentifier)
    {
        if (!$this->hasForm($formIdentifier)) {
            throw new RuntimeException(
                "Form '{$formIdentifier}' does not exist in '{$this->identifier}' block definition."
            );
        }

        return $this->forms[$formIdentifier];
    }

    /**
     * Returns the block definition view types.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    public function getViewTypes()
    {
        return $this->viewTypes;
    }

    /**
     * Returns the block definition view type identifiers.
     *
     * @return string[]
     */
    public function getViewTypeIdentifiers()
    {
        return array_keys($this->viewTypes);
    }

    /**
     * Returns if the block definition has a view type with provided identifier.
     *
     * @param string $viewTypeIdentifier
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
     * @param string $viewTypeIdentifier
     *
     * @throws \RuntimeException If view type does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    public function getViewType($viewTypeIdentifier)
    {
        if (!$this->hasViewType($viewTypeIdentifier)) {
            throw new RuntimeException(
                sprintf(
                    "View type '%s' does not exist in '%s' block definition.",
                    $viewTypeIdentifier,
                    $this->identifier
                )
            );
        }

        return $this->viewTypes[$viewTypeIdentifier];
    }
}
