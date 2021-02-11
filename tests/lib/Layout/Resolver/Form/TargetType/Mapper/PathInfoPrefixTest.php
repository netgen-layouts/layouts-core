<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PathInfoPrefixTest extends TestCase
{
    private PathInfoPrefix $mapper;

    protected function setUp(): void
    {
        $this->mapper = new PathInfoPrefix();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefix::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
