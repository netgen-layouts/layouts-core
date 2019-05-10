<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Context\Admin;

use Behat\Behat\Context\Context;

abstract class AdminContext implements Context
{
    /**
     * @Then /^there should be no error$/
     */
    abstract public function thereShouldBeNoError(): void;

    /**
     * @Then /^I should get an error saying "([^"]+)"$/
     */
    abstract public function iShouldGetAnError(string $errorMessage): void;
}
