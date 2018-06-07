<?php

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
     * @var \Netgen\BlockManager\API\Values\Value
     */
    private $entity;

    public function __construct($entityType, array $data, Value $entity)
    {
        $this->entityType = $entityType;
        $this->data = $data;
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
     * Returns the imported entity.
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
