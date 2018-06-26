<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Layout\Zone as ZoneValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Zone value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Layout\Zone
 */
final class Zone extends Visitor
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function accept($value): bool
    {
        return $value instanceof ZoneValue;
    }

    public function visit($zone, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Layout\Zone $zone */

        return [
            'identifier' => $zone->getIdentifier(),
            'linked_zone' => $this->visitLinkedZone($zone),
            'blocks' => $this->visitBlocks($zone, $subVisitor),
        ];
    }

    /**
     * Visit the given $zone linked zone into hash representation.
     */
    private function visitLinkedZone(ZoneValue $zone): ?array
    {
        $linkedZone = $zone->getLinkedZone();

        if (!$linkedZone instanceof ZoneValue) {
            return null;
        }

        return [
            'identifier' => $linkedZone->getIdentifier(),
            'layout_id' => $linkedZone->getLayoutId(),
        ];
    }

    /**
     * Visit the given $zone blocks into hash representation.
     *
     * Note: here we rely on API returning blocks already sorted by their position in the zone.
     */
    private function visitBlocks(ZoneValue $zone, VisitorInterface $subVisitor): array
    {
        $hash = [];
        $blocks = $this->blockService->loadZoneBlocks($zone);

        foreach ($blocks as $block) {
            $hash[] = $subVisitor->visit($block, $subVisitor);
        }

        return $hash;
    }
}
