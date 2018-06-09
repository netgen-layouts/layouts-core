<?php

namespace Netgen\BlockManager\Transfer\Input\Result;

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

    /**
     * @param string $entityType
     * @param array $data
     * @param \Throwable $error
     */
    public function __construct($entityType, array $data, /* Throwable */ $error)
    {
        $this->entityType = $entityType;
        $this->data = $data;
        $this->error = $error;
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
     * Returns the import error.
     *
     * @return \Throwable
     */
    public function getError()
    {
        return $this->error;
    }
}
