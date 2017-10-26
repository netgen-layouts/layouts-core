<?php

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\ParameterAwareValueTrait;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\ValueObject;

final class Block extends ValueObject implements APIBlock
{
    use ConfigAwareValueTrait;
    use ParameterAwareValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $layoutId;

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
     * @var \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    protected $placeholders = array();

    /**
     * @var \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    protected $collectionReferences = array();

    /**
     * @var int
     */
    protected $status;

    /**
     * @var \Netgen\BlockManager\Block\DynamicParameters
     */
    protected $dynamicParameters;

    /**
     * @var string[]
     */
    protected $availableLocales = array();

    /**
     * @var string
     */
    protected $mainLocale;

    /**
     * @var bool
     */
    protected $isTranslatable;

    /**
     * @var bool
     */
    protected $alwaysAvailable;

    /**
     * @var string
     */
    protected $locale;

    public function getId()
    {
        return $this->id;
    }

    public function getLayoutId()
    {
        return $this->layoutId;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function getViewType()
    {
        return $this->viewType;
    }

    public function getItemViewType()
    {
        return $this->itemViewType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    public function getPlaceholder($identifier)
    {
        if ($this->hasPlaceholder($identifier)) {
            return $this->placeholders[$identifier];
        }

        throw BlockException::noPlaceholder($identifier);
    }

    public function hasPlaceholder($identifier)
    {
        return isset($this->placeholders[$identifier]);
    }

    public function getCollectionReferences()
    {
        return $this->collectionReferences;
    }

    public function getCollectionReference($identifier)
    {
        if ($this->hasCollectionReference($identifier)) {
            return $this->collectionReferences[$identifier];
        }

        throw BlockException::noCollection($identifier);
    }

    public function hasCollectionReference($identifier)
    {
        return isset($this->collectionReferences[$identifier]);
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDynamicParameter($parameter)
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetGet($parameter);
    }

    public function hasDynamicParameter($parameter)
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetExists($parameter);
    }

    public function isContextual()
    {
        return $this->definition->isContextual($this);
    }

    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    public function isAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Builds the dynamic parameters of the block from the block definition.
     */
    private function buildDynamicParameters()
    {
        if ($this->dynamicParameters === null) {
            $this->dynamicParameters = $this->definition->getDynamicParameters($this);
        }
    }
}
