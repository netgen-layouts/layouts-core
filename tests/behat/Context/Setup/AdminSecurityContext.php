<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Setup;

use Behat\Behat\Context\Context;

final class AdminSecurityContext implements Context
{
    /**
     * @Given /^I am logged in as an administrator$/
     */
    public function iAmLoggedInAsAnAdministrator(): void
    {
        // No need to do anything
    }
}
