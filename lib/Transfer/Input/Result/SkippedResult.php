<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Ramsey\Uuid\UuidInterface;

final class SkippedResult implements ResultInterface
{
    private string $entityType;

    /**
     * @var array<string, mixed>
     */
    private array $data;

    private UuidInterface $entityId;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $entityType, array $data, UuidInterface $entityId)
    {
        $this->entityType = $entityType;
        $this->data = $data;
        $this->entityId = $entityId;
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
}
