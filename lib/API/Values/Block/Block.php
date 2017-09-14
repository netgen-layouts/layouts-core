<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Block extends Value, ParameterBasedValue, ConfigAwareValue
{
    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition();

    /**
     * Returns if the block is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType();

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType();

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    public function getPlaceholders();

    /**
     * Returns the specified placeholder.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the placeholder does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder($identifier);

    /**
     * Returns if block has a specified placeholder.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholder($identifier);

    /**
     * Returns all collection references from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference[]
     */
    public function getCollectionReferences();

    /**
     * Returns the specified collection reference.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the collection reference does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\CollectionReference
     */
    public function getCollectionReference($identifier);

    /**
     * Returns if block has a specified collection reference.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCollectionReference($identifier);

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getDynamicParameter($parameter);

    /**
     * Returns if the object has a specified dynamic parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasDynamicParameter($parameter);

    /**
     * Returns if the block is dependent on a context, i.e. current request.
     *
     * @return bool
     */
    public function isContextual();

    /**
     * Returns the list of all available locales in the block.
     *
     * @return string[]
     */
    public function getAvailableLocales();

    /**
     * Returns the main locale for the block.
     *
     * @return string
     */
    public function getMainLocale();

    /**
     * Returns if the block is translatable.
     *
     * @return bool
     */
    public function isTranslatable();

    /**
     * Returns if the main translation of the block is used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable();

    /**
     * Returns if the block has a translation in specified locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasTranslation($locale);

    /**
     * Returns a block translation in specified locale.
     *
     * If locale is not specified, first locale in the list of available locales is returned.
     *
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\Core\TranslationException If the requested translation does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockTranslation
     */
    public function getTranslation($locale = null);

    /**
     * Returns all block translations.
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockTranslation[]
     */
    public function getTranslations();
}
