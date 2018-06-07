<?php

namespace Netgen\BlockManager\Transfer\Output;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Transfer\Descriptor;

/**
 * Serializer serializes domain entities into hash representation, which can be
 * transferred through a plain text format, like JSON or XML.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 */
final class Serializer implements SerializerInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Transfer\Output\VisitorInterface
     */
    private $visitor;

    public function __construct(
        LayoutService $layoutService,
        LayoutResolverService $layoutResolverService,
        VisitorInterface $visitor
    ) {
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;
        $this->visitor = $visitor;
    }

    public function serializeLayouts(array $layoutIds)
    {
        $data = $this->createBasicData();

        foreach ($this->loadLayouts($layoutIds) as $layout) {
            $data['entities'][] = $this->visitor->visit($layout);
        }

        return $data;
    }

    public function serializeRules(array $ruleIds)
    {
        $data = $this->createBasicData();

        foreach ($this->loadRules($ruleIds) as $rule) {
            $data['entities'][] = $this->visitor->visit($rule);
        }

        return $data;
    }

    /**
     * Loads the layouts for provided IDs.
     *
     * @param array $layoutIds
     *
     * @return \Generator
     */
    private function loadLayouts(array $layoutIds)
    {
        foreach ($layoutIds as $layoutId) {
            try {
                yield $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                continue;
            }
        }
    }

    /**
     * Loads the rules for provided IDs.
     *
     * @param array $ruleIds
     *
     * @return \Generator
     */
    private function loadRules(array $ruleIds)
    {
        foreach ($ruleIds as $ruleId) {
            try {
                yield $this->layoutResolverService->loadRule($ruleId);
            } catch (NotFoundException $e) {
                continue;
            }
        }
    }

    /**
     * Creates the array with basic serialized data from provided type and version.
     *
     * @return array
     */
    private function createBasicData()
    {
        return [
            '__version' => Descriptor::FORMAT_VERSION,
            'entities' => [],
        ];
    }
}
