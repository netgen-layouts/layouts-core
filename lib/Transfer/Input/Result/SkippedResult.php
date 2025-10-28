<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Ramsey\Uuid\UuidInterface;

final class SkippedResult implements ResultInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private string $entityType,
        private array $data,
        private UuidInterface $entityId,
    ) {}

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
