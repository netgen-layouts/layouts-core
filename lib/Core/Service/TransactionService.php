<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Service;

use Netgen\Layouts\API\Service\TransactionService as APITransactionService;
use Netgen\Layouts\Persistence\TransactionHandlerInterface;

final class TransactionService implements APITransactionService
{
    use TransactionTrait;

    public function __construct(TransactionHandlerInterface $transactionHandler)
    {
        $this->transactionHandler = $transactionHandler;
    }
}
