<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\UuidInterface;

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
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $entityId;

    /**
     * @var \Netgen\Layouts\API\Values\Value
     */
    private $entity;

    public function __construct(string $entityType, array $data, UuidInterface $entityId, Value $entity)
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
     * Returns the UUID of the entity which was imported.
     */
    public function getEntityId(): UuidInterface
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
