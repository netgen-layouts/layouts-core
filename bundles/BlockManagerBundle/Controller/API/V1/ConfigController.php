<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Configuration\Registry\SourceRegistryInterface;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ConfigController extends Controller
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface
     */
    protected $blockTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\SourceRegistryInterface
     */
    protected $sourceRegistry;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var string
     */
    protected $csrfTokenId;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\Registry\BlockTypeRegistryInterface $blockTypeRegistry
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Netgen\BlockManager\Configuration\Registry\SourceRegistryInterface $sourceRegistry
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @param string $csrfTokenId
     */
    public function __construct(
        BlockTypeRegistryInterface $blockTypeRegistry,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        SourceRegistryInterface $sourceRegistry,
        CsrfTokenManagerInterface $csrfTokenManager = null,
        $csrfTokenId = null
    ) {
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->sourceRegistry = $sourceRegistry;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenId = $csrfTokenId;
    }

    /**
     * Returns the general config.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function getConfig()
    {
        return new ValueList(
            array(
                'csrf_token' => $this->getCsrfToken(),
            )
        );
    }

    /**
     * Serializes the block types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function getBlockTypes()
    {
        $blockTypeGroups = array();
        foreach ($this->blockTypeRegistry->getBlockTypeGroups() as $blockTypeGroup) {
            if ($blockTypeGroup->isEnabled()) {
                $blockTypeGroups[] = new VersionedValue($blockTypeGroup, Version::API_V1);
            }
        }

        $blockTypes = array();
        foreach ($this->blockTypeRegistry->getBlockTypes() as $blockType) {
            if ($blockType->isEnabled()) {
                $blockTypes[] = new VersionedValue($blockType, Version::API_V1);
            }
        }

        return new ValueList(
            array(
                'block_type_groups' => $blockTypeGroups,
                'block_types' => $blockTypes,
            )
        );
    }

    /**
     * Serializes the layout types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function getLayoutTypes()
    {
        $layoutTypes = array();
        foreach ($this->layoutTypeRegistry->getLayoutTypes() as $layoutType) {
            if ($layoutType->isEnabled()) {
                $layoutTypes[] = new VersionedValue($layoutType, Version::API_V1);
            }
        }

        return new ValueList($layoutTypes);
    }

    /**
     * Serializes the collection sources.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function getSources()
    {
        $sources = array();
        foreach ($this->sourceRegistry->getSources() as $source) {
            if ($source->isEnabled()) {
                $sources[] = new VersionedValue($source, Version::API_V1);
            }
        }

        return new ValueList($sources);
    }

    /**
     * Returns the CSRF token.
     *
     * @return string
     */
    protected function getCsrfToken()
    {
        if (
            !$this->csrfTokenManager instanceof CsrfTokenManagerInterface ||
            $this->csrfTokenId === null
        ) {
            return;
        }

        return $this->csrfTokenManager->refreshToken($this->csrfTokenId)->getValue();
    }
}
