<?php

namespace Netgen\BlockManager\Core\Service;

use Exception;
use Netgen\BlockManager\API\Service\Service as APIService;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Persistence\Handler;

abstract class Service implements APIService
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    private $persistenceHandler;

    public function __construct(Handler $persistenceHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        try {
            $return = $callable();
        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        $this->commitTransaction();

        return $return;
    }

    public function beginTransaction()
    {
        $this->persistenceHandler->beginTransaction();
    }

    public function commitTransaction()
    {
        try {
            $this->persistenceHandler->commitTransaction();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function rollbackTransaction()
    {
        try {
            $this->persistenceHandler->rollbackTransaction();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
