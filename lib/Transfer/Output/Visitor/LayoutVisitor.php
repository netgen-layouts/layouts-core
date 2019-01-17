<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Generator;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\StatusStringTrait;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Layout value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Layout\Layout
 */
final class LayoutVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function accept($value): bool
    {
        return $value instanceof Layout;
    }

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /** @var \Netgen\BlockManager\API\Values\Layout\Layout $value */

        return [
            '__type' => 'layout',
            'id' => $value->getId(),
            'type_identifier' => $value->getLayoutType()->getIdentifier(),
            'name' => $value->getName(),
            'description' => $value->getDescription(),
            'status' => $this->getStatusString($value),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'creation_date' => $value->getCreated()->getTimestamp(),
            'modification_date' => $value->getModified()->getTimestamp(),
            'is_shared' => $value->isShared(),
            'zones' => iterator_to_array($this->visitZones($value, $subVisitor)),
        ];
    }

    /**
     * Visit the given $layout zones into hash representation.
     */
    private function visitZones(Layout $layout, VisitorInterface $subVisitor): Generator
    {
        foreach ($layout->getZones() as $zone) {
            yield $zone->getIdentifier() => $subVisitor->visit($zone, $subVisitor);
        }
    }
}
