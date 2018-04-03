<?php

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\API\Values\Block\CollectionReference as APICollectionReference;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Exception\Core\BlockException;

final class Block extends Value implements APIBlock
{
    use ConfigAwareValueTrait;
    use ParameterBasedValueTrait;

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
     * @var int
     */
    protected $parentPosition;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    protected $placeholders = array();

    /**
     * @var \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    protected $collectionReferences = array();

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

    public function getParentPosition()
    {
        return $this->parentPosition;
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

    public function getCollections()
    {
        return array_map(
            function (APICollectionReference $collectionReference) {
                return $collectionReference->getCollection();
            },
            $this->collectionReferences
        );
    }

    public function getCollection($identifier)
    {
        if ($this->hasCollection($identifier)) {
            return $this->collectionReferences[$identifier]->getCollection();
        }

        throw BlockException::noCollection($identifier);
    }

    public function hasCollection($identifier)
    {
        return isset($this->collectionReferences[$identifier]);
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
