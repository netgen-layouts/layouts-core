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
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(Handler $persistenceHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * Runs the callable inside a transaction.
     *
     * @param callable $callable
     *
     * @throws \Exception When an error occurs.
     *
     * @return $mixed
     */
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

    /**
     * Begins a transaction.
     */
    public function beginTransaction()
    {
        $this->persistenceHandler->beginTransaction();
    }

    /**
     * Commits the transaction.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If no transaction has been started
     */
    public function commitTransaction()
    {
        try {
            $this->persistenceHandler->commitTransaction();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Rollbacks the transaction.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If no transaction has been started
     */
    public function rollbackTransaction()
    {
        try {
            $this->persistenceHandler->rollbackTransaction();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
