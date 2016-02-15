<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;

class BlockTypesController extends Controller
{
    /**
     * Serializes the block types.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function viewBlockTypes()
    {
        $configuration = $this->get('netgen_block_manager.configuration');

        $configBlockTypeGroups = $configuration->getParameter('block_type_groups');
        $configBlockTypes = $configuration->getParameter('block_types');

        $blockTypeGroups = array();
        foreach ($configBlockTypeGroups as $identifier => $blockTypeGroup) {
            $blockTypeGroups[] = array(
                'identifier' => $identifier,
            ) + $blockTypeGroup;
        }

        $blockTypes = array();
        foreach ($configBlockTypes as $identifier => $blockType) {
            $blockTypes[] = array(
                'identifier' => $identifier,
            ) + $blockType;
        }

        $data = array(
            'block_type_groups' => $blockTypeGroups,
            'block_types' => $blockTypes,
        );

        return $this->handleData($data);
    }
}
