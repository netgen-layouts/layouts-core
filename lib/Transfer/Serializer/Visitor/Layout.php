<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\Layout\Layout as LayoutValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Layout value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Layout\Layout
 */
class Layout extends Visitor
{
    public function accept($value)
    {
        return $value instanceof LayoutValue;
    }

    public function visit($layout, Visitor $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Layout\Layout $layout */

        return array(
            'id' => $layout->getId(),
            'type_identifier' => $layout->getLayoutType()->getIdentifier(),
            'name' => $layout->getName(),
            'description' => $layout->getDescription(),
            'status' => $this->getStatusString($layout),
            'is_published' => $layout->isPublished(),
            'main_locale' => $layout->getMainLocale(),
            'available_locales' => $layout->getAvailableLocales(),
            'creation_date' => $layout->getCreated()->getTimestamp(),
            'modification_date' => $layout->getModified()->getTimestamp(),
            'is_shared' => $layout->isShared(),
            'zones' => $this->visitZones($layout, $subVisitor),
        );
    }

    /**
     * Visit the given $layout zones into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return array
     */
    private function visitZones(LayoutValue $layout, Visitor $subVisitor)
    {
        $hash = array();

        foreach ($layout->getZones() as $zone) {
            $hash[$zone->getIdentifier()] = $subVisitor->visit($zone);
        }

        return $hash;
    }
}
