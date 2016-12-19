<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class Configuration extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form[]
     */
    protected $forms = array();

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form[]
     */
    protected $placeholderForms = array();

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType[]
     */
    protected $viewTypes = array();

    /**
     * Returns the container definition human readable name.
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
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Returns if the container definition has a form with provided name.
     *
     * @param string $formName
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
     * @param string $formName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If form does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form
     */
    public function getForm($formName)
    {
        if (!$this->hasForm($formName)) {
            throw new InvalidArgumentException(
                'formName',
                sprintf(
                    'Form "%s" does not exist in "%s" container definition.',
                    $formName,
                    $this->identifier
                )
            );
        }

        return $this->forms[$formName];
    }

    /**
     * Returns all placeholder forms.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form[]
     */
    public function getPlaceholderForms()
    {
        return $this->placeholderForms;
    }

    /**
     * Returns if the container definition has a placeholder form with provided name.
     *
     * @param string $formName
     *
     * @return bool
     */
    public function hasPlaceholderForm($formName)
    {
        return isset($this->placeholderForms[$formName]);
    }

    /**
     * Returns the form for provided form name.
     *
     * @param string $formName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If form does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form
     */
    public function getPlaceholderForm($formName)
    {
        if (!$this->hasPlaceholderForm($formName)) {
            throw new InvalidArgumentException(
                'formName',
                sprintf(
                    'Placeholder form "%s" does not exist in "%s" container definition.',
                    $formName,
                    $this->identifier
                )
            );
        }

        return $this->placeholderForms[$formName];
    }

    /**
     * Returns the container definition view types.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType[]
     */
    public function getViewTypes()
    {
        return $this->viewTypes;
    }

    /**
     * Returns the container definition view type identifiers.
     *
     * @return string[]
     */
    public function getViewTypeIdentifiers()
    {
        return array_keys($this->viewTypes);
    }

    /**
     * Returns if the container definition has a view type with provided identifier.
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If view type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType
     */
    public function getViewType($viewTypeIdentifier)
    {
        if (!$this->hasViewType($viewTypeIdentifier)) {
            throw new InvalidArgumentException(
                'viewTypeIdentifier',
                sprintf(
                    'View type "%s" does not exist in "%s" container definition.',
                    $viewTypeIdentifier,
                    $this->identifier
                )
            );
        }

        return $this->viewTypes[$viewTypeIdentifier];
    }
}
