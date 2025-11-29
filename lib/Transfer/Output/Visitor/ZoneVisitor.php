<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Zone value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Layout\Zone
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Layout\Zone>
 */
final class ZoneVisitor implements VisitorInterface
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    public function accept(object $value): bool
    {
        return $value instanceof Zone;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'identifier' => $value->identifier,
            'linked_zone' => $this->visitLinkedZone($value),
            'blocks' => [...$this->visitBlocks($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $zone linked zone into hash representation.
     *
     * @return array<string, mixed>|null
     */
    private function visitLinkedZone(Zone $zone): ?array
    {
        $linkedZone = $zone->linkedZone;

        if (!$linkedZone instanceof Zone) {
            return null;
        }

        return [
            'identifier' => $linkedZone->identifier,
            'layout_id' => $linkedZone->layoutId->toString(),
        ];
    }

    /**
     * Visit the given $zone blocks into hash representation.
     *
     * Note: here we rely on API returning blocks already sorted by their position in the zone.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitBlocks(Zone $zone, OutputVisitor $outputVisitor): iterable
    {
        foreach ($this->blockService->loadZoneBlocks($zone) as $block) {
            yield $outputVisitor->visit($block);
        }
    }
}
