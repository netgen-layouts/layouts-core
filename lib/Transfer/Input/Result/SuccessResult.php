<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input\Result;

use Netgen\BlockManager\API\Values\Value;

final class SuccessResult implements ResultInterface
{
    /**
     * @var string
     */
    private $entityType;

    /**
     * @var array
     */
    private $data;

    /**
     * @var int|string
     */
    private $entityId;

    /**
     * @var \Netgen\BlockManager\API\Values\Value
     */
    private $entity;

    /**
     * @param string $entityType
     * @param array $data
     * @param int|string $entityId
     * @param \Netgen\BlockManager\API\Values\Value $entity
     */
    public function __construct($entityType, array $data, $entityId, Value $entity)
    {
        $this->entityType = $entityType;
        $this->data = $data;
        $this->entityId = $entityId;
        $this->entity = $entity;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the ID of the entity which was imported.
     *
     * @return int|string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Returns the imported entity.
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
