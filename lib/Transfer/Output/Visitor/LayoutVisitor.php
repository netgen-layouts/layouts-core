<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Layout value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Layout\Layout
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Layout\Layout>
 */
final class LayoutVisitor implements VisitorInterface
{
    use StatusStringTrait;

    private const string ENTITY_TYPE = 'layout';

    public function accept(object $value): bool
    {
        return $value instanceof Layout;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            '__type' => self::ENTITY_TYPE,
            'id' => $value->id->toString(),
            'type_identifier' => $value->layoutType->identifier,
            'name' => $value->name,
            'description' => $value->description,
            'status' => $this->getStatusString($value),
            'main_locale' => $value->mainLocale,
            'available_locales' => $value->availableLocales,
            'creation_date' => $value->created->getTimestamp(),
            'modification_date' => $value->modified->getTimestamp(),
            'is_shared' => $value->isShared,
            'zones' => [...$this->visitZones($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $layout zones into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitZones(Layout $layout, OutputVisitor $outputVisitor): Generator
    {
        foreach ($layout->zones as $zone) {
            yield $zone->identifier => $outputVisitor->visit($zone);
        }
    }
}
