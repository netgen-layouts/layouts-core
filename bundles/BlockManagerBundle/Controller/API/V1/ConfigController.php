<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistryInterface;
use Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface;
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
    private $blockTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistryInterface
     */
    private $blockTypeGroupRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var string
     */
    private $csrfTokenId;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface $blockTypeRegistry
     * @param \Netgen\BlockManager\Block\Registry\BlockTypeGroupRegistryInterface $blockTypeGroupRegistry
     * @param \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @param string $csrfTokenId
     */
    public function __construct(
        BlockTypeRegistryInterface $blockTypeRegistry,
        BlockTypeGroupRegistryInterface $blockTypeGroupRegistry,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        CsrfTokenManagerInterface $csrfTokenManager,
        $csrfTokenId
    ) {
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->blockTypeGroupRegistry = $blockTypeGroupRegistry;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
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
        foreach ($this->blockTypeGroupRegistry->getBlockTypeGroups(true) as $blockTypeGroup) {
            if (!empty($blockTypeGroup->getBlockTypes(true))) {
                $blockTypeGroups[] = new VersionedValue($blockTypeGroup, Version::API_V1);
            }
        }

        $blockTypes = array();
        foreach ($this->blockTypeRegistry->getBlockTypes(true) as $blockType) {
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
        foreach ($this->layoutTypeRegistry->getLayoutTypes(true) as $layoutType) {
            $layoutTypes[] = new VersionedValue($layoutType, Version::API_V1);
        }

        return new Value($layoutTypes);
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }

    /**
     * Returns the CSRF token.
     *
     * @return string|null
     */
    private function getCsrfToken()
    {
        $token = $this->csrfTokenManager->getToken($this->csrfTokenId);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            $token = $this->csrfTokenManager->refreshToken($this->csrfTokenId);
        }

        return $token->getValue();
    }
}
