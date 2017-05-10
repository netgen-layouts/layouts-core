<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface;
use Netgen\BlockManager\Collection\Registry\SourceRegistryInterface;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ConfigController extends Controller
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface
     */
    protected $blockTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\SourceRegistryInterface
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
     * @param \Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface $blockTypeRegistry
     * @param \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Netgen\BlockManager\Collection\Registry\SourceRegistryInterface $sourceRegistry
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
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function getConfig()
    {
        return new Value(
            array(
                'csrf_token' => $this->getCsrfToken(),
            )
        );
    }

    /**
     * Serializes the block types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function getBlockTypes()
    {
        $blockTypeGroups = array();
        foreach ($this->blockTypeRegistry->getBlockTypeGroups() as $blockTypeGroup) {
            if (!empty($blockTypeGroup->getBlockTypes())) {
                $blockTypeGroups[] = new VersionedValue($blockTypeGroup, Version::API_V1);
            }
        }

        $blockTypes = array();
        foreach ($this->blockTypeRegistry->getBlockTypes() as $blockType) {
            $blockTypes[] = new VersionedValue($blockType, Version::API_V1);
        }

        return new Value(
            array(
                'block_type_groups' => $blockTypeGroups,
                'block_types' => $blockTypes,
            )
        );
    }

    /**
     * Serializes the layout types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function getLayoutTypes()
    {
        $layoutTypes = array();
        foreach ($this->layoutTypeRegistry->getLayoutTypes() as $layoutType) {
            if (!$layoutType->isEnabled()) {
                continue;
            }

            $layoutTypes[] = new VersionedValue($layoutType, Version::API_V1);
        }

        return new Value($layoutTypes);
    }

    /**
     * Serializes the collection sources.
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function getSources()
    {
        $sources = array();
        foreach ($this->sourceRegistry->getSources() as $source) {
            $sources[] = new VersionedValue($source, Version::API_V1);
        }

        return new Value($sources);
    }

    /**
     * Returns the CSRF token.
     *
     * @return string|null
     */
    protected function getCsrfToken()
    {
        if (
            !$this->csrfTokenManager instanceof CsrfTokenManagerInterface ||
            $this->csrfTokenId === null
        ) {
            return null;
        }

        $token = $this->csrfTokenManager->getToken($this->csrfTokenId);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            $token = $this->csrfTokenManager->refreshToken($this->csrfTokenId);
        }

        return $token->getValue();
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }
}
