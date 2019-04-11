<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class DoctrineDatabaseContext implements Context
{
    use TestCaseTrait;

    /**
     * @BeforeScenario
     */
    public function resetDatabase(): void
    {
        $this->createDatabase();
    }
}
