<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\RoutePrefix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(RoutePrefix::class)]
final class RoutePrefixTest extends TestCase
{
    private RoutePrefix $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RoutePrefix();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
