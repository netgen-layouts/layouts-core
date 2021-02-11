<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\RoutePrefix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RoutePrefixTest extends TestCase
{
    private RoutePrefix $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RoutePrefix();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\RoutePrefix::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
