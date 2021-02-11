<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PathInfoTest extends TestCase
{
    private PathInfo $mapper;

    protected function setUp(): void
    {
        $this->mapper = new PathInfo();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfo::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
