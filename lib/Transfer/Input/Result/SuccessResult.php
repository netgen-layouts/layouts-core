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
     * @param array<string, mixed> $data
     * @param int|string $entityId
     * @param \Netgen\BlockManager\API\Values\Value $entity
     */
    public function __construct(string $entityType, array $data, $entityId, Value $entity)
    {
        $this->entityType = $entityType;
        $this->data = $data;
        $this->entityId = $entityId;
        $this->entity = $entity;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getData(): array
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
     */
    public function getEntity(): Value
    {
        return $this->entity;
    }
}
