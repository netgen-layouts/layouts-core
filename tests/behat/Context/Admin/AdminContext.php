<?php

namespace Netgen\BlockManager\Behat\Context\Admin;

use Behat\Behat\Context\Context;

abstract class AdminContext implements Context
{
    /**
     * @Then /^I should get an error saying "([^"]+)"$/
     *
     * @param string $errorMessage
     */
    abstract public function iShouldGetAnError($errorMessage);
}
