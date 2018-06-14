<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Context\Admin;

use Behat\Behat\Context\Context;

abstract class AdminContext implements Context
{
    /**
     * @Then /^I should get an error saying "([^"]+)"$/
     */
    abstract public function iShouldGetAnError(string $errorMessage): void;
}
