<?php

namespace Netgen\BlockManager\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

final class DoctrineDatabaseContext implements Context
{
    use TestCaseTrait;

    /**
     * @BeforeScenario
     */
    public function resetDatabase()
    {
        $this->createDatabase();
    }
}
