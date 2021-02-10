<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\UuidInterface;

final class SuccessResult implements ResultInterface
{
    private string $entityType;

    /**
     * @var array<string, mixed>
     */
    private array $data;

    private UuidInterface $entityId;

    private Value $entity;

    /**
     * @param array<string, mixed> $data
     */
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
