<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service\Doctrine;

use Netgen\Layouts\Core\Service\LayoutService;
use Netgen\Layouts\Tests\Core\Service\LayoutServiceTestBase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutService::class)]
final class LayoutServiceTest extends LayoutServiceTestBase
{
    use TestCaseTrait;
}
