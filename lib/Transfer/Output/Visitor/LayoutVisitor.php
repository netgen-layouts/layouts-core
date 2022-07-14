<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;

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

    public const ENTITY_TYPE = 'layout';

    public function accept(object $value): bool
    {
        return $value instanceof Layout;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            '__type' => self::ENTITY_TYPE,
            'id' => $value->getId()->toString(),
            'type_identifier' => $value->getLayoutType()->getIdentifier(),
            'name' => $value->getName(),
            'description' => $value->getDescription(),
            'status' => $this->getStatusString($value),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'creation_date' => $value->getCreated()->getTimestamp(),
            'modification_date' => $value->getModified()->getTimestamp(),
            'is_shared' => $value->isShared(),
            'zones' => iterator_to_array($this->visitZones($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $layout zones into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitZones(Layout $layout, OutputVisitor $outputVisitor): Generator
    {
        foreach ($layout->getZones() as $zone) {
            yield $zone->getIdentifier() => $outputVisitor->visit($zone);
        }
    }
}
