<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Layout\Zone as ZoneValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Zone value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Layout\Zone
 */
class Zone extends Visitor
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function accept($value)
    {
        return $value instanceof ZoneValue;
    }

    public function visit($zone, Visitor $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Layout\Zone $zone */

        return array(
            'identifier' => $zone->getIdentifier(),
            'status' => $this->getStatusString($zone),
            'is_published' => $zone->isPublished(),
            'linked_zone' => $this->visitLinkedZone($zone),
            'blocks' => $this->visitBlocks($zone, $subVisitor),
        );
    }

    /**
     * Visit the given $zone linked zone into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @return mixed
     */
    private function visitLinkedZone(ZoneValue $zone)
    {
        $hash = null;

        if ($zone->hasLinkedZone()) {
            $hash = array(
                'identifier' => $zone->getLinkedZone()->getIdentifier(),
                'layout_id' => $zone->getLinkedZone()->getLayoutId(),
            );
        }

        return $hash;
    }

    /**
     * Visit the given $zone blocks into hash representation.
     *
     * Note: here we rely on API returning blocks already sorted by their position in the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return mixed
     */
    private function visitBlocks(ZoneValue $zone, Visitor $subVisitor)
    {
        $hash = array();
        $blocks = $this->blockService->loadZoneBlocks($zone);

        foreach ($blocks as $block) {
            $hash[] = $subVisitor->visit($block);
        }

        return $hash;
    }
}
