<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;

final class DoctrineDatabaseContext implements Context
{
    use DatabaseTrait;

    #[BeforeScenario]
    public function resetDatabase(): void
    {
        $this->createDatabase();
    }

    #[AfterScenario]
    public function destroyDatabase(): void
    {
        $this->closeDatabase();
    }
}
