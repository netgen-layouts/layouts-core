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

    public function visit($layout, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Layout\Layout $layout */

        return [
            '__type' => 'layout',
            'id' => $layout->getId(),
            'type_identifier' => $layout->getLayoutType()->getIdentifier(),
            'name' => $layout->getName(),
            'description' => $layout->getDescription(),
            'status' => $this->getStatusString($layout),
            'main_locale' => $layout->getMainLocale(),
            'available_locales' => $layout->getAvailableLocales(),
            'creation_date' => $layout->getCreated()->getTimestamp(),
            'modification_date' => $layout->getModified()->getTimestamp(),
            'is_shared' => $layout->isShared(),
            'zones' => iterator_to_array($this->visitZones($layout, $subVisitor)),
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
