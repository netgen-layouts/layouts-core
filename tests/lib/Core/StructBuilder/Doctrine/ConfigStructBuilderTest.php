<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder\Doctrine;

use Netgen\Layouts\Tests\Core\StructBuilder\ConfigStructBuilderTest as BaseConfigStructBuilderTest;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;

final class ConfigStructBuilderTest extends BaseConfigStructBuilderTest
{
    use TestCaseTrait;

    protected function tearDown(): void
    {
        $this->closeDatabase();
    }
}
