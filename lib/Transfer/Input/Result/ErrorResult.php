<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Input\Result;

use Throwable;

final class ErrorResult implements ResultInterface
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
     * @var \Throwable
     */
    private $error;

    public function __construct(string $entityType, array $data, Throwable $error)
    {
        $this->entityType = $entityType;
        $this->data = $data;
        $this->error = $error;
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
     * Returns the import error.
     */
    public function getError(): Throwable
    {
        return $this->error;
    }
}
