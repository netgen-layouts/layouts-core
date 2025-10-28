<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Ramsey\Uuid\UuidInterface;
use Throwable;

final class ErrorResult implements ResultInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private string $entityType,
        private array $data,
        private UuidInterface $entityId,
        private Throwable $error,
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

    /**
     * Returns the import error.
     */
    public function getError(): Throwable
    {
        return $this->error;
    }
}
