<?php

namespace Netgen\BlockManager\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class Block extends ValueObject implements APIBlock
{
    use ParameterBasedValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var string
     */
    protected $viewType;

    /**
     * @var string
     */
    protected $itemViewType;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\API\Values\Page\Placeholder[]
     */
    protected $placeholders = array();

    /**
     * @var int
     */
    protected $status;

    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Returns if the block is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType()
    {
        return $this->itemViewType;
    }

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Placeholder[]
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Returns the specified placeholder.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the placeholder does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Placeholder
     */
    public function getPlaceholder($identifier)
    {
        if ($this->hasPlaceholder($identifier)) {
            return $this->placeholders[$identifier];
        }

        throw new InvalidArgumentException(
            'identifier',
            sprintf(
                'Placeholder with "%s" identifier does not exist in the block.',
                $identifier
            )
        );
    }

    /**
     * Returns if blocks has a specified placeholder.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholder($identifier)
    {
        return isset($this->placeholders[$identifier]);
    }

    /**
     * Returns the status of the block.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
