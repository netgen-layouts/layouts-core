<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RouteTest extends TestCase
{
    private Route $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Route();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\Route::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
