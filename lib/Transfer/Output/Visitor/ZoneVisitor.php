<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Generator;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Zone value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Layout\Zone
 */
final class ZoneVisitor implements VisitorInterface
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
        return $value instanceof Zone;
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
            'blocks' => iterator_to_array($this->visitBlocks($zone, $subVisitor)),
        ];
    }

    /**
     * Visit the given $zone linked zone into hash representation.
     */
    private function visitLinkedZone(Zone $zone): ?array
    {
        $linkedZone = $zone->getLinkedZone();

        if (!$linkedZone instanceof Zone) {
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
    private function visitBlocks(Zone $zone, VisitorInterface $subVisitor): Generator
    {
        foreach ($this->blockService->loadZoneBlocks($zone) as $block) {
            yield $subVisitor->visit($block, $subVisitor);
        }
    }
}
