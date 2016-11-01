<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\ZoneDraft;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\LayoutDraft;
use Netgen\BlockManager\Persistence\Handler;

class LayoutMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(BlockMapper $blockMapper, Handler $persistenceHandler, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        parent::__construct($persistenceHandler);

        $this->blockMapper = $blockMapper;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Builds the API zone value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone|\Netgen\BlockManager\API\Values\Page\ZoneDraft
     */
    public function mapZone(PersistenceZone $zone)
    {
        $persistenceBlocks = $this->persistenceHandler->getBlockHandler()->loadZoneBlocks($zone);

        $linkedZone = null;

        if ($zone->linkedLayoutId !== null && $zone->linkedZoneIdentifier !== null) {
            try {
                // We're always using published versions of linked zones
                $linkedZone = $this->persistenceHandler->getLayoutHandler()->loadZone(
                    $zone->linkedLayoutId,
                    Value::STATUS_PUBLISHED,
                    $zone->linkedZoneIdentifier
                );

                $linkedZone = $this->mapZone($linkedZone);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            $blocks[] = $this->blockMapper->mapBlock($persistenceBlock);
        }

        $zoneData = array(
            'identifier' => $zone->identifier,
            'layoutId' => $zone->layoutId,
            'status' => $zone->status,
            'linkedZone' => $linkedZone,
            'blocks' => $blocks,
        );

        return $zone->status === Value::STATUS_PUBLISHED ?
            new Zone($zoneData) :
            new ZoneDraft($zoneData);
    }

    /**
     * Builds the API layout value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout|\Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function mapLayout(PersistenceLayout $layout)
    {
        $persistenceZones = $this->persistenceHandler->getLayoutHandler()->loadLayoutZones($layout);

        $zones = array();
        foreach ($persistenceZones as $persistenceZone) {
            $zones[$persistenceZone->identifier] = $this->mapZone($persistenceZone);
        }

        $layoutData = array(
            'id' => $layout->id,
            'layoutType' => $this->layoutTypeRegistry->getLayoutType(
                $layout->type
            ),
            'name' => $layout->name,
            'created' => $this->createDateTime($layout->created),
            'modified' => $this->createDateTime($layout->modified),
            'status' => $layout->status,
            'shared' => $layout->shared,
            'zones' => $zones,
        );

        return $layout->status === Value::STATUS_PUBLISHED ?
            new Layout($layoutData) :
            new LayoutDraft($layoutData);
    }
}
