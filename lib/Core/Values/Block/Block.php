<?php

namespace Netgen\BlockManager\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\Exception\Core\TranslationException;
use Netgen\BlockManager\ValueObject;

class Block extends ValueObject implements APIBlock
{
    use ConfigAwareValueTrait;

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
     * @var \Netgen\BlockManager\API\Values\Block\BlockTranslation[]
     */
    protected $translations = array();

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
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
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
     * Returns all parameter values.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    public function getParameters()
    {
        return $this->getTranslation()->getParameters();
    }

    /**
     * Returns the specified parameter value.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameterName)
    {
        return $this->getTranslation()->getParameter($parameterName);
    }

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return $this->getTranslation()->hasParameter($parameterName);
    }

    /**
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
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
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the placeholder does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder($identifier)
    {
        if ($this->hasPlaceholder($identifier)) {
            return $this->placeholders[$identifier];
        }

        throw BlockException::noPlaceholder($identifier);
    }

    /**
     * Returns if block has a specified placeholder.
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
     * Returns all collection references from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    public function getCollectionReferences()
    {
        return $this->collectionReferences;
    }

    /**
     * Returns the specified collection reference.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the collection reference does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
     */
    public function getCollectionReference($identifier)
    {
        if ($this->hasCollectionReference($identifier)) {
            return $this->collectionReferences[$identifier];
        }

        throw BlockException::noCollection($identifier);
    }

    /**
     * Returns if block has a specified collection reference.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCollectionReference($identifier)
    {
        return isset($this->collectionReferences[$identifier]);
    }

    /**
     * Returns the status of the value.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getDynamicParameter($parameter)
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetGet($parameter);
    }

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasDynamicParameter($parameter)
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetExists($parameter);
    }

    /**
     * Returns if the block is dependent on a context, i.e. current request.
     *
     * @return bool
     */
    public function isContextual()
    {
        return $this->definition->isContextual($this);
    }

    /**
     * Returns the list of all available locales in the block.
     *
     * @return string[]
     */
    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    /**
     * Returns the main locale for the block.
     *
     * @return string
     */
    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    /**
     * Returns if the block is translatable.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    /**
     * Returns if the main translation of the block is used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    /**
     * Returns if the block has a translation in specified locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasTranslation($locale)
    {
        return array_key_exists($locale, $this->translations);
    }

    /**
     * Returns a block translation in specified locale.
     *
     * If locale is not specified, first locale in the list of available locales is used.
     *
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\Core\TranslationException If the requested translation does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockTranslation
     */
    public function getTranslation($locale = null)
    {
        if ($locale === null) {
            return $this->translations[$this->availableLocales[0]];
        }

        if (!$this->hasTranslation($locale)) {
            throw TranslationException::noTranslation($locale);
        }

        return $this->translations[$locale];
    }

    /**
     * Returns all block translations.
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Builds the dynamic parameters of the block from the definition.
     */
    protected function buildDynamicParameters()
    {
        if ($this->dynamicParameters === null) {
            $this->dynamicParameters = $this->definition->getDynamicParameters($this);
        }
    }
}
