<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\Service as APIService;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Throwable;

abstract class Service implements APIService
{
    /**
     * @var \Netgen\BlockManager\Persistence\HandlerInterface
     */
    private $persistenceHandler;

    public function __construct(HandlerInterface $persistenceHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        try {
            $return = $callable();
        } catch (Throwable $t) {
            $this->rollbackTransaction();
            throw $t;
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
        } catch (Throwable $t) {
            throw new RuntimeException($t->getMessage(), 0, $t);
        }
    }

    public function rollbackTransaction()
    {
        try {
            $this->persistenceHandler->rollbackTransaction();
        } catch (Throwable $t) {
            throw new RuntimeException($t->getMessage(), 0, $t);
        }
    }
}
