<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\LayoutService as LayoutServiceInterface;
use Netgen\BlockManager\API\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\API\Service\Mapper as MapperInterface;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Exception;

class LayoutService implements LayoutServiceInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\Validator\LayoutValidator
     */
    protected $layoutValidator;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\API\Service\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\Validator\LayoutValidator $layoutValidator
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\API\Service\Mapper $mapper
     */
    public function __construct(
        LayoutValidator $layoutValidator,
        Handler $persistenceHandler,
        MapperInterface $mapper
    ) {
        $this->layoutValidator = $layoutValidator;
        $this->persistenceHandler = $persistenceHandler;
        $this->mapper = $mapper;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status = Layout::STATUS_PUBLISHED)
    {
        if (!is_int($layoutId) && !is_string($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must be an integer or a string.');
        }

        if (empty($layoutId)) {
            throw new InvalidArgumentException('layoutId', 'Value must not be empty.');
        }

        $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout($layoutId, $status);

        return $this->mapper->mapLayout($layout);
    }

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If zone ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If zone with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($zoneId, $status = Layout::STATUS_PUBLISHED)
    {
        if (!is_int($zoneId) && !is_string($zoneId)) {
            throw new InvalidArgumentException('zoneId', 'Value must be an integer or a string.');
        }

        if (empty($zoneId)) {
            throw new InvalidArgumentException('zoneId', 'Value must not be empty.');
        }

        return $this->mapper->mapZone(
            $this->persistenceHandler->getLayoutHandler()->loadZone($zoneId, $status)
        );
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $parentLayout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, Layout $parentLayout = null)
    {
        $this->layoutValidator->validateLayoutCreateStruct($layoutCreateStruct);

        $this->persistenceHandler->beginTransaction();

        try {
            $createdLayout = $this->persistenceHandler->getLayoutHandler()->createLayout(
                $layoutCreateStruct,
                $parentLayout !== null ? $parentLayout->getId() : null
            );
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($createdLayout);
    }

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param bool $createNew
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(Layout $layout, $createNew = true, $status = Layout::STATUS_PUBLISHED, $newStatus = Layout::STATUS_DRAFT)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            $copiedLayout = $this->persistenceHandler->getLayoutHandler()->copyLayout(
                $layout->getId(),
                $createNew,
                $status,
                $newStatus
            );

            // @TODO Copy blocks and block items
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();

        return $this->mapper->mapLayout($copiedLayout);
    }

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     */
    public function deleteLayout(Layout $layout, $status = null)
    {
        $this->persistenceHandler->beginTransaction();

        try {
            // @TODO Delete blocks and block items

            $this->persistenceHandler->getLayoutHandler()->deleteLayout($layout->getId(), $status);
        } catch (Exception $e) {
            $this->persistenceHandler->rollbackTransaction();
            throw $e;
        }

        $this->persistenceHandler->commitTransaction();
    }

    /**
     * Creates a new layout create struct.
     *
     * @param string $identifier
     * @param string $name
     * @param string[] $zoneIdentifiers
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($identifier, $name, array $zoneIdentifiers)
    {
        return new LayoutCreateStruct(
            array(
                'identifier' => $identifier,
                'name' => $name,
                'zoneIdentifiers' => $zoneIdentifiers,
            )
        );
    }
}
